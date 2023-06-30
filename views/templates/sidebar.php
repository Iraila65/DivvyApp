<aside class="sidebar">
    <div class="contenedor-sidebar">
        <h2>Divvy App</h2>
        <div class="cerrar-menu">
            <img id="cerrar-menu" src="build/img/cerrar.svg" alt="imagen cerrar">
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a class="<?php echo ($titulo == 'Grupos') ? 'activo' : '' ?>" href="/dashboard">Grupos</a>
        <a class="<?php echo ($titulo == 'Crear Grupo') ? 'activo' : '' ?>" href="/crear-grupo">Crear Grupo</a>
        <a class="<?php echo ($titulo == 'Perfil') ? 'activo' : '' ?>" href="/perfil">Perfil</a>
    </nav>

    <div class="cerrar-sesion-mobile">
        <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
    </div>
</aside>

