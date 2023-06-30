<?php include_once __DIR__."/../templates/header.php"; ?>  

    <div class="contenedor">
        <h3 class="nombre-pagina"><?php echo $grupo->grupo ?></h3>
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>
        
        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <form class="formulario" action="/alta-miembro?url=<?php echo $grupo->url ?>" method="POST">
            <?php include_once __DIR__."/formulario-miembro.php"; ?>
            <input type="submit" value="Crear Miembro">
        </form>

    </div>

<?php include_once __DIR__."/../templates/footer.php"; ?> 
