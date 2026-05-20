<?php

class CACLBD extends CACLBase
{

    private $_bd;
    private $_hayConeccion;
    private $_prefijo = '_$"_';  //usado con algoritmo md5 como prefijo que se adjunta a la contraseña
    //para darle mas seguridad



    public function __construct($servidor, $usuario, $contra, $bd)
    {
        $this->_hayConeccion = true;
        $this->_bd = new CBaseDatos($servidor, $usuario, $contra, $bd);
        if ($this->_bd->error() !== 0) $this->_hayConeccion = false;
    }

    /**
     * Añade un role a nuesta ACL
     * 
     * @param string $nombre Nombre del role a añadir 
     * @param array $permisos Permisos que tendrá el role. Array con hasta 10 permisos
     * @return bool True si se ha podido crear, false en caso contrario
     */
    public function anadirRole(string $nombre, array $permisos = []): bool
    {
        if (!$this->_hayConeccion)
            return false;

        $nombre = mb_substr(mb_strtolower($nombre), 0, 30);

        if ($this->existeNombreRole($nombre))
            return false;

        for ($cont = 0; $cont <= 10; $cont++) {
            if (isset($permisos[$cont])) {
                $aPermisos[$cont] = isset($permisos[$cont]) && (bool)$permisos[$cont] ? '1' : '0';
            } else {
                $aPermisos[$cont] = '0';
            }
        }

        $nombre = $this->_bd->getEnlace()->escape_string($nombre);

        $sentencia = "insert into acl_roles (" .
            "     nombre, " .
            "    perm1, perm2,perm3,perm4,perm5," .
            "    perm6,perm7, perm8, perm9, perm10" .
            "       ) values (" .
            "     '$nombre', " .
            "    {$aPermisos[0]}, {$aPermisos[1]}," .
            "    {$aPermisos[2]},{$aPermisos[3]}," .
            "    {$aPermisos[4]}, {$aPermisos[5]}," .
            "    {$aPermisos[6]}, {$aPermisos[7]}," .
            "    {$aPermisos[8]}, {$aPermisos[9]}" .
            "          )";
        $resultado = $this->_bd->crearConsulta($sentencia);
        return $resultado->error() === 0;
    }


    /**
     * Funcion privada para comprobar que un role (nombre)
     * exite
     *
     * @param string $role role a comprobar
     * @return boolean devuelve true si existe, false en otro caso
     */
    private function existeNombreRole(string $role): bool
    {
        if (!$this->_hayConeccion)
            return false;

        $role = mb_substr(mb_strtolower($role), 0, 30);
        $role = $this->_bd->getEnlace()->escape_string($role);

        $sentencia = "select * from acl_roles where nombre='$role'";
        $resultado = $this->_bd->crearConsulta($sentencia);
        if (!$resultado)
            return false;

        $fila = $resultado->fila();

        return ($fila != false);
    }

    /**
     * Función que localiza un role y devuelve el código de ese role o false si no
     * lo encuentra
     *
     * @param string $nombre nombre del role
     * @return integer|false Devuelve el código de role para el nombre indicado o false si no encuentra el role
     */
    public function getCodRole(string $nombre): int|false
    {
        if (!$this->_hayConeccion)
            return false;

        $sentencia = "SELECT cod_acl_role FROM acl_roles WHERE nombre = '{$this->_bd->getEnlace()->real_escape_string($nombre)}'";
        $resul = $this->_bd->crearConsulta($sentencia)->fila();

        if (is_null($resul))
            return false;

        return (int)$resul["cod_acl_role"];
    }

    /**
     * Función que comprueba la existencia de un role
     *
     * @param integer $codRole role a buscar
     * @return boolean Devuelve true si lo encuentra o false en caso contrario 
     */
    public function existeRole(int $codRole): bool
    {
        if (!$this->_hayConeccion)
            return false;


        $consulta = "SELECT * from acl_roles " .
            "      where cod_acl_role = $codRole";

        $resul = $this->_bd->crearConsulta($consulta)->fila();

        if (is_null($resul) || !$resul)
            return false;

        return true;
    }

