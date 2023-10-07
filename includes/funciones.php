<?php

function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) {
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth() {
    return isset($_SESSION['nombre']) && !empty($_SESSION);
}

function isAdmin() {
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}

function pagina_actual($path) {
    if ( isset ($_SERVER['PATH_INFO'] )){
        $currentUrl = $_SERVER['PATH_INFO'];
    } else {
        $currentUrl = "/";
    }
    //return strpos($_SERVER['PATH_INFO'], $path) ? true : false;
    return strchr($currentUrl, $path) ? true : false;
}

// Función para aplicar animaciones de forma aleatoria
function aos_animacion() {
    $efectos = [
        'fade-up', 'fade-down', 'fade-right', 'fade-left',
        'fade-up-right', 'fade-up-right', 'fade-down-right', 'fade-down-left',
        'flip-left', 'flip-right', 'flip-up', 'flip-down',
        'zoom-in', 'zoom-in-up', 'zoom-in-down', 'zoom-in-left', 'zoom-in-right',
        'zoom-out', 'zoom-out-up', 'zoom-out-down', 'zoom-out-right', 'zoom-out-left'
    ];
    $indice = array_rand($efectos, 1);
    echo $efectos[$indice];
}

// Función para comprobar si hay saldos
function comprobarSaldo($saldos) {
    $datos = [
        'haySaldos' => false,
        'mayorSaldo' => 0,
        'menorSaldo' => 0,
        'mayorSaldo_id' => 0,
        'menorSaldo_id' => 0
    ];
    foreach($saldos as $saldo) {  
        $saldo_redondeado = round($saldo['saldo'],6); 
        if (abs($saldo_redondeado) > 0.000001) {
            if ($saldo['saldo'] > $datos['mayorSaldo']) {
                $datos['mayorSaldo'] = $saldo['saldo'];
                $datos['mayorSaldo_id'] = $saldo['miembro_id'];
            }
            if ($saldo['saldo'] < $datos['menorSaldo'] ) {
                $datos['menorSaldo'] = $saldo['saldo'];
                $datos['menorSaldo_id'] = $saldo['miembro_id'];
            }
            if ($datos['menorSaldo_id'] != 0 && $datos['mayorSaldo_id'] != 0) {
                $datos['haySaldos'] = true;
            }
        }
    }
    return $datos;
}

// Función para actualizar los saldos con un movimiento
function actualizarSaldos($saldos, $movimiento) {
    // Sumamos la cantidad a "ingresos" y "saldo" del miembro que hace el movimiento
    foreach($saldos as &$saldo) {
        if ($saldo['miembro_id'] == $movimiento->miembro_id) {
            $saldo['ingresos'] += $movimiento->cantidad;
            $saldo['saldo'] += $movimiento->cantidad;
        }
    }

    // Si el tipo es gasto o ingreso:
    // -- se suma a "gastos" la cantidad correspondiente a cada destinatario
    // -- se resta la misma cantidad del saldo del destinatario
    if ($movimiento->tipo == 1 || $movimiento->tipo == 2) {
        foreach($movimiento->paraQuien as $destinatario) {
            foreach($saldos as &$saldo) {
                if ($saldo['miembro_id'] == $destinatario['miembro_id']) {
                    $saldo['gastos'] += $destinatario['importe'];
                    $saldo['saldo'] -= $destinatario['importe'];
                }
            }
        }                        
    }

    //Si el movimiento es de tipo "transferencia": 
    // -- se resta a "ingresos" y a "saldo" la cantidad correspondiente al destinatario
    if ($movimiento->tipo == 3) {
        foreach($movimiento->paraQuien as $destinatario) {
            foreach($saldos as &$saldo) {
                if ($saldo['miembro_id'] == $destinatario['miembro_id']) {
                    $saldo['ingresos'] -= $destinatario['importe'];
                    $saldo['saldo'] -= $destinatario['importe'];
                }
            }
        }    
    }
    return $saldos;
}
