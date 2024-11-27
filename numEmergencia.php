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

// Consulta a la colección servicioAyuda
try {
    $coleccion = "serviciosAyuda"; // Nombre de la colección
    $query = new MongoDB\Driver\Query([]); // Puedes especificar un filtro si es necesario
    $cursor = $manager->executeQuery("$base_de_datos.$coleccion", $query);

    // Obtiene todos los documentos
    $servicioAyudaDocs = $cursor->toArray(); // Obtiene todos los documentos en un array
} catch (MongoDB\Driver\Exception\Exception $e) {
    $servicioAyudaDocs = []; // Manejo de error: inicializa como un array vacío
}
?>

<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:300,400|Montserrat&display=swap" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/cards.css">
    <link rel="icon" href="https://cdn-icons-png.freepik.com/512/5722/5722394.png?ga=GA1.1.1543009478.1729142181">

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <title>CleanAir</title>
</head>

<body>
    <?php include 'menuin.php'; ?>

    <!-- Aquí comienza el contenido que deseas mostrar -->
    

 

    <div class="margCentral">
        <div class="info-servicio-ayuda">
            <div class="cards"> <!-- Aquí está el contenedor de las cartas -->
                <?php if (!empty($servicioAyudaDocs)): ?>
                    <?php foreach ($servicioAyudaDocs as $servicioAyuda): ?>
                        <?php
                        // Generar un color aleatorio para cada tarjeta
                        $colors = ['color1', 'color2', 'color3', 'color4', 'color5'];
                        $randomColor = $colors[array_rand($colors)];
                        ?>
                        <div class="card <?php echo $randomColor; ?>">
                            <div class="card-header">
                                <span><?php echo htmlspecialchars($servicioAyuda->description ?? 'Descripción no disponible'); ?><br></span>
                                <span></span>
                            </div>
                            <span class="temp">#<?php echo htmlspecialchars($servicioAyuda->num ?? 'Número no disponible'); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay información disponible.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

    <div id="container" style="height: 500px; min-width: 310px; max-width: 800px; margin: 0 auto;"></div>

    <!-- gsap-latest-beta.min.js -->
    <script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js?r=5426'></script>
    <!-- ScrollTrigger.min.js -->
    <script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js'></script>
    <script src="assets/script.js"></script>
</body>

</html>