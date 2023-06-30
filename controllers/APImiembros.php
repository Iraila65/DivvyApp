<?php

namespace Controllers;

use Model\Grupo;
use Model\Saldo;
use Model\Miembro;


class APImiembros {

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
                $miembros = Miembro::belongsTo('grupo_id', $grupo->id);
                echo json_encode(['miembros' => $miembros]);
            }
        }
    }

    public static function miembrosActivos() {
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
                $datos = [
                    'grupo_id' => $grupo->id,
                    'activo' => 1
                ];
                $miembros = Miembro::whereArray($datos);
                echo json_encode(['miembros' => $miembros]);
            }
        }
    }

    public static function crear() {
        isAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'];
            $grupo = Grupo::where('url', $url);
            if(!$grupo) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el grupo'
                ];
            } else {
                // Agregar el miembro al grupo
                $miembro = new Miembro($_POST);
                // Validar que no existe un miembro con el mismo nombre
                $datos = [
                    'nombre' => $miembro->nombre,
                    'grupo_id' => $miembro->grupo_id
                ];
                $resultado = Miembro::whereArray($datos);
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Ya existe un miembro con este nombre'
                    ];
                } else {
                    $miembro->grupo_id = $grupo->id;
                    $resultado = $miembro->guardar();
                    if ($resultado['resultado']) {
                        $respuesta = [
                            'tipo' => 'exito',
                            'mensaje' => 'Miembro agregado correctamente',
                            'id' => $resultado['id']
                        ];
                    } else {
                        $respuesta = [
                            'tipo' => 'error',
                            'mensaje' => 'Hubo un error al crear el miembro',
                            'miembro' => $miembro
                        ];
                    }
                }
            }
            echo json_encode($respuesta);   
        }
    }

    public static function actualizar() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Acceder al miembro a actualizar
            $miembro = Miembro::find($_POST['id']);
            if(!$miembro) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el miembro'
                ];
            } else {
                $miembro->peso = $_POST['peso']; 
                if ($_POST['soy_yo'] == "true") {
                    $miembro->usuario_id = $_POST['usuario_id'];
                }

                // Actualizar en la BD
                $resultado = $miembro->guardar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualización realizada',
                        'id' => $miembro->id
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

    public static function actualizarPeso() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Acceder al miembro a actualizar
            $miembro = Miembro::find($_POST['id']);
            if(!$miembro) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el miembro'
                ];
            } else {
                if (is_numeric($_POST['peso'])) {
                    $miembro->peso = $_POST['peso'];
                    // Actualizar en la BD
                    $resultado = $miembro->guardar();
                    if ($resultado) {
                        $respuesta = [
                            'tipo' => 'exito',
                            'mensaje' => 'Actualización realizada',
                            'id' => $miembro->id
                        ];
                    } else {
                        $respuesta = [
                            'tipo' => 'error',
                            'mensaje' => 'Hubo un error al actualizar'
                        ];
                    }
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error porque el peso no es numérico'
                    ];
                } 
            }
            echo json_encode($respuesta); 
        }
    }

    public static function actualizarSoyYo() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Acceder al miembro a actualizar
            $miembro = Miembro::find($_POST['id']);
            if(!$miembro) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el miembro'
                ];
            } else {
                if ($_POST['soy_yo'] == "true") {
                    $miembro->usuario_id = $_POST['usuario_id'];
                }

                // Actualizar en la BD
                $resultado = $miembro->guardar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualización realizada',
                        'id' => $miembro->id
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
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que el grupo exista
            $grupo = Grupo::where('url', $_POST['url']);
            
            if(!$grupo) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el grupo'
                ];
            } else {
                $miembro = new Miembro($_POST);
                // Validar que el miembro a elminar no tiene saldo
                $saldo = Saldo::where('miembro_id', $miembro->id);
                if ($saldo && $saldo->saldo != 0) {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'No se puede eliminar el miembro porque tiene saldo',
                        'saldo' => $saldo->saldo
                    ];
                } else {
                    // Eliminar el miembro en la BD  
                    $resultado = $miembro->eliminar();
                    $resulsaldo = $saldo->eliminar();
                    if ($resultado && $resulsaldo) {
                        $respuesta = [
                            'tipo' => 'exito',
                            'mensaje' => 'Miembro eliminado correctamente',
                            'id' => $miembro->id
                        ];
                    } else {
                        $respuesta = [
                            'tipo' => 'error',
                            'mensaje' => 'Hubo un error al eliminar el miembro',
                            'miembro' => $miembro,
                            'resultado' => $resultado,
                            'saldo' => $saldo,
                            'resulsaldo' => $resulsaldo
                        ];
                    }
                }   
            }
            echo json_encode($respuesta);
        }
    }

    public static function actualizarActivo() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Acceder al miembro a actualizar
            $miembro = Miembro::find($_POST['id']);
            if(!$miembro) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el miembro'
                ];
            } else {
                $miembro->activo = $_POST['activo'];
                // Actualizar en la BD
                $resultado = $miembro->guardar();
                if ($resultado) {
                    if ($miembro->activo) {
                        $respuesta = [
                            'tipo' => 'exito',
                            'mensaje' => 'Miembro activado correctamente',
                            'id' => $miembro->id
                        ];
                    } else {
                        $respuesta = [
                            'tipo' => 'exito',
                            'mensaje' => 'A partir de ahora el miembro no participará en el grupo',
                            'id' => $miembro->id
                        ];
                    }
                    
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al actualizar',
                        'miembro' => $miembro
                    ];
                }            
            }
            echo json_encode($respuesta); 
        }
    }

    
    
}