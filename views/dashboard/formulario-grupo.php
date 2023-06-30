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
<div class="campo">
    <label>Color: </label>
    <div class="colores">
        <div 
            class="circle color <?php echo ($grupo->color=='color1' ? 'color-marcado' : '') ?>" 
            id="color1">
        </div>
        <div 
            class="circle color <?php echo ($grupo->color=='color2' ? 'color-marcado' : '') ?>" 
            id="color2">
        </div>
        <div 
            class="circle color <?php echo ($grupo->color=='color3' ? 'color-marcado' : '') ?>" 
            id="color3">
        </div>
        <div 
            class="circle color <?php echo ($grupo->color=='color4' ? 'color-marcado' : '') ?>" 
            id="color4">
        </div>
        <div 
            class="circle color <?php echo ($grupo->color=='color5' ? 'color-marcado' : '') ?>" 
            id="color5">
        </div>
        <div 
            class="circle color <?php echo ($grupo->color=='color6' ? 'color-marcado' : '') ?>" 
            id="color6">
        </div>
    </div>
    <input 
        type="hidden"
        class="color-seleccionado"
        value="<?php echo $grupo->color ?>"
        name="color"
    />

</div>
