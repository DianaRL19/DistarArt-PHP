<?php

class Encargos extends CActiveRecord
{

    protected function fijarNombre(): string
    {
        return 'Encargo';
    }

    protected function fijarTabla(): string
    {
        return 'encargos';
    }

    protected function fijarId(): string
    {
        return "cod_encargo";
    }

    protected function fijarAtributos(): array
    {
        return [
            "cod_encargo",
            "cod_usuario",
            "cod_cliente",
            "nombre",
            "descripcion",
            "estado",
            "precio_base",
            "iva",
            "precio_total",
            "fecha_alta",
            "fecha_limite",
            "version",
            "imagen_proceso",
            "comentarios",
            "borrado"
        ];
    }

    protected function fijarDescripciones(): array
    {
        return [
            "cod_encargo" => "Código del encargo",
            "cod_usuario" => "Código del artista",
            "cod_cliente" => "Código del cliente",
            "nombre" => "Nombre del encargo",
            "descripcion" => "Descripción del encargo",
            "estado" => "Estado del encargo",
            "precio_base" => "Precio base",
            "iva" => "IVA",
            "precio_total" => "Precio total",
            "fecha_alta" => "Fecha de creación",
            "fecha_limite" => "Fecha límite de entrega",
            "version" => "Versión de la imagen",
            "imagen_proceso" => "Imagen del proceso",
            "comentarios" => "Comentarios del artista",
            "borrado" => "Borrado"
        ];
    }

    protected function fijarRestricciones(): array
    {
        return [
            [
                "ATRI" => "cod_cliente",
                "TIPO" => "REQUERIDO"
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
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "descripcion",
                "TIPO" => "CADENA",
                "TAMANIO" => 1000
            ],
            [
                "ATRI" => "precio_base",
                "TIPO" => "NUMERICO",
                "TAMANIO" => "10,2"
            ],
            [
                "ATRI" => "iva",
                "TIPO" => "NUMERICO",
                "TAMANIO" => "5,2"
            ],
            [
                "ATRI" => "precio_total",
                "TIPO" => "NUMERICO",
                "TAMANIO" => "10,2"
            ],
            [
                "ATRI" => "fecha_limite",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "fecha_limite",
                "TIPO" => "FECHA"
            ],
            [
                "ATRI" => "fecha_limite",
                "TIPO" => "FUNCION",
                "FUNCION" => "validarFechaLimite"
            ],
            [
                "ATRI" => "version",
                "TIPO" => "NUMERICO"
            ],
            [
                "ATRI" => "imagen_proceso",
                "TIPO" => "CADENA",
                "TAMANIO" => 150
            ],
            [
                "ATRI" => "comentarios",
                "TIPO" => "CADENA",
                "TAMANIO" => 65535
            ]
        ];
    }

    // —————————————————————————————————————————————
    //            INICIALIZACIÓN
    // —————————————————————————————————————————————

    protected function afterCreate(): void
    {
        $this->cod_encargo = 0;
        $this->cod_usuario = 0;
        $this->cod_cliente = 0;
        $this->nombre = "";
        $this->descripcion = "";
        $this->estado = 1;  // → Estado inicial → "Lluvia de ideas"
        $this->precio_base = 0;
        $this->iva = 0;
        $this->precio_total = 0;
        $this->fecha_alta = date('Y-m-d');
        $this->fecha_limite = date('Y-m-d');
        $this->version = 1;
        $this->imagen_proceso = "EncargoDefault.png";
        $this->comentarios = "";
        $this->borrado = 0;
    }

    // ____________________________________________________________

    /**
     * Función que valida que la fecha límite no sea anterior a la de hoy.
     * Se aplica tanto al crear como al modificar encargos.
     *
     * @return void
     */
    public function validarFechaLimite(): void
    {
        if (empty($this->fecha_limite)) {
            return;
        }

        // Parseamos la fecha en ambos formatos posibles (Y-m-d o d/m/Y)
        $fechaLimite = null;
        
        // Intentamos parsear en formato Y-m-d (formulario y/o BD)
        $fechaObj = DateTime::createFromFormat('Y-m-d', $this->fecha_limite);

        if ($fechaObj) {
            $fechaLimite = $fechaObj;
        } else {
            // Intentamos parsear en formato d/m/Y (después de cargar de la BD)
            $fechaObj = DateTime::createFromFormat('d/m/Y', $this->fecha_limite);
            if ($fechaObj) {
                $fechaLimite = $fechaObj;
            }
        }

        // Si se parsea correctamente, validamos que no sea anterior a hoy
        if ($fechaLimite) {
            $hoy = new DateTime();
            $hoy->setTime(0, 0, 0);
            $fechaLimite->setTime(0, 0, 0);
            
            if ($fechaLimite < $hoy) {
                $this->setError("fecha_limite", "La fecha límite no puede ser anterior a hoy");
            }
        }
    }

