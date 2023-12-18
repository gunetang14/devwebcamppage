<?php 

namespace Controllers;

use MVC\Router;
#[\AllowDynamicProperties]
class RegalosController {

    public static function index(router $router){
        if(!isAdmin()) {
            header('Location: /login');
        }
        $router->render('admin/regalos/index', [
            'titulo' => 'Regalos'
        ]);
    }
}

?>