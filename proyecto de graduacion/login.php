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

if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gmail_institucional = $_POST['gmail_institucional'];
    $contraseña = $_POST['contraseña'];

    $sql = "SELECT id_usuario, contraseña FROM usuarios WHERE gmail_institucional = :gmail_institucional LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':gmail_institucional', $gmail_institucional, PDO::PARAM_STR);

    try {
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar la contraseña
            if (password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['gmail_institucional'] = $gmail_institucional; // Establecer la variable de sesión
                header("Location: index.php");
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Correo institucional no encontrado.";
        }
    } catch (PDOException $e) {
        $error = "Error en la consulta: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Biblioteca - Iniciar sesión</title>
    <link rel="icon" href="./img/logo_chaleco.png" type="image/png">
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

    <style>
        .logo {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .sidebar {
            display: none !important;
            width: 0 !important;
        }
        .main_content_iner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
        }
        .container-fluid {
            width: 100%;
            padding: 0;
        }
        .header_iner {
            display: none;
        }
        .main_content {
            margin: 0;
            padding: 0;
        }
        .white_box {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group label {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }
        .btn_1 {
            margin-top: 20px;
        }
        .form-control {
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="crm_body_bg">

<section class="main_content dashboard_part">
    <div class="container-fluid g-0">
        <div class="row">
            <div class="col-lg-12 p-0">
            </div>
        </div>

        <div class="main_content_iner">
            <div class="container-fluid plr_30 body_white_bg pt_30">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="white_box mb_30">
                            <div class="modal-content cs_modal">
                                <div class="modal-header">
                                    <h5 class="modal-title">Inicio de sesión</h5>
                                </div>
                                <div class="modal-body">
                                    <!-- Mostrar mensaje de error si hay -->
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                  
                                    <!-- Formulario de Login -->
                                    <form action="login.php" method="POST">
                                        <div class="row social_login_btn" style="background-color: white;">
                                            <div class="form-group col-md-12 text-center">
                                                <a href="login.php">
                                                    <img src="./img/logo_chaleco.png" alt="Logo Chaleco" class="logo">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="border_style">
                                            <span></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="gmail_institucional">Correo Institucional</label>
                                            <input type="email" class="form-control" name="gmail_institucional" placeholder="Correo Institucional" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="contraseña">Contraseña</label>
                                            <input type="password" class="form-control" name="contraseña" placeholder="Contraseña" required>
                                        </div>

                                        <button type="submit" class="btn_1 full_width text-center">Entrar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

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
</section>

<script src="./js/jquery1-3.4.1.min.js"></script>
<script src="./js/popper1.min.js"></script>
<script src="./js/bootstrap1.min.js"></script>
<script src="./js/metisMenu.js"></script>
<script src="./js/custom.js"></script>

</body>
</html>
