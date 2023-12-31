<?php

namespace Controllers;

use Model\Concepto;
use Model\Movimiento;
use Model\Grupo;
use Model\Miembro;
use Model\Tipo;



class APImovimientos {

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
                $movimientos = Movimiento::belongsToOrdenado('grupo_id', $grupo->id, 'fecha', 'DESC');
                foreach($movimientos as $movimiento) {
                    $movimiento->miembro = Miembro::find($movimiento->miembro_id);
                    $movimiento->tipo_nombre = Tipo::find($movimiento->tipo)->nombre;
                    $movimiento->paraquien = explode(",", $movimiento->quien);
                    $movimiento->fecha_date = date("Y-m-d", strtotime($movimiento->fecha));
                    $movimiento->fecha_hora = date("H:i", strtotime($movimiento->fecha));
                }
                echo json_encode(['movimientos' => $movimientos]);
            }
        }
        
    }

    public static function crear() {
        isAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movimiento = new Movimiento($_POST);
            $resultado = $movimiento->guardar();
            if ($resultado['resultado']) {
                // Hay que formatear la respuesta para que pueda mostrarla en el listado de mvtos
                $mvtoarray['id'] = $resultado['id'];
                $mvtoarray['grupo_id'] = $movimiento->grupo_id;
                $mvtoarray['miembro_id'] = $movimiento->miembro_id;
                $mvtoarray['cantidad'] = $movimiento->cantidad;
                $mvtoarray['tipo'] = $movimiento->tipo;
                $mvtoarray['quien'] = $movimiento->quien;
                $mvtoarray['concepto_id'] = $movimiento->concepto_id;
                $mvtoarray['descripcion'] = $movimiento->descripcion;
                $mvtoarray['fecha'] = $movimiento->fecha;
                $mvtoarray['creado'] = $movimiento->creado;
                $mvtoarray['creador_id'] = $movimiento->creador_id;
                $mvtoarray['actualizado'] = $movimiento->actualizado;
                $mvtoarray['actualizador_id'] = $movimiento->actualizador_id;
                $mvtoarray['miembro'] = Miembro::find($movimiento->miembro_id);
                $mvtoarray['tipo_nombre'] = Tipo::find($movimiento->tipo)->nombre;
                $mvtoarray['paraquien'] = explode(",", $movimiento->quien);
                $mvtoarray['fecha_date'] = date("Y-m-d", strtotime($movimiento->fecha));
                $mvtoarray['fecha_hora'] = date("H:i", strtotime($movimiento->fecha));
                
                $respuesta = [
                    'tipo' => 'exito',
                    'mensaje' => 'Movimiento agregado correctamente',
                    'id' => $resultado['id'],
                    'movimiento' => $mvtoarray
                ];
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al crear el movimiento',
                    'resultado' => $resultado,
                    'movimiento' => $movimiento
                ];
            }
            echo json_encode($respuesta);   
        }
    }

    public static function actualizar() {
        isAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Acceder al movimiento a actualizar
            $movimiento = Movimiento::find($_POST['id']);
            if(!$movimiento) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque no se encontró el movimiento'
                ];
            } else {
                $movimiento->sincronizar($_POST); 
                if ($movimiento->concepto_id == "") {
                    $movimiento->concepto_id = null;
                }
                $movimiento->actualizado = date("Y-m-d H:i:s");

                // Actualizar en la BD
                $resultado = $movimiento->guardar();
                if ($resultado) {
                    // Hay que formatear la respuesta para que pueda mostrarla en el listado de mvtos
                    $mvtoarray['id'] = $movimiento->id;
                    $mvtoarray['grupo_id'] = $movimiento->grupo_id;
                    $mvtoarray['miembro_id'] = $movimiento->miembro_id;
                    $mvtoarray['cantidad'] = $movimiento->cantidad;
                    $mvtoarray['tipo'] = $movimiento->tipo;
                    $mvtoarray['quien'] = $movimiento->quien;
                    $mvtoarray['concepto_id'] = $movimiento->concepto_id;
                    $mvtoarray['descripcion'] = $movimiento->descripcion;
                    $mvtoarray['fecha'] = $movimiento->fecha;
                    $mvtoarray['creado'] = $movimiento->creado;
                    $mvtoarray['creador_id'] = $movimiento->creador_id;
                    $mvtoarray['actualizado'] = $movimiento->actualizado;
                    $mvtoarray['actualizador_id'] = $movimiento->actualizador_id;
                    $mvtoarray['miembro'] = Miembro::find($movimiento->miembro_id);
                    $mvtoarray['tipo_nombre'] = Tipo::find($movimiento->tipo)->nombre;
                    $mvtoarray['paraquien'] = explode(",", $movimiento->quien);
                    $mvtoarray['fecha_date'] = date("Y-m-d", strtotime($movimiento->fecha));
                    $mvtoarray['fecha_hora'] = date("H:i", strtotime($movimiento->fecha));
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Actualización realizada',
                        'id' => $movimiento->id,
                        'movimiento' => $mvtoarray
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al actualizar',
                        'movimiento' => $movimiento
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
            $movimiento = Movimiento::find($_POST['id']);
            if ($movimiento) {
                $resultado = $movimiento->eliminar();
                if ($resultado) {
                    $respuesta = [
                        'tipo' => 'exito',
                        'mensaje' => 'Movimiento eliminado correctamente',
                        'id' => $movimiento->id
                    ];
                } else {
                    $respuesta = [
                        'tipo' => 'error',
                        'mensaje' => 'Hubo un error al eliminar el movimiento',
                        'movimiento' => $movimiento
                    ];
                }
            } else {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error porque el movimiento no existe',
                    'id' => $_POST['id']
                ];
            }    
            echo json_encode($respuesta);
        }
    }

    public static function gastos() {
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
                    'grupo_id' => $grupo->id
                //    'tipo' => 1
                ];
                // Obtenemos todos los movimientos
                $movimientos = Movimiento::whereArray($datos);
                // Filtramos los de tipo 1 y 2 (gasto e ingreso)
                $movimientos = array_filter($movimientos, function($movimiento) {
                    if($movimiento->tipo == 1 || $movimiento->tipo == 2) {
                        return $movimiento;
                    };
                });
                // Los ingresos los ponemos con importe negativo pues son devoluciones de gastos
                $movimientos = array_map(function($movimiento) {
                    if ($movimiento->tipo == 2) {
                        $movimiento->cantidad = $movimiento->cantidad * (-1);
                    }
                    return $movimiento;
                }, $movimientos);

                foreach($movimientos as $movimiento) {
                    $arrayparaQuien = [];
                    $arrayResultado = [];
                    $total_pesos = 0;
                    $paraquien = explode(",", $movimiento->quien);
                    foreach($paraquien as $miembro_gasto) {
                        $miembro = Miembro::find($miembro_gasto);
                        settype($miembro->peso, "float");
                        $total_pesos += $miembro->peso;
                        settype($movimiento->cantidad, "float");
                        $importe = $movimiento->cantidad * $miembro->peso;
                        $datos_miembro = [
                            'miembro_id' => $miembro_gasto,
                            'nombre' => $miembro->nombre,
                            'peso' => $miembro->peso,
                            'importe' => $importe
                        ];
                        $arrayparaQuien[] = $datos_miembro;
                    };
                    $movimiento->total_pesos = $total_pesos;
                    foreach($arrayparaQuien as $miembro_gasto) {
                        $datos = [
                            'miembro_id' => $miembro_gasto['miembro_id'],
                            'nombre' => $miembro_gasto['nombre'],
                            'peso' => $miembro_gasto['peso'],
                            'importe' => $miembro_gasto['importe'] / $total_pesos
                        ];
                        $arrayResultado[] = $datos;
                    };
                    $movimiento->paraQuien = $arrayResultado;
                    if ($movimiento->concepto_id) {
                        $concepto = Concepto::find($movimiento->concepto_id);
                    } else {
                        $concepto = new Concepto();
                    }
                    $movimiento->concepto = $concepto->nombre;
                    $movimiento->fecha_date = date("Y-m-d", strtotime($movimiento->fecha));
                    $movimiento->fecha_mes = date("Y-m", strtotime($movimiento->fecha));
                    $movimiento->fecha_hora = date("H:i", strtotime($movimiento->fecha));
                }

                // Construimos el array de totales
                $array_totales = [];
                foreach($movimientos as $movimiento) {
                    
                    if (isset($array_totales[$movimiento->concepto])) {
                        foreach($movimiento->paraQuien as $miembro_gasto) {
                            // buscamos el miembro en el array de totales, si lo encontramos le sumamos el importe
                            $encontrado = false;
                            foreach($array_totales[$movimiento->concepto] as &$miembro_totales) {
                                if ($miembro_totales['miembro_id'] == $miembro_gasto['miembro_id']) {
                                    $miembro_totales['importe'] += $miembro_gasto['importe'];
                                    $encontrado = true;
                                    break; // Si encontramos el miembro, podemos salir del bucle
                                }
                            }
                            // y si no lo encontramos lo creamos
                            if (!$encontrado) {
                                $array_totales[$movimiento->concepto][] = [
                                    'miembro_id' => $miembro_gasto['miembro_id'],
                                    'nombre' => $miembro_gasto['nombre'],
                                    'importe' => $miembro_gasto['importe'] 
                                ];
                            }
                        }
                    } else {
                        // Si no existe el concepto creamos uno nuevo con todos los miembros del paraQuien
                        $array_totales[$movimiento->concepto] = [];
                        
                        foreach($movimiento->paraQuien as $miembro_gasto) {
                            $array_totales[$movimiento->concepto][] = [
                                'miembro_id' => $miembro_gasto['miembro_id'],
                                'nombre' => $miembro_gasto['nombre'],
                                'importe' => $miembro_gasto['importe'] 
                            ];
                        }
                    }
                }

                // Construimos el array de miembros
                $array_miembros = [];
                foreach($movimientos as $movimiento) {
                    foreach($movimiento->paraQuien as $miembro_gasto) {
                        // buscamos el miembro en el array de miembros
                        $encontrado = false;
                        foreach($array_miembros as &$miembro_totales) {
                            if ($miembro_totales['miembro_id'] == $miembro_gasto['miembro_id']) {
                                $encontrado = true;
                                break; 
                            }
                        }
                        // y si no lo encontramos lo creamos
                        if (!$encontrado) {
                            $array_miembros[] = [
                                'miembro_id' => $miembro_gasto['miembro_id'],
                                'nombre' => $miembro_gasto['nombre'],
                                'importes' => [],
                                'importesMeses' => []
                            ];
                        }
                    }     
                }
                
                $miembros = [];
                $nombresM = [];
                $importes = [];
                // Recorremos el array de miembros buscando sus importes en el array de totales
                foreach($array_miembros as &$miembro_respuesta) {
                    $miembros[] = $miembro_respuesta['miembro_id'];
                    $nombresM[] = $miembro_respuesta['nombre'];
                    $importes[] = 0;
                    foreach($array_totales as $concepto) {
                        $encontrado = false;
                        foreach($concepto as $miembro_gasto) {
                            if ($miembro_gasto['miembro_id'] == $miembro_respuesta['miembro_id']) {
                                $miembro_respuesta['importes'][] = $miembro_gasto['importe'];
                                $encontrado = true;
                                break;
                            }
                        }
                        if (!$encontrado) {
                            $miembro_respuesta['importes'][] = 0;
                        }  
                    }   
                }

                // Construimos el array de conceptos
                $array_conceptos = [];
                foreach($movimientos as $movimiento) {
                    // buscamos el concepto del movimiento en el array de conceptos
                    $encontrado = false;
                    foreach($array_conceptos as &$concepto_respuesta) {
                        if ($concepto_respuesta['concepto'] == $movimiento->concepto) {
                            $encontrado = true;
                            break; 
                        }
                    }
                    // y si no lo encontramos lo creamos
                    if (!$encontrado) {
                        $array_conceptos[] = [
                            'concepto' => $movimiento->concepto,
                            'miembros' => $miembros,
                            'importes' => $importes,
                            'importesMeses' => []
                        ];
                    }
                }

                // Rellenamos los importes a partir de los movimientos

                foreach($movimientos as $movimiento) {    

                    // buscamos el concepto del movimiento en el array de conceptos
                    foreach($array_conceptos as &$concepto_respuesta) {

                        if ($concepto_respuesta['concepto'] == $movimiento->concepto) {
                            // Sumamos el importe de cada miembro en su lugar correspondiente
                            foreach($movimiento->paraQuien as $miembro_gasto) {
                                // buscamos en qué lugar se encuentra el miembro en el array de miembros
                                for ($i = 0; $i < count($miembros); $i++) {
                                    if ($miembros[$i] == $miembro_gasto['miembro_id']) {
                                        $concepto_respuesta['importes'][$i] += $miembro_gasto['importe'];
                                    }
                                }
                            }
                            break; 
                        }
                    }
                }

            
                $conceptos = [];
                $importesC = [];
                // Recorremos el array de conceptos buscando sus importes en el array de totales
                foreach($array_conceptos as &$concepto_respuesta) {
                    $conceptos[] = $concepto_respuesta['concepto'];
                    $importesC[] = 0;  
                }

                // Construimos el array de serie mensual
                $array_fechas = [];
                $meses = [];
                foreach($movimientos as $movimiento) {
                    // buscamos la fecha en el array de fechas
                    $encontrado = false;
                    foreach($array_fechas as &$fecha_respuesta) {
                        if ($fecha_respuesta['mes'] == $movimiento->fecha_mes) {
                            $encontrado = true;
                            break; 
                        }
                    }
                    // y si no lo encontramos lo creamos
                    if (!$encontrado) {
                        $array_fechas[] = [
                            'mes' => $movimiento->fecha_mes,
                            'miembros' => $miembros,
                            'nombres' => $nombresM,
                            'importesM' => $importes,
                            'conceptos' => $conceptos,
                            'importesC' => $importesC
                        ];
                        $meses[] = $movimiento->fecha_mes;
                    }
                }
                sort($array_fechas);

                // Rellenamos los importes a partir de los movimientos
                foreach($movimientos as $movimiento) {    

                    // buscamos la fecha del movimiento en el array de fechas
                    foreach($array_fechas as &$fecha_respuesta) {

                        if ($fecha_respuesta['mes'] == $movimiento->fecha_mes) {
                            // Sumamos el importe de cada miembro en su lugar correspondiente
                            foreach($movimiento->paraQuien as $miembro_gasto) {
                                // buscamos en qué lugar se encuentra el miembro en el array de miembros
                                for ($i = 0; $i < count($miembros); $i++) {
                                    if ($miembros[$i] == $miembro_gasto['miembro_id']) {
                                        $fecha_respuesta['importesM'][$i] += $miembro_gasto['importe'];
                                    }
                                }
                            }
                            // Sumamos el importe de cada concepto en su lugar correspondiente
                            // buscamos en qué lugar se encuentra el concepto en el array de conceptos
                            for ($i = 0; $i < count($conceptos); $i++) {
                                 if ($conceptos[$i] == $movimiento->concepto) {
                                     $fecha_respuesta['importesC'][$i] += $movimiento->cantidad;
                                 }
                            }
                            break; 
                        }
                    }
                }

                // Rellenamos el desglose de meses en el array de miembros
                
                foreach($array_miembros as &$miembro_respuesta) {
                    // buscamos en qué lugar se encuentra el miembro en el array de miembros
                    for ($i = 0; $i < count($miembros); $i++) {
                        if ($miembros[$i] == $miembro_respuesta['miembro_id']) {
                            // aquí tengo que rellenar $miembro_respuesta['importesMeses]
                            for ($j = 0; $j < count($array_fechas); $j++) {
                                //$miembro_respuesta['importesMeses'][$i] += $fecha['importesM'][$j];
                                $miembro_respuesta['importesMeses'][] = $array_fechas[$j]['importesM'][$i];
                            }
                        }
                    }   
                }

                // Rellenamos el desglose de meses en el array de conceptos
                
                foreach($array_conceptos as &$concepto_respuesta) {
                    // buscamos en qué lugar se encuentra el concepto en el array de conceptos
                    for ($i = 0; $i < count($conceptos); $i++) {
                        if ($conceptos[$i] == $concepto_respuesta['concepto']) {
                            for ($j = 0; $j < count($array_fechas); $j++) {
                                $concepto_respuesta['importesMeses'][] = $array_fechas[$j]['importesC'][$i];
                            }
                        }
                    }   
                }
                
                // Por último, generamos el array de detalle

                $array_detalles = [];
                foreach($movimientos as $movimiento) {
                    // buscamos la línea que tenga el mismo concepto, mismo miembro y mismo mes del movimiento
                    
                    foreach($movimiento->paraQuien as $miembro_gasto) {
                        $encontrado = false;
                        foreach($array_detalles as &$detalle) {
                            if (
                                $movimiento->fecha_mes == $detalle['mes'] &&
                                $movimiento->concepto == $detalle['concepto'] &&
                                $miembro_gasto['miembro_id'] == $detalle['miembro']
                            ) {
                                $encontrado = true;
                                $detalle['importe'] += $miembro_gasto['importe'];
                            }
                        }
                        if (!$encontrado) {
                            $array_detalles[] = [
                                'mes' => $movimiento->fecha_mes,
                                'concepto' => $movimiento->concepto,
                                'miembro' => $miembro_gasto['miembro_id'],
                                'nombre' => $miembro_gasto['nombre'],
                                'importe' => $miembro_gasto['importe']
                            ];
                        }
                    }
                }
                sort($array_detalles);
                
                echo json_encode([
                    'totales' => $array_totales,
                    'miembros' => $array_miembros,
                    'conceptos' => $array_conceptos,
                    'fechas' => $array_fechas,
                    'detalles' => $array_detalles
                ]);
            }
        }
        
    }

    public static function saldos() {
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
                    'grupo_id' => $grupo->id
                ];
                // Obtenemos todos los movimientos
                $movimientos = Movimiento::whereArray($datos);
            
                // Los ingresos los ponemos con importe negativo pues son devoluciones de gastos
                $movimientos = array_map(function($movimiento) {
                    if ($movimiento->tipo == 2) {
                        $movimiento->cantidad = $movimiento->cantidad * (-1);
                    }
                    return $movimiento;
                }, $movimientos);

                // Obtenemos los miembros del grupo
                $miembros = Miembro::whereArray($datos);

                foreach($movimientos as $movimiento) {
                    $arrayparaQuien = [];
                    $arrayResultado = [];
                    $total_pesos = 0;
                    $paraquien = explode(",", $movimiento->quien);
                    foreach($paraquien as $miembro_gasto) {
                        $miembro = Miembro::find($miembro_gasto);
                        settype($miembro->peso, "float");
                        $total_pesos += $miembro->peso;
                        settype($movimiento->cantidad, "float");
                        $importe = $movimiento->cantidad * $miembro->peso;
                        $datos_miembro = [
                            'miembro_id' => $miembro_gasto,
                            'nombre' => $miembro->nombre,
                            'peso' => $miembro->peso,
                            'importe' => $importe
                        ];
                        $arrayparaQuien[] = $datos_miembro;
                    };
                    $movimiento->total_pesos = $total_pesos;
                    foreach($arrayparaQuien as $miembro_gasto) {
                        $datos = [
                            'miembro_id' => $miembro_gasto['miembro_id'],
                            'nombre' => $miembro_gasto['nombre'],
                            'peso' => $miembro_gasto['peso'],
                            'importe' => $miembro_gasto['importe'] / $total_pesos
                        ];
                        $arrayResultado[] = $datos;
                    };
                    $movimiento->paraQuien = $arrayResultado;
                }

                // Construimos el array de saldos
                $array_saldos = [];

                foreach($miembros as $miembro) {
                    $datos = [
                        'miembro_id' => $miembro->id,
                        'ingresos' => 0,
                        'gastos' => 0,
                        'saldo' => 0
                    ];
                    $array_saldos[] = $datos;
                }

                foreach($movimientos as $movimiento) {
                    $array_saldos = actualizarSaldos($array_saldos, $movimiento);
                }

                foreach($array_saldos as &$saldo) {
                    $saldo['ingresos'] = round( $saldo['ingresos'],6);
                    $saldo['gastos'] = round( $saldo['gastos'],6);
                    $saldo['saldo'] = round( $saldo['saldo'],6);
                }
    
                echo json_encode([
                    'saldos' => $array_saldos                    
                ]);
            }
        }
        
    }


    public static function deudas() {
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
                    'grupo_id' => $grupo->id
                ];
                // Obtenemos todos los movimientos
                $movimientos = Movimiento::whereArray($datos);
            
                // Los ingresos los ponemos con importe negativo pues son devoluciones de gastos
                $movimientos = array_map(function($movimiento) {
                    if ($movimiento->tipo == 2) {
                        $movimiento->cantidad = $movimiento->cantidad * (-1);
                    }
                    return $movimiento;
                }, $movimientos);

                // Obtenemos los miembros del grupo
                $miembros = Miembro::whereArray($datos);

                foreach($movimientos as $movimiento) {
                    $arrayparaQuien = [];
                    $arrayResultado = [];
                    $total_pesos = 0;
                    $paraquien = explode(",", $movimiento->quien);
                    foreach($paraquien as $miembro_gasto) {
                        $miembro = Miembro::find($miembro_gasto);
                        settype($miembro->peso, "float");
                        $total_pesos += $miembro->peso;
                        settype($movimiento->cantidad, "float");
                        $importe = $movimiento->cantidad * $miembro->peso;
                        $datos_miembro = [
                            'miembro_id' => $miembro_gasto,
                            'nombre' => $miembro->nombre,
                            'peso' => $miembro->peso,
                            'importe' => $importe
                        ];
                        $arrayparaQuien[] = $datos_miembro;
                    };
                    $movimiento->total_pesos = $total_pesos;
                    foreach($arrayparaQuien as $miembro_gasto) {
                        $datos = [
                            'miembro_id' => $miembro_gasto['miembro_id'],
                            'nombre' => $miembro_gasto['nombre'],
                            'peso' => $miembro_gasto['peso'],
                            'importe' => $miembro_gasto['importe'] / $total_pesos
                        ];
                        $arrayResultado[] = $datos;
                    };
                    $movimiento->paraQuien = $arrayResultado;
                }

                // Construimos el array de saldos
                $array_saldos = [];

                foreach($miembros as $miembro) {
                    $datos = [
                        'miembro_id' => $miembro->id,
                        'ingresos' => 0,
                        'gastos' => 0,
                        'saldo' => 0
                    ];
                    $array_saldos[] = $datos;
                }

                foreach($movimientos as $movimiento) {
                    $array_saldos = actualizarSaldos($array_saldos, $movimiento);
                }

                foreach($array_saldos as &$saldo) {
                    $saldo['ingresos'] = round( $saldo['ingresos'],6);
                    $saldo['gastos'] = round( $saldo['gastos'],6);
                    $saldo['saldo'] = round( $saldo['saldo'],6);
                }

                // Construimos el array de deudas, generando movimientos ficticios hasta que todos
                // los saldos sean cero
                $array_deudas = [];

                $mvtosficticios = [];

                // Identificamos si hay algún miembro con saldo distinto de cero
                $comprobacion = comprobarSaldo($array_saldos); 
                    
                $i = 0;

                while ($comprobacion['haySaldos'] && $i<100) {
                    // generar movimiento saldar deuda
                    if (abs($comprobacion['mayorSaldo']) > abs($comprobacion['menorSaldo'])) {
                        $cantidad_a_saldar = abs($comprobacion['menorSaldo']);
                    } else {
                        $cantidad_a_saldar = abs($comprobacion['mayorSaldo']);
                    }

                    if (round($cantidad_a_saldar,6) > 0.000001 && $comprobacion['menorSaldo_id'] !=0 && $comprobacion['mayorSaldo_id'] !=0) {
                        
                        // genero movimiento desde menorSaldo_id a mayorSaldo_id
                        $mvtofic = new Movimiento ();
                        $mvtofic->miembro_id = $comprobacion['menorSaldo_id'];
                        $mvtofic->cantidad = $cantidad_a_saldar;
                        $mvtofic->tipo = 3;
                        $paraquien = [
                            'miembro_id' => $comprobacion['mayorSaldo_id'],
                            'importe' => $cantidad_a_saldar
                        ];
                        $mvtofic->paraQuien[] = $paraquien;
                        $mvtosficticios[] = $mvtofic;
                        
                        $array_saldos = actualizarSaldos($array_saldos, $mvtofic);
                        $comprobacion = comprobarSaldo($array_saldos);
                    }
                        
                    $i++;   
                }

                if ($comprobacion['haySaldos']) {
                    echo json_encode(([
                        'error' => "Error de programación, saldar deudas ha entrado en un bucle",
                        'datos' => $comprobacion,
                        'mvtosficticios' => $mvtosficticios,
                        'i' => $i,
                        'array_saldos' => $array_saldos
                    ]));
                } else {
                    foreach($mvtosficticios as $mvto) {
                        $deuda = [
                            'grupo_id' => $grupo->id,
                            'from_miembro_id' => $mvto->miembro_id,
                            'to_miembro_id' => $mvto->paraQuien[0]['miembro_id'],
                            'importe' => $mvto->cantidad
                        ];
                        $array_deudas[] = $deuda;
                    };
                    echo json_encode([
                        'deudas' => $array_deudas                  
                    ]);
                }
            }
        }
        
    }
    
}