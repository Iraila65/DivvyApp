<?php

namespace Model;

class Deuda extends ActiveRecord {
    protected static $tabla = "deudas";
    protected static $columnasDB = ['id', 'grupo_id', 'from_miembro_id', 'to_miembro_id', 'importe', 'fecha'];
    
    public $id;
    public $grupo_id;
    public $from_miembro_id;
    public $to_miembro_id;
    public $importe;
    public $fecha;     

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['grupo_id'])) $args['grupo_id']=0;
        if (!isset($args['from_miembro_id'])) $args['from_miembro_id']=0;
        if (!isset($args['to_miembro_id'])) $args['to_miembro_id']=0;
        if (!isset($args['importe'])) $args['importe']=0;
        
        $this->id = $args['id'];
        $this->grupo_id = $args['grupo_id'];
        $this->from_miembro_id = $args['from_miembro_id'];
        $this->to_miembro_id = $args['to_miembro_id'];
        $this->importe = $args['importe'];
        $this->fecha = date('Y/m/d');
    }

    public static function resetGrupo($grupo_id) {
        $query = "DELETE FROM "  . static::$tabla;  
        $query .= " WHERE grupo_id = '".$grupo_id."'";
        $resultado = self::$db->query($query);
        return $resultado;
    }

}