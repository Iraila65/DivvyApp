<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor"> 

        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <!-- El botón de añadir miembro sólo le aparece al propietario del grupo -->
        <?php if ($grupo->propietarioId == $_SESSION['id']) { ?>
            <a class="boton-miembro" href="/alta-miembro?url=<?php echo $grupo->url ?>">
                <i class="fa-solid fa-circle-plus"></i>
                 Añadir miembro
            </a>
        <?php } ?>

        <div class="">
            <?php if (!empty($miembros)) { ?>
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th scope="col" class="table__th">Nombre</th>
                            <th scope="col" class="table__th">Usuario</th>
                            <th scope="col" class="table__th">Peso</th>
                            <th scope="col" class="table__th"></th>
                            <th scope="col" class="table__th"></th>
                            <th scope="col" class="table__th">Este soy yo</th>
                            <th scope="col" class="table__th">Gastos</th>
                            <th scope="col" class="table__th">Saldo</th>
                            <th scope="col" class="table__th">Activo</th>
                            <!-- El botón de eliminar miembro sólo le aparece al propietario del grupo -->
                            <?php if ($grupo->propietarioId == $_SESSION['id']) { ?>
                                <th scope="col" class="table__th"></th>
                            <?php } ?>
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
                                <td class="table__td--acciones">
                                    <button class="table__accion table__accion--editar"  type="submit" 
                                        value="<?php echo $miembro->id; ?>">
                                        <i class="fa-solid fa-user-pen"></i>
                                        Cambiar peso
                                    </button>                                    
                                </td>
                                <td class="table_td">
                                    <input 
                                        class="usuario-id" 
                                        type="hidden" 
                                        name="usuario-id" 
                                        value="<?php echo $miembro->usuario_id ?>"
                                    >
                                </td>
                                <td class="table__td">
                                    <input 
                                        type="checkbox" 
                                        class="check-usuario <?php echo ($miembro->usuario_id == $_SESSION['id']) ? 'soy-yo' : '' ?>"
                                        name="" 
                                        value="<?php echo $miembro->id ?>"
                                        <?php echo ($miembro->usuario_id == $_SESSION['id']) ? 'checked' : '' ?>
                                    /> 
                                </td>
                                <td class="table__td">
                                    <?php echo rtrim(rtrim(number_format($miembro->saldo->gastos, 1), '0'), '.') ?>
                                </td>
                                <td class="table__td">
                                    <?php echo rtrim(rtrim(number_format($miembro->saldo->saldo, 1), '0'), '.') ?>
                                </td>
                                <td class="table__td">
                                    <input 
                                        type="checkbox" 
                                        class="check-activo"
                                        name="" 
                                        value="<?php echo $miembro->id ?>"
                                        <?php echo ($miembro->activo) ? 'checked' : '' ?>
                                        disabled
                                    /> 
                                </td>
                                <!-- El botón de eliminar miembro sólo le aparece al propietario del grupo -->
                                <!-- y sólo para los miembros que no tienen saldo, para el resto saldrá activar/inactivar -->
                                <?php if ($grupo->propietarioId == $_SESSION['id']) { ?>
                                    <td class="table__td--acciones">                                 
                                        <button 
                                            class="table__accion 
                                                   table__accion--boton
                                                    <?php echo ($miembro->saldo->saldo == 0) ? 'table__accion--eliminar' 
                                                                    : 'table__accion--inactivar' ?>"  
                                            type="submit"
                                            value="<?php echo $miembro->id; ?>">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            <?php echo ($miembro->saldo->saldo == 0) ? 'Eliminar' 
                                                   : ( ($miembro->activo) ? 'Inactivar' : 'Activar' ) ?>
                                        </button>                                    
                                    </td>
                                <?php } ?>
                            </tr>
            
                        <?php } ?>
                    </tbody>
                </table>

            <?php } else { ?>
                <p class="text-center"> No hay miembros asignados </p>
            <?php } ?>
    
        </div>

        <?php // echo $paginacion; ?>

    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 