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

$query = "SELECT id, titulo, autor, archivo_pdf FROM libros WHERE disponible = 1";
$result = $conn->query($query);

// Comprobar si hay libros disponibles
if ($result->num_rows == 0) {
    echo "<p>No hay libros disponibles para leer.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<?php include 'header.php'; ?>

    <style>
        /* Ajuste general de color de fondo */
        body {
            background-color: #f4f4f4;  /* Fondo suave */
            font-family: 'Arial', sans-serif;
        }

        /* Estilo del contenido */
        .main-content {
            margin-left: 250px; /* Deja espacio para el sidebar */
            padding: 30px;
            background-color: #ffffff;  /* Fondo blanco */
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .container {
            margin-top: 30px;
        }

        .book-item {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .book-item:hover {
            background-color: #f1f1f1;  /* Color suave cuando pasas el ratón */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .book-item h5 {
            font-size: 1.25rem;
            color: #333;
        }

        .book-item p {
            color: #666;
            margin-bottom: 15px;
        }

        .book-list {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .book-list .col {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #4CAF50;  /* Botón verde */
            border-color: #4CAF50;
            padding: 12px;
            width: 100%;
            text-align: center;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #45a049;  /* Verde más oscuro al pasar el ratón */
            border-color: #45a049;
        }

        /* Estilo de cabecera */
        .page-header {
            margin-top: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Estilo para cuando el contenido es responsive */
        @media (max-width: 768px) {
            .book-list {
                grid-template-columns: 1fr 1fr;
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .book-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container mt-5">
            <div class="page-header">
                <h3>Libros Disponibles</h3>
                <p class="lead">Explora nuestra selección de libros disponibles para leer en línea.</p>
            </div>

            <div class="book-list">
                <?php while($libro = $result->fetch_assoc()) { ?>
                    <div class="col book-item">
                        <h5><?php echo htmlspecialchars($libro['titulo']); ?></h5>
                        <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalPdf" data-id="<?php echo $libro['id']; ?>" data-titulo="<?php echo $libro['titulo']; ?>">Leer en PDF</button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar PDF -->
    <div class="modal fade" id="modalPdf" tabindex="-1" role="dialog" aria-labelledby="modalPdfLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPdfLabel">Leer PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfIframe" src="" style="width:100%; height: 500px;" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="footer text-center mt-5 py-4 bg-light">
        <p>2024 © Biblioteca - Todos los derechos reservados</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Cuando se haga clic en el botón de "Leer en PDF"
        $('#modalPdf').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Botón que activó el modal
            var libroId = button.data('id');
            var titulo = button.data('titulo');
            var iframe = $('#pdfIframe');
            
            // Cambiar la fuente del iframe para cargar el PDF correspondiente
            iframe.attr('src', 'ver_pdf.php?id=' + libroId);
        });
    </script>

</body>
</html>

<?php
$conn->close();
?>
