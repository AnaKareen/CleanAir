<?php
// conexion.php

// Datos de conexión a MongoDB
$usuario = "20030837";            // Tu nombre de usuario
$contraseña = "DnE12345";         // Tu contraseña
$servidor = "mayolo.zleui.mongodb.net";  // Tu servidor MongoDB
$base_de_datos = "CalidadAire";   // Nombre de la base de datos

// Crear la URI de conexión
$uri = "mongodb+srv://$usuario:$contraseña@$servidor/$base_de_datos?retryWrites=true&w=majority";

// Intentamos crear una nueva conexión con el MongoDB Driver
try {
    // Crear la conexión a MongoDB usando el manager
    $manager = new MongoDB\Driver\Manager($uri);
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Error al conectar a MongoDB: " . $e->getMessage());
}

// Función para obtener las colecciones
function obtenerColecciones($manager, $base_de_datos) {
    $colecciones = [];
    try {
        // Usar el método listCollections
        $command = new MongoDB\Driver\Command(['listCollections' => 1]);
        $cursor = $manager->executeCommand($base_de_datos, $command);
        
        foreach ($cursor as $document) {
            $colecciones[] = $document->name; // Agregar el nombre de la colección
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        die("Error al obtener las colecciones: " . $e->getMessage());
    }
    return $colecciones;
}
?>