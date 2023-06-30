<?php

namespace Controllers;

use Model\Usuario;
use Classes\Email;

class LoginController {
    public static function login($router) {
        $auth = new Usuario();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();
            if (empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);
                if ($usuario) {
                    $correcto = $auth->validarPassAndConf($usuario);
                    if ($correcto) {
                        // Autenticar al usuario
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre." ".$usuario->apellido;
                        $_SESSION['usuario'] = $usuario->email;
                        $_SESSION['admin'] = $usuario->admin;
                        $_SESSION['login'] = true;
                        // Redireccionar al usuario a la página de grupos si es cliente o a la de administración si es admin
                        if ($usuario->admin) {
                            header("Location: /admin");
                        } else {
                            header("Location: /dashboard");
                        }
                        
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render ('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'auth' => $auth,
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location:/');
    }

    public static function olvide($router) {
        $auth = new Usuario();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth = new Usuario($_POST);
            
            $usuario = Usuario::where("email", $auth->email);
            if ($usuario) {
                //existe el usuario y le permitimos cambiar la password
                // Generar un token único
                $usuario->generarToken();
                $usuario->guardar();

                // Enviar el mail
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarInstrucciones();

                Usuario::setAlerta('exito', 'Revisa tu email');
                
            } else {
                Usuario::setAlerta('error', 'Usuario no encontrado');
            }            
        }

        $alertas = Usuario::getAlertas();
        $router->render ('auth/olvide', [
            'titulo' => 'Olvide Password',
            'auth' => $auth,
            'alertas' => $alertas
        ]);
    }

    public static function recuperar($router) {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where("token", $token);
        $tokenValido = false;
        if (empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            $tokenValido = true;
            
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar la longitud de la password
            $password = new Usuario($_POST);
            $alertas = $password->validarPass();
            if (empty($alertas)) {
                $usuario->password = $password->password;
                // Hashear el password
                $usuario->hashPasword();
                // Modificar el usuario a confirmado
                $usuario->confirmado = "1";
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
                    Usuario::setAlerta('exito', 'Password modificada correctamente');
                    header('Location: /');
                } else {
                    Usuario::setAlerta('error', 'error en la actualización');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render ('auth/recuperar-password', [
            'titulo' => 'Recuperar Password',
            'alertas' => $alertas,
            'usuario' => $usuario,
            'tokenValido' => $tokenValido
        ]);
    }

    public static function crear($router) {
        $usuario = new Usuario();
        $alertas = Usuario::getAlertas();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario->sincronizar($_POST);

            // Validar los errores
            $alertas = $usuario->validar();
            
            if (empty($alertas)) {
                // Verificar si el usuario ya existe
                $resultado = $usuario->existeUsuario();
               
                if ($resultado->num_rows>0) {
                    $alertas = Usuario::getAlertas();
                } else {    // El usuario no está registrado
                    // Hashear el password
                    $usuario->hashPasword();

                    // Generar un token único
                    $usuario->generarToken();

                    // Enviar el mail
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    
                    if($resultado) {
                        header ('Location: /mensaje');
                    }
                }   
            }   
        }

        $router->render ('auth/crear-cuenta', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function confirmar($router) {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where("token", $token);
        
        if (empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // Modificar el usuario a confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $resultado = $usuario->guardar();
            if ($resultado) {
                Usuario::setAlerta('exito', 'Usuario confirmado correctamente');
            } else {
                Usuario::setAlerta('error', 'error en la actualización');
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render ('auth/confirmar-cuenta', [
            'titulo' => 'Confirmar cuenta',
            'alertas' => $alertas
        ]);
    }

    public static function mensaje($router) {
        $alertas = [];
        $router->render ('auth/mensaje', [
            'titulo' => 'Cuenta creada',
            'alertas' => $alertas
        ]);
    }
}