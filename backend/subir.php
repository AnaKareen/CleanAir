<?php
require_once '../vendor/autoload.php'; // Cargar autoload de Composer
include 'conexion.php'; // Conexión a MongoDB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $coleccion = $_POST['coleccion'];
    $archivo = $_FILES['archivo'];

    // Validar si ocurrió algún error durante la subida del archivo
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir el archivo. Código de error: " . $archivo['error']);
    }

    // Verificar si el archivo está vacío
    if (filesize($archivo['tmp_name']) == 0) {
        die("El archivo está vacío.");
    }

    // Obtener el tipo MIME del archivo
    $mimeType = obtenerMimeType($archivo['tmp_name']);
    if (!$mimeType) {
        die("No se pudo determinar el tipo MIME del archivo.");
    }

    echo "Tipo MIME del archivo: " . $mimeType . "<br>"; // Depuración

    // Leer el contenido del archivo
    $contenido = file_get_contents($archivo['tmp_name']);
    echo "<pre>Contenido del archivo:\n" . htmlspecialchars($contenido) . "</pre>"; // Depuración

    $documento = null;

    // Verificar el tipo MIME y procesar el archivo
    if ($mimeType === 'application/pdf') {
        echo "Procesando archivo PDF...<br>";
        $datosExtraidos = extraerDatosDesdePDF($archivo['tmp_name']);
        $documento = json_decode($datosExtraidos, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error al interpretar el contenido del PDF como JSON: " . json_last_error_msg());
        }
    } elseif ($mimeType === 'application/json') {
        echo "Procesando archivo JSON...<br>";
        $documento = json_decode($contenido, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error al interpretar el archivo JSON: " . json_last_error_msg());
        }
    } elseif ($mimeType === 'application/x-ndjson') {
        echo "Procesando archivo NDJSON...<br>";
        $lineas = explode(PHP_EOL, $contenido); // Dividir en líneas
        $documento = [];
        foreach ($lineas as $linea) {
            $registro = json_decode($linea, true); // Decodificar cada línea como JSON
            if (json_last_error() === JSON_ERROR_NONE) {
                $documento[] = $registro;
            } else {
                echo "Error de JSON en línea: " . htmlspecialchars($linea) . "<br>";
            }
        }
    } elseif ($mimeType === 'text/csv' || $mimeType === 'application/vnd.ms-excel') {
        echo "Procesando archivo CSV...<br>";
        $documento = procesarCSV($contenido);
    } elseif ($mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        echo "Procesando archivo DOCX...<br>";
        $documento = extraerDatosDesdeDOCX($archivo['tmp_name']);
    } elseif ($mimeType === 'text/plain') {
        echo "Procesando archivo TXT...<br>";
        $documento = extraerDatosDesdeTXT($archivo['tmp_name']);
    } else {
        die("Tipo de archivo no soportado. Se recibió: $mimeType");
    }
    
    // Validar si se procesaron correctamente los datos
    if (empty($documento)) {
        die("El archivo procesado no contiene datos válidos.");
    }

    try {
        $bulk = new MongoDB\Driver\BulkWrite;

        // Verificar si el documento es un array de documentos
        if (is_array($documento) && isset($documento[0]) && is_array($documento[0])) {
            foreach ($documento as $doc) {
                $bulk->insert($doc);
            }
        } else {
            $bulk->insert($documento);
        }

        // Insertar los documentos en MongoDB
        $resultado = $manager->executeBulkWrite("$base_de_datos.$coleccion", $bulk);
        echo "Archivo subido exitosamente a la colección '$coleccion'. Documentos insertados: " . $resultado->getInsertedCount();
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("Error al insertar los documentos en MongoDB: " . $e->getMessage());
    }
} else {
    echo "Método de solicitud no válido.";
}

