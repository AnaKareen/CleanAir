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
    <div id="wrapper" class="landingPage">


    <?php include 'menu.php'?>
        <!------------Intro------------>
        <section>
            <h1><span>CleanAir</span></h1>
            <p><span>Contaminación del aire</span></p>
            <p><span>en tu ciudad</span></p>
        </section>
        <!------------/Intro------------>

        <!------------D------------>
        <div id="d">
            <p>CA</p>
        </div>
        <!------------/D------------>

        <!------------ScrollDown------------>
        <div id="ScrollDown">
            <p>ScrollDown</p>
        </div>
        <!------------/ScrollDown------------>

    </div>
    <!----------------------------------------------------/LandingPage--------------------------------------------------->

    <!----------------------------------------------------Slide1--------------------------------------------------->
    <div id="wrapper" class="slide1">
        <div id="ImageContainer">
            <img src="https://img.freepik.com/premium-photo/leaves-flying-air-front-blue-sky_1213951-48377.jpg?ga=GA1.1.1543009478.1729142181&semt=ais_hybrid"
                alt="ModernLiving">
        </div>
        <div id="slide">
            <h1>Funcion y Objetivo</h1>
            <p>Nuestra sistema utiliza datos de fuentes confiables como el Sistema Nacional de Información de la
                Calidad del Aire (SINAICA),
                la Agencia de Protección Ambiental (EPA) y la Organización Mundial de la Salud (OMS).
                Ofrecemos información detallada sobre los niveles de contaminación del aire, emisiones de CO2 y otros
                contaminantes.
                Nuestro objetivo es promover la toma de decisiones informadas para mejorar la salud pública y fomentar
                ciudades sostenibles y resilientes,
                siguiendo los lineamientos del ODS 11.</p>
        </div>
    </div>
    <!----------------------------------------------------/Slide1--------------------------------------------------->

    <!----------------------------------------------------Slide2--------------------------------------------------->
    <div id="wrapper" class="slide2">
        <div id="ImageContainer">
            <img src="https://cdn-icons-png.freepik.com/512/2707/2707254.png?ga=GA1.1.1543009478.1729142181"
                alt="CoffeeTable">
        </div>
        <div id="slide">
            <h1>Beneficios</h1>
            <p>Toma de decisiones informadas para la mejora de la calidad del aire.
                Información sobre el impacto en la salud pública.
                Apoyo a las políticas públicas para ciudades más sostenibles.
                Objetivo a largo plazo: "Nuestro sistema busca alinearse con el Objetivo de Desarrollo Sostenible (ODS)
                11,
                promoviendo ciudades sostenibles y resilientes mediante el uso de datos y tecnologías de monitoreo.</p>
        </div>
    </div>
    <!----------------------------------------------------/Slide2--------------------------------------------------->

    <!----------------------------------------------------Slide3--------------------------------------------------->
    <div id="wrapper" class="slide3">
        <div id="ImageContainer">
            <div id="chart_div" style="width: 400px; height: 120px; margin-left: 100px;  margin-bottom: 200px;">

                <script type="text/javascript">
                    google.charts.load('current', { 'packages': ['gauge'] });
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {

                        var data = google.visualization.arrayToDataTable([
                            ['Label', 'Value'],
                            ['Clean', 55],
                        ]);

                        var options = {
                            width: 600, height: 320,
                            redFrom: 90, redTo: 100,
                            yellowFrom: 75, yellowTo: 90,
                            greenFrom: 60, greenTo: 75,
                            minorTicks: 5
                        };

                        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

                        chart.draw(data, options);

                        setInterval(function () {
                            data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
                            chart.draw(data, options);
                        }, 13000);
                        setInterval(function () {
                            data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
                            chart.draw(data, options);
                        }, 5000);
                        setInterval(function () {
                            data.setValue(2, 1, 60 + Math.round(20 * Math.random()));
                            chart.draw(data, options);
                        }, 26000);
                    }
                </script>


            </div>

        </div>
        <div id="slide">
            <h1>Medidores de aire en Mexico</h1>
            <p>Este indicador muestra de manera clara y visual cuán contaminado está el aire que respiramos, permitiendo a los usuarios
                 tomar decisiones informadas para proteger su salud y la de su comunidad.
                  Utilizando datos de fuentes confiables como el Sistema Nacional de Información de la Calidad del Aire (SINAICA),
                   este medidor no solo informa sobre la calidad del aire, sino que también educa sobre el impacto de los 
                   contaminantes en nuestra salud.
            </p>
        </div>
    </div>
    <!----------------------------------------------------/Slide3--------------------------------------------------->




    <!-- gsap-latest-beta.min.js -->
    <script src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js?r=5426'></script>
    <script src="assets/script.js"></script>
    <!-- ScrollTrigger.min.js -->
</body>
<footer>
        <!----------------------------------------------------CodeBy--------------------------------------------------->
        <div id="codeby">
        <a target="_blank" href="https://icodeayush.github.io/">Aire Limpio para vivir<span>CleanAir</span></a>
    </div>
    <!----------------------------------------------------/CodeBy--------------------------------------------------->
</footer>


</html>