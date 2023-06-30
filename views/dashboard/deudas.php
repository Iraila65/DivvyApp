<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <div class="contenedor-sm">
            <h4 class="nombre-pagina">Deudas existentes</h4>

            <?php include_once __DIR__."/../templates/alertas.php"; ?>

            <div class="tabla-deudas">
                <ul>
                    <?php if (empty($deudas)) { ?>
                        <li class="no-deudas animate__animated animate__rubberBand">No hay deudas</li>
                    <?php } else { ?>
                        <?php foreach($deudas as $deuda) { ?>
                            <li class="deuda">
                                <p class="animate__animated animate__lightSpeedInLeft">
                                    <?php echo $deuda->from_miembro->nombre ?>
                                </p>
                                <div class='flecha animate__animated animate__bounce'>
                                    <span>debe <?php echo rtrim(rtrim(number_format($deuda->importe, 2), '0'), '.') ?> € a</span> 
                                </div>
                                <p class="animate__animated animate__lightSpeedInRight">
                                    <?php echo $deuda->to_miembro->nombre ?>
                                </p>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    
                </ul>
            </div>

            

            <!-- <ul style="list-style-type:none;">
                <li>&larr;	Flecha hacía la izquierda</li>
                <li>&uarr;	Flecha hacía arriba</li>
                <li>&rarr;	Flecha hacía la derecha</li>
                <li>&darr;	Flecha hacía abajo</li>
                <li>&harr;	Flecha a izquierda y derecha</li>
                <li>&crarr;	Flecha de retorno de carro</li>
                <li>&lArr;	Flecha doble hacía la izquierda</li>
                <li>&uArr;	Flecha doble hacía arriba</li>
                <li>&rArr;	Flecha doble hacía la derecha</li>
                <li>&dArr;	Felcha doble hacía abajo</li>
                <li>&hArr;	Flecha doble a izquierda y derecha</li>
            </ul> -->

            

        </div>

    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 
