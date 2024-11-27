<?php

// Incluir el archivo de conexión
include 'backend/conexion.php';


// Obtener las colecciones de la base de datos
$colecciones = obtenerColecciones($manager, $base_de_datos);

session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige a la página de inicio de sesión
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="assets/form.css">
    <link rel="stylesheet" href="assets/dashboardg.css">

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
                    'text/plain' // Agregado para permitir archivos .txt
                ];

                if (!allowedTypes.includes(fileType)) {
                    alert('Tipo de archivo no permitido. Solo se permiten PDF, Word, CSV, JSON y TXT.');
                    event.preventDefault(); // Evitar el envío del formulario
                }
            });
        });
    </script>
</head>
<header class="header">
    <div class="header-content responsive-wrapper">
        <div class="header-logo">
            <a class="logo">
                <h1>CleanAir</h1>
            </a>
        </div>
        <div class="header-navigation">
            <nav class="header-navigation-links">
            <a href="WebScrapping.php">Web Scrapping</a>
                
            </nav>
            <div class="header-navigation-actions">
                <!-- Botón de cerrar sesión -->
                <a href="backend/logout.php" class="button">
                    <i class="ph-lightning-bold"></i>
                    <span>Cerrar sesión</span>
                </a>
                <a href="#" class="icon-button">
                    <i class="ph-gear-bold"></i>
                </a>
                <a href="#" class="icon-button">
                    <i class="ph-bell-bold"></i>
                </a>
                <a href="#" class="avatar">
                    <img src="https://assets.codepen.io/285131/hat-man.png" alt="Avatar">
                </a>
            </div>
        </div>
        <a href="#" class="button">
            <i class="ph-list-bold"></i>
            <span>Menu</span>
        </a>
    </div>
</header>

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
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js?r=5426'></script>
    <!-- ScrollTrigger.min.js -->
    <script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js'></script>
<script src="assets/script.js"></script>


</html>