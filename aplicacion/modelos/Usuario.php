<?php

class Usuario extends CActiveRecord
{

    protected function fijarNombre(): string
    {
        return 'Usuario';
    }

    protected function fijarTabla(): string
    {
        return 'usuarios';
    }

    protected function fijarId(): string
    {
        return "cod_usuario";
    }

    protected function fijarAtributos(): array
    {
        return [
            "cod_usuario",
            "nombre",
            "nick",
            "email",
            "descripcion",
            "direccion",
            "pais",
            "img_perfil",
            "img_banner",
            "valoracion",
            "fecha_alta",
            "borrado"
        ];
    }

    protected function fijarDescripciones(): array
    {
        return [
            "cod_usuario" => "Código de usuario",
            "nombre" => "Nombre",
            "nick" => "Nick",
            "email" => "Email",
            "descripcion" => "Descripción",
            "direccion" => "Dirección",
            "pais" => "País",
            "img_perfil" => "Imagen de perfil",
            "img_banner" => "Imagen de banner",
            "valoracion" => "Valoración",
            "fecha_alta" => "Fecha de alta",
            "borrado" => "Borrado"
        ];
    }

    protected function fijarRestricciones(): array
    {
        return [
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
                "ATRI" => "nick",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "nick",
                "TIPO" => "CADENA",
                "TAMANIO" => 30
            ],
            [
                "ATRI" => "email",
                "TIPO" => "EMAIL"
            ],
            [
                "ATRI" => "descripcion",
                "TIPO" => "CADENA",
                "TAMANIO" => 500
            ],
            [
                "ATRI" => "direccion",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "direccion",
                "TIPO" => "CADENA",
                "TAMANIO" => 150
            ],
            [
                "ATRI" => "pais",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "pais",
                "TIPO" => "CADENA",
                "TAMANIO" => 50
            ],
            [
                "ATRI" => "img_perfil",
                "TIPO" => "CADENA",
                "TAMANIO" => 100,
                "DEFECTO" => "ImgDefault.jpg"
            ],
            [
                "ATRI" => "img_banner",
                "TIPO" => "CADENA",
                "TAMANIO" => 100,
                "DEFECTO" => "Img_BannerDefault.jpg"
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
            ]
        ];
    }

    /**
     * Funcion que inicializa las propiedades del modelo
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        $this->nick = "";
        $this->nombre = "";
        $this->email = "";
        $this->descripcion = "";
        $this->direccion = "";
        $this->pais = "";
        $this->img_perfil = "ImgDefault.jpg";
        $this->img_banner = "Img_BannerDefault.jpg";
        $this->valoracion = 0;
        $this->fecha_alta = date('Y-m-d');
        $this->borrado = 0;
    }

    /**
     * Funcion que valida la fecha dada
     *     - Acepta formato d/m/Y (del formulario) o Y-m-d (de la BD)
     *     - Solo valida que sea un formato de fecha válido
     *     - No rechaza fechas antiguas (necesarias para modificar usuarios)
     *     - Restricción de fecha futura solo se aplica al crear (forzada a hoy en controlador)
     *
     * @return void
     */
    public function validaFecha() : void
    {
        // Creamos un objeto DateTime a partir de la fecha dada, para validar su formato
        $fechaDada = DateTime::createFromFormat("d/m/Y", $this->fecha_alta);
        
        // Si falla, intentamos con el formato Y-m-d (de la BD)
        if ($fechaDada === false) {
            $fechaDada = DateTime::createFromFormat("Y-m-d", $this->fecha_alta);
        }

        // Si los dos fallan, le mostramos un error
        if ($fechaDada === false) {
            $this->setError("fecha_alta", "La fecha no tiene un formato válido.");
        }
    }

    // —————————————————————————————————————————————
    //           FUNCIONES BASE DE DATOS
    // —————————————————————————————————————————————

    /**
     * Función que prepara la sentencia para la tabla usuarios
     *
     * @return string
     */
    protected function fijarSentenciaInsert(): string
    {
        // Saneamiento de datos
        $nombre = CGeneral::addSlashes($this->nombre);
        $nick = CGeneral::addSlashes($this->nick);
        $email = CGeneral::addSlashes($this->email);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $direccion = CGeneral::addSlashes($this->direccion);
        $pais = CGeneral::addSlashes($this->pais);
        $img_perfil = CGeneral::addSlashes($this->img_perfil);
        $img_banner = CGeneral::addSlashes($this->img_banner);
        $valoracion = floatval($this->valoracion);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $borrado = intval($this->borrado);

        // Fijamos la sentencia SQL para insertar un nuevo usuario
        $sentencia = "INSERT INTO usuarios (nombre, nick, email, descripcion, direccion, pais, img_perfil, img_banner, valoracion, fecha_alta, borrado) 
                VALUES ('$nombre', '$nick', '$email', '$descripcion', '$direccion', '$pais', '$img_perfil', '$img_banner', $valoracion, '$fecha_alta', $borrado)";

        return $sentencia;
    }

    /**
     * Función que prepara la sentencia update para la tabla usuarios.
     *
     * @return string
     */
    protected function fijarSentenciaUpdate(): string
    {
        // Si es para el borrado lógico, hacemos un UPDATE específico
        if ($this->borrado == 1) {
            return "UPDATE " . $this->fijarTabla() . " SET borrado = 1 WHERE cod_usuario = " . intval($this->cod_usuario);
        }

        // Saneamiento de datos
        $nombre = CGeneral::addSlashes($this->nombre);
        $nick = CGeneral::addSlashes($this->nick);
        $email = CGeneral::addSlashes($this->email);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $direccion = CGeneral::addSlashes($this->direccion);
        $pais = CGeneral::addSlashes($this->pais);
        $img_perfil = CGeneral::addSlashes($this->img_perfil);
        $img_banner = CGeneral::addSlashes($this->img_banner);
        $valoracion = floatval($this->valoracion);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $cod_usuario = intval($this->cod_usuario);

        // Fijamos la sentencia SQL para actualizar un usuario existente
        $sentencia = "UPDATE usuarios SET " .
            "nombre='$nombre', nick='$nick', email='$email', descripcion='$descripcion', " .
            "direccion='$direccion', pais='$pais', img_perfil='$img_perfil', img_banner='$img_banner', " .
            "valoracion=$valoracion, fecha_alta='$fecha_alta' " .
            "WHERE cod_usuario=$cod_usuario";

        return $sentencia;
    }

    // —————————————————————————————————————————————
    //       ACTUALIZAR VALORACIÓN DESDE OBRAS
    // —————————————————————————————————————————————

    /**
     * Función que calcula la valoración promedio de todas las obras del artista y actualiza su valoración
     * - Busca todas las obras NO borradas del usuario
     * - Calcula la media de valoraciones
     * - Si el promedio es >= 5, asigna 5 (máximo permitido)
     * - Valida que el valor esté entre 0 y 5
     * - Guarda el cambio en la BD
     *
     * @return void
     */
    public function actualizarValoracionDesdeObras(): void
    {
        // Buscamos todas las obras del usuario (no borradas)
        $obra = new Obras();
        $resultados = $obra->buscarTodos(["where" => "cod_usuario = " . intval($this->cod_usuario) . " AND borrado = 0"]);

        // Si no tiene obras o la búsqueda falla, valoración = 0
        if (empty($resultados) || $resultados === false) {
            $this->valoracion = 0;
        } else {
            // Calculamos la suma y media de valoraciones
            $sumaValoraciones = 0;
            $cantidadObras = count($resultados);

            foreach ($resultados as $obraData) {
                $sumaValoraciones += floatval($obraData->valoracion);
            }

            $valoracionPromedio = $sumaValoraciones / $cantidadObras;

            // Si la media es >= 5, asignamos 5 (máximo)
            if ($valoracionPromedio >= 5) {
                $this->valoracion = 5;
            } else {
                $this->valoracion = $valoracionPromedio;
            }

            // Validamos el rango (min 0, max 5)
            if ($this->valoracion < 0) {
                $this->valoracion = 0;
            } else if ($this->valoracion > 5) {
                $this->valoracion = 5;
            }
        }

        // Guardamos el cambio sin validación (solo actualizamos la valoración)
        $this->guardar();
    }

}
