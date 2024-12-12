<?php 
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['gmail_institucional'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost'; 
$usuario = 'root';   
$clave = '';        
$base_de_datos = 'biblioteca'; 

$conn = new mysqli($host, $usuario, $clave, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para contar estudiantes
$sql_estudiantes = "SELECT COUNT(*) AS total_estudiantes FROM usuarios WHERE rol = 'estudiante'";
$result_estudiantes = $conn->query($sql_estudiantes);
$total_estudiantes = 0;
if ($result_estudiantes && $row = $result_estudiantes->fetch_assoc()) {
    $total_estudiantes = $row['total_estudiantes'];
}

// Consulta para contar libros
$sql_libros = "SELECT COUNT(*) AS total_libros FROM libros";
$result_libros = $conn->query($sql_libros);
$total_libros = 0;
if ($result_libros && $row = $result_libros->fetch_assoc()) {
    $total_libros = $row['total_libros'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="./img/logo_chaleco.png" type="image/png">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="./css/bootstrap1.min.css" />
    <link rel="stylesheet" href="./vendors/themefy_icon/themify-icons.css" />
    <link rel="stylesheet" href="./vendors/swiper_slider/css/swiper.min.css" />
    <link rel="stylesheet" href="./vendors/select2/css/select2.min.css" />
    <link rel="stylesheet" href="./vendors/niceselect/css/nice-select.css" />
    <link rel="stylesheet" href="./vendors/owl_carousel/css/owl.carousel.css" />
    <link rel="stylesheet" href="./vendors/gijgo/gijgo.min.css" />
    <link rel="stylesheet" href="./vendors/font_awesome/css/all.min.css" />
    <link rel="stylesheet" href="./vendors/tagsinput/tagsinput.css" />
    <link rel="stylesheet" href="./vendors/datatable/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="./vendors/datatable/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="./vendors/datatable/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="./vendors/text_editor/summernote-bs4.css" />
    <link rel="stylesheet" href="./vendors/morris/morris.css">
    <link rel="stylesheet" href="./vendors/material_icon/material-icons.css" />
    <link rel="stylesheet" href="./css/metisMenu.css">
    <link rel="stylesheet" href="./css/style1.css" />
    <link rel="stylesheet" href="./css/colors/default.css" id="colorSkinCSS">
</head>

<body class="crm_body_bg">
    <?php include 'sidebar.php'; ?>

    <!-- Main Content Section -->
    <section class="main_content dashboard_part">
        <div class="container mt-5">
            <div class="col-lg-12 p-0">
                <div class="header_iner d-flex justify-content-between align-items-center">
                    <div class="sidebar_icon d-lg-none">
                        <i class="ti-menu"></i>
                    </div>
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
                    <div class="header_right d-flex justify-content-between align-items-center">
                        <div class="profile_info">
                            <img src="./img/user_img.png" alt="#">
                            <div class="profile_info_iner">
                                <p>Welcome!</p>
                                <h5><?php echo htmlspecialchars($_SESSION['gmail_institucional']); ?></h5>
                                <div class="profile_info_details">
                                    <a href="editar_perfil.php">My Profile <i class="ti-user"></i></a>
                                    <a href="#">Settings <i class="ti-settings"></i></a>
                                    <li><a href="logout.php">Log Out <i class="ti-shift-left"></i></a></li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="main_content_iner">
            <div class="container-fluid plr_30 body_white_bg pt_30">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="single_element">
                            <div class="quick_activity">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="quick_activity_wrap">
                                            <!-- Total Estudiantes -->
                                            <div class="single_quick_activity">
                                                <h4>Total Estudiantes</h4>
                                                <h3><span class="counter"><?php echo number_format($total_estudiantes); ?></span></h3>
                                                <p>Estudiantes registrados</p>
                                            </div>
                                            <!-- Total Libros -->
                                            <div class="single_quick_activity">
                                                <h4>Total Libros</h4>
                                                <h3><span class="counter"><?php echo number_format($total_libros); ?></span></h3>
                                                <p>Libros registrados</p>
                                            </div>
                                            <!-- Puedes agregar más actividades aquí, si es necesario -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Footer Section -->
    <div class="footer_part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="footer_iner text-center">
                        <p>2020 © Biblioteca - Diseñado por<a href="#"> Dashboard</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="./vendors/datatable/js/buttons.flash.min.js"></script>
    <script src="./vendors/datatable/js/jszip.min.js"></script>
    <script src="./vendors/datatable/js/pdfmake.min.js"></script>
    <script src="./vendors/datatable/js/vfs_fonts.js"></script>
    <script src="./vendors/datatable/js/buttons.php5.min.js"></script>
    <script src="./vendors/datatable/js/buttons.print.min.js"></script>
    <script src="./js/chart.min.js"></script>
    <script src="./vendors/progressbar/jquery.barfiller.js"></script>
    <script src="./vendors/tagsinput/tagsinput.js"></script>
    <script src="./vendors/text_editor/summernote-bs4.js"></script>
    <script src="./vendors/apex_chart/apexcharts.js"></script>
    <script src="./js/custom.js"></script>
    <script src="./js/active_chart.js"></script>
    <script src="./vendors/apex_chart/radial_active.js"></script>
    <script src="./vendors/apex_chart/stackbar.js"></script>
    <script src="./vendors/apex_chart/area_chart.js"></script>
    <script src="./vendors/apex_chart/bar_active_1.js"></script>
    <script src="./vendors/chartjs/chartjs_active.js"></script>
</body>
</html>
