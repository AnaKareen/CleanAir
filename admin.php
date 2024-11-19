<?php

// Incluir el archivo de conexión
include 'backend/conexion.php';

// Obtener las colecciones de la base de datos
$colecciones = obtenerColecciones($manager, $base_de_datos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="assets/form.css">
    <meta charset="UTF-8">
    <title>Subir Archivos a MongoDB</title>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').addEventListener('submit', function(event) {
            const archivoInput = document.getElementById('archivo');
            const tipoInput = document.getElementById('tipo');
            
            // Validar que se ha seleccionado un archivo
            if (!archivoInput.files.length) {
                alert('Por favor, selecciona un archivo.');
                event.preventDefault(); // Evitar el envío del formulario
                return;
            }

            // Validar el tipo de archivo
            const fileType = archivoInput.files[0].type;
            const allowedTypes = [
                'application/pdf', 
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                'application/json', 
                'text/csv', 
                'text/plain'  // Agregado para permitir archivos .txt
            ];
            
            if (!allowedTypes.includes(fileType)) {
                alert('Tipo de archivo no permitido. Solo se permiten PDF, Word, CSV, JSON y TXT.');
                event.preventDefault(); // Evitar el envío del formulario
            }
        });
    });
    </script>
</head>
<body>
    <h1>Subir Archivos a MongoDB</h1>
    <form action="backend/subir.php" method="POST" enctype="multipart/form-data">
        <label for="tipo">Selecciona el tipo de archivo:</label>
        <select name="tipo" id="tipo" required>
            <option value="csv">CSV</option>
            <option value="pdf">PDF</option>
            <option value="word">Word</option>
            <option value="json">JSON</option>
            <option value="txt">Text</option> <!-- Opción para archivo .txt -->
        </select>
        <br><br>
        <label for="coleccion">Selecciona la colección:</label>
        <select name="coleccion" id="coleccion" required>
            <?php
            // Generar opciones para el select de colecciones
            foreach ($colecciones as $coleccion) {
                echo '<option value="' . $coleccion . '">' . $coleccion . '</option>'; // Corrección aquí
            }
            ?>
        </select>
        <br><br>
        <label for="archivo">Selecciona el archivo:</label>
        <input type="file" name="archivo" id="archivo" required>
        <br><br>
        <button type="submit">Subir Archivo</button>
    </form>
</body>
</html>
