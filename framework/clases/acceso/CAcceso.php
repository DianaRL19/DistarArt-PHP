<?php

class CAcceso
{

    // Variables de instancia
    private $_validado;
    private $_nick;
    private $_nombre;
    private $_permisos;
    private CSesion $sesion;


    // Constructor
    public function __construct()
    {

        $this->sesion = new CSesion();
        if (!$this->sesion->haySesion())
            $this->sesion->crearSesion();

        $this->_validado = false;
        $this->_nick = "";
        $this->_nombre = "";
        $this->_permisos = [];

        $this->recogerDeSesion();
    }

    /**
     * Funcion privada para guardar la información en la sesión
     *
     * @return boolean Devuelve true si se ha podido hacer. False en cualquier otro caso
     */
    private function escribirASesion(): bool
    {
        if (!$this->sesion->haySesion())
            return false;

        // → Inicializar el array de acceso si no existe
        if (!isset($_SESSION["acceso"])) {
            $_SESSION["acceso"] = [];
        }

        if ($this->_validado) {
            $_SESSION["acceso"]["validado"] = true;
            $_SESSION["acceso"]["nick"] = $this->_nick;
            $_SESSION["acceso"]["nombre"] = $this->_nombre;
            $_SESSION["acceso"]["permisos"] = $this->_permisos;
            return true;
        } else {
            $_SESSION["acceso"]["validado"] = false;
        }
        return false;
    }

    /**
     * Función privada que recoje la información de la sesión
     *
     * @return boolean Devuelve true si se ha podido recoger
     */
    private function recogerDeSesion(): bool
    {

        if (
            !isset($_SESSION) ||
            !isset($_SESSION["acceso"]) ||
            !isset($_SESSION["acceso"]["validado"]) ||
            $_SESSION["acceso"]["validado"] == false
        ) {
            $this->_validado = false;
        } else {
            $this->_validado = true;
            $this->_nick = $_SESSION["acceso"]["nick"];
            $this->_nombre = $_SESSION["acceso"]["nombre"];
            $this->_permisos = $_SESSION["acceso"]["permisos"];
        }

        return true;
    }

    /**
     * Sirve para registrar un usuario en la aplicación. Almacena
     * los valores en las propiedades apropiadas y en la sesión 
     * para guardar en la sesión la información del usuario validado.
     *
     * @param string $nick nick del usuario a registrar
     * @param string $nombre nombre del usuario a registrar
     * @param array $permisos permisos del usuario a registrar
     * @return boolean Devuelve true si ha podido registrar el usuario
     */
    public function registrarUsuario(string $nick, string $nombre, array $permisos): bool
    {
        if ($nick == "")
            $this->_validado = false;
        else
            $this->_validado = true;
        $this->_nick = $nick;
        $this->_nombre = $nombre;
        $this->_permisos = $permisos;

        if (!$this->escribirASesion())
            return false;

        return true;
    }


    /**
     * Elimina la información de registro de un usuario
     *
     * @return boolean Devuelve true si ha podido hacerlo
     */
    public function quitarRegistroUsuario(): bool
    {
        $this->_validado = false;
        if (!$this->escribirASesion())
            return false;

        return true;
    }


    /**
     * Función que devuelve si hay o no un usuario registrado
     *
     * @return boolean Devuelve true si hay usuario registrado. False en caso contrario
     */
    public function hayUsuario(): bool
    {
        if ($this->_validado) return true;
        else return false;
    }


    /**
     * Función que devuelve si el usuario registrado tiene o no el permiso indicado
     *
     * @param integer $numero Numero de permiso a comprobar
     * @return bool Devuelve true si hay usuario registrado y tiene el permiso indicado
     */
    public function puedePermiso(int $numero): bool
    {
        if ($numero < 1 || $numero > 10) return false;
        if ($this->hayUsuario())
            return in_array("perm" . $numero, $this->_permisos);
        return false;
    }
    
    
    /*
     * Métodos get
     */

    /**
     * Devuelve el nick del usuario indicado o false si no hay usuario
     *
     * @return string|false
     */
    public function getNick(): string|false
    {
        if ($this->hayUsuario()) return $this->_nick;
        return false;
    }

    /**
     * Devuelve el nombre del usuario registrado o false si no hay usuario
     *
     * @return string|false
     */
    public function getNombre(): string|false
    {
        if (!$this->hayUsuario())
            return false;

        return $this->_nombre;
    }
}
