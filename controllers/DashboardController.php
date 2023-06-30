<?php

namespace Controllers;

use MVC\Router;
use Model\Grupo;
use Model\Saldo;
use Model\Miembro;
use Model\Deuda;
use Model\Movimiento;
use Model\Usuario;

class DashboardController {

    public static function index(Router $router) {
        isAuth();

        // Buscar todos los grupos en los que está el usuario 
        $arraygrupos = Miembro::gruposdeUsuario($_SESSION['id']);
        $grupos = [];
        foreach($arraygrupos as $grupo) {
            $grupos[] = Grupo::find($grupo['grupo_id']);
        }

        $router->render('dashboard/index', [
            'titulo' => 'Grupos',
            'nombre' => $_SESSION['nombre'],
            'usuConectado' => $_SESSION['id'],
            'grupos' => $grupos
        ]);
    }

    public static function crear(Router $router) {
        isAuth();
        $alertas = [];
        $grupo = new Grupo();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $grupo = new Grupo ($_POST);
            $alertas = $grupo->validar();
            if (empty($alertas)) {
                // Almacenar el id del propietario
                $grupo->propietarioId = $_SESSION['id'];

                // Validar que no existe un grupo con el mismo nombre
                $datos = [
                    'grupo' => $grupo->grupo,
                    'propietarioId' => $grupo->propietarioId
                ];
                
                $resultado = Grupo::whereArray($datos);
                if ($resultado) {
                    $alertas['error'][] = "El nombre del grupo ya existe";
                } else {
                    // Generar una url única
                    $grupo->url = md5(uniqid());
                    
                    $resultadoGrupo = $grupo->guardar();
                    if ($resultadoGrupo['resultado']) {
                        // Crear el miembro del grupo con el propietario
                        $miembro = new Miembro();
                        $miembro->nombre = $_SESSION['nombre'];
                        $miembro->grupo_id = $resultadoGrupo['id'];
                        $miembro->usuario_id = $grupo->propietarioId;
                        $resultadoMiembro = $miembro->guardar();
                        if ($resultadoMiembro['resultado']) {
                            $saldo = new Saldo();
                            $saldo->grupo_id = $resultadoGrupo['id'];
                            $saldo->miembro_id = $resultadoMiembro['id'];
                            $resultadoSaldo = $saldo->guardar();
                            if ($resultadoSaldo['resultado']) {
                                Header('Location:/grupo?url='.$grupo->url);
                            } else {
                                $alertas['error'][] = "Hubo un error al crear los saldos. Inténtalo de nuevo";
                                $miembro->eliminar();
                                $grupo->eliminar();
                            }    

                        } else {
                            $alertas['error'][] = "Hubo un error al insertar el miembro";
                        }                        
                    } else {
                        $alertas['error'][] = "Hubo un error al guardar el grupo";
                    }

                }
            }
        }

