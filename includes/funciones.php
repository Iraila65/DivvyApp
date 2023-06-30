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