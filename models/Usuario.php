<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = "usuarios";
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password',
                    'admin', 'confirmado', 'token', 'creado'];
    
    public $id;
    public $nombre;
    public $apellido;
    public $email; 
    public $password; 
    public $password2;
    public $pass_actual;
    public $new_pass;
    public $admin;
    public $confirmado;
    public $token;
    public $creado;

    public function __construct($args = [])
    {
        if (!isset($args['id'])) $args['id']=null;
        if (!isset($args['nombre'])) $args['nombre']="";
        if (!isset($args['apellido'])) $args['apellido']="";
        if (!isset($args['email'])) $args['email']="";
        if (!isset($args['password'])) $args['password']="";
        if (!isset($args['password2'])) $args['password2']="";
        if (!isset($args['pass_actual'])) $args['pass_actual']="";
        if (!isset($args['new_pass'])) $args['new_pass']="";
        if (!isset($args['password2'])) $args['password2']="";
        if (!isset($args['admin'])) $args['admin']=0;
        if (!isset($args['confirmado'])) $args['confirmado']=0;
        if (!isset($args['token'])) $args['token']="";
        
        $this->id = $args['id'];
        $this->nombre = $args['nombre'];
        $this->apellido = $args['apellido'];
        $this->email = $args['email'];
        $this->password = $args['password'];
        $this->password2 = $args['password2'];
        $this->pass_actual = $args['pass_actual'];
        $this->new_pass = $args['new_pass'];
        $this->admin = $args['admin'];
        $this->confirmado = $args['confirmado'];
        $this->token = $args['token'];
        $this->creado = date('Y/m/d');
    }

    // Validación para la creación de una cuenta
    public function validar() {
        if(!$this->nombre) {
            self::$alertas['error'][] = "El nombre es obligatorio";
        }
        if(!$this->apellido) {
            self::$alertas['error'][] = "El apellido es obligatorio";
        }
        $emailValido = s(filter_var($this->email, FILTER_VALIDATE_EMAIL));
        if (!$emailValido) {
            self::$alertas['error'][] = "Email no valido";
        }
        if (!$this->password) {
            self::$alertas['error'][] = "Es obligatoria una password";
        } elseif ( strlen($this->password) < 6) {
            self::$alertas['error'][] = "La password es demasiado corta, debe ser de al menos 6 caracteres";
        } elseif ($this->password !== $this->password2) {
            self::$alertas['error'][] = "Las passwords tecleadas no coinciden";
        }
        return self::$alertas;
    }

    public function existeUsuario() {
        $query = "SELECT * FROM ".static::$tabla." WHERE email= '".$this->email."' LIMIT 1";    
        $resultado = self::$db->query($query);
        if ($resultado->num_rows) {
            self::$alertas['error'][] = "El usuario ya está registrado";
        }
        return $resultado;  
    }

    public function hashPasword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function generarToken() {
        $this->token = uniqid();
    }

    public function validarLogin() {    
        $emailValido = s(filter_var($this->email, FILTER_VALIDATE_EMAIL));
        if (!$emailValido) {
            self::$alertas['error'][] = "Email no valido";
        }
        if (!$this->password) {
            self::$alertas['error'][] = "Es obligatoria la password";
        } 
        return self::$alertas;
    }

    public function validarPassAndConf($usuario) {
        $correcto = false;
        
        // Comprobar el password
        $passCorrecta = password_verify($this->password, $usuario->password);
        if ($passCorrecta) {
            if ($usuario->confirmado) {
                $correcto = true;
            } else {
                self::$alertas['error'][] = 'El usuario aún no está confirmado';
            }
            
        } else {
            Usuario::setAlerta('error', 'La password no es correcta');
        }

        return $correcto;
    }

    public function validarPass() {
        
        if (!$this->password) {
            self::$alertas['error'][] = "Es obligatoria una password";
        } elseif ( strlen($this->password) < 6) {
            self::$alertas['error'][] = "La password es demasiado corta, debe ser de al menos 6 caracteres";
        }
        return self::$alertas;
    }

    public function validarPerfil() {
        if(!$this->nombre) {
            self::$alertas['error'][] = "El nombre es obligatorio";
        }    
        $emailValido = s(filter_var($this->email, FILTER_VALIDATE_EMAIL));
        if (!$emailValido) {
            self::$alertas['error'][] = "Email no valido";
        }
        $query = "SELECT * FROM ".static::$tabla." WHERE email= '".$this->email."' AND id != '".$this->id."' LIMIT 1";    
        $resultado = self::$db->query($query);
        if ($resultado->num_rows) {
            self::$alertas['error'][] = "El nuevo email ya está registrado";
        }
        
        return self::$alertas;
    }

    public function validarNuevoPass() {
        if (!$this->pass_actual) {
            self::$alertas['error'][] = "Debes teclear la password actual";
        }
        if (!$this->new_pass) {
            self::$alertas['error'][] = "Es obligatoria una password";
        } elseif ( strlen($this->new_pass) < 6) {
            self::$alertas['error'][] = "La password es demasiado corta, debe ser de al menos 6 caracteres";
        }
        return self::$alertas;
    }

    public function validarPassActual() {
        // La función password_verify necesita que la pass hasheada vaya en segundo lugar y en primer lugar la que 
        // queremos comprobar.
        $passCorrecta = password_verify($this->pass_actual, $this->password);
        if ($passCorrecta) {
            if (!$this->confirmado) {
                self::$alertas['error'][] = 'El usuario aún no está confirmado';
            }
        } else {
            self::$alertas['error'][] = 'La password actual no es correcta';
        }
        return self::$alertas;
    }

}