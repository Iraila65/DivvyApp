<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = ['id'];

    public $id;

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            if ($value !== null) {
                $sanitizado[$key] = self::$db->escape_string($value);
            } 
        }
        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    // Devuelve un array
    public static function all($orden = "ASC") {
        $query = "SELECT * FROM " . static::$tabla." ORDER BY id ".$orden;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    // Devuelve un objeto
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = ".$id;
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Busca un registro por un campo determinado
    // Devuelve un objeto
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ".$columna." = '".$valor."'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Busca por varios campos que vienen en un arreglo
    // Devuelve un arreglo
    public static function whereArray($array = []) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ";
        foreach($array as $key => $value) {
            // Mi PHP no tiene la función array_key_last así que lo tengo que hacer de otra forma
            // if ($key == array_key_last($array)) {
            //     $query .= $key." = '".$value."'"; 
            // } else {
            //     $query .= $key." = '".$value."' AND"; 
            // }
            $query .= $key." = '".$value."' AND "; 
        }
        $query = substr($query, 0, -5);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca por varios campos que vienen en un arreglo
    // Devuelve un objeto
    public static function whereArrayOne($array = []) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ";
        foreach($array as $key => $value) {
            $query .= $key." = '".$value."' AND "; 
        }
        $query = substr($query, 0, -5);
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Busca todos los registros por un campo determinado
    public static function belongsTo($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ".$columna." = '".$valor."'";
        $resultado = self::consultarSQL($query);
        return $resultado;  
    }

    // Devuelve un arreglo ordenado
    public static function ordenar($columna, $orden) {
        $query = "SELECT * FROM " . static::$tabla  ." ORDER BY ".$columna." ".$orden;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca todos los registros por un campo determinado
    // Devuelve un arreglo ordenado
    public static function BelongsToOrdenado($colBelongs, $valor, $colOrden, $orden) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE ".$colBelongs." = '".$valor."'"." ORDER BY ".$colOrden." ".$orden;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Devuelve un arreglo ordenado y limitado
    public static function ordenarLimit($columna, $orden, $limite) {
        $query = "SELECT * FROM " . static::$tabla  ." ORDER BY ".$columna." ".$orden." LIMIT ".$limite;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Contar el total de registros
    public static function total($columna = '', $valor = '') {
        $query = "SELECT COUNT(*) FROM " . static::$tabla;
        if ($columna) {
            $query .= " WHERE ".$columna." = '".$valor."'";
        }
        $resultado = self::$db->query($query);
        $total = $resultado->fetch_array();
        return array_shift($total) ;
    }

    // Busca por varios campos que vienen en un arreglo
    // Devuelve el total de registros
    public static function totalArray($array = []) {
        $query = "SELECT COUNT(*) FROM " . static::$tabla." WHERE ";
        foreach($array as $key => $value) {
            $query .= $key." = '".$value."' AND "; 
        }
        $query = substr($query, 0, -5);
        $resultado = self::$db->query($query);
        $total = $resultado->fetch_array();
        return array_shift($total) ;
    }

    // Consulta plana de SQL para usar cuando los métodos no son suficientes
    public static function SQL($query) {
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT ".$limite;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Paginar los registros
    public static function paginar($rows_por_pagina, $offset) {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id LIMIT ".$rows_por_pagina." OFFSET ".$offset;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public static function paginarBelongsTo($rows_por_pagina, $offset, $columna, $valor) {
        $query = "SELECT * FROM ".static::$tabla." WHERE ".$columna." = '".$valor."' ORDER BY id LIMIT ".$rows_por_pagina." OFFSET ".$offset;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Esta query la tengo que montar omitiendo los atributos nulos
        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ('"; 
        $query .= join("', '", array_values($atributos));
        $query .= "') ";

        // Para debuguear el query en APIs
        //return json_encode(['query' => $query]);

        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id
        ];
    }

    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

}