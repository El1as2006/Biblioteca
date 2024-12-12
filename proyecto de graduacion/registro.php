<?php

$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "biblioteca";     

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $gmail_institucional = $_POST['gmail_institucional'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol'];

    $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, gmail_institucional, contraseña, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $gmail_institucional, $contraseña_encriptada, $rol);

   
    if ($stmt->execute()) {
        echo "Nuevo registro creado exitosamente";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Formulario no enviado correctamente.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form action="registro.php" method="POST">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="gmail_institucional">Correo Institucional:</label><br>
        <input type="email" id="gmail_institucional" name="gmail_institucional" required><br><br>

        <label for="contraseña">Contraseña:</label><br>
        <input type="password" id="contraseña" name="contraseña" required><br><br>

        <label for="rol">Rol:</label><br>
        <select name="rol" id="rol" required>
            <option value="estudiante">Estudiante</option>
            <option value="docente">Docente</option>
            <option value="admin">Administrador</option>
            <option value="super_admin">Super Administrador</option>
        </select><br><br>

        <input type="submit" value="Registrarse">
    </form>
</body>
</html>
