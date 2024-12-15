<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_libro = $_GET['id'];
} else {
    echo "ID no recibido en la URL.<br>";
    exit();
}

if (!is_numeric($id_libro)) {
    echo "ID no válido o no numérico.<br>";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$result = $stmt->get_result();
$libro = $result->fetch_assoc();

if (!$libro) {
    echo "Libro no encontrado.<br>";
    exit();
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar los datos del formulario
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $autor = htmlspecialchars(trim($_POST['autor']));
    $genero = htmlspecialchars(trim($_POST['genero']));
    $año_publicacion = $_POST['año_publicacion'];
    $isbn = $_POST['isbn'];
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Validación de campos (si es necesario)
    if (empty($titulo) || empty($autor)) {
        echo "Los campos título y autor son obligatorios.<br>";
        exit();
    }

    // Validación para asegurarse de que ISBN no esté vacío o sea '0'
    if (empty($isbn) || $isbn == '0') {
        echo "El campo ISBN no puede estar vacío o ser igual a 0.<br>";
        exit();
    }

    // Manejar la carga del archivo PDF (si existe)
    $archivo_pdf = null;

    // Verificar si se subió un archivo
    if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] == 0) {
        $archivo_tipo = mime_content_type($_FILES['archivo_pdf']['tmp_name']);

        if ($archivo_tipo != 'application/pdf') {
            echo "El archivo debe ser un PDF.<br>";
            exit();
        }

        $archivo_pdf = file_get_contents($_FILES['archivo_pdf']['tmp_name']);
    }

    // Preparar la consulta de actualización
    if ($archivo_pdf) {
        $stmt_update = $conn->prepare("UPDATE libros SET titulo = ?, autor = ?, genero = ?, año_publicacion = ?, isbn = ?, descripcion = ?, disponible = ?, archivo_pdf = ? WHERE id = ?");
        $stmt_update->bind_param("sssiissbi", $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $disponible, $archivo_pdf, $id_libro);
    } else {
        $stmt_update = $conn->prepare("UPDATE libros SET titulo = ?, autor = ?, genero = ?, año_publicacion = ?, isbn = ?, descripcion = ?, disponible = ? WHERE id = ?");
        $stmt_update->bind_param("sssiissi", $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $disponible, $id_libro);
    }

    // Ejecutar la actualización
    if ($stmt_update->execute()) {
        echo "Libro actualizado exitosamente.<br>";
        header("Location: historial_libros.php");
        exit();
    } else {
        echo "Error al ejecutar la consulta de actualización: " . $stmt_update->error . "<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'header.php'; ?>
    <!-- Cargar Bootstrap CSS (asegúrate de que está disponible) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-OgVRvuATP1z7JjHLkuOU63xUkw4O57iX6Rjq+rtv0wpGVXxH6BqFw3c0tL5A55Gz" crossorigin="anonymous">
    <style>
        /* Estilo del Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #f4f4f4;
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

        /* Contenido principal */
        .main-content {
            margin-left: 250px; /* Deja espacio para el sidebar */
            padding: 30px;
            background-color: #fff;
            min-height: 100vh; /* Asegura que el contenido ocupe toda la altura de la página */
        }

        /* Estilo de formulario */
        .form-group {
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

        /* Ajustes generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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

        .main-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #777;
        }

    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        <div class="container">
            <h3 class="main-title">Editar Libro</h3>

            <form action="editar_libro.php?id=<?php echo $libro['id']; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="autor">Autor:</label>
                    <input type="text" name="autor" class="form-control" value="<?php echo htmlspecialchars($libro['autor']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="genero">Género:</label>
                    <input type="text" name="genero" class="form-control" value="<?php echo htmlspecialchars($libro['genero']); ?>">
                </div>
                <div class="form-group">
                    <label for="año_publicacion">Año de Publicación:</label>
                    <input type="number" name="año_publicacion" class="form-control" value="<?php echo htmlspecialchars($libro['año_publicacion']); ?>">
                </div>
                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" name="isbn" class="form-control" value="<?php echo htmlspecialchars($libro['isbn']); ?>">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" class="form-control"><?php echo htmlspecialchars($libro['descripcion']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="archivo_pdf">Archivo PDF:</label>
                    <input type="file" name="archivo_pdf" class="form-control" accept="application/pdf">
                </div>
                <div class="form-group">
                    <label for="disponible">Disponible:</label>
                    <input type="checkbox" name="disponible" <?php echo $libro['disponible'] ? 'checked' : ''; ?>>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>2024 © Biblioteca - Todos los derechos reservados</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>