    // ____________________________________________________________

    /**
     * Función que prepara la sentencia insert para la tabla encargos.
     *
     * @return string
     */
    protected function fijarSentenciaInsert(): string
    {
        $nick = Sistema::app()->acceso()->getNick(); // → Obtener el nick del usuario autentificado
        
        // Si el cod_usuario aún no está asignado (sigue siendo 0), asignamos el del usuario autentifcado
        if ($this->cod_usuario <= 0) {
            $this->cod_usuario = Sistema::app()->acl()->getCodUsuario($nick);
        }

        // Saneamiento de datos
        $nombre = CGeneral::addSlashes($this->nombre);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $imagen_proceso = $this->imagen_proceso ? CGeneral::addSlashes($this->imagen_proceso) : null;
        $comentarios = $this->comentarios ? CGeneral::addSlashes($this->comentarios) : null;
        $cod_usuario = intval($this->cod_usuario);
        $cod_cliente = intval($this->cod_cliente);
        $estado = intval($this->estado);
        $precio_base = floatval($this->precio_base);
        $iva = floatval($this->iva);
        $precio_total = floatval($this->precio_total);
        $version = intval($this->version);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $fecha_limite = CGeneral::fechaNormalAMysql($this->fecha_limite);
        $borrado = intval($this->borrado);

        $imagen_proceso_sql = $imagen_proceso ? "'$imagen_proceso'" : "'EncargoDefault.png'";
        $comentarios_sql = $comentarios ? "'$comentarios'" : "''";

        $sentencia = "INSERT INTO encargos (cod_usuario, cod_cliente, nombre, descripcion, estado, precio_base, iva, precio_total, fecha_alta, fecha_limite, version, imagen_proceso, comentarios, borrado) 
                            VALUES ( $cod_usuario, $cod_cliente, '$nombre', '$descripcion', $estado, $precio_base, $iva, $precio_total, '$fecha_alta', '$fecha_limite', $version, $imagen_proceso_sql, $comentarios_sql, $borrado)";

        return $sentencia;
    }

    /**
     * Función que prepara la sentencia update para la tabla encargos.
     *
     * @return string
     */
    protected function fijarSentenciaUpdate(): string
    {
        // Si es para el borrado lógico, hacemos este UPDATE específico
        if ($this->borrado == 1) {
            return "UPDATE " . $this->fijarTabla() . " SET borrado = 1 WHERE cod_encargo = " . intval($this->cod_encargo);
        }

        // Para el UPDATE normal con todos los campos
        $nombre = CGeneral::addSlashes($this->nombre);
        $descripcion = CGeneral::addSlashes($this->descripcion);
        $imagen_proceso = $this->imagen_proceso ? CGeneral::addSlashes($this->imagen_proceso) : null;
        $comentarios = $this->comentarios ? CGeneral::addSlashes($this->comentarios) : null;
        $cod_usuario = intval($this->cod_usuario);
        $cod_cliente = intval($this->cod_cliente);
        $estado = intval($this->estado);
        $precio_base = floatval($this->precio_base);
        $iva = floatval($this->iva);
        $precio_total = floatval($this->precio_total);
        $version = intval($this->version);
        $cod_encargo = intval($this->cod_encargo);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $fecha_limite = CGeneral::fechaNormalAMysql($this->fecha_limite);

        $imagen_proceso_sql = $imagen_proceso ? "'$imagen_proceso'" : "'EncargoDefault.png'";
        $comentarios_sql = $comentarios ? "'$comentarios'" : "''";

        $sentencia = "UPDATE encargos SET " .
            "nombre='$nombre', descripcion='$descripcion', " .
            "cod_usuario=$cod_usuario, cod_cliente=$cod_cliente, " .
            "estado=$estado, precio_base=$precio_base, iva=$iva, precio_total=$precio_total, " .
            "fecha_alta='$fecha_alta', fecha_limite='$fecha_limite', " .
            "version=$version, imagen_proceso=$imagen_proceso_sql, comentarios=$comentarios_sql " .
            "WHERE cod_encargo=$cod_encargo";

        return $sentencia;
    }
}

