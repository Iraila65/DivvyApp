<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <div class="contenedor">
            <h4 class="nombre-pagina">Análisis de gastos</h4>

            <?php include_once __DIR__."/../templates/alertas.php"; ?>

            <div class="dashboard__grafica">
                <canvas id="grafica-gastos"></canvas>
                <canvas id="grafica-gastos-pagador"></canvas>
                <canvas id="grafica-total-miembros"></canvas>
                <canvas id="grafica-miembros-conceptos"></canvas>
            </div>

        </div>

    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 
