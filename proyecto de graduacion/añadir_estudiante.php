<?php
session_start();

$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "biblioteca";     

$conn = new mysqli($servername, $username, $password, $dbname);

// Establecer la codificación a UTF-8
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['estudiantes']) && is_array($_POST['estudiantes'])) {
        $estudiantes = $_POST['estudiantes'];

        foreach ($estudiantes as $estudiante) {
            $nombre = $estudiante['nombre'];
            $gmail_institucional = $estudiante['gmail_institucional'];
            $contraseña = $estudiante['contraseña'];
            $rol = 'estudiante';

            $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, gmail_institucional, contraseña, rol) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }

            $stmt->bind_param("ssss", $nombre, $gmail_institucional, $contraseña_encriptada, $rol);

            if (!$stmt->execute()) {
                echo "Error al añadir estudiante: " . $stmt->error;
            }
        }

        $stmt->close();
        $conn->close();

        header("Location: añadir_estudiante.php");
        exit();
    } else {
        echo "No se enviaron los datos de los estudiantes correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<?php include 'header.php'; ?>
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
                    <h3 class="mb-0">Registro de Estudiantes</h3>
                </div>

                <form action="añadir_estudiante.php" method="POST" id="formEstudiantes">
                    <div id="estudiantesContainer"></div>
                    <button type="button" class="btn btn-secondary mt-3" onclick="agregarFormulario()">+</button>
                    <button type="submit" class="btn btn-primary mt-3">Registrar Estudiantes</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        let contadorEstudiantes = 0;

        function agregarFormulario() {
            contadorEstudiantes++;
            const container = document.getElementById('estudiantesContainer');

            const estudianteDiv = document.createElement('div');
            estudianteDiv.classList.add('card', 'mb-3');
            estudianteDiv.setAttribute('id', `estudiante${contadorEstudiantes}`);
            estudianteDiv.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Estudiante ${contadorEstudiantes}</h5>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFormulario(${contadorEstudiantes})">-</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="nombre${contadorEstudiantes}">Nombre:</label>
                        <input type="text" class="form-control" id="nombre${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][nombre]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="gmail_institucional${contadorEstudiantes}">Correo Institucional:</label>
                        <input type="email" class="form-control" id="gmail_institucional${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][gmail_institucional]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="contraseña${contadorEstudiantes}">Contraseña:</label>
                        <input type="password" class="form-control" id="contraseña${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][contraseña]" required>
                    </div>
                </div>
            `;
            container.appendChild(estudianteDiv);
        }

        function eliminarFormulario(id) {
            const estudianteDiv = document.getElementById(`estudiante${id}`);
            estudianteDiv.remove();
        }
    </script>

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
