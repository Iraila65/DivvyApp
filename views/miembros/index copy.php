<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <h3 class="nombre-pagina"><?php echo $grupo->grupo ?></h3>
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>
        
        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <!-- El botón de añadir miembro sólo le aparece al propietario del grupo -->
        <?php if ($grupo->propietarioId == $_SESSION['id']) { ?>
            <a class="boton-miembro" href="/alta-miembro?url=<?php echo $grupo->url ?>">
                <i class="fa-solid fa-circle-plus"></i>
                 Añadir miembro
            </a>
        <?php } ?>

        <input class="usuario-conectado" type="hidden" name="usuario_id" value="<?php echo $_SESSION['id'] ?>">
        <input class="grupo-conectado" type="hidden" name="url" value="<?php echo $grupo->url ?>">

        <!-- <?php if ($grupo->propietarioId == $_SESSION['id']) ?> El usuario es propietario del grupo -->
        
        <div class="">
            <?php if (!empty($miembros)) { ?>
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th scope="col" class="table__th">Nombre</th>
                            <th scope="col" class="table__th">Usuario</th>
                            <th scope="col" class="table__th">Peso</th>
                            <th scope="col" class="table__th">Este soy yo</th>
                            <th scope="col" class="table__th"></th>
                        </tr>
                    </thead>

                    <tbody class="table__tbody">
                        <?php foreach($miembros as $miembro) { ?>
                            
                            <tr class="table__tr">
                                <td class="table__td">
                                    <?php echo $miembro->nombre ?>
                                </td>
                                <td class="table__td">
                                    <?php echo $miembro->usuario->email ?>
                                </td>
                                <td class="table__td peso-miembro" contenteditable="true">
                                    <?php echo rtrim(rtrim(number_format($miembro->peso, 1), '0'), '.') ?>
                                </td>
                                <td class="table__td">
                                    <input 
                                        type="checkbox" 
                                        class="check-usuario <?php echo ($miembro->usuario_id == $_SESSION['id']) ? 'soy-yo' : '' ?>"
                                        name="" 
                                        value="<?php echo ($miembro->usuario_id == $_SESSION['id']) ? '1' : '0' ?>"
                                        <?php echo ($miembro->usuario_id == $_SESSION['id']) ? 'checked' : '' ?>
                                    /> 
                                </td>
                                <td class="table__td--acciones">

                                    <button class="table__accion table__accion--editar"  type="submit" 
                                        value="<?php echo $miembro->id; ?>">
                                        <i class="fa-solid fa-user-pen"></i>
                                        Modificar
                                    </button>
                                    
                                    <button class="table__accion table__accion--eliminar"  type="submit"
                                        value="<?php echo $miembro->id; ?>">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        Eliminar
                                    </button>
                                    
                                </td>
                            </tr>
            
                        <?php } ?>
                    </tbody>
                </table>

            <?php } else { ?>
                <p class="text-center"> No hay miembros asignados </p>
            <?php } ?>
    
        </div>

        <?php echo $paginacion; ?>

    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 