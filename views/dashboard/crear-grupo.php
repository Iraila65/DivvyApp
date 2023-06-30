<?php include_once __DIR__."/../templates/header.php"; ?>  

    <div class="contenedor-sm">
        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <form class="formulario" action="/crear-grupo" method="POST">
            <?php include_once __DIR__."/formulario-grupo.php"; ?>
            <input type="submit" value="Crear Grupo">
        </form>

    </div>

<?php include_once __DIR__."/../templates/footer.php"; ?> 