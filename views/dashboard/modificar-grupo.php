<?php include_once __DIR__."/../templates/header.php"; ?>  

    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <div class="contenedor-sm">
            <h4 class="nombre-pagina">Modificar grupo</h4>

            <?php include_once __DIR__."/../templates/alertas.php"; ?>

            <form class="formulario" action="/modificar-grupo?url=<?php echo $grupo->url ?>" method="POST">
                <?php include_once __DIR__."/formulario-grupo.php"; ?>
                <input type="submit" value="Modificar">
            </form>

        </div>

    </div>

<?php include_once __DIR__."/../templates/footer.php"; ?> 
