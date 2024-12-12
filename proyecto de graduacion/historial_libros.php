<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "biblioteca";      

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';  // Termino de búsqueda

$sql = "SELECT id, titulo, autor, genero, creado_en, actualizado_en FROM libros";

// Si hay un término de búsqueda, agregar filtro
if ($searchTerm) {
    $searchTerm = $conn->real_escape_string($searchTerm);  // Escapar el término para evitar inyección SQL
    $sql .= " WHERE (titulo LIKE '%$searchTerm%' OR autor LIKE '%$searchTerm%')";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<?php include 'header.php'; ?>


    <!-- Estilos Responsivos -->
    <style>
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

        .serach_field_2 input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
        }

        .QA_section {
            margin-top: 20px;
        }

        .status_btn {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .main_content_iner {
                margin-left: 0;
            }

            .QA_table table th, .QA_table table td {
                font-size: 12px;
                padding: 8px;
            }

            .QA_table table {
                font-size: 12px;
            }
        }

        .status_btn {
            padding: 6px 12px;
            font-size: 14px;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>
    
    <div class="main_content_iner">
        <div class="container-fluid plr_30 body_white_bg pt_30">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="QA_section">
                        <div class="white_box_tittle list_header">
                            <h4>Historial de Libros</h4>
                            <div class="box_right d-flex lms_block">
                                <div class="serach_field_2">
                                    <div class="search_inner">
                                        <form action="" method="GET">
                                            <div class="search_field">
                                                <input type="text" name="search" placeholder="Buscar libros..." value="<?php echo htmlspecialchars($searchTerm); ?>">
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
                                        <th scope="col">Título</th>
                                        <th scope="col">Autor</th>
                                        <th scope="col">Género</th>
                                        <th scope="col">Fecha de Creación</th>
                                        <th scope="col">Última Modificación</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<th scope='row'>" . htmlspecialchars($row['titulo']) . "</th>";
                                            echo "<td>" . htmlspecialchars($row['autor']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['genero']) . "</td>";
                                            echo "<td>" . date('d-m-Y H:i:s', strtotime($row['creado_en'])) . "</td>";
                                            echo "<td>" . ($row['actualizado_en'] ? date('d-m-Y H:i:s', strtotime($row['actualizado_en'])) : 'Nunca') . "</td>";
                                            echo "<td><a href='editar_libro.php?id=" . $row['id'] . "' class='status_btn'>Editar</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No se encontraron libros.</td></tr>";
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

    <div class="footer_part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="footer_iner text-center">
                        <p>2024 © Biblioteca - Diseño por <a href="#">Dashboard</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
</body>

</html>
