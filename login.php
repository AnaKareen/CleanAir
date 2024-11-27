<?php
session_start(); // Inicia la sesión

require 'backend/conexion.php'; // Incluye la conexión a la base de datos

// Función para obtener las ciudades de MongoDB

// Función para obtener las ciudades de MongoDB
function obtenerCiudades($manager, $base_de_datos) {
    $filter = []; // Puedes agregar filtros si es necesario
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("$base_de_datos.ciudadesConMoniteo", $query);

    $ciudades = [];
    foreach ($cursor as $document) {
        if (isset($document->ZONAS_METROPOLITANAS_O_POBLACIONES)) {
            $ciudades[] = [
                'id' => (string)$document->_id,
                'nombre' => $document->ZONAS_METROPOLITANAS_O_POBLACIONES
            ];
        }
    }

    // Verifica si se encontraron ciudades
    if (empty($ciudades)) {
        // Si no hay ciudades, puedes manejar el error de la forma que desees
        echo "<p>No se encontraron ciudades disponibles.</p>";
    }

    return $ciudades;
}

// Recuperar las ciudades de la base de datos
$ciudades = obtenerCiudades($manager, $base_de_datos);

// Función para verificar si el correo electrónico ya está registrado
function emailYaRegistrado($manager, $base_de_datos, $email) {
    $filter = ['email' => $email];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("$base_de_datos.usuarios", $query);
    return $cursor->isDead() ? false : true; // Devuelve true si el correo ya está registrado
}

// Función para registrar un nuevo usuario
function registrarUsuario($manager, $base_de_datos, $nuevoUsuario) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($nuevoUsuario);
    $result = $manager->executeBulkWrite("$base_de_datos.usuarios", $bulk);
    return $result->getInsertedCount() === 1; // Devuelve true si se insertó un documento
}

// Manejo de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrarse'])) {
    $ciudadSeleccionada = $_POST['ciudad'];
    $email = $_POST['email'];

    // Verifica si el correo ya está registrado
    if (emailYaRegistrado($manager, $base_de_datos, $email)) {
        $mensaje = "El correo electrónico ya está registrado. Por favor, utiliza otro.";
    } else {
        // Obtén la entidad federativa correspondiente a la ciudad seleccionada
        $filter = ['_id' => new MongoDB\BSON\ObjectId($ciudadSeleccionada)];
        $options = [];
        $query = new MongoDB\Driver\Query($filter, $options);
        $cursor = $manager->executeQuery("$base_de_datos.ciudadesConMoniteo", $query);

        $entidadFederativa = null;
        foreach ($cursor as $document) {
            if (isset($document->ZONAS_METROPOLITANAS_O_POBLACIONES)) {
                $entidadFederativa = $document->ZONAS_METROPOLITANAS_O_POBLACIONES; // Obtén el valor de ZONAS_METROPOLITANAS_O_POBLACIONES
                break; // Sal del bucle una vez que hayas encontrado la entidad
            }
        }

        // Prepara el nuevo usuario
        $nuevoUsuario = [
            'nombre' => $_POST['nombre'],
            'email' => $email,
            'password' => password_hash($_POST['contrasena'], PASSWORD_DEFAULT), // Almacena la contraseña hasheada
            'id_rol' => 3, // Asigna el id_rol automáticamente a 3
            'ZONAS_METROPOLITANAS_O_POBLACIONES' => $entidadFederativa // Guarda ZONAS_METROPOLITANAS_O_POBLACIONES
        ];

        // Registra el usuario
        if (registrarUsuario($manager, $base_de_datos, $nuevoUsuario)) {
            $mensaje = "Registro exitoso. Puedes iniciar sesión ahora.";
        } else {
            $mensaje = "Error al registrar el usuario.";
        }
    }
}