    /**
     * Función que devuelve los permisos de un role dado
     *
     * @param integer $codRole Role a buscar
     * @return array|false Devuelve los permisos o false si no encuentra el role
     */
    public function getPermisosRole(int $codRole): array|false
    {
        if (!$this->_hayConeccion)
            return false;

        if (!$this->existeRole($codRole))
            return false;


        $consulta = "SELECT `perm1`, `perm2`, `perm3`, `perm4`, `perm5`," .
            "      `perm6`, `perm7`, `perm8`, `perm9`, `perm10` " .
            "     FROM `acl_roles` " .
            "     WHERE cod_acl_role = $codRole";


        $resul = $this->_bd->crearConsulta($consulta)->fila();
        $perm = [];
        for ($cont = 1; $cont < 11; $cont++)
            $perm[$cont] = (bool)$resul[$cont - 1];

        return ($perm);
    }

    /**
     * Función que devuelve si un role tiene o no un permiso concreto
     *
     * @param integer $codRole Role a buscar
     * @param integer $numero Número de permiso
     * @return boolean True si encuentra el role y lo tiene. False en cualquier otro caso
     */
    function getPermisoRole(int $codRole, int $numero): bool
    {
        if (!$this->_hayConeccion)
            return false;

        if (!$this->existeRole($codRole))
            return false;

        $codRole = (int)$codRole;
        $sentencia = "SELECT * FROM acl_roles where cod_acl_role='{$codRole}'";

        $resul = $this->_bd->crearConsulta($sentencia)->fila();

        if ($resul["perm" . $numero] == 1) return true;
        return false;
    }

    /**
     * Función que añade un nuevo usuario a nuestra ACL
     *
     * @param string $nombre Nombre del usuario
     * @param string $nick Nick unico para el usuario
     * @param string $contrasena contraseña del usuario
     * @param integer $codRole Role a asignarle
     * @return boolean Devuelve true si puede crearlo. False en caso contrario
     */
    public function anadirUsuario(string $nombre, string $nick, string $contrasena, int $codRole): bool
    {
        if (!$this->_hayConeccion)
            return false;

        if (!$this->existeRole($codRole))
            return false;

        $nick = mb_strtolower($nick);

        if ($this->existeUsuario($nick))
            return false;

        $nombre = $this->_bd->getEnlace()->escape_string(mb_substr($nombre, 0, 50));
        $nick = $this->_bd->getEnlace()->escape_string(mb_substr($nick, 0, 50));

        //usando md5
        //$contrasena=md5($this->_prefijo.$contrasena);

        //usando sha1
        $contrasena = mb_substr(sha1($contrasena), 0, 64);

        //usando blowfish
        //$contrasena= password_hash($contrasena, PASSWORD_BCRYPT);

        $consulta = "INSERT INTO  acl_usuarios (" .
            " nick,nombre,contrasenia,cod_acl_role,borrado" .
            "    ) VALUES (" .
            "'$nick', '$nombre', '$contrasena', $codRole,false)";
        $consulta = $this->_bd->crearConsulta($consulta);
        return $consulta->error() === 0;
    }

    /**
     * Obtiene el código de usuario para un nick dado
     *
     * @param string $nick nick del usuario a buscar
     * @return integer|false Devuelve el codigo del usuario o false si no lo encuentra
     */
    function getCodUsuario(string $nick): int|false
    {
        if (!$this->_hayConeccion)
            return false;

        $nick = mb_strtolower($nick);
        $nick = $this->_bd->getEnlace()->escape_string(mb_substr($nick, 0, 50));

        $consulta = "SELECT cod_acl_usuario FROM acl_usuarios WHERE nick = '$nick'";

        $resul = $this->_bd->crearConsulta($consulta)->fila();

        if ($resul === false) {
            return false; 
        }

        return intval($resul["cod_acl_usuario"]);
    }


    /**
     * Verifica si existe un usuario dado un código
     *
     * @param integer $codUsuario Código del usuario a verificar
     * @return boolean Devuelve si existe o no el usuario
     */
    function existeCodUsuario(int $codUsuario): bool
    {
        if (!$this->_hayConeccion)
            return false;

        $sentencia = "select * from acl_usuarios where cod_acl_usuario={$codUsuario}";
        $resul = $this->_bd->crearConsulta($sentencia);

        if (!$resul) return false;

        return $resul->numFilas() > 0 ? true : false;
    }

    /**
     * Verifica si existe o no un usuario con el nick dado
     *
     * @param string $nick Nick del usuario a comprobar
     * @return boolean Devuelve true si encuentra el usuario y 
     * false en caso contrario
     */
    function existeUsuario(string $nick): bool
    {
        if ($this->getCodUsuario($nick) !== false)
            return true;

        return false;
    }

