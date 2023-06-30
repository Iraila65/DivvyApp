<div class="barra barra-grupo">
    <!-- El botón modificar grupo sólo le aparece al propietario -->
    <?php if ($grupo->propietarioId == $_SESSION['id']) { ?>
        <a 
            class="boton mod-grupo boton-<?php echo $grupo->color ?>" 
            href="/modificar-grupo?url=<?php echo $grupo->url ?>">
            Modificar grupo
        </a>
    <?php } ?>
    
    <a 
        class="boton miembros boton-<?php echo $grupo->color ?>" 
        href="/miembros?url=<?php echo $grupo->url ?>">
        Miembros
    </a>
    <a 
        class="boton movtos boton-<?php echo $grupo->color ?>" 
        href="/grupo?url=<?php echo $grupo->url ?>">
        Movimientos
    </a>
    <a 
        class="boton deudas boton-<?php echo $grupo->color ?>" 
        href="/deudas?url=<?php echo $grupo->url ?>">
        Deudas
    </a>
    <a 
        class="boton analisis boton-<?php echo $grupo->color ?>" 
        href="/analisis?url=<?php echo $grupo->url ?>">
        Análisis de gastos
    </a>
</div>

<input class="usuario-conectado" type="hidden" name="usuario_id" value="<?php echo $_SESSION['id'] ?>">
<input class="nombre-usuario" type="hidden" name="nombre" value="<?php echo $_SESSION['nombre'] ?>">
<input class="grupo-conectado" type="hidden" name="url" value="<?php echo $grupo->url ?>">
<input class="propietario-grupo" type="hidden" name="propietarioId" value="<?php echo $grupo->propietarioId ?>">