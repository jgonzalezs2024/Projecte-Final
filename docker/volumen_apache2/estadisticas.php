
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Sistema de Gestión de Contenedores</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Cabecera -->
    <header>
        <h1>Panel de Administración - Estadísticas</h1>
        <nav>
            <ul>
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="contenedores.php">Contenedores</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="rutas_activas.php">Rutas Activas</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor principal -->
    <main class="container">
        <h2>Estadísticas del Sistema</h2>

        <!-- Sección de estadísticas de contenedores -->
        <section class="estadistica-seccion">
            <h3>Estadísticas de Contenedores</h3>
            <div class="grafica">
                <iframe src="http://localhost:3000/d-solo/belyc4qos92bka/new-dashboard?orgId=1&from=1747285130528&to=1747306730528&panelId=1" width="450" height="200" frameborder="0"></iframe>
                
            </div>

        </section>

        <!-- Sección de estadísticas de usuarios -->
        <section class="estadistica-seccion">
            <h3>Estadísticas de Usuarios</h3>
            <div class="grafica">
            <iframe src="http://localhost:3000/d-solo/felyb0qcs0740b/rango-edad?orgId=1&from=1747284436645&to=1747306036645&panelId=1" width="450" height="200" frameborder="0"></iframe>
            <iframe src="http://localhost:3000/d-solo/delyboh4u50jkf/grafico-edad-rfid?orgId=1&from=1747284933191&to=1747306533191&panelId=1" width="450" height="200" frameborder="0"></iframe>
        </section>

        <!-- Sección de registros históricos -->
        <section class="estadistica-seccion">
            <h3>Registros Históricos</h3>
            <!-- Aquí puedes agregar más gráficos según sea necesario -->
            <div class="grafica">
            <iframe src="http://localhost:3000/d-solo/delycdpy27bi8d/new-dashboard3?orgId=1&from=1747285287708&to=1747306887708&panelId=1" width="450" height="200" frameborder="0"></iframe>
        </section>
    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2025 Sistema de Gestión de Contenedores</p>
    </footer>

</body>
</html>
