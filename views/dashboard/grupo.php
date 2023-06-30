<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>
        
        <div class="contenedor-nueva-tx">
            <button type="button" class="agregar-tx" id="agregar-tx">&#43; Nueva transacción</button>
        </div>
        <?php include_once __DIR__."/../templates/alertas.php"; ?>

        <div class="filtros" id="filtros">
            <div class="filtros-inputs">
                <h2>Ver movimientos: </h2>
                <div class="campo">
                    <label>De quién?</label>                        
                    <select 
                        name="mvtos_de" 
                        id="mvtos_de"
                        class="miembro__select"
                    >
                        <option value="T"> Todos </option>
                        <?php foreach($miembros as $miembro) { ?>
                            <option  
                                value="<?php echo $miembro->id ?>"
                            ><?php echo $miembro->nombre ?></option>
                        <?php } ?>
                    </select>  
                </div>

            </div>
        </div>

        <ul id="listado-txs" class="listado-txs"></ul>

    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 