        $router->render('dashboard/crear-grupo', [
            'titulo' => 'Crear Grupo',
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo
        ]);
    }

    public static function modificar(Router $router) {
        isAuth();
        
        $urlGrupo = $_GET['url'];
        if (!$urlGrupo) header('Location: /dashboard');
        $grupo = Grupo::where('url', $urlGrupo);
        
        // Validar que la persona que modifica el grupo es el propietario
        if ($grupo->propietarioId !== $_SESSION['id']) header('Location: /dashboard');

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $grupo->sincronizar($_POST);
            
            $alertas = $grupo->validar();
            if (empty($alertas)) {
                // Validar que no existe un grupo con distinto id y con el mismo nombre
                $resultado = Grupo::where('grupo', $grupo->grupo);
                
                if (isset($resultado) && $resultado->id !== $grupo->id) {
                    $alertas['error'][] = "El nombre del grupo ya existe";
                } else {
                    $resultado = $grupo->guardar();
                    if ($resultado) {
                        Header('Location:/grupo?url='.$grupo->url);
                    } else {
                        $alertas['error'][] = "Hubo un error en la actualización. Inténtelo más tarde";
                    }                    
                }
            }
        }

        $router->render('dashboard/modificar-grupo', [
            'titulo' => $grupo->grupo,
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo
        ]);
    }

    public static function eliminar(Router $router) {

        $urlGrupo = $_GET['url'];
        if (!$urlGrupo) header('Location: /dashboard');
        $grupo = Grupo::where('url', $urlGrupo);
    
        // Validar que la persona que elimina el grupo es el propietario
        if ($grupo->propietarioId !== $_SESSION['id']) header('Location: /dashboard');

        // Obtener los miembros del grupo
        $miembros = Miembro::belongsTo('grupo_id', $grupo->id);
        foreach($miembros as $miembro) {
            if ($miembro->usuario_id !== null) {
                $miembro->usuario = Usuario::find($miembro->usuario_id);
            } else {
                $miembro->usuario = new Usuario();
            }
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Eliminar los saldos
            $saldos = Saldo::belongsTo('grupo_id', $grupo->id);
            if ($saldos) {
                foreach($saldos as $saldo) {
                    $saldo->eliminar();
                }
            }

            // Eliminar los miembros
            foreach($miembros as $miembro) {
                $miembro->eliminar();
            }

            // Eliminar el grupo
            $grupo->eliminar();
            
            // Redireccionar
            header('Location: /dashboard');
        }

        $router->render('dashboard/eliminar-grupo', [
            'titulo' => 'Eliminar Grupo',
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo,
            'miembros' => $miembros
        ]);
        
    }

    public static function grupo(Router $router) {
        isAuth();
        $alertas = [];

        // Leer el grupo de la base de datos
        $token = $_GET['url'];
        if (!$token) header('Location: /dashboard');
        $grupo = Grupo::where('url', $token);

        // También pasamos a la vista los miembros del grupo
        $miembros = Miembro::belongsTo('grupo_id', $grupo->id);
        

        // OJO AQUÍ NO HACEMOS ESTA VALIDACION PORQUE PUEDE LLEGAR POR INVITACION Y DECIDIR QUÉ MIEMBRO ES CON EL "SOY_YO"
        // Validar que la persona que visita el grupo pertenece a él
        // $datos = [
        //     'grupo_id' => $grupo->id,
        //     'usuario_id' => $_SESSION['id']
        // ];
        // $resultado = Miembro::whereArray($datos);
        // if (!$resultado) {
        //     header('Location: /dashboard');
        // }

        $router->render('dashboard/grupo', [
            'titulo' => $grupo->grupo,
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo,
            'miembros' => $miembros
        ]);
    }

    public static function deudas(Router $router) {
        isAuth();
        $alertas = [];

        // Leer el grupo de la base de datos
        $token = $_GET['url'];
        if (!$token) header('Location: /dashboard');
        $grupo = Grupo::where('url', $token);

        // También pasamos a la vista los miembros y las deudas del grupo
        $deudas = Deuda::belongsTo('grupo_id', $grupo->id);
        foreach($deudas as $deuda) {
            $deuda->from_miembro = Miembro::find($deuda->from_miembro_id);
            $deuda->to_miembro = Miembro::find($deuda->to_miembro_id);
        }

        $router->render('dashboard/deudas', [
            'titulo' => $grupo->grupo,
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo,
            'deudas' => $deudas
        ]);
    }

    public static function analisis(Router $router) {
        isAuth();
        $alertas = [];

        // Leer el grupo de la base de datos
        $token = $_GET['url'];
        if (!$token) header('Location: /dashboard');
        $grupo = Grupo::where('url', $token);

        $router->render('dashboard/analisis', [
            'titulo' => $grupo->grupo,
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo
        ]);
    }

    public static function perfil($router) {
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validarPerfil();
            if (empty($alertas)) {
                $usuario->guardar();

                // Reescribimos la sesión con los cambios
                $_SESSION['nombre'] = $usuario->nombre." ".$usuario->apellido;
                $_SESSION['usuario'] = $usuario->email;

                Usuario::setAlerta('exito', 'Cambios realizados correctamente');
                
            }            
        }

        $alertas = Usuario::getAlertas();
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'nombre' => $_SESSION['nombre'], 
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiarPass($router) {
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validarNuevoPass();
            if (empty($alertas)) {
                $alertas = $usuario->validarPassActual();
            
                if (empty($alertas)) {
                    // Hashear la nueva password
                    $usuario->password = $usuario->new_pass;
                    $usuario->hashPasword();

                    // Los eliminamos para que no queden en memoria
                    unset($usuario->pass_actual);
                    unset($usuario->new_pass);

                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Password cambiada correctamente');
                    } else {
                        Usuario::setAlerta('error', 'Hubo un error al guardar la Password, inténtelo de nuevo');
                    }
                    
                }                
            }            
        }

        $alertas = Usuario::getAlertas();
        $router->render('dashboard/cambiar-pass', [
            'titulo' => 'Cambiar Password',
            'nombre' => $_SESSION['nombre'], 
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

}