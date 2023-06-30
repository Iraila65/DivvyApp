<?php include_once __DIR__."/../templates/header.php"; ?>  

    <?php if (count($grupos) == 0) { ?>
        <div class="no-grupos">
            <p>No hay grupos</p>
            <a class="enlace-no-proyectos" href="/crear-grupo">Comienza creando uno</a>
        </div>  
        
    <?php } else { ?>
        <ul class="listado-grupos">
            <?php foreach($grupos as $grupo) { ?>
                <li class="grupo grupo--<?php echo $grupo->color ?>">
                    <a href="/grupo?url=<?php echo $grupo->url ?>"> 
                        <?php echo $grupo->grupo ?> 
                    </a>
                    
                    <a 
                        class="papelera-<?php echo ($grupo->propietarioId == $usuConectado) ? 'mostrar' : 'ocultar' ?>" 
                        href="/eliminar-grupo?url=<?php echo $grupo->url ?>"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </li>
            <?php }  ?>
        </ul>

    <?php }  ?>



<?php include_once __DIR__."/../templates/footer.php"; ?> 