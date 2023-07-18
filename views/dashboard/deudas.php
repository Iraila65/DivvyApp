<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <div class="contenedor-sm">
            <h4 class="nombre-pagina">Deudas existentes</h4>

            <?php include_once __DIR__."/../templates/alertas.php"; ?>

            <div class="tabla-deudas">
                <ul id="listado-deudas" class="listado-deudas"></ul>
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
