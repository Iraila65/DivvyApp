<main class="contenedor mensaje">
    <?php 
        include_once __DIR__."/../templates/nombre-sitio.php";
    ?>    

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Hemos enviado las instrucciones para confirmar tu cuenta a tu email.</p>

        <!-- Mostramos los errores  -->
        <?php 
            include_once __DIR__."/../templates/alertas.php";
        ?>

    </div> <!-- contenedor-sm  -->
    
</main>
