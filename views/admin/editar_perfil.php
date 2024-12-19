<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_estudiante = $_SESSION['id_usuario']; 

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ? AND rol = 'super_admin'");
$stmt->bind_param("i", $id_estudiante);
$stmt->execute();
$result = $stmt->get_result();
$estudiante = $result->fetch_assoc();

if (!$estudiante) {
    echo "Perfil no encontrado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $gmail_institucional = $_POST['gmail_institucional'];
    $contraseña = $_POST['contraseña'];

    if (!empty($contraseña)) {
        $contraseña = password_hash($contraseña, PASSWORD_DEFAULT);
    } else {
        $contraseña = $estudiante['contraseña'];
    }

    $stmt_update = $conn->prepare("UPDATE usuarios SET nombre = ?, gmail_institucional = ?, contraseña = ? WHERE id_usuario = ?");
    $stmt_update->bind_param("sssi", $nombre, $gmail_institucional, $contraseña, $id_estudiante);

    if ($stmt_update->execute()) {
        header("Location: index.php"); // Redirige a la página de perfil después de la actualización
        exit();
    } else {
        echo "Error al actualizar los datos: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'header.php'; ?>

    <style>
        /* Estilos generales para la página */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding-top: 50px;
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: bold;
        }

        .main-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #777;
        }

        /* Estilo para el Sidebar */
        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            position: fixed;
            height: 100vh;
            padding-top: 30px;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            flex-grow: 1;
            background-color: #fff;
            min-height: 100vh;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="main-title">
                <h3>Editar Perfil</h3>
            </div>

            <form action="editar_perfil.php" method="POST"> <!-- Actualiza la acción del formulario -->
                <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="gmail_institucional">Correo Institucional:</label>
                    <input type="email" class="form-control" id="gmail_institucional" name="gmail_institucional" value="<?php echo htmlspecialchars($estudiante['gmail_institucional']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="contraseña">Contraseña (Dejar en blanco para no cambiarla):</label>
                    <input type="password" class="form-control" id="contraseña" name="contraseña">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Actualizar Perfil</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>2024 © Biblioteca - Todos los derechos reservados</p>
    </div>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
