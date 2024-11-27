<?php
include 'backend/conexion.php';
session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige a la página de inicio de sesión
    exit();
}
?>

<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:300,400|Montserrat&display=swap" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" href="https://cdn-icons-png.freepik.com/512/5722/5722394.png?ga=GA1.1.1543009478.1729142181">

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <title>CleanAir</title>
</head>
<body>
<?php include 'menuin.php'; ?>
<!-- Mapa -->
<iframe 
    src="https://graficasrstudio.shinyapps.io/mapa/" 
    width="1000" 
    height="700" 
    style="border:none; display: block; margin: 20px auto 0;">
</iframe>



<!-- Gráficas en flexbox -->
<div style="display: flex; align-items: flex-start; margin-top: 10px;">
    <div style="flex: 1; margin-right: 10px;">
        <iframe 
            src="https://graficasrstudio.shinyapps.io/coanual/" 
            width="100%" 
            height="600" 
            style="border:none; margin-bottom: -180px;">
        </iframe>
        <iframe 
            src="https://graficasrstudio.shinyapps.io/ciudadesmonitoreo/" 
            width="100%" 
            height="600" 
            style="border:none;">
        </iframe>
    </div>
    <div style="flex: 1;">
        <iframe 
            src="https://graficasrstudio.shinyapps.io/graficas/" 
            width="100%" 
            height="1020" 
            style="border:none;">
        </iframe>
    </div>
</div>


<!-- Primera gráfica -->
<iframe 
    src="https://graficasrstudio.shinyapps.io/excedecalidad/" 
    width="1000" 
    height="600" 
    style="border:none; display:block; margin:0 auto;">
</iframe>
<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/maps/modules/data.js"></script>
<script src="https://code.highcharts.com/maps/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
<script src="https://code.highcharts.com/maps/modules/offline-exporting.js"></script>
<script src="https://code.highcharts.com/mapdata/countries/mx/mx-all.js"></script>
<script src="assets/mapa.js"></script>

<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

<div id="container" style="height: 500px; min-width: 310px; max-width: 800px; margin: 0 auto;"></div>

<!-- gsap-latest-beta.min.js -->
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js?r=5426'></script>
<!-- ScrollTrigger.min.js -->
<script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/ScrollTrigger.min.js'></script>
<script src="assets/script.js"></script>
</body>
</html>