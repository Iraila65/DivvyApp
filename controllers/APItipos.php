<?php

namespace Controllers;

use Model\Tipo;

class APItipos {

    public static function index() {
        $tipos = Tipo::all();
        echo json_encode(['tipos' => $tipos]);
    }

    public static function crear() {
        isAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = new Tipo($_POST);
            // Validar que no existe un concepto con el mismo nombre
            $resultado = Tipo::where('nombre', $tipo->nombre);
            if ($resultado) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Ya existe un tipo con este nombre'
                ];
            } else {
                $resultado = $tipo->guardar();
                if ($$resultado['resultado']) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Tipo de movimiento agregado correctamente',
                        'id' => $resultado['id']
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al crear el tipo de movimiento',
                        'tipo_mvto' => $tipo
                    ];
                }
            }
            
            echo json_encode($respuesta);   
        }
    }

    public static function actualizar() {
        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = Tipo::find($_POST['id']);
            if(!$tipo) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el tipo de movimiento'
                ];
            } else {
                $tipo->nombre = $_POST['nombre']; 
                $resultado = $tipo->guardar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualización realizada',
                        'id' => $tipo->id
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al actualizar'
                    ];
                }
            }
            echo json_encode($respuesta); 
        }
    }

    public static function eliminar() {
        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Eliminar el concepto en la BD
            $tipo = new Tipo($_POST);
            $resultado = $tipo->eliminar();
            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Tipo de movimiento eliminado correctamente',
                    'id' => $tipo->id
                ];
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar el tipo de movimiento'
                ];
            }           
            echo json_encode($respuesta);
        }
    }
    
}