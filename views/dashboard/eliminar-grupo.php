<?php include_once __DIR__."/../templates/header.php"; ?>  

    <div class="contenedor-sm">
        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <form class="formulario" action="/eliminar-grupo?url=<?php echo $grupo->url ?>" method="POST">

            <div class="campo">
                <label for="grupo">Nombre del grupo: </label>
                <input 
                    type="text"
                    name="grupo"
                    id="grupo"
                    placeholder="Nombre del grupo"
                    value="<?php echo $grupo->grupo ?>"
                />
            </div>
            
            <div class="miembros-eliminar">
                <h4>Miembros</h4>
                <?php if (!empty($miembros)) { ?>
                    <table class="table">
                        <thead class="table__thead table__thead-<?php echo $grupo->color ?>">
                            <tr>
                                <th scope="col" class="table__th">Nombre</th>
                                <th scope="col" class="table__th">Usuario</th>
                                <th scope="col" class="table__th">Peso</th>
                                <th scope="col" class="table__th"></th>
                                <th scope="col" class="table__th">Este soy yo</th>  
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
                                    <td class="table__td peso-miembro" contenteditable="false">
                                        <?php echo rtrim(rtrim(number_format($miembro->peso, 1), '0'), '.') ?>
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
                                    
                                </tr>
                
                            <?php } ?>
                        </tbody>
                    </table>

                <?php } else { ?>
                    <p class="text-center"> No hay miembros asignados </p>
                <?php } ?>
        
            </div>

            <input class="boton-eliminar" type="submit" value="Eliminar">
        </form>

    </div>

<?php include_once __DIR__."/../templates/footer.php"; ?> 
