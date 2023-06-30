<?php

namespace Controllers;

use Model\Concepto;

class APIconceptos {

    public static function index() {
        $conceptos = Concepto::all();
        echo json_encode(['conceptos' => $conceptos]);
    }

    public static function crear() {
        isAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $concepto = new Concepto($_POST);
            // Validar que no existe un concepto con el mismo nombre
            $resultado = Concepto::where('nombre', $concepto->nombre);
            if ($resultado['resultado']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Ya existe un concepto con este nombre'
                ];
            } else {
                $resultado = $concepto->guardar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Concepto agregado correctamente',
                        'id' => $resultado['id']
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al crear el concepto',
                        'concepto' => $concepto
                    ];
                }
            }
            
            echo json_encode($respuesta);   
        }
    }

    public static function actualizar() {
        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $concepto = Concepto::find($_POST['id']);
            if(!$concepto) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el concepto'
                ];
            } else {
                $concepto->nombre = $_POST['nombre']; 
                $resultado = $concepto->guardar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualización realizada',
                        'id' => $concepto->id
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
            $concepto = new Concepto($_POST);
            $resultado = $concepto->eliminar();
            if ($resultado) {
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Miembro eliminado correctamente',
                    'id' => $concepto->id
                ];
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar el concepto'
                ];
            }           
            echo json_encode($respuesta);
        }
    }
    
}