function obtenerMimeType($rutaArchivo) {
    $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));

    // Verifica si la extensión del archivo es .txt
    if ($extension === 'txt') {
        return 'text/plain';
    }

    // Verifica si la extensión del archivo es .ndjson
    if ($extension === 'ndjson') {
        return 'application/x-ndjson';
    }

    // Usar finfo para obtener el tipo MIME
    if (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $rutaArchivo);
        finfo_close($finfo);

        // Corregir errores comunes en el tipo MIME detectado
        if ($mimeType === 'application/x-ndjason') {
            $mimeType = 'application/x-ndjson';
        }

        echo "Tipo MIME detectado: $mimeType<br>"; // Imprimir tipo MIME detectado
        return $mimeType;
    }

    // Valor por defecto si no se puede determinar el tipo MIME
    return 'application/octet-stream';
}

// Funciones para procesar los diferentes tipos de archivos (PDF, CSV, DOCX, TXT)...
// Incluye las funciones extraerDatosDesdePDF, procesarCSV, extraerDatosDesdeDOCX, extraerDatosDesdeTXT aquí.


// Función para extraer datos de un archivo PDF
function extraerDatosDesdePDF($rutaArchivo) {
    try {
        $parser = new Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($rutaArchivo);
        $textoExtraido = $pdf->getText();
        echo "Texto extraído: " . $textoExtraido . "<br>";  // Depuración
        $filas = explode("\n", $textoExtraido);
        $documentos = [];
        foreach ($filas as $fila) {
            $columnas = preg_split('/\s{2,}/', trim($fila));
            if (count($columnas) >  1) {
                $documento = [];
                foreach ($columnas as $i => $columna) {
                    $documento["campo_$i"] = $columna;
                }
                $documentos[] = $documento;
            }
        }
        return json_encode($documentos);
    } catch (Exception $e) {
        die("Error al procesar el archivo PDF: " . $e->getMessage());
    }
}

// Función para procesar un archivo CSV
function procesarCSV($contenido) {
    $filas = explode(PHP_EOL, $contenido); // Dividir el archivo en líneas
    $encabezados = []; // Para almacenar los nombres de los campos
    $documentos = [];

    foreach ($filas as $index => $fila) {
        if (trim($fila) === '') continue; // Ignorar líneas vacías

        $datos = str_getcsv($fila); // Convertir la línea en un array

        if ($index === 0) {
            // La primera fila es el encabezado
            $encabezados = $datos;
        } else {
            // Resto de las filas son datos
            $documento = [];

            foreach ($datos as $indice => $valor) {
                $campo = isset($encabezados[$indice]) ? $encabezados[$indice] : "campo_$indice";
                $documento[$campo] = $valor; // Asignar el valor al campo correspondiente
            }

            $documentos[] = $documento; // Agregar el documento procesado
        }
    }

    return $documentos;
}

// Función para extraer datos de un archivo DOCX
function extraerDatosDesdeDOCX($rutaArchivo) {
    try {
        // Cargar el archivo DOCX
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($rutaArchivo);
        $documentos = [];

        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();

            foreach ($elements as $element) {
                // Verificar si el elemento contiene texto
                if (method_exists($element, 'getText')) {
                    $texto = trim($element->getText());

                    // Decodificar entidades HTML
                    $textoDecodificado = htmlspecialchars_decode($texto);

                    if (!empty($textoDecodificado)) {
                        // Separar líneas (si el texto tiene varios registros)
                        $lineas = explode("\n", $textoDecodificado);

                        foreach ($lineas as $linea) {
                            $usuario = json_decode($linea, true);

                            if (json_last_error() === JSON_ERROR_NONE) {
                                // Añadir usuario al array de documentos
                                $documentos[] = $usuario;
                            } else {
                                echo "<pre>Error de JSON en línea: " . htmlspecialchars($linea) . "</pre>";
                            }
                        }
                    }
                }
            }
        }

        if (empty($documentos)) {
            throw new Exception ("El archivo DOCX no contiene datos válidos.");
        }

        return $documentos;
    } catch (Exception $e) {
        die("Error al procesar el archivo DOCX: " . $e->getMessage());
    }
}

// Función para extraer datos de un archivo TXT
function extraerDatosDesdeTXT($rutaArchivo) {
    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $documentos = [];

    foreach ($lineas as $linea) {
        $documento = json_decode($linea, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $documentos[] = $documento;
        } else {
            echo "<pre>Error de JSON en línea: " . htmlspecialchars($linea) . "</pre>";
        }
    }

    return $documentos;
}
?>