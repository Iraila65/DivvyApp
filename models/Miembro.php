<?php

namespace Model;

class Miembro extends ActiveRecord {
    protected static $tabla = "miembros";
    protected static $columnasDB = ['id', 'nombre', 'peso', 'grupo_id', 'usuario_id', 'creado', 'activo'];
    
    public $id;
    public $nombre;
    public $peso;
    public $grupo_id;
    public $usuario_id; 
    public $creado;
    public $activo;

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['nombre'])) $args['nombre']="";
        if (!isset($args['peso'])) $args['peso']=1;
        if (!isset($args['grupo_id'])) $args['grupo_id']=0;
        if (!isset($args['usuario_id'])) $args['usuario_id']=null;
        if (!isset($args['activo'])) $args['activo']=true;
        
        $this->id = $args['id'];
        $this->nombre = $args['nombre'];
        $this->peso = $args['peso'];
        $this->grupo_id = $args['grupo_id'];
        $this->usuario_id = $args['usuario_id'];
        $this->creado = date('Y/m/d');
        $this->activo = $args['activo'];
    }

    public static function gruposdeUsuario($id) {
        $query = "SELECT grupo_id FROM ".static::$tabla." WHERE usuario_id= '".$id."'";    
        $resultado = self::$db->query($query);
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = $registro;
        }
        $resultado->free();
        return $array;
    }

    public static function miembrosdeGrupo($id) {
        $query = "SELECT usuario_id FROM ".static::$tabla." WHERE grupo_id= '".$id."'";    
        $resultado = self::$db->query($query);
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = $registro;
        }
        $resultado->free();
        return $array;
    }

}