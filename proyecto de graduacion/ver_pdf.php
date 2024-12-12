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

// Obtener el id del libro desde la URL
if (isset($_GET['id'])) {
    $id_libro = $_GET['id'];
} else {
    echo "ID de libro no proporcionado.";
    exit();
}

if (!is_numeric($id_libro)) {
    echo "ID no válido o no numérico.";
    exit();
}

// Obtener el archivo PDF del libro desde la base de datos
$query = "SELECT archivo_pdf, titulo FROM libros WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $libro = $result->fetch_assoc();
    $pdf_data = $libro['archivo_pdf'];
    $titulo = $libro['titulo'];

    // Verificar si el archivo PDF está vacío
    if (!empty($pdf_data)) {
        // Mostrar el archivo PDF en el navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $titulo . '.pdf"');
        echo $pdf_data;
    } else {
        echo "El archivo PDF está vacío.";
    }
} else {
    echo "Libro no encontrado o no tiene archivo PDF.";
}

$conn->close();
?>
