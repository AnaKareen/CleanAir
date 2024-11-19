<?php
require 'backend/conexion.php'; // Incluye la conexión a la base de datos

// Función para registrar un nuevo usuario
function registrarUsuario($manager, $base_de_datos, $nuevoUsuario) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($nuevoUsuario);
    $result = $manager->executeBulkWrite("$base_de_datos.usuarios", $bulk);
    return $result->getInsertedCount() === 1; // Devuelve true si se insertó un documento
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
                return ['usuario' => $document, 'rol' => $rol]; // Devuelve el documento del usuario y su rol
            } else {
                return false; // Contraseña incorrecta
            }
        }
    }
    return false; // Usuario no encontrado
}

// Manejo de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrarse'])) {
    $nuevoUsuario = [
        'nombre' => $_POST['nombre'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['contrasena'], PASSWORD_DEFAULT), // Almacena la contraseña hasheada
        'id_rol' => 3 // Asigna el id_rol automáticamente a 3
    ];
    
    if (registrarUsuario($manager, $base_de_datos, $nuevoUsuario)) {
        $mensaje = "Registro exitoso. Puedes iniciar sesión ahora.";
    } else {
        $mensaje = "Error al registrar el usuario.";
    }
}

// Manejo de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iniciar_sesion'])) {
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    $resultado = iniciarSesion($manager, $base_de_datos, $email, $contrasena);
    if ($resultado) {
        $usuario = $resultado['usuario'];
        $rol = $resultado['rol'];

        // Redirigir según el nombre del rol
        if ($rol) {
            $nombreRol = $rol->name; // Asumiendo que el campo 'name' contiene el nombre del rol
            if ($nombreRol === 'Administrador') {
                header("Location: admin.php");
                exit();
            } elseif ($nombreRol === 'Cliente') {
                header("Location: user.php");
                exit();
            } elseif ($nombreRol === 'superusuario') { // Cambia 'otro_rol' por el nombre real del rol
                header("Location: gerente.php"); // Cambia 'otra_pagina.php' por la página correspondiente
                exit();
            }
        } else {
            echo "Rol no encontrado.";
        }
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro e Inicio de Sesión</title>
    <link rel="stylesheet" href="assets/login.css"> <!-- Asegúrate de tener el CSS adecuado -->
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form method="POST">
                <h1>Registrarse</h1>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
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