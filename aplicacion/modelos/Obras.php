<?php

class Obras extends CActiveRecord
{
    protected function fijarNombre(): string
    {
        return 'Obra';
    }

    protected function fijarTabla(): string
    {
        return 'obras';
    }

    protected function fijarId(): string
    {
        return "cod_obra";
    }

    protected function fijarAtributos(): array
    {
        return [
            "cod_obra",
            "cod_usuario",
            "cod_categoria",
            "nombre",
            "descripcion",
            "img_principal",
            "valoracion",
            "fecha_alta",
            "borrado"
        ];
    }

    protected function fijarDescripciones(): array
    {
        return [
            "cod_obra" => "Código de la obra",
            "cod_usuario" => "Código del artista",
            "cod_categoria" => "Código de la categoría",
            "nombre" => "Título",
            "descripcion" => "Descripción",
            "img_principal" => "Imagen principal",
            "valoracion" => "Valoración",
            "fecha_alta" => "Fecha de alta",
            "borrado" => "Borrada"
        ];
    }

    protected function fijarRestricciones(): array
    {
        return [
            [
                "ATRI" => "cod_obra",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "cod_usuario",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "cod_usuario",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "cod_usuario",
                "TIPO" => "FUNCION",
                "FUNCION" => "comprobarUsuario"
            ],
            [
                "ATRI" => "cod_categoria",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "cod_categoria",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "cod_categoria",
                "TIPO" => "FUNCION",
                "FUNCION" => "comprobarCategoria"
            ],
            [
                "ATRI" => "nombre",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "nombre",
                "TIPO" => "CADENA",
                "TAMANIO" => 100
            ],
            [
                "ATRI" => "descripcion",
                "TIPO" => "CADENA",
                "TAMANIO" => 1000,
                "DEFECTO" => ""
            ],
            [
                "ATRI" => "img_principal",
                "TIPO" => "CADENA",
                "TAMANIO" => 60,
                "DEFECTO" => "ImgDefault.jpg"
            ],
            [
                "ATRI" => "valoracion",
                "TIPO" => "REAL",
                "DEFECTO" => 0,
                "MIN" => 0,
                "MAX" => 5
            ],
            [
                "ATRI" => "fecha_alta",
                "TIPO" => "FECHA"
            ],
            [
                "ATRI" => "fecha_alta",
                "TIPO" => "FUNCION",
                "FUNCION" => "validaFecha"
            ],
            [
                "ATRI" => "borrado",
                "TIPO" => "ENTERO",
                "MIN" => 0,
                "MAX" => 1,
                "DEFECTO" => 0
            ]
        ];
    }

    // —————————————————————————————————————————————
    //            INICIALIZACIÓN
    // —————————————————————————————————————————————

    protected function afterCreate(): void
    {
        $this->cod_obra = 0;
        $this->cod_usuario = 0;
        $this->cod_categoria = 0;
        $this->nombre = "";
        $this->descripcion = "";
        $this->img_principal = "ImgDefault.jpg";
        $this->valoracion = 0;
        $this->fecha_alta = date('Y-m-d');
        $this->borrado = 0;
    }

    // ____________________________________________________________

    
    /**
     * Función que comprueba que el usuario existe
     *
     * @return boolean
     */
    public function comprobarUsuario(): bool
    {
        $usuario = new Usuario(); 
        
        if ($usuario->buscarPorId($this->cod_usuario)) {
            return true;
        }
        return false;
    }

    /**
     * Función que comprueba que la categoria existe
     *
     * @return boolean
     */
    public function comprobarCategoria(): bool
    {
        $categoria = new Categorias();
        if ($categoria->buscarPorId($this->cod_categoria)) {
            return true;
        }
        return false;
    }

    /**
     * Función que valida la fecha
     *     - Acepta formato d/m/Y (del formulario) o Y-m-d (de la BD)
     *     - Solo valida que sea un formato de fecha válido
     *     - No rechaza fechas antiguas (necesarias para modificar obras)
     *     - Restricción de fecha futura solo se aplica al crear (forzada a hoy en controlador)
     *
     * @return void
     */
    public function validaFecha(): void
    {
        // Intentamos parsear como d/m/Y primero (del formulario)
        $fechaDada = DateTime::createFromFormat("d/m/Y", $this->fecha_alta);
        
        // Si falla, intentamos como Y-m-d (de la BD)
        if ($fechaDada === false) {
            $fechaDada = DateTime::createFromFormat("Y-m-d", $this->fecha_alta);
        }

        // Si ambos formatos fallan, error
        if ($fechaDada === false) {
            $this->setError("fecha_alta", "La fecha no tiene un formato válido.");
        }
    }

    // —————————————————————————————————————————————
    //           FUNCIONES BASE DE DATOS
    // —————————————————————————————————————————————

    /**
     * Función que prepara la sentencia insert para la tabla obras.
     *
     * @return string
     */
    protected function fijarSentenciaInsert(): string
    {
        $nick = Sistema::app()->acceso()->getNick(); // → Obtener el nick del usuario que ha iniciado sesión
        
        // Si el cod_usuario aún no está asignado (sigue siendo 0), asignar el del usuario autenticado
        // Esto permite que un admin asigne la obra a otro artista durante la creación
        if ($this->cod_usuario <= 0) {
            $this->cod_usuario = Sistema::app()->acl()->getCodUsuario($nick);
        }
        
        // (La fecha ya viene asignada del controlador)

        // Saneamiento de datos
        $nombre = CGeneral::addSlashes($this->nombre);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $img_principal = CGeneral::addSlashes($this->img_principal);
        $cod_usuario = intval($this->cod_usuario);
        $cod_categoria = intval($this->cod_categoria);
        $valoracion = floatval($this->valoracion);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $borrado = intval($this->borrado);

        $sentencia = "INSERT INTO obras (cod_usuario, cod_categoria, nombre, descripcion, img_principal, valoracion, fecha_alta, borrado) 
                            VALUES ( $cod_usuario, $cod_categoria, '$nombre', '$descripcion', '$img_principal', $valoracion, '$fecha_alta', $borrado)";

        return $sentencia;
    }

    /**
     * Función que prepara la sentencia update para la tabla obras
     *
     * @return string
     */
    protected function fijarSentenciaUpdate(): string
    {
        //  Si es para el borrado lógico, hacemos este UPDATE especifico
        if ($this->borrado == 1) {
            return "UPDATE " . $this->fijarTabla() . " SET borrado = 1 WHERE cod_obra = " . intval($this->cod_obra);
        }

        // Para el UPDATE normal con todos los campos
        $nombre = CGeneral::addSlashes($this->nombre);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $img_principal = CGeneral::addSlashes($this->img_principal);
        $cod_usuario = intval($this->cod_usuario);
        $cod_categoria = intval($this->cod_categoria);
        $valoracion = floatval($this->valoracion);

        if($valoracion>=5){
            $valoracion=5;
        }  else if($valoracion<0){ // No creo que ocurra pero mejor prevenir que curar
            $valoracion=0;
        }

        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $cod_obra = intval($this->cod_obra);

        $sentencia = "UPDATE obras SET " .
            "nombre='$nombre', descripcion='$descripcion', " .
            "cod_usuario=$cod_usuario, " .
            "img_principal='$img_principal', cod_categoria=$cod_categoria, " .
            "valoracion=$valoracion, fecha_alta='$fecha_alta' " .
            "WHERE cod_obra=$cod_obra";

        return $sentencia;
    }
}
