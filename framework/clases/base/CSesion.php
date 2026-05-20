<?php


class CSesion {
    public function __construct(){}

    public function crearSesion(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function haySesion() {
        return session_status() == PHP_SESSION_ACTIVE;
    }

    public function destruirSesion() {
        if($this->haySesion()) {
            session_unset();
            session_destroy();
        }
    }
}