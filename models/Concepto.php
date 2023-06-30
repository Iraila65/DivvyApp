<?php

namespace Model;

class Concepto extends ActiveRecord {
    protected static $tabla = "conceptos";
    protected static $columnasDB = ['id', 'nombre'];
    
    public $id;
    public $nombre;

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['nombre'])) $args['nombre']="";
        
        $this->id = $args['id'];
        $this->nombre = $args['nombre'];
    }

}