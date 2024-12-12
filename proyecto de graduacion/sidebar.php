<nav class="sidebar">
    <div class="logo d-flex justify-content-between">
        <a href="index.php"><img src="./img/logo_chaleco.png" alt></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu">
        <li class="mm-active">
            <a href="index.php" aria-expanded="false">
                <img src="./img/menu-icon/1.svg" alt>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a class="has-arrow" href="#" aria-expanded="false">
                <img src="./img/menu-icon/6.svg" alt>
                <span>Estudiantes</span>
            </a>
            <ul>
                <li><a href="añadir_estudiante.php">Añadir estudiante</a></li>
                <li><a href="historial_estudiantes.php">Historial de estudiantes</a></li>
            </ul>
        </li>
        <li>
            <a class="has-arrow" href="#" aria-expanded="false">
                <img src="./img/menu-icon/2.svg" alt>
                <span>Libros</span>
            </a>
            <ul>
                <li><a href="libros.php">Añadir libros</a></li>
                <li><a href="historial_libros.php">historial de libros</a></li>
                <li><a href="libros_disponibles.php">libros disponibles</a></li>


            </ul>
        </li>
        <li>
            <a class="has-arrow" href="#" aria-expanded="false">
                <img src="./img/menu-icon/3.svg" alt>
                <span>Prestamos</span>
            </a>
            <ul>
                <li><a href="prestamo_libros.php">Realizar préstamo libro</a></li>
                <li><a href="historial_prestamos.php">Historial de préstamos</a></li>


            </ul>
        </li>
    </ul>
</nav>
