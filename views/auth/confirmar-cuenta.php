<main class="contenedor confirmar">
    <?php 
        include_once __DIR__."/../templates/nombre-sitio.php";
    ?>    

    <div class="contenedor-sm">
        <p class="descripcion-pagina"></p>

        <!-- Mostramos los errores  -->
        <?php 
            include_once __DIR__."/../templates/alertas.php";
        ?>

        
        <div class="acciones">
            <a href="/">Iniciar sesión</a>
        </div>

    </div> <!-- contenedor-sm  -->
    
</main>