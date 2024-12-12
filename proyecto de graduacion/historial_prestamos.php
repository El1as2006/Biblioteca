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

// Manejar acciones de cancelar y marcar como entregado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancelar'])) {
        $id_prestamo = $_POST['id_prestamo'];
        $sql = "DELETE FROM prestamos WHERE id_prestamo = :id_prestamo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
        try {
            if ($stmt->execute()) {
                $alertMessage = "Préstamo cancelado correctamente.";
                $alertType = "success";
            } else {
                $alertMessage = "Error al cancelar el préstamo.";
                $alertType = "danger";
            }
        } catch (PDOException $e) {
            $alertMessage = "Error en la consulta: " . $e->getMessage();
            $alertType = "danger";
        }
    } elseif (isset($_POST['entregado'])) {
        $id_prestamo = $_POST['id_prestamo'];
        $fecha_devolucion = date('Y-m-d');
        $status = (strtotime($fecha_devolucion) > strtotime($_POST['fecha_devolucion'])) ? 'entregado con retraso' : 'entregado';
        $sql = "UPDATE prestamos SET fecha_devolucion = :fecha_devolucion, status = :status WHERE id_prestamo = :id_prestamo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fecha_devolucion', $fecha_devolucion, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
        try {
            if ($stmt->execute()) {
                $alertMessage = "Préstamo marcado como entregado.";
                $alertType = "success";
            } else {
                $alertMessage = "Error al marcar el préstamo como entregado.";
                $alertType = "danger";
            }
        } catch (PDOException $e) {
            $alertMessage = "Error en la consulta: " . $e->getMessage();
            $alertType = "danger";
        }
    }
}

// Obtener lista de préstamos
$sqlPrestamos = "SELECT p.id_prestamo, p.fecha_prestamo, p.fecha_devolucion, p.status, l.titulo, l.autor, u.nombre AS estudiante
                 FROM prestamos p
                 JOIN libros l ON p.id_libro = l.id
                 JOIN usuarios u ON p.id_usuario = u.id_usuario";
$stmtPrestamos = $pdo->query($sqlPrestamos);
$prestamos = $stmtPrestamos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Préstamos</title>
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
                    <h3 class="mb-0">Historial de Préstamos</h3>
                </div>

                <?php if ($alertMessage): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alertMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Título del Libro</th>
                            <th>Autor</th>
                            <th>Estudiante</th>
                            <th>Fecha de Préstamo</th>
                            <th>Fecha de Devolución</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr class="<?php echo $prestamo['status'] == 'entregado con retraso' ? 'table-warning' : ''; ?>">
                                <td><?php echo htmlspecialchars($prestamo['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['autor']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['estudiante']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['fecha_devolucion']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['status']); ?></td>
                                <td>
                                    <?php if ($prestamo['status'] == 'no entregado'): ?>
                                        <form action="historial_prestamos.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_prestamo" value="<?php echo $prestamo['id_prestamo']; ?>">
                                            <input type="hidden" name="fecha_devolucion" value="<?php echo $prestamo['fecha_devolucion']; ?>">
                                            <button type="submit" name="entregado" class="btn btn-success btn-sm">Entregado</button>
                                        </form>
                                        <form action="historial_prestamos.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_prestamo" value="<?php echo $prestamo['id_prestamo']; ?>">
                                            <button type="submit" name="cancelar" class="btn btn-danger btn-sm">Cancelar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo htmlspecialchars($prestamo['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
</body>
</html>