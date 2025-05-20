<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Estadísticas</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <h1>Estadísticas</h1>
        <nav>
            <ul>
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="contenedores.php">Contenedores</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="rutas_activas.php">Rutas Activas</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h2>Estadísticas del Sistema</h2>

        <section class="estadistica-seccion info-box">
            <h3>Estadísticas de Contenedores</h3>
            <div class="grafica" style="display:flex; gap:20px; flex-wrap:wrap; justify-content:center;">
                <iframe 
                    src="http://localhost:3000/d-solo/eemam1kxgbke8c/peso-acumulado-por-usuario?orgId=1&from=282622761937&to=3209673232337&panelId=3" 
                    width="450" height="200" frameborder="0">
                </iframe>
                <iframe 
                    src="http://localhost:3000/d-solo/eemam1kxgbke8c/peso-acumulado-por-usuario?orgId=1&from=282622761937&to=3209673232337&panelId=4" 
                    width="450" height="200" frameborder="0">
                </iframe>
            </div>
        </section>

        <section class="estadistica-seccion info-box" style="margin-top: 30px;">
            <h3>Estadísticas de Usuarios</h3>
            <div class="grafica" style="display:flex; gap:20px; flex-wrap:wrap; justify-content:center;">
                <iframe 
                    src="http://localhost:3000/d-solo/eemam1kxgbke8c/peso-acumulado-por-usuario?orgId=1&from=1744718773275&to=1747577221000&panelId=1" 
                    width="450" height="200" frameborder="0">
                </iframe>
                <iframe 
                    src="http://localhost:3000/d-solo/eemam1kxgbke8c/peso-acumulado-por-usuario?orgId=1&from=1744718773275&to=1747577221000&panelId=2" 
                    width="450" height="200" frameborder="0">
                </iframe>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Sistema de Gestión de Contenedores</p>
    </footer>
</body>
</html>
