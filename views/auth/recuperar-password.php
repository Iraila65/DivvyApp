<main class="contenedor recuperar">
    <?php 
        include_once __DIR__."/../templates/nombre-sitio.php";
    ?>    

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Escribe la nueva password a continuación</p>

        <!-- Mostramos los errores  -->
        <?php 
            include_once __DIR__."/../templates/alertas.php";
        ?>

        <?php if($tokenValido) : ?>
            <form class="formulario" method="POST">
                
                <div class="campo">
                        <label for="pass">Nueva Password: </label>
                        <input type="password" name="password" placeholder="Tu nueva password" id="pass" 
                                value="<?php //echo $usuario->password ?>" required>
                </div>

                <input type="submit" value="Modificar password" class="boton">
                    
            </form>

        <?php endif ?>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar sesión</a>
            <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
        </div>

    </div> <!-- contenedor-sm  -->
    
</main>




