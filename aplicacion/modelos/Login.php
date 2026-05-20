<?php

class Login extends CActiveRecord
{

    protected function fijarNombre(): string
    {
        return 'login';
    }

    protected function fijarAtributos(): array
    {
        return array("nick", "contrasenia", "cod", "nombre", "permisos", "validado");
    }

    protected function fijarDescripciones(): array
    {
        return array(
            "nick" => "Nick del usuario",
            "contrasenia" => "Contraseña del usuario"
        );
    }

    protected function fijarRestricciones(): array
    {
        return [
            [
                "ATRI" => "nick,contrasenia",
                "TIPO" => "REQUERIDO"
            ],
            [
                "ATRI" => "nick",
                "TIPO" => "CADENA",
                "TAMANIO" => 30
            ],
            [
                "ATRI" => "contrasenia",
                "TIPO" => "CADENA",
                "TAMANIO" => 65
            ],
            [
                "ATRI" => "contrasenia",
                "TIPO" => "FUNCION",
                "FUNCION" => "validaContrasenia"
            ]
        ];
    }

    protected function afterCreate():void
    {
        $this->nick="";
        $this->contrasenia="";
        $this->cod=0;
        $this->nombre="";
        $this->permisos=[];
        $this->validado=false;
    }
    protected function validaContrasenia()
    {
        $acl = Sistema::app()->acl();
        
        if (!$acl->esValido($this->nick, $this->contrasenia)) {
            $this->setError("contrasenia", "Usuario o Contraseña incorrecto");
            $this->contrasenia="";
            $this->cod=0;
            $this->nombre="";
            $this->permisos=[];
            $this->validado=false;
        }
        else
            {   // Si los datos son correctos
                $codUsuario = $acl->getCodUsuario($this->nick); // → Obtener código usuario una sola vez
                
                $this->cod = $codUsuario;
                $this->nombre = $acl->getNombre($codUsuario);
                $this->permisos = $acl->getPermisos($codUsuario);
                $this->validado=true;
            }
    }

    public function autenticar()
    {
        if (!$this->validado)
            return false;

        if (!Sistema::app()->acceso()->registrarUsuario($this->nick,$this->nombre, $this->permisos))
            return false;

        return true;
    }
}
