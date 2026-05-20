<?php

class Categorias extends CActiveRecord
{

    protected function fijarNombre(): string
    {
        return 'cate';
    }

    protected function fijarTabla(): string
    {
        return 'categorias';
    }

    protected function fijarId(): string
    {
        return "cod_categoria";
    }

    protected function fijarAtributos(): array
    {
        return ["cod_categoria", "descripcion"];
    }

    protected function fijarDescripciones(): array
    {
        return array(
            "cod_categoria" => "Código de la categoría",
            "descripcion" => "Nombre de la categoría"
        );
    }

    protected function fijarRestricciones(): array
    {
        return [
            [
                "ATRI" => "cod_categoria,descripcion",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "cod_categoria",
                "TIPO" => "ENTERO"
            ],
            [
                "ATRI" => "descripcion",
                "TIPO" => "CADENA",
                "TAMANIO" => 100
            ]
        ];
    }

    // —————————————————————————————————————————————
    //           FUNCIONES BASE DE DATOS
    // —————————————————————————————————————————————

    /**
     * Función que prepara la sentencia insert para la tabla categorias.
     *
     * @return string
     */
    protected function fijarSentenciaInsert(): string
    {
        $descripcion = CGeneral::addSlashes($this->descripcion);

        $sentencia = "INSERT INTO categorias (descripcion) VALUES ('$descripcion')";

        return $sentencia;
    }

    /**
     * Función que prepara la sentencia update para la tabla categorias.
     *
     * @return string
     */
    protected function fijarSentenciaUpdate(): string
    {
        $cod_categoria = intval($this->cod_categoria);
        $descripcion = CGeneral::addSlashes($this->descripcion);

        $sentencia = "UPDATE categorias SET descripcion='$descripcion' " .
                                " WHERE cod_categoria=$cod_categoria";

        return $sentencia;
    }
}
