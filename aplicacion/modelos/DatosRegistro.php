<?php
class DatosRegistro extends CActiveRecord
{

    protected function fijarNombre(): string
    {
        return 'DatosRegistro';
    }

    protected function fijarAtributos(): array
    {
        return array(
            "nick",
            "nombre",
            "email",
            "descripcion",
            "direccion",
            "pais",
            "img_perfil",
            "img_banner",
            "valoracion",
            "fecha_alta",
            "contrasenia",
            "confirmar_contrasenia",
            "borrado"
        );
    }

    protected function fijarDescripciones(): array
    {
        return array(
            "nick" => "Nick",
            "nombre" => "Nombre",
            "email" => "Email",
            "descripcion" => "Descripción",
            "direccion" => "Dirección",
            "pais" => "País",
            "img_perfil" => "Imagen de perfil",
            "img_banner" => "Imagen de banner",
            "valoracion" => "Valoración",
            "fecha_alta" => "Fecha de alta",
            "contrasenia" => "Contraseña",
            "confirmar_contrasenia" => "Confirma contraseña",
            "borrado" => "Borrado"
        );
    }

    protected function fijarRestricciones(): array
    {
        return
            array(
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
                    "ATRI" => "nombre",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 100
                ],
                [
                    "ATRI" => "email",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 100
                ],
                [
                    "ATRI" => "descripcion",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 500,
                ],
                [
                    "ATRI" => "direccion",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 150,
                ],  
                [
                    "ATRI" => "pais",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 50,
                ],  
                [
                    "ATRI" => "img_perfil",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 100,
                ],
                [
                    "ATRI" => "img_banner",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 100,
                ],
                [
                    "ATRI" => "valoracion",
                    "TIPO" => "REAL",
                    "MIN" => 0,
                    "MAX" => 5,
                ],
                [
                    "ATRI" => "fecha_alta",
                    "TIPO" => "FECHA",
                    "DEFECTO" => fechaDefecto()
                ],
                [
                    "ATRI" => "fecha_alta",
                    "TIPO" => "FUNCION",
                    "FUNCION" => "validaFecha"
                ],
                [
                    "ATRI" => "contrasenia, confirmar_contrasenia",
                    "TIPO" => "CADENA",
                    "TAMANIO" => 65,
                ],
                [
                    "ATRI" => "confirmar_contrasenia",
                    "TIPO" => "FUNCION",
                    "FUNCION" => "validaContrasena"
                ]

            );
    }

    /**
     * Función que inicializa las propiedades del modelo
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
        $this->fecha_alta = fechaDefecto();
        $this->contrasenia = "";
        $this->confirmar_contrasenia = "";
    }

    /**
     * Función que comprueba que la contraseña y el confirma contraseña sean iguales
     * En el caso de que no sea así, se añade un error
     * 
     * @return void
     */
    public function validaContrasena(): void
    {
        if ($this->contrasenia !== $this->confirmar_contrasenia)
            $this->setError("confirmar_contrasenia", "Las contraseñas no coinciden");
    }

    /**
     * Función que valida la fecha dada
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

