<?php include_once __DIR__."/../templates/header.php"; ?>  

<div class="contenedor-sm">
    <?php include_once __DIR__."/../templates/alertas.php"; ?>  

    <a href="/perfil" class="enlace">Volver al perfil</a>

    <form class="formulario" action="/cambiar-pass" method="POST">
        <div class="campo">
            <label for="pass_actual">Password actual: </label>
            <input 
                type="text"
                value=""
                name="pass_actual"
                placeholder="Tu password actual"
            />
        </div>

        <div class="campo">
            <label for="new_pass">Nueva Password: </label>
            <input 
                type="text"
                value=""
                name="new_pass"
                placeholder="Tu nueva password"
            />
        </div>

        <input type="submit" value="Guardar cambios">
    </form>
</div>

<?php include_once __DIR__."/../templates/footer.php"; ?> 