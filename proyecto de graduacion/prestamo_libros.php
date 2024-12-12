<?php
session_start();

$host = 'localhost'; 
$dbname = 'biblioteca';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$alertMessage = "";
$alertType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $fecha_prestamo = date('Y-m-d');
    $fecha_devolucion = $_POST['fecha_devolucion'];
    $libros = $_POST['libros'];
    $status = 'no entregado';

    try {
        $pdo->beginTransaction();
        foreach ($libros as $id_libro) {
            $sql = "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, status) VALUES (:id_usuario, :id_libro, :fecha_prestamo, :fecha_devolucion, :status)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_prestamo', $fecha_prestamo, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_devolucion', $fecha_devolucion, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->execute();
        }
        $pdo->commit();
        $alertMessage = "Préstamo realizado correctamente.";
        $alertType = "success";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $alertMessage = "Error en la consulta: " . $e->getMessage();
        $alertType = "danger";
    }
}

// Obtener lista de libros disponibles
$sqlLibros = "SELECT id, titulo, autor, genero FROM libros";
$stmtLibros = $pdo->query($sqlLibros);
$libros = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de estudiantes
$sqlEstudiantes = "SELECT id_usuario, nombre, gmail_institucional FROM usuarios WHERE rol = 'estudiante'";
$stmtEstudiantes = $pdo->query($sqlEstudiantes);
$estudiantes = $stmtEstudiantes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamo de Libros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="crm_body_bg">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="header_iner d-flex justify-content-between align-items-center">
                        <div class="serach_field-area">
                            <div class="search_inner">
                                <form action="#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Search here...">
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
                    <h3 class="mb-0">Préstamo de Libros</h3>
                </div>

                <?php if ($alertMessage): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alertMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <h4>Libros Disponibles</h4>
                        <ul class="list-group">
                            <?php foreach ($libros as $libro): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($libro['titulo']); ?></strong><br>
                                    Autor: <?php echo htmlspecialchars($libro['autor']); ?><br>
                                    Género: <?php echo htmlspecialchars($libro['genero']); ?><br>
                                    <button class="btn btn-primary mt-2" onclick="agregarLibro(<?php echo $libro['id']; ?>, '<?php echo htmlspecialchars($libro['titulo']); ?>')">Seleccionar</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Realizar Préstamo</h4>
                        <form action="prestamo_libros.php" method="POST">
                            <div class="form-group">
                                <label for="librosSeleccionados">Libros Seleccionados</label>
                                <ul class="list-group" id="librosSeleccionados"></ul>
                            </div>
                            <div class="form-group">
                                <label for="id_usuario">Carnet del Estudiante</label>
                                <select class="form-control" id="id_usuario" name="id_usuario" required>
                                    <option value="">Seleccione un estudiante</option>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <option value="<?php echo $estudiante['id_usuario']; ?>">
                                            <?php echo htmlspecialchars($estudiante['nombre']) . " - " . htmlspecialchars($estudiante['gmail_institucional']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha_devolucion">Fecha de Devolución</label>
                                <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Realizar Préstamo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function agregarLibro(id, titulo) {
            const librosSeleccionados = document.getElementById('librosSeleccionados');
            const libroItem = document.createElement('li');
            libroItem.classList.add('list-group-item');
            libroItem.setAttribute('id', `libro-${id}`);
            libroItem.innerHTML = `
                ${titulo}
                <input type="hidden" name="libros[]" value="${id}">
                <button type="button" class="btn btn-danger btn-sm float-right" onclick="eliminarLibro(${id})">Eliminar</button>
            `;
            librosSeleccionados.appendChild(libroItem);
        }

        function eliminarLibro(id) {
            const libroItem = document.getElementById(`libro-${id}`);
            libroItem.remove();
        }
    </script>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
</body>
</html>