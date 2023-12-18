<?php

namespace Model;

#[\AllowDynamicProperties]
class Paquete extends ActiveRecord {
    protected static $tabla = 'paquetes';
    protected static $columnasDB = ['id', 'nombre'];

   
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
    }

}

?>