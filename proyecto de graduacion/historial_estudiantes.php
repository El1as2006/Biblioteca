<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "biblioteca";     

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id_usuario, nombre, gmail_institucional, rol FROM usuarios WHERE rol = 'estudiante'";

// Si hay un término de búsqueda, agregar el filtro
if ($searchTerm) {
    $searchTerm = $conn->real_escape_string($searchTerm);  // Escapar el término para evitar inyección SQL
    $sql .= " AND (nombre LIKE '%$searchTerm%' OR gmail_institucional LIKE '%$searchTerm%')";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<?php include 'header.php'; ?>


    <!-- Estilos Responsivos -->
    <style>
        /* Estilos generales para el layout */
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .main_content_iner {
            margin-left: 250px;
            padding: 30px;
            flex-grow: 1;
            background-color: #fff;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .footer_part {
            margin-left: 250px;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .footer_iner {
            text-align: center;
        }

        /* Estilos para la tabla */
        .QA_table table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .QA_table table th, .QA_table table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .QA_table table th {
            background-color: #f7f7f7;
        }

        /* Hacer que el formulario de búsqueda sea responsivo */
        .serach_field_2 input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
        }

        .QA_section {
            margin-top: 20px;
        }

        /* Estilo para los botones de acción */
        .status_btn {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Hacer la tabla desplazable en pantallas pequeñas */
        .QA_table table {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Estilos para pantallas pequeñas */
        @media (max-width: 768px) {
            .main_content_iner {
                margin-left: 0;
            }

            /* Ajustes para las tablas */
            .QA_table table th, .QA_table table td {
                font-size: 12px;
                padding: 8px;
            }

            .QA_table table {
                font-size: 12px;
            }
        }

        /* Estilo de los botones en pantallas pequeñas */
        .status_btn {
            padding: 6px 12px;
            font-size: 14px;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include 'header.php'; ?>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>
    
    <!-- Main content -->
    <div class="main_content_iner">
        <div class="container-fluid plr_30 body_white_bg pt_30">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="QA_section">
                        <div class="white_box_tittle list_header">
                            <h4>Estudiantes</h4>
                            <div class="box_right d-flex lms_block">
                                <div class="serach_field_2">
                                    <div class="search_inner">
                                        <form action="" method="GET">
                                            <div class="search_field">
                                                <input type="text" name="search" placeholder="Buscar estudiantes..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                                            </div>
                                            <button type="submit"> <i class="ti-search"></i> </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="QA_table">
                            <table class="table lms_table_active">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Correo Institucional</th>
                                        <th scope="col">Rol</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<th scope='row'><a href='#' class='question_content'>" . htmlspecialchars($row['nombre']) . "</a></th>";
                                            echo "<td>" . htmlspecialchars($row['gmail_institucional']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                                            echo "<td><a href='editar_estudiante.php?id=" . $row['id_usuario'] . "' class='status_btn'>Editar</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No se encontraron estudiantes.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer_part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="footer_iner text-center">
                        <p>2020 © Influence - Designed by<a href="#"> Dashboard</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>

    <script>
        // Función para alternar el sidebar en pantallas pequeñas
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }
    </script>

</body>

</html>
