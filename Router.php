<?php

namespace MVC;

class Router
{
    public $getRoutes = [];
    public $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {
        
        // Proteger Rutas...
        session_start();
        if (isset($_SESSION['admin'])) {
            $admin = $_SESSION['admin'];
        } else {
            $admin = null;
        }

        // Arreglo de rutas protegidas...
        $rutas_protegidas = [
            '/admin/dashboard', 
            '/admin/ponentes', 
            '/admin/ponentes/crear', 
            '/admin/ponentes/editar', 
            '/admin/ponentes/eliminar', 
            '/admin/eventos',
            '/admin/eventos/crear',
            '/admin/eventos/editar',
            '/admin/eventos/eliminar',
            '/admin/registrados',
            '/admin/regalos'
        ];

        if ( isset ($_SERVER['PATH_INFO'] )){
            $currentUrl = $_SERVER['PATH_INFO'];
        } else {
            $currentUrl = "/";
        }
        // if ( isset ($_SERVER['REQUEST_URI'] )){
        //     $currentUrl = $_SERVER['REQUEST_URI'];
        // } else {
        //     $currentUrl = "/";
        // }
        if (isset($_SERVER["REQUEST_METHOD"])) {
            $metodo = $_SERVER["REQUEST_METHOD"];
        } else {
            $metodo = "GET";
        }

        if ($metodo == 'GET') {
            if (isset($this->getRoutes[$currentUrl])) {
                $fn = $this->getRoutes[$currentUrl];
            } else {
                $fn = null;
            }   
        } else {  // el método es POST porque en PHP sólo existen GET y POST
            if (isset($this->postRoutes[$currentUrl])) {
                $fn = $this->postRoutes[$currentUrl];
            } else {
                $fn = null;
            }
        }

        // Proteger las rutas protegidas
        if (in_array($currentUrl, $rutas_protegidas) && !$admin) {  
            Header ('Location: /login');
        }

        if ($fn) {  // la url existe y tiene una función asociada
            call_user_func($fn, $this);
        } else {
            //echo "Página no encontrada"; // Error 404
            $inicio = false;
            $this->render("/notfn", [
                'titulo' => 'Error 404'
            ]);
        } 
       
    }

    public function render($view, $datos = [])
    {
        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer

        // utilizar un layout distinto para las url de admin
        if ( isset ($_SERVER['PATH_INFO'] )){
            $currentUrl = $_SERVER['PATH_INFO'];
        } else {
            $currentUrl = "/";
        }
        if (strpos($currentUrl, 'admin')) {
        //if (str_contains($currentUrl, 'admin')) {  // la función str_contains está desde PHP 8 en adelante
            include_once __DIR__ . '/views/admin-layout.php';
        } else {
            include_once __DIR__ . '/views/layout.php';
        }

        
    }
}


