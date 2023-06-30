<?php

namespace Controllers;

use Model\Grupo;
use Model\Miembro;
use MVC\Router;
use Model\Usuario;
use Model\Saldo;
use Classes\PaginacionGrupo;

class MiembroController {

    public static function index(Router $router) {
        isAuth();

        $urlGrupo = $_GET['url'];
        if (!$urlGrupo) header('Location: /dashboard');
        $grupo = Grupo::where('url', $urlGrupo);
        
        // No se valida que la persona que visita el grupo pertenece a él para que pueda entrar
        // por invitación y decir qué miembro es él.
        // $datos = [
        //     'grupo_id' => $grupo->id,
        //     'usuario_id' => $_SESSION['id']
        // ];
        // $resultado = Miembro::whereArray($datos);
        // if (!$resultado) {
        //     header('Location: /dashboard');
        // }

        $alertas = [];

        // No puedo poner paginación porque entonces no funciona la lógica de las casillas "soy yo" 

        // Paginación
        // $pagina_actual = $_GET['page'];
        // $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        // if(!$pagina_actual || $pagina_actual<1) {
        //     Header('Location: /miembros?url='.$grupo->url.'&page=1');
        // }
        // $registros_por_pagina = 4;
        // $total_registros = Miembro::total('grupo_id', $grupo->id);

        // $paginacion = new PaginacionGrupo($urlGrupo, $pagina_actual, $registros_por_pagina, $total_registros);

        // if ($pagina_actual > $paginacion->total_paginas() && $paginacion->total_paginas() > 0) {
        //     Header('Location: /miembros?url='.$grupo->url.'&page='.$paginacion->total_paginas());            
        // }

        // $miembros = Miembro::paginarBelongsTo($registros_por_pagina, $paginacion->offset(), 'grupo_id', $grupo->id);
        $miembros = Miembro::BelongsTo('grupo_id', $grupo->id);
        foreach($miembros as $miembro) {
            if ($miembro->usuario_id !== null) {
                $miembro->usuario = Usuario::find($miembro->usuario_id);
            } else {
                $miembro->usuario = new Usuario();
            }
            $miembro->saldo = Saldo::where('miembro_id', $miembro->id);
        }

        $router->render('miembros/index', [
            'titulo' => $grupo->grupo,
            'nombre' => $_SESSION['nombre'],
            'grupo' => $grupo,
            'miembros' => $miembros,
            'alertas' => $alertas
        ]);
    }

    public static function crear(Router $router) {
        isAuth();
        $alertas = [];
        
        $urlGrupo = $_GET['url'];
        if (!$urlGrupo) header('Location: /dashboard');
        $grupo = Grupo::where('url', $urlGrupo);
        if (!$grupo) header('Location: /dashboard');

        $miembro = new Miembro();
        $miembro->grupo_id = $grupo->id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $miembro = new Miembro ($_POST);
            $alertas = $miembro->validar();
            if (empty($alertas)) {
                // Validar que no existe un miembro con el mismo nombre
                $datos = [
                    'nombre' => $miembro->nombre,
                    'grupo_id' => $miembro->grupo_id
                ];
                
                $resultado = Miembro::whereArray($datos);
                if ($resultado) {
                    $alertas['error'][] = "Ya existe un miembro con este nombre";
                } else {
                    $resultado = $miembro->guardar();
                    if ($resultado['resultado']) {
                        $saldo = new Saldo($_POST);
                        $saldo->miembro_id = $resultado['id'];
                        $resultadoSaldo = $saldo->guardar();
                        if ($resultadoSaldo['resultado']) {
                            Header('Location:/miembros?url='.$grupo->url);
                        } else {
                            $alertas['error'][] = "Hubo un error al crear los saldos. Inténtalo de nuevo";
                            $resultado = $miembro->eliminar();
                        }    
                    } else {
                        $alertas['error'][] = "Hubo un error al intentar crear el miembro. Inténtalo de nuevo";
                    }
                }
            }
        }

        $router->render('miembros/crear-miembro', [
            'titulo' => $grupo->grupo,
            'nombre' => $_SESSION['nombre'],
            'alertas'=> $alertas,
            'grupo' => $grupo,
            'miembro' => $miembro
        ]);
    }

}