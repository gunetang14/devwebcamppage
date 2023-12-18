<?php 

namespace Controllers;

use Model\Categoria;
use Model\Dia;
use Model\Evento;
use Model\EventosRegistros;
use Model\Hora;
use Model\Paquete;
use Model\Ponente;
use Model\Regalo;
use Model\Registro;
use Model\Usuario;
use MVC\Router;

#[\AllowDynamicProperties]
class RegistroController {

    public static function crear(router $router){
        if(!isAuth()){
            header('Location: /login');
            return;
        }
        //verificar si el usuario ya esta registrado
        $registro = Registro::where('usuario_id', $_SESSION['id']);

        if(isset($registro) && $registro->paquete_id === "3"){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }
        if(isset($registro) && $registro->paquete_id === "2" && isset($registro->pago_id)){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }        
        if(isset($registro) && $registro->paquete_id === "1" && isset($registro->pago_id)){
            header('Location: /finalizar-registro/conferencias');
            return;
        }

        $router->render('registro/crear',[
            'titulo' => 'Finalizar Registro'
        ]);
    }

    public static function gratis(router $router){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!isAuth()){
                header('Location: /login');
                return;
            }

            //verificar si el usuario ya esta registrado
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if(isset($registro) && $registro->paquete_id === "3"){
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }

            $token = substr( md5(uniqid( rand(), true)), 0, 8);
            
            $datos = [
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            ];

            $registro = new Registro($datos);
            $resultado = $registro->guardar();
            if($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }
        }
    }

    public static function boleto(router $router){

        //validar URL
        $id = $_GET['id'];
        if(!$id || !strlen($id) === 8){
            header('Location: /');
            return;
        }
        //buscarlo en la BD
        $registro = Registro::where('token', $id);
        if(!$registro){
            header('Location: /');
            return;
        }
        //llenar las tablas de referencia
        $registro->usuario = Usuario::find($registro->usuario_id);
        $registro->paquete = Paquete::find($registro->paquete_id);

        $router->render('registro/boleto',[
            'titulo' => 'Asistencia a DevWebCamp',
            'registro' => $registro
        ]);
    }

    public static function pagar(router $router){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!isAuth()){
                header('Location: /login');
                return;
            }
            // validar que post no venga vacio
            if(empty($_POST)){
                echo json_encode([]);
                return;
            }
            // crear el registro
            $datos = $_POST;
            $datos['token'] = substr( md5(uniqid( rand(), true)), 0, 8);
            $datos['usuario_id'] = $_SESSION['id'];         
            
            try {
                $registro = new Registro($datos);
                $resultado = $registro->guardar();
                echo json_encode($resultado);
            } catch (\Throwable $th) {
                echo json_encode([
                    'resultado' => 'error'
                ]);
            }
        }
    }

    public static function conferencias(router $router){

        if(!isAuth()){
            header('Location: /login');
            return;
        }
        //validar que el usuario tenga el pase presencial
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);
        if(!isset($registro)){
            header('Location: /finalizar-registro');
            return;
        }
        // Redireccionar a boleto virtual en caso de haber finalizado su registro
        if( isset($registro) && $registro->paquete_id === "2" && isset($registro->pago_id)){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }
        // Redireccionar a boleto virtual en caso de NO haber finalizado su registro
        if( isset($registro) && $registro->paquete_id === "2" && !isset($registro->pago_id)){
            header('Location: /finalizar-registro');
            return;
        }
        // Redireccionar a boleto gratis en caso de ingresar a esta pagina
        if(isset($registro) && $registro->paquete_id === "3"){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }
        // Redireccionar a boleto virtual en caso de NO haber finalizado su registro
        if(isset($registro) && $registro->paquete_id === "1" && !isset($registro->pago_id)){
            header('Location: /finalizar-registro');
            return;
        }
        
        $tieneAgenda = EventosRegistros::where('registro_id', $registro->id);

        if(isset($tieneAgenda)){
            header('Location: /boleto?id=' . urlencode($registro->token));
        }      
        

        $eventos = Evento::ordenar('hora_id', 'ASC');
        $eventos_formateados = [];
        foreach($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);
            if($evento->dia_id === "1" && $evento->categoria_id === "1"){
                $eventos_formateados['conferencias_v'][] = $evento;
            }
            if($evento->dia_id === "2" && $evento->categoria_id === "1"){
                $eventos_formateados['conferencias_s'][] = $evento;
            }
            if($evento->dia_id === "1" && $evento->categoria_id === "2"){
                $eventos_formateados['workshops_v'][] = $evento;
            }
            if($evento->dia_id === "2" && $evento->categoria_id === "2"){
                $eventos_formateados['workshops_s'][] = $evento;
            }
        }
        $regalos = Regalo::all('ASC');

        //Manejando el registro mediante $_POST
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //revisar que el usuario este autenticado
            if(!isAuth()){
                header('Location: /login');
                return;
            }
            $eventos = explode(',', $_POST['eventos']);
            if(empty($eventos)){
                echo json_encode(['resultado' => false]);
                return;
            }
            //obtener el registro del usuario
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if(!isset($registro) || $registro->paquete_id !== "1"){
                echo json_encode(['resultado' => false]);
                return;
            }
            $eventos_array = []; // para uso y evitar la consulta de la base de datos a cada comprobracion
            //validar la disponibilidad de los eventos seleccionados
            foreach($eventos as $evento_id){
                $evento = Evento::find($evento_id);
                //comprobar que el evento exista
                if(!isset($evento) || $evento->disponibles === "0"){
                    echo json_encode(['resultado' => false]);
                    return;
                }
                $eventos_array[] = $evento;
            }
            foreach($eventos_array as $evento){
                $evento->disponibles -=1;
                $evento->guardar();
                // Almacenar el registro
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id
                ];
                
                $registro_usuario = new EventosRegistros($datos);
                $registro_usuario->guardar();
            }
            //añmacenar el regalo
            $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);
            $resultado = $registro->guardar();
            if($resultado){
                echo json_encode([
                    'resultado' => $resultado,
                    'token' => $registro->token
                ]);
            } else {
                echo json_encode(['resultado' => false]);                
            }
            return;
        }
        $router->render('registro/conferencias',[
            'titulo' => 'Elige Workshops y Conferencias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos
        ]);
    }
}

?>