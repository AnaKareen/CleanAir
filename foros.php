<?php
include 'backend/conexion.php';
session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige a la página de inicio de sesión
    exit();
}

// Obtener información del usuario
$usuario = $_SESSION['usuario'];
$nombreUsuario = $usuario->nombre; // Suponiendo que el campo 'nombre' existe en el documento del usuario
$ciudadUsuario = $usuario->ZONAS_METROPOLITANAS_O_POBLACIONES; // Asegúrate de que este campo existe

// Obtener los comentarios de la colección 'foros'
$comentarios = [];
try {
    $query = new MongoDB\Driver\Query([]);
    $comentarios = $manager->executeQuery("$base_de_datos.foros", $query)->toArray();
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Error al obtener comentarios: " . $e->getMessage();
}
?>

<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:300,400|Montserrat&display=swap" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/forocard.css">
    <link rel="icon" href="https://cdn-icons-png.freepik.com/512/5722/5722394.png?ga=GA1.1.1543009478.1729142181">

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <title>CleanAir</title>
</head>
<body>
<?php include 'menuin.php'; ?>

<!-- Aquí comienza el contenido que deseas mostrar -->
<div class="welcome-message">
    <h1>¡Hola, <?php echo htmlspecialchars($nombreUsuario); ?>!</h1>
</div>

<!-- Mostrar comentarios -->
<?php if (!empty($comentarios)): ?>
    <div class="card-container">
        <?php foreach ($comentarios as $comentario): ?>
            <div class="card">
                <div class="container">
                    <div class="left">
                        <div class="status-ind"></div>
                    </div>
                    <div class="right">
                        <div class="text-wrap">
                            <p class="text-content">
                                <a class="text-link" href="#"><?php echo htmlspecialchars($comentario->usuario); ?></a> comentó: 
                                <?php 
                                // Reemplaza &nbsp; por un espacio normal
                                $comentario_texto = str_replace('&nbsp;', ' ', $comentario->comentario);
                                echo htmlspecialchars($comentario_texto); 
                                ?>
                            </p>
                            <p class="time"><?php echo htmlspecialchars($comentario->fecha); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

<div id="container" style="height: 500px; min-width: 310px; max-width: 800px; margin: 0 auto;"></div>

<!-- gsap-latest-beta.min.js -->
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js?r=5426'></script>
<!-- ScrollTrigger.min.js -->
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js'></script>
<script src="assets/script.js"></script>
</body>
</html>