<?php

class Galeria extends CActiveRecord
{

    protected function fijarNombre(): string
    {
        return 'galeria';
    }

    protected function fijarTabla(): string
    {
        return 'datos_obras';
    }

    protected function fijarId(): string
    {
        return "cod_obra";
    }

    protected function fijarAtributos(): array
    {
        return ["cod_obra", "cod_usuario", "cod_categoria", "nombre", "descripcion", "img_principal", "valoracion", "fecha_alta", "borrado", "nick_usuario", "descripcion_categoria"];
    }

    protected function fijarDescripciones(): array
    {
        return [
            "cod_obra" => "Código de la obra",
            "cod_categoria" => "Código de la categoría",
            "nombre" => "Nombre de la obra",
            "descripcion" => "Descripción de la obra",
            "img_principal" => "Imagen principal",
            "valoracion" => "Valoración",
            "fecha_alta" => "Fecha de alta",
            "borrado" => "Obra Borrada",
            "nick_usuario" => "Artista",
            "descripcion_categoria" => "Descripción de la categoría"
        ];
    }

    protected function fijarRestricciones(): array
    {
        return [
            [
                "ATRI" => "cod_obra",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "cod_obra",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "cod_usuario",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "cod_categoria",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "nombre",
                "TIPO" => "CADENA",
                "TAMANIO" => 100
            ],
            [
                "ATRI" => "descripcion",
                "TIPO" => "CADENA",
                "TAMANIO" => 2000
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
            ],
            [
                "ATRI" => "nick_usuario",
                "TIPO" => "CADENA",
                "TAMANIO" => 20
            ],
            [
                "ATRI" => "descripcion_categoria",
                "TIPO" => "CADENA",
                "TAMANIO" => 60
            ]
        ];
    }

    protected function afterCreate(): void
    { 
        $this->cod_obra = 0;
        $this->cod_usuario = 0;
        $this->cod_categoria = 0;
        $this->nombre = "";
        $this->descripcion = "";
        $this->img_principal = "ImgDefault.jpg";
        $this->valoracion = 0;
        $this->borrado = 0;
        $this->nick_usuario = "";
        $this->descripcion_categoria = "";
    }

    protected function afterBuscar(): void
    {
        //$this->fecha_alta=CGeneral::fechaMysqlANormal($this->fecha_alta);
    }


    /**
     * Función que comprueba que la categoría sea válida
     *
     * @return void
     */
    protected function comprobarCategoria(): void
    {
        $categoria = new Categorias();

        $cat = intval($this->cod_categoria);

        if (!$categoria->buscarPorId($cat)) {
            $this->setError("cod_categoria", "La categoria es incorrecta");
            $this->descripcion_categoria = "";
        } else {
            $this->descripcion_categoria = $categoria->descripcion;
        }
    }

    /**
     * Funcion que valida la fecha dada
     *     - Debe ser igual al día actual (no se permite fechar pasadas ni futuras)
     *     - La fecha de alta se asigna automáticamente al crear el usuario
     *
     * @return void
     */
    public function validaFecha() : void
    {
        $fechaDada = DateTime::createFromFormat("d/m/Y", $this->fecha_alta);
        $fechaHoy = new DateTime();

        // Establecer la hora a 00:00:00 para comparar solo fechas sin importar la hora :)
        $fechaHoy->setTime(0, 0, 0);
        $fechaDada->setTime(0, 0, 0);

        if ($fechaDada < $fechaHoy) {
            // Añado el error
            $this->setError("fecha_alta", "La fecha de alta no puede ser anterior al día de hoy.");
        }

        if ($fechaDada > $fechaHoy) {
            // Añado el error
            $this->setError("fecha_alta", "La fecha de alta no puede ser posterior al día de hoy.");
        }
    }

    /**
     * Función que devuelve la fecha de hoy en formato d/m/Y
     *
     * @return string
     */
    function fechaDefecto(): string
    {
        $fechaHoy = new DateTime();
        return $fechaHoy->format("d/m/Y");
    }

    // —————————————————————————————————————————————
    //           FUNCIONES BASE DE DATOS
    // —————————————————————————————————————————————

    /**
     * Función que prepara la sentencia insert para la tabla obras.
     *
     * @return string
     */
    function fijarSentenciaInsert(): string
    {
        $sentencia = "";

        $cod_usuario = CGeneral::addSlashes($this->cod_usuario);
        $cod_categoria = CGeneral::addSlashes($this->cod_categoria);
        $nombre_obra = CGeneral::addSlashes($this->nombre);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $img_principal = CGeneral::addSlashes($this->img_principal);
        $valoracion = floatval($this->valoracion);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $borrado = intval($this->borrado);
        $nick_usuario = CGeneral::addSlashes($this->nick_usuario);
        $descripcion_categoria = CGeneral::addSlashes($this->descripcion_categoria);

        $sentencia = "insert into obras (cod_usuario, cod_categoria ,nombre, descripcion, img_principal, valoracion, fecha_alta, borrado, nick_usuario, descripcion_categoria" .
            ") values ('$cod_usuario, $cod_categoria, $nombre_obra', '$descripcion', '$img_principal', $valoracion, '$fecha_alta', $borrado, '$nick_usuario', '$descripcion_categoria')";

        return $sentencia;
    }

    /**
    * Función que prepara la sentencia update para la tabla obras
    *
    * @return string
    */
    function fijarSentenciaUpdate(): string
    {
        $sentencia = "";

        $cod_obra = CGeneral::addSlashes($this->cod_obra);
        $cod_usuario = CGeneral::addSlashes($this->cod_usuario);
        $cod_categoria = CGeneral::addSlashes($this->cod_categoria);
        $nombre_obra = CGeneral::addSlashes($this->nombre);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $img_principal = CGeneral::addSlashes($this->img_principal);
        $valoracion = floatval($this->valoracion);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $borrado = intval($this->borrado);
        $nick_usuario = CGeneral::addSlashes($this->nick_usuario);
        $descripcion_categoria = CGeneral::addSlashes($this->descripcion_categoria);

        $sentencia = "update obras set" .
            " cod_usuario='$cod_usuario', " .
            " cod_categoria='$cod_categoria', " .
            " nombre='$nombre_obra', " .
            " descripcion='$descripcion', " .
            " img_principal='$img_principal', " .
            " valoracion=$valoracion, " .
            " fecha_alta='$fecha_alta', " .
            " borrado=$borrado, " .
            " nick_usuario='$nick_usuario', " .
            " descripcion_categoria='$descripcion_categoria' " .
            " where cod_obra=$cod_obra";

        return $sentencia;
    }
}