// Función para obtener el rol del usuario
function obtenerRol($manager, $base_de_datos, $id_rol) {
    $filter = ['_id' => (int)$id_rol]; // Convertimos a entero
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("$base_de_datos.roles", $query);

    // Almacena el resultado en un array
    $roles = $cursor->toArray(); // Solo lo llamas una vez

    return !empty($roles) ? $roles[0] : null; // Devuelve el rol si se encuentra
}

// Función para iniciar sesión
function iniciarSesion($manager, $base_de_datos, $email, $contrasena) {
    $filter = ['email' => $email];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("$base_de_datos.usuarios", $query);

    foreach ($cursor as $document) {
 if (isset($document->password)) {
            // Verifica la contraseña
            if (password_verify($contrasena, $document->password)) {
                // Obtener el rol del usuario
                $rol = obtenerRol($manager, $base_de_datos, $document->id_rol);

                // Almacenar información en la sesión
                $_SESSION['usuario'] = $document; // Almacena el documento del usuario
                $_SESSION['rol'] = $rol; // Almacena el rol

                return true; // Devuelve true si la autenticación fue exitosa
            } else {
                return false; // Contraseña incorrecta
            }
        }
    }
    return false; // Usuario no encontrado
}

// Manejo de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iniciar_sesion'])) {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    if (iniciarSesion($manager, $base_de_datos, $email, $contrasena)) {
        // Redirigir según el nombre del rol
        if (isset($_SESSION['rol'])) {
            $nombreRol = $_SESSION['rol']->name; // Asumiendo que el campo 'name' contiene el nombre del rol
            if ($nombreRol === 'Administrador') {
                header("Location: admin.php");
                exit();
            } elseif ($nombreRol === 'Cliente') {
                header("Location: user.php");
                exit();
            } elseif ($nombreRol === 'superusuario') {
                header("Location: gerente.php");
                exit();
            }
        } else {
            echo "Rol no encontrado.";
        }
    } else {
        echo "Credenciales incorrectas.";
    }
}

// Cerrar sesión
if (isset($_GET['cerrar_sesion'])) {
    session_destroy(); // Destruye la sesión
    header("Location: login.php"); // Redirige a la página de inicio de sesión
    exit();
}

// Verificar si el usuario ya está autenticado
if (isset($_SESSION['usuario'])) {
    // Redirigir según el nombre del rol
    $nombreRol = $_SESSION['rol']->name;
    if ($nombreRol === 'Administrador') {
        header("Location: admin.php");
        exit();
    } elseif ($nombreRol === 'Cliente') {
        header("Location: user.php");
        exit();
    } elseif ($nombreRol === 'superusuario') {
        header("Location: gerente.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro e Inicio de Sesión</title>
    <link rel="stylesheet" href="assets/login.css">
    <style>
        .regresar-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .regresar-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <button class="regresar-btn" onclick="window.location.href='index.php'">Regresar</button>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form method="POST">
                <h1>Registrarse</h1>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>

                <label for="ciudad">Selecciona tu ciudad:</label>
                <select name="ciudad" id="ciudad" required>
                    <option value="">Seleccione una ciudad</option>
                    <?php foreach ($ciudades as $ciudad): ?>
                        <option value="<?php echo $ciudad['id']; ?>">
                            <?php echo $ciudad['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="registrarse">Registrarse</button>
                <?php if (isset($mensaje)) echo "<p class='mensaje'>$mensaje</p>"; ?>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="POST">
                <h1>Iniciar Sesión</h1>
 <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <button type="submit" name="iniciar_sesion">Entrar</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Bienvenido de vuelta</h1>
                    <p>Ingresa tu usuario y contraseña para iniciar</p>
                    <button class="ghost" id="signIn">Iniciar sesión</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Bienvenido a CLEANAIR</h1>
                    <p>Regístrate a nuestro sistema</p>
                    <button class="ghost" id="signUp">Registrarse</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const signInButton = document.getElementById('signIn');
        const signUpButton = document.getElementById('signUp');
        const container = document.getElementById('container');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    </script>
</body>

</html>