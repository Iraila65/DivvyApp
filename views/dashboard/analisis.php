<?php include_once __DIR__."/../templates/header.php"; ?>  
    
    <div class="contenedor">
        <?php include_once __DIR__."/../templates/barraAcciones.php"; ?>

        <!-- <h4 class="nombre-pagina">Análisis de gastos</h4> -->
  
        <div class="tab">
            <a href="#" class="tablinks pest-tab1" onclick="openTab(event, 'Tab1')">Por categoría</a>
            <a href="#" class="tablinks pest-tab2" onclick="openTab(event, 'Tab2')">Por miembro</a>
            <a href="#" class="tablinks pest-tab3" onclick="openTab(event, 'Tab3')">Serie mensual</a>
        </div>

        <div id="Tab1" class="tabcontent">
            <h3>Análisis por categoría de gasto</h3>

            <div class="dashboard__grafica">
                <canvas id="grafica-totales-categoria"></canvas>
                <canvas id="grafica-desglose-miembro"></canvas>
            </div>
        </div>

        <div id="Tab2" class="tabcontent">
            <h3>Análisis por miembro del grupo</h3>

            <div class="dashboard__grafica">
                <canvas id="grafica-totales-miembros"></canvas>
                <canvas id="grafica-desglose-categoria"></canvas>
            </div>
        </div>

        <div id="Tab3" class="tabcontent">
            <h3>Serie mensual de los gastos</h3>



        </div>

        <script>
            function openTab(evt, tabName) {
                var i, tabcontent, tablinks;
                
                // Oculta todos los contenidos de las pestañas
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                
                // Desactiva todos los enlaces de las pestañas
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                
                // Muestra el contenido de la pestaña seleccionada y marca el enlace como activo
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.className += " active";
            }

            // Establece la pestaña 1 como activa por defecto
            document.getElementsByClassName("tablinks")[0].click();
        </script>


    </div>


<?php include_once __DIR__."/../templates/footer.php"; ?> 
