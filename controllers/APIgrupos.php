<?php

namespace Controllers;

use Model\Grupo;

class APIgrupos {

    public static function index() {
        $grupos = Grupo::all();
        echo json_encode(['grupos' => $grupos]);
    }

    public static function getGrupo() {
        $url = $_GET['url'];
        // Leer el grupo de la base de datos
        if (!$url) {
            $respuesta = [
                'tipo' => 'error',
                'mensaje' => 'No existe la url'
            ];
        } else {
            $grupo = Grupo::where('url', $url);
            if ($grupo) {
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Grupo encontrado',
                    'grupo' => $grupo
                ];
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'No se ha encontrado el grujpo'
                ];
            }
        }            
        echo json_encode($respuesta);   
    }

}