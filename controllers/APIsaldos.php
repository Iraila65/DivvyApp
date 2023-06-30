<?php

namespace Controllers;

use Model\Grupo;
use Model\Saldo;

class APIsaldos {

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
                $saldos = Saldo::belongsTo('grupo_id', $grupo->id);
                if (!$saldos) {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'No se han encontrado saldos asociados al Grupo'
                    ];
                    echo json_encode($respuesta); 
                } else {
                    echo json_encode(['saldos' => $saldos]);
                }                
            }
        }
    }

    public static function crear() {
        isAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saldo = new Saldo($_POST);
            $resultado = $saldo->guardar();
            if ($resultado['resultado']) {                
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Saldos generados correctamente',
                    'id' => $resultado['id'],
                    'saldo' => $saldo
                ];
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al crear los saldos',
                    'resultado' => $resultado,
                    'saldo' => $saldo
                ];
            }
            echo json_encode($respuesta);   
        }
    }

    public static function actualizar() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Acceder al movimiento a actualizar
            $saldo = Saldo::find($_POST['id']);
            if(!$saldo) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el registro de saldos'
                ];
            } else {
                $saldo->sincronizar($_POST); 
                
                $saldo->fecha_saldo = date("Y-m-d H:i:s");

                // Actualizar en la BD
                $resultado = $saldo->guardar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualización realizada',
                        'id' => $saldo->id,
                        'saldo' => $saldo
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al actualizar',
                        'saldo' => $saldo
                    ];
                }
            }
            echo json_encode($respuesta); 
        }
    }

    public static function eliminar() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Eliminar el movimiento en la BD
            $saldo = Saldo::find($_POST['id']);
            if ($saldo) {
                $resultado = $saldo->eliminar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Registro de saldos eliminado correctamente',
                        'id' => $saldo->id
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al eliminar el registro de saldos',
                        'saldo' => $saldo
                    ];
                }
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque el registro de saldos no existe',
                    'id' => $_POST['id']
                ];
            }    
            echo json_encode($respuesta);
        }
    }
    
}