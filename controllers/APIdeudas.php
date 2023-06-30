<?php

namespace Controllers;

use Model\Grupo;
use Model\Miembro;
use Model\Deuda;


class APIdeudas {

    public static function index() {
        isAuth();
        $url = $_GET['url'];
        if (!$url) {
            $respuesta = [
                'tipo' => 'error',
                'mensaje' => 'No se ha encontrado el Grupo'
            ];
            echo json_encode($respuesta);   
        } else {
            $grupo = Grupo::where('url', $url);
            if (!$grupo) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'No se ha encontrado el Grupo'
                ];
                echo json_encode($respuesta);   
            } else {
                $deudas = Deuda::belongsTo('grupo_id', $grupo->id);
                foreach($deudas as $deuda) {
                    $deuda->from_miembro = Miembro::find($deuda->from_miembro_id);
                    $deuda->to_miembro = Miembro::find($deuda->to_miembro_id);
                }
                echo json_encode(['deudas' => $deudas]);
            }
        }
        
    }

    public static function crear() {
        isAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deuda = new Deuda($_POST);
            $resultado = $deuda->guardar();
            if ($resultado['resultado']) {
                // Hay que formatear la respuesta para que pueda mostrarla en el listado de mvtos
                $deudaarray['id'] = $resultado['id'];
                $deudaarray['grupo_id'] = $deuda->grupo_id;
                $deudaarray['from_miembro_id'] = $deuda->from_miembro_id;
                $deudaarray['to_miembro_id'] = $deuda->to_miembro_id;
                $deudaarray['importe'] = $deuda->importe;
                $deudaarray['from_miembro'] = Miembro::find($deuda->from_miembro_id);
                $deudaarray['to_miembro'] = Miembro::find($deuda->to_miembro_id);
                
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Deuda agregada correctamente',
                    'id' => $resultado['id'],
                    'deuda' => $deuda
                ];
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al crear el movimiento',
                    'resultado' => $resultado,
                    'movimiento' => $deuda
                ];
            }
            echo json_encode($respuesta);   
        }
    }

    public static function eliminar() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $grupo = Grupo::find($_POST['grupo_id']);
            if (!$grupo) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'No se ha encontrado el Grupo'
                ];
                echo json_encode($respuesta);   
            } else {
                // Eliminar TODAS las deudas del grupo
                $deudas = Deuda::belongsTo('grupo_id', $grupo->id);
                $exito = true;
                if ($deudas) {
                    foreach($deudas as $deuda) {
                        $resultado = $deuda->eliminar();
                        if (!$resultado) {
                            $exito = false;
                        }
                    }
                }
                if ($exito) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Deudas borradas correctamente'
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al eliminar las deudas'
                    ];
                }
            }
            echo json_encode($respuesta);
        }
    }
    
}