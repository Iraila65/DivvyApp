<?php

namespace Model;

class Saldo extends ActiveRecord {
    protected static $tabla = "saldos";
    protected static $columnasDB = ['id', 'grupo_id', 'miembro_id', 'gastos', 'ingresos', 'saldo', 'fecha_saldo'];
    
    public $id;
    public $grupo_id;
    public $miembro_id;
    public $gastos;
    public $ingresos; 
    public $saldo; 
    public $fecha_saldo;     

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['grupo_id'])) $args['grupo_id']=0;
        if (!isset($args['miembro_id'])) $args['miembro_id']=0;
        if (!isset($args['gastos'])) $args['gastos']=0;
        if (!isset($args['ingresos'])) $args['ingresos']=0;
        if (!isset($args['saldo'])) $args['saldo']=0;
        
        $this->id = $args['id'];
        $this->grupo_id = $args['grupo_id'];
        $this->miembro_id = $args['miembro_id'];
        $this->gastos = $args['gastos'];
        $this->ingresos = $args['ingresos'];
        $this->saldo = $args['saldo'];
        $this->fecha_saldo = date("Y-m-d H:i:s");
    }

    public static function saldosdeGrupo($grupo_id) {
        $query = "SELECT SUM saldo FROM ".static::$tabla;    
        $query .= " WHERE grupo_id = '".$grupo_id."'";
        
        $resultado = self::$db->query($query);
        $saldo = $resultado->fetch_array();
        return array_shift($saldo) ;
    }

}