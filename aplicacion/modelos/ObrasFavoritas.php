<?php

class ObrasFavoritas extends CActiveRecord
{
    protected function fijarNombre(): string
    {
        return 'obraFavorita';
    }

    protected function fijarTabla(): string
    {
        return 'obras_favoritas';
    }

    protected function fijarId(): string
    {
        return "cod_obra_favorita";
    }

    protected function fijarAtributos(): array
    {
        return [
            "cod_obra_favorita",
            "cod_usuario",
            "cod_obra",
            "fecha_alta",
            "borrado"
        ];
    }

    protected function fijarDescripciones(): array
    {
        return [
            "cod_obra_favorita" => "Código del favorito",
            "cod_usuario" => "Código del usuario",
            "cod_obra" => "Código de la obra",
            "fecha_alta" => "Fecha del like",
            "borrado" => "Eliminado"
        ];
    }

    protected function fijarRestricciones(): array
    {
        return [
            "cod_obra" => [
                "REQUERIDO" => true,
                "TIPO" => "ENTERO",
                "MINIMO" => 1
            ],
            "cod_usuario" => [
                "REQUERIDO" => true,
                "TIPO" => "ENTERO",
                "MINIMO" => 1
            ],
            "fecha_alta" => [
                "REQUERIDO" => true,
                "TIPO" => "FECHA"
            ],
            "borrado" => [
                "REQUERIDO" => false,
                "TIPO" => "ENTERO",
                "DEFECTO" => 0
            ]
        ];
    }

    /**
     * Función que prepara la sentancia insert para la tabla obras favoritas
     *
     * @return string
     */
    protected function fijarSentenciaInsert(): string
    {
        // —————————————————————————————————————
        //     GENERAMOS SQL INSERT
        // —————————————————————————————————————

        $cod_usuario = intval($this->cod_usuario);
        $cod_obra = intval($this->cod_obra);
        $fecha_alta = CGeneral::fechaNormalAMysql($this->fecha_alta);
        $borrado = intval($this->borrado);

        $sentencia = "INSERT INTO obras_favoritas (cod_usuario, cod_obra, fecha_alta, borrado) 
                        VALUES ($cod_usuario, $cod_obra, '$fecha_alta', $borrado)";

        return $sentencia;
    }

    /**
     * Función que prepara la sentencia update para la tabla obras favoritas
     *
     * @return string
     */
    protected function fijarSentenciaUpdate(): string
    {
        // —————————————————————————————————————
        //     GENERAMOS SQL UPDATE
        // —————————————————————————————————————

        // Para borrado lógico
        $cod_obra_favorita = intval($this->cod_obra_favorita);
        $borrado = intval($this->borrado);

        $sentencia = "UPDATE obras_favoritas SET borrado = $borrado WHERE cod_obra_favorita = $cod_obra_favorita";

        return $sentencia;
    }

    /**
     * Función de validación  que verifica que la fecha sea válida 
     * y que el campo de borrado sea correcto.
     *
     * @return bool
     */
    public function validar(): bool
    {
        $valido = parent::validar();

        if (!$valido) {
            return false;
        }

        // Verificamos que la fecha sea válida
        if (!$this->fecha_alta || !strtotime($this->fecha_alta)) {
            $this->errores["fecha_alta"] = "Fecha inválida";
            return false;
        }

        // Verificamos que el borrado sea 0 o 1
        if (!in_array($this->borrado, [0, 1])) {
            $this->errores["borrado"] = "Valor debe ser 0 o 1";
            return false;
        }

        return true;
    }
}
