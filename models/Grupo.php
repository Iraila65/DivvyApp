<?php

namespace Model;

class Grupo extends ActiveRecord {
    protected static $tabla = "grupos";
    protected static $columnasDB = ['id', 'grupo', 'color', 'propietarioId', 'url', 'creado'];
    
    public $id;
    public $grupo;
    public $color;
    public $propietarioId; 
    public $url; 
    public $creado;

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['grupo'])) $args['grupo']="";
        if (!isset($args['color'])) $args['color']="";
        if (!isset($args['propietarioId'])) $args['propietarioId']="";
        if (!isset($args['url'])) $args['url']="";
        
        $this->id = $args['id'];
        $this->grupo = $args['grupo'];
        $this->color = $args['color'];
        $this->propietarioId = $args['propietarioId'];
        $this->url = $args['url'];
        $this->creado = date('Y/m/d');
    }

    // Validación para la creación de un grupo
    public function validar() {
        if(!$this->grupo) {
            self::$alertas['error'][] = "El nombre del grupo es obligatorio";
        }
        if(!$this->color) {
            self::$alertas['error'][] = "Selecciona un color para el grupo";
        }
        return self::$alertas;
    }
}