    /**
     * Función que comprueba que existe un usuario y la contraseña indicada es la correcta
     *
     * @param string $nick Nick del usuario a comprobar
     * @param string $contrasena Contraseña del usuario a comprobar
     * @return boolean Devuelve true si existe el usuario y tiene la contraseña indicada. 
     * False en otro caso
     */
    function esValido(string $nick, string $contrasena): bool
    {
        if (!$this->_hayConeccion)
            return false;
        if (!$this->existeUsuario($nick)) return false;

        $nick = mb_strtolower($nick);
        $nick = $this->_bd->getEnlace()->escape_string(mb_substr($nick, 0, 50));

        $sentencia = "SELECT * from acl_usuarios where nick='{$nick}'";
        $resul = $this->_bd->crearConsulta($sentencia);

        if (!$resul) return false;

        $resul = $resul->fila();
        if ($resul["contrasenia"] !== mb_substr(sha1($contrasena), 0, 64)) return false;
        return true;
    }


    /**
     * Función que comprueba si un usuario tiene un permiso concreto
     *
     * @param integer $codUsuario Usuario a buscar
     * @param integer $numero Permiso a buscar
     * @return boolean Devuelve true si existe el usuario y tiene el permiso. 
     * False en otro caso
     */
    function getPermiso(int $codUsuario, int $numero): bool
    {
        if (!$this->_hayConeccion)
            return false;

        if ($this->existeCodUsuario($codUsuario) === false)
            return false;

        $resul = $this->getPermisos($codUsuario);

        if ($resul === false || $numero < 1 || $numero > 10)
            return false;

        return $resul[$numero];
    }

    /**
     * Función que devuelve los permisos de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @return array|false Devuelve los permisos del usuario o false si 
     * no existe el usuario
     */
    function getPermisos(int $codUsuario): array|false
    {
        if (!$this->_hayConeccion)
            return false;
        if (!$this->existeCodUsuario($codUsuario)) return false;

        $sentencia = "SELECT * from acl_usuarios where cod_acl_usuario = {$codUsuario}";
        $resul = $this->_bd->crearConsulta($sentencia);

        if (!$resul) return false;

        $resul = $resul->fila();
        $codRol = (int)$resul["cod_acl_role"];

        $sentencia = "SELECT * FROM acl_roles where cod_acl_role= {$codRol}";
        $resul = $this->_bd->crearConsulta($sentencia);

        if (!$resul) return false;

        $resul = $resul->fila();

        $array = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($resul["perm" . $i] == 1) {
                $array[] = "perm" . $i;
            }
        }

        return $array;
    }

    /**
     * Función que devuelve el nombre de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @return string|false Devuelve el nombre del usuario o false si no existe
     */
    function getNombre(int $codUsuario): string|false
    {
        if (!$this->_hayConeccion)
            return false;

        if ($this->existeCodUsuario($codUsuario) === false)
            return false;


        $consulta = "SELECT nombre FROM acl_usuarios " .
            "    WHERE cod_acl_usuario = $codUsuario";

        $resul = $this->_bd->crearConsulta($consulta)->fila();

        if (is_null($resul))
            return false;

        return $resul["nombre"];
    }

    /**
     * Devuelve si un usuario está borrado
     *
     * @param integer $codUsuario Usuario a buscar.
     * @return boolean true si el usuario existe y no está borrado.
     * False en otro caso
     */
    function getBorrado(int $codUsuario): bool
    {
        if (!$this->_hayConeccion)
            return false;

        if ($this->existeCodUsuario($codUsuario) === false)
            return false;

        $consulta = "SELECT borrado FROM acl_usuarios " .
            "    WHERE cod_acl_usuario = $codUsuario";

        $resul = $this->_bd->crearConsulta($consulta)->fila();

        if (is_null($resul))
            return false;

        return boolval($resul["borrado"]);
    }

    /**
     * Devuelve el role que tiene un usuario concreto
     *
     * @param integer $codUsuario Usuario a buscar
     * @return integer|false Devuelve el role del usuario o false si no existe.
     */
    function getUsuarioRole(int $codUsuario): int|false
    {
        if (!$this->_hayConeccion) return false;
        if (!$this->existeCodUsuario($codUsuario)) return false;

        $sentencia = "SELECT * from acl_usuarios where cod_acl_usuario = {$codUsuario}";
        $resul = $this->_bd->crearConsulta($sentencia);

        if (!$resul) return false;

        if ($resul->numFilas() > 0) {
            $resul = $resul->fila();
            return (int)$resul["cod_acl_role"];
        }

        return false;
    }

    /**
     * Función que asigna un nombre a un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param string $nombre Nombre a asignar
     * @return boolean Devuelve true si ha podido asignar el nombre, false en otro caso
     */
    function setNombre(int $codUsuario, string $nombre): bool
    {
        if (!$this->_hayConeccion)
            return false;


        $nombre = $this->_bd->getEnlace()->escape_string(mb_substr($nombre, 0, 50));


        $consulta = "UPDATE acl_usuarios SET nombre = '$nombre' " .
            "    WHERE cod_acl_usuario = '$codUsuario'";
        $this->_bd->crearConsulta($consulta);
        return true;
    }

    /**
     * Función que asigna una contraseña a un usuario
     *
     * @param integer $codUsuario usuario a buscar
     * @param string $contrasenia contraseña a asignar
     * @return boolean Devuelve true si ha podido asignar la contraseña
     * False en otro caso
     */
    function setContrasenia(int $codUsuario, string $contrasenia): bool
    {
        if (!$this->_hayConeccion)
            return false;

        if ($this->existeCodUsuario($codUsuario) === false)
            return false;

        //usando md5
        //$contrasenia=md5($this->_prefijo.$contrasenia);

        //usando sha1
        $contrasenia = mb_substr(sha1($contrasenia), 0, 64);

        //usando blowfish
        //$contrasenia= password_hash($contrasenia, PASSWORD_BCRYPT);


        $consulta = "UPDATE acl_usuarios SET contrasenia = '$contrasenia'" .
            "    WHERE cod_acl_usuario = '$codUsuario'";
        $this->_bd->crearConsulta($consulta);

        return true;
    }

    /**
     * Función que borra/desborra lógicamente un usuario 
     *
     * @param integer $codUsuario Usuario a buscar
     * @param boolean $borrado Estado a asignar
     * @return boolean Devuelve true si ha podido asignar el estado. 
     * False en otro caso
     */
    function setBorrado(int $codUsuario, bool $borrado): bool
    {
        if (!$this->_hayConeccion)
            return false;
        if ($this->existeCodUsuario($codUsuario) === false)
            return false;

        $borrado = $borrado ? "1" : "0";
        $consulta = "UPDATE acl_usuarios SET borrado = '$borrado' " .
            "    WHERE cod_acl_usuario  = '$codUsuario'";
        $this->_bd->crearConsulta($consulta);
        return true;
    }

    /**
     * Función que cambia el role de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param integer $role Role a asignar
     * @return boolean Devuelve true si ha podido asignar el role al usuario.
     * False si no existe el usuario, role o no ha podido asignarlo
     */
    function setUsuarioRole(int $codUsuario, int $role): bool
    {
        if (!$this->_hayConeccion) return false;
        if (!$this->existeCodUsuario($codUsuario)) return false;
        if (!$this->existeRole($role)) return false;

        $sentencia = "UPDATE acl_usuarios SET cod_acl_role = {$role} where cod_acl_usuario= {$codUsuario}";
        $consulta = $this->_bd->crearConsulta($sentencia);

        if (!$consulta) return false;

        return true;
    }


    /**
     * Devuelve un array con todos los usuarios existentes. 
     * La clave es el codigo de usuario, el valor es el nick del usuario 
     *
     * @return array Array con todos los usuarios existentes
     */
    function dameUsuarios(): array|false
    {
        if (!$this->_hayConeccion)
            return false;


        $consulta = "SELECT cod_acl_usuario, nick " .
            "      from acl_usuarios " .
            "ORDER BY cod_acl_usuario";

        $datos = $this->_bd->crearConsulta($consulta);
        $res = [];

        while ($fila = $datos->fila())
            $res[(int)$fila["cod_acl_usuario"]] = $fila["nick"];

        return $res;
    }

    /**
     * Devuelve un array con todos los roles existentes. 
     * La clave es el codigo de role, el valor es el nombre del role 
     *
     * @return array Array con todos los roles existentes
     */
    function dameRoles(): array
    {
        $sentencia = "SELECT * FROM acl_roles";
        $consulta = $this->_bd->crearConsulta($sentencia);

        $array = [];
        while ($fila = $consulta->fila()) {
            $array[$fila["cod_acl_role"]] = $fila["nombre"];
        }

        return $array;
    }
}