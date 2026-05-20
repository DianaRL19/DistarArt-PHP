<?php

/**
 * Clase CCaja que hereda de CWidget para dibujar una caja con tabla
 * Almacenar en: /framework/clases/widgets/
 */
class CCaja extends CWidget
{
    private $_titulo;
    private $_contenido;
    private $_atributosHTML = array();

    /**
     * Constructor de la clase CCaja
     * @param string $titulo Título de la caja
     * @param string $contenido Contenido de la caja (opcional)
     * @param array $atributosHTML Atributos HTML para la tabla (opcional)
     */
    public function __construct($titulo, $contenido = "", $atributosHTML = array())
    {
        $this->_titulo = $titulo;
        $this->_contenido = $contenido;
        $this->_atributosHTML = $atributosHTML;

        // Establecer clase por defecto si no se especifica
        if (!isset($this->_atributosHTML["class"])) {
            $this->_atributosHTML["class"] = "caja";
        }
    }



    public static function requisitos(): string
    {
        $cadena = <<<Fin
             function mostrarCaja(){

                            let estilos = window.getComputedStyle(document.getElementsByClassName("cuerpo")[0]);
                            
                            estilos.display== "none" ?  document.getElementsByClassName("cuerpo")[0].style.display = "block" 
                            :  document.getElementsByClassName("cuerpo")[0].style.display = "none"
                        }
        Fin;
        return  CHTML::script($cadena, []);
    }
    /**
     * Dibuja la caja completa (apertura y cierre)
     * @return string HTML de la caja completa
     */
    public function dibujate(): string
    {
        return $this->dibujaApertura() . $this->dibujaFin();
    }

    /**
     * Dibuja la apertura de la caja con tabla
     * @return string HTML de apertura de la caja
     */
    public function dibujaApertura(): string
    {
        ob_start();

        // Iniciar tabla principal con los atributos HTML
        echo CHTML::dibujaEtiqueta("table", $this->_atributosHTML, "", false);

        // Fila del título
        echo "<tr>\n";
        $id = CHTML::generaID();
        echo CHTML::dibujaEtiqueta("td", array("class" => "titulo"), $this->_titulo, false);
        echo CHTML::boton("Ocultar/mostrar", ["id" => $id, "onclick" => "mostrarCaja(this);return false;"]);
        echo "</tr>\n";



        // Fila del contenido
        echo "<tr>\n";
        echo CHTML::dibujaEtiqueta("td", array("class" => "cuerpo"), "", false);

        // Mostrar contenido inicial si existe
        if (!empty($this->_contenido)) {
            echo $this->_contenido;
        }

        $escrito = ob_get_contents();
        ob_end_clean();

        return $escrito;
    }

    /**
     * Dibuja el cierre de la caja
     * @return string HTML de cierre de la caja
     */
    public function dibujaFin(): string
    {
        ob_start();

        // Cerrar celda del contenido
        echo CHTML::dibujaEtiquetaCierre("td"); // Cierra .cuerpo

        // Cerrar fila del contenido
        echo "</tr>\n";

        // Cerrar tabla principal
        echo CHTML::dibujaEtiquetaCierre("table"); // Cierra .caja

        $escrito = ob_get_contents();
        ob_end_clean();

        return $escrito;
    }
}
