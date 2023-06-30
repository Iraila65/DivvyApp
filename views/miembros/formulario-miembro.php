<div class="campo">
    <label for="nombre">Nombre: </label>
    <input 
        type="text"
        name="nombre"
        id="nombre"
        placeholder="Nombre del miembro"
        value="<?php echo $miembro->nombre ?>"
    />
    <input 
        type="hidden"
        value="<?php echo $miembro->grupo_id ?>"
        name="grupo_id"
    />
</div>
<div class="campo">
    <label for="peso">Peso: </label>
    <input 
        type="number"
        pattern="[0-9]+([.,][0-9]+)?"
        step="0.1"
        name="peso"
        id="peso"
        placeholder="1"
        value="<?php echo $miembro->peso ?>"
    />
</div>