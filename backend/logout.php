<?php
// Inicia la sesión
session_start();

// Destruye todos los datos de la sesión
session_unset();
session_destroy();

// Redirige al usuario a la página de inicio de sesión
header("Location: ../login.php");
exit();
?>
