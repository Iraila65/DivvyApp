<?php include_once __DIR__."/../templates/header.php"; ?>  

    <div class="contenedor">
        
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <h4 class="nombre-pagina">Crear miembro</h4>
        
        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <form class="formulario" action="/alta-miembro?url=<?php echo $grupo->url ?>" method="POST">
            <?php include_once __DIR__."/formulario-miembro.php"; ?>
            <input type="submit" value="Crear Miembro">
        </form>

    </div>

<?php include_once __DIR__."/../templates/footer.php"; ?> 
