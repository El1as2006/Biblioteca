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

if (isset($_GET['id'])) {
    $id_estudiante = $_GET['id'];
} else {
    echo "ID no recibido en la URL.<br>";
    exit();
}

if (!is_numeric($id_estudiante)) {
    echo "ID no válido o no numérico.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ? AND rol = 'estudiante'");
$stmt->bind_param("i", $id_estudiante);
$stmt->execute();
$result = $stmt->get_result();
$estudiante = $result->fetch_assoc();

if (!$estudiante) {
    echo "Estudiante no encontrado.";
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
        header("Location: historial_estudiantes.php");
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
        /* Ajustes generales de márgenes, padding y tamaño para una mejor visualización */
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

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.25rem;
            padding: 10px;
            border-radius: 8px 8px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
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

        .sidebar a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            font-weight: bold;
        }

        .sidebar a:hover {
            background-color: #007bff;
            color: white;
        }

        .main-content {
            margin-left: 250px; /* Deja espacio para el sidebar */
            padding: 30px;
            flex-grow: 1;
            background-color: #fff;
            min-height: 100vh; /* Asegura que el contenido ocupe toda la altura de la página */
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="main-title">
                <h3>Editar Estudiante</h3>
            </div>

            <form action="editar_estudiante.php?id=<?php echo $estudiante['id_usuario']; ?>" method="POST">
                <div class="card">
                    <div class="card-header">
                        <h5>Datos del Estudiante</h5>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Actualizar Estudiante</button>
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
