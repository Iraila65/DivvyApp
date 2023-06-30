<main class="contenedor olvide">
    <?php 
        include_once __DIR__."/../templates/nombre-sitio.php";
    ?>    

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Reestablece tu password escribiendo tu email a continuación</p>

        <!-- Mostramos los errores  -->
        <?php 
            include_once __DIR__."/../templates/alertas.php";
        ?>

        
        <form action="/olvide" class="formulario" method="POST">
            
            <div class="campo">
                    <label for="email">E-mail: </label>
                    <input type="email" name="email" placeholder="Tu e-mail" id="email" 
                            value="<?php echo $auth->email ?>" required>
                    
            </div>

            <input type="submit" value="Enviar instrucciones" class="boton">
                
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar sesión</a>
            <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
        </div>
        
    </div> <!-- contenedor-sm  -->
    
</main>




