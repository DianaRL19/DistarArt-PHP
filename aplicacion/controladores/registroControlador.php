<?php

class registroControlador extends CControlador
{
    public array $barraMenu = [];
    public array $barraUbi = [];

    public function __construct()
    {
        $this->barraMenu = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ]
        ];
    }

    public function accionPedirDatosRegistro()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio", 
                "enlace" => "/index.php"
            ],
            [
                "texto" => "Registro",
                "enlace" => ""
            ],
        ];

        // Añadimos "Gestión Obras" si el usuario tiene Permiso 7 y/o 9
		if (Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->acceso()->puedePermiso(9)) {
                $this->barraMenu[] = [
                    "texto" => "Gestión Obras",
                    "enlace" => ["obras", "index"]
                ];
            }
            if (Sistema::app()->acceso()->puedePermiso(7)) {
                $this->barraMenu[] = [
                    "texto" => "Mis Obras",
                    "enlace" => ["obras", "index"]
                ];
            }
		}

		// Añadimos "Gestión Usuarios" si el usuario tiene Permiso 10
		if (Sistema::app()->acceso()->hayUsuario() && Sistema::app()->acceso()->puedePermiso(10)) {
			$this->barraMenu[] = [
				"texto" => "Gestión Usuarios",
				"enlace" => ["usuarios", "index"]
			];
		}

		// Añadimos "Gestión Clientes" si el usuario tiene Permiso 9,7
		if (Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->acceso()->puedePermiso(9)) {
                $this->barraMenu[] = [
                    "texto" => "Gestión Clientes",
                    "enlace" => ["clienteAPI", "index"]
                ];
            }
            if (Sistema::app()->acceso()->puedePermiso(7)) {
                $this->barraMenu[] = [
                    "texto" => "Mis Clientes",
                    "enlace" => ["clienteAPI", "index"]
                ];
            }
		}

        // Añadimos "Mis Encargos" si el usuario tiene Permiso 8 y si tiene Permiso 9, lo añadimos a Gestión Encargos
		if (Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->acceso()->puedePermiso(9)) {
                $this->barraMenu[] = [
                    "texto" => "Gestión Encargos",
                    "enlace" => ["encargos", "gestion"]
                ];
            } else if (Sistema::app()->acceso()->puedePermiso(8)) {
                $this->barraMenu[] = [
                    "texto" => "Mis Encargos",
                    "enlace" => ["encargos", "index"]
                ];
            }
		}


        $modeloFormulario = new DatosRegistro();

        $nombre = $modeloFormulario->getNombre();

        if (isset($_POST[$nombre])) {

            $modeloFormulario->setValores($_POST[$nombre]);

            // Validar datos del formulario
            if ($modeloFormulario->validar()) {

                // 1. Guardamos el perfil del usuario en la tabla usuarios
                $modeloUsuario = new Usuario();

                $modeloUsuario->nombre = $modeloFormulario->nombre;           // → Nombre
                $modeloUsuario->nick = $modeloFormulario->nick;               // → Nick
                $modeloUsuario->email = $modeloFormulario->email;             // → Email
                $modeloUsuario->descripcion = $modeloFormulario->descripcion; // → Descripción
                $modeloUsuario->direccion = $modeloFormulario->direccion;     // → Dirección
                $modeloUsuario->pais = $modeloFormulario->pais;               // → País
                $modeloUsuario->img_perfil = $modeloFormulario->img_perfil;   // → Imagen perfil
                $modeloUsuario->img_banner = $modeloFormulario->img_banner;   // → Imagen banner
                $modeloUsuario->fecha_alta = $modeloFormulario->fecha_alta;   // → Fecha de alta
                $modeloUsuario->borrado = 0;                                  // → Borrado

                if ($modeloUsuario->validar()) { // → Si es valido, lo guardamos
                    
                    if ($modeloUsuario->guardar()) { // → Si se guarda correctamente
                        
                        // 2. Guardamos las credenciales del usuario en ACL
                        
                        $acl = Sistema::app()->acl();
                        $codRolDefecto = 2; // artista
                        
                        if ($acl->anadirUsuario(
                            $modeloFormulario->nombre,
                            $modeloFormulario->nick,
                            $modeloFormulario->contrasenia,
                            $codRolDefecto
                        )) {
                            // 3. Iniciamos sesión automáticamente
                            $codUsuario = $acl->getCodUsuario($modeloFormulario->nick);
                            $permisos = $acl->getPermisos($codUsuario);
                            
                            Sistema::app()->acceso()->registrarUsuario(
                                $modeloFormulario->nick,
                                $modeloFormulario->nombre,
                                $permisos
                            );
                            
                            // 4. Redirigimos a inicio
                            Sistema::app()->irAPagina(["inicial"]);
                        } else {
                            // Si falla ACL, mostrar error
                            $modeloFormulario->setError("nick", "No se pudo registrar las credenciales del usuario.");
                        }
                    } else {
                        // Si falla el guardado del usuario, mostramos un error
                        $modeloFormulario->setError("nick", "Error al guardar el perfil en la base de datos.");
                    }
                } else {
                    // Si falla validación de usuario
                    $erroresUsuario = $modeloUsuario->getErrores();
                    foreach ($erroresUsuario as $campo => $error) {
                        $modeloFormulario->setError($campo, $error);
                    }
                }
            }
        }

        $this->dibujaVista("pedirDatosRegistro", ["modelo" => $modeloFormulario], "Registro");
    }

    public function accionLogin()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio", 
                "enlace" => "/index.php"
            ],
            [
                "texto" => "LogIn",
                "enlace" => ""
            ],
        ];

        $modelo = new Login();
        $nombre = $modelo->getNombre();

        if (isset($_POST[$nombre])) {
            $modelo->setValores($_POST[$nombre]);

            // Comprobamos si los datos son válidos 
            if ($modelo->validar()) {
                if ($modelo->autenticar())
                    {
                        // Redirigimos siempre a inicio después de login
                        Sistema::app()->irAPagina(["inicial"]);
                    }
            }
        }

        $this->dibujaVista("login", ["modelo" => $modelo], "Iniciar Sesión");
    }

    public function accionLogOut()
    {
        // si la session esta cerrada la abre
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Sistema::app()->acceso()->quitarRegistroUsuario(); // → Cerrar sesión

        Sistema::app()->irAPagina("/index.php"); // → Redirigir a inicio

    }
}
