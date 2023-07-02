<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <div class="contenedor">
            <h4 class="nombre-pagina">Análisis de gastos</h4>

            <?php include_once __DIR__."/../templates/alertas.php"; ?>

            <div class="dashboard__grafica">
                <canvas id="grafica-totales-categoria"></canvas>
                <canvas id="grafica-totales-miembros"></canvas>

                <canvas id="grafica-desglose-miembro"></canvas>
                <canvas id="grafica-desglose-categoria"></canvas>
            </div>

        </div>

    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 
