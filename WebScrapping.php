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

// Incluimos la librería simple_html_dom
include('simple_html_dom.php');

$extracted_comments = []; // Inicializamos el array para almacenar los comentarios extraídos

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    // Usamos la URL proporcionada en el formulario
    $url = $_POST['url'];

    // Usamos file_get_contents para obtener el HTML de la página
    $html = file_get_contents($url);

    // Creamos un objeto DOM a partir del HTML
    $dom = str_get_html($html);

    // Buscamos todos los comentarios
    foreach ($dom->find('div.wpforo-post') as $comment) {
        // Nombre del usuario (autor)
        $user = $comment->find('div.author-name', 0);
        $user_name = $user ? $user->plaintext : 'Desconocido';

        // Fecha de la respuesta
        $date = $comment->find('div.cbleft', 0);
        $date_text = $date ? $date->plaintext : 'Fecha no disponible';

        // Contenido del comentario
        $content = $comment->find('div.wpforo-post-content', 0);
        if ($content) {
            // Reemplazamos &nbsp; por un espacio en blanco
            $content_text = str_replace('&nbsp;', ' ', $content->plaintext);
            $content_text = str_replace('</span>', ' ', $content_text);
            $content_text = str_replace('</p>', ' ', $content_text);
            
            // Limpiamos otros caracteres no deseados
            $content_text = preg_replace('/[\x00-\x1F\x7F]/u', '', $content_text);
            $content_text = preg_replace('/\s+/', ' ', $content_text);
            $content_text = trim($content_text);
            
            // Limpiamos la fecha
            $date_text = str_replace('Respondido :', '', $date_text);
            $date_text = trim($date_text);
        } else {
            $content_text = 'Comentario no disponible';
        }

        // Truncar el contenido a 10,000 caracteres
        if (strlen($content_text) > 10000) {
            $content_text = substr($content_text, 0, 10000);
        }

        // Agregamos el comentario al array
        $extracted_comments[] = [
            'user' => $user_name,
            'date' => $date_text,
            'content' => $content_text,
        ];

        // Insertamos el comentario en la colección de MongoDB
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert([
            'usuario' => $user_name,
            'fecha' => $date_text,
            'comentario' => $content_text,
        ]);
        $manager->executeBulkWrite("$base_de_datos.foros", $bulk);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/form.css">
    <link rel="stylesheet" href="assets/dashboardg.css">
    <title>Comentarios del Foro</title>
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
                <a href="admin.php">Subir Archivos</a>
                <a href="WebScrapping.php">Web Scrapping</a>
            </nav>
            <div class="header-navigation -actions">
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
    <h1>Extracción de Comentarios del Foro</h1>
    
    <form method="post">
        <label for="url">Introduce la URL del foro:</label><br>
        <input type="url" id="url" name="url" placeholder="https://ejemplo.com" required style="width: 50%;"><br><br>
        <button type="submit">Extraer Comentarios</button>
    </form>

    <h2>Comentarios Extraídos</h2>
    <?php if (!empty($extracted_comments)): ?>
        <ul>
            <?php foreach ($extracted_comments as $comment): ?>
                <li>
                    <strong>Usuario:</strong> <?php echo htmlspecialchars($comment['user']); ?><br>
                    <strong>Fecha:</strong> <?php echo htmlspecialchars($comment['date']); ?><br>
                    <strong>Comentario:</strong> <?php echo htmlspecialchars($comment['content']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se encontraron comentarios o no se proporcionó una URL.</p>
    <?php endif; ?>
</body>
</html>