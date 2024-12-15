<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "biblioteca";     

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para listar los libros
function listarLibros($conn) {
    $sql = "SELECT * FROM libros";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Función para agregar un nuevo libro
function agregarLibro($conn, $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $archivo_pdf) {
    $sql = "INSERT INTO libros (titulo, autor, genero, año_publicacion, isbn, descripcion, archivo_pdf) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
    $stmt->bind_param("sssissb", $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $archivo_pdf);

    // Ejecutar la consulta
    return $stmt->execute();
}

// Agregar libro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    // Obtener los datos del formulario
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $genero = $_POST['genero'];
    $año_publicacion = $_POST['año_publicacion'];
    $isbn = $_POST['isbn'];
    $descripcion = $_POST['descripcion'];

    // Subir archivo PDF
    if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] == 0) {
        $archivo_pdf = file_get_contents($_FILES['archivo_pdf']['tmp_name']);
        agregarLibro($conn, $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $archivo_pdf);
    }
}

// Listar libros
$libros = listarLibros($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<?php include 'header.php'; ?>

</head>

<body class="crm_body_bg">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content Section -->
    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="header_iner d-flex justify-content-between align-items-center">
                        <div class="serach_field-area">
                            <div class="search_inner">
                                <form action="#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Buscar...">
                                    </div>
                                    <button type="submit">
                                        <img src="./img/icon/icon_search.svg" alt>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="main-title">
                    <h3 class="mb-0">Administrar Biblioteca - Libros</h3>
                </div>

                <!-- Agregar Nuevo Libro -->
                <form action="libros.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Agregar Nuevo Libro</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" for="titulo">Título:</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="autor">Autor:</label>
                                        <input type="text" class="form-control" id="autor" name="autor" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="genero">Género:</label>
                                        <input type="text" class="form-control" id="genero" name="genero">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="año_publicacion">Año de Publicación:</label>
                                        <input type="number" class="form-control" id="año_publicacion" name="año_publicacion">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="isbn">ISBN:</label>
                                        <input type="text" class="form-control" id="isbn" name="isbn">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="descripcion">Descripción:</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="archivo_pdf">Archivo PDF:</label>
                                        <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf">
                                    </div>
                                    <button type="submit" name="agregar" class="btn btn-primary">Agregar Libro</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Libros Disponibles -->
                <h2>Libros Disponibles</h2>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Género</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                <td><?php echo htmlspecialchars($libro['genero']); ?></td>
                                <td>
                                    <a href="editar_libro.php?id=<?php echo $libro['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="eliminar_libro.php?id=<?php echo $libro['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Footer Section -->

    <!-- JavaScript Libraries -->
    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./vendors/count_up/jquery.waypoints.min.js"></script>
    <script src="./vendors/chartlist/Chart.min.js"></script>
    <script src="./vendors/count_up/jquery.counterup.min.js"></script>
    <script src="./vendors/swiper_slider/js/swiper.min.js"></script>
    <script src="./vendors/niceselect/js/jquery.nice-select.min.js"></script>
    <script src="./vendors/owl_carousel/js/owl.carousel.min.js"></script>
    <script src="./vendors/gijgo/gijgo.min.js"></script>
    <script src="./vendors/datatable/js/jquery.dataTables.min.js"></script>
    <script src="./vendors/datatable/js/dataTables.responsive.min.js"></script>
    <script src="./vendors/datatable/js/dataTables.buttons.min.js"></script>
    <script src="./vendors/datatable/js/jszip.min.js"></script>
    <script src="./vendors/datatable/js/pdfmake.min.js"></script>
    <script src="./vendors/datatable/js/vfs_fonts.js"></script>
    <script src="./vendors/datatable/js/buttons.print.min.js"></script>
    <script src="./js/chart.min.js"></script>
    <script src="./js/custom.js"></script>
</body>

</html>
