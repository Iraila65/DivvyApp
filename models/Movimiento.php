<?php

namespace Model;

class Movimiento extends ActiveRecord {
    protected static $tabla = "movimientos";
    protected static $columnasDB = ['id', 'grupo_id', 'miembro_id', 'cantidad', 'tipo', 'quien', 'concepto_id',
                                    'descripcion', 'fecha', 'creado', 'creador_id', 'actualizado', 'actualizador_id'];
    
    public $id;
    public $grupo_id;
    public $miembro_id;
    public $cantidad;
    public $tipo; 
    public $quien; 
    public $concepto_id; 
    public $descripcion; 
    public $fecha; 
    public $creado;
    public $creador_id; 
    public $actualizado;
    public $actualizador_id; 
    

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['grupo_id'])) $args['grupo_id']=0;
        if (!isset($args['miembro_id'])) $args['miembro_id']=0;
        if (!isset($args['cantidad'])) $args['cantidad']=0;
        if (!isset($args['tipo'])) $args['tipo']=1;
        if (!isset($args['quien'])) $args['quien']=null;
        if (!isset($args['concepto_id']) || $args['concepto_id']=="") $args['concepto_id']=null;
        if (!isset($args['descripcion'])) $args['descripcion']=null;
        if (!isset($args['fecha'])) $args['fecha']=date("Y-m-d H:i:s");
        if (!isset($args['creador_id'])) $args['creador_id']=0;
        if (!isset($args['actualizado'])) $args['actualizado']=null;
        if (!isset($args['actualizador_id'])) $args['actualizador_id']=null;
        
        $this->id = $args['id'];
        $this->grupo_id = $args['grupo_id'];
        $this->miembro_id = $args['miembro_id'];
        $this->cantidad = $args['cantidad'];
        $this->tipo = $args['tipo'];
        $this->quien = $args['quien'];
        $this->concepto_id = $args['concepto_id'];
        $this->descripcion = $args['descripcion'];
        $this->fecha = $args['fecha'];
        $this->creado = date('Y/m/d');
        $this->creador_id = $args['creador_id'];
        $this->actualizado = $args['actualizado'];
        $this->actualizador_id = $args['actualizador_id'];
        
    }

    public static function gastosdeMiembro($miembro_id, $grupo_id) {
        $query = "SELECT SUM cantidad FROM ".static::$tabla;    
        $query .= " WHERE miembro_id= '".$miembro_id;
        $query .= " AND grupo_id = '".$grupo_id;
        $query .= " AND tipo = '1'";    
        
        $resultado = self::$db->query($query);
        $total = $resultado->fetch_array();
        return array_shift($total) ;
    }

}