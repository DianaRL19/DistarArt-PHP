<?php

require_once __DIR__ . "/../../scripts/librerias/peticionesCURL.php";

class encargosControlador extends CControlador
{
    public array $barraUbi = [];
    public array $barraMenu = [];

    public function __construct()
    {
        $this->barraMenu = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ]
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
                    "texto" => "Gestión Clientes API",
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
    }

    // —————————————————————————————————————————————————————————————
    //              ACCIÓN - GESTIÓN ENCARGOS (ADMIN PERMISO 9)
    // —————————————————————————————————————————————————————————————

    public function accionGestion()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",            
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Encargos",  
                "enlace" => ["encargos", "gestion"]
            ],
        ];

        // Validamos que el usuario tenga permiso 9 (administrador)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }
        if (!Sistema::app()->acceso()->puedePermiso(9)) {
            Sistema::app()->paginaError(403, "Solo los administradores pueden acceder a la gestión de encargos");
            return;
        }

        // Establecemos los parámetros de busqueda, ordenación y paginación con validación y valores por defecto    
        $busquedaNombre = $_GET["nombre_busqueda"] ?? "";
        $busquedaArtista = $_GET["artista_busqueda"] ?? "";
        $filtroEstado = intval($_GET["estado_filtro"] ?? 0);
        $mostrarEliminados = isset($_GET["borrado"]) && $_GET["borrado"] == 1; // → Filtro para mostrar eliminados
        $pagina = max(1, intval($_GET["pag"] ?? 1));

        // Montamos el array de opciones de ordenación permitidas
        $ordenesPermitidos = [
            "nombre_asc", "nombre_desc",
            "artista_asc", "artista_desc",
            "estado_asc", "estado_desc",
            "fecha_asc", "fecha_desc",
            "precio_asc", "precio_desc",
        ];

        // Indicamos la opcion seleccionada y/o el valor por defecto
        $ordenSeleccionado = in_array($_GET["orden"] ?? "", $ordenesPermitidos) ? $_GET["orden"] : "fecha_desc";

        // Creamos un array donde indicamos las opciones de ordenación a su equivalente en SQL
        $arrayOrdenCombo = [
            "nombre_asc" => "e.nombre ASC",
            "nombre_desc" => "e.nombre DESC",
            "artista_asc" => "u.nick ASC",
            "artista_desc" => "u.nick DESC",
            "estado_asc" => "e.estado ASC",
            "estado_desc" => "e.estado DESC",
            "fecha_asc" => "e.fecha_alta ASC",
            "fecha_desc" => "e.fecha_alta DESC",
            "precio_asc" => "e.precio_total ASC",
            "precio_desc" => "e.precio_total DESC",
        ];

        // _______ CONSULTA A LA BD _______
        $bd = Sistema::app()->BD();

        $sql = "SELECT e.cod_encargo, e.nombre, e.estado, e.precio_total, e.fecha_alta, e.fecha_limite,
                    u.nick AS artista_nick, u.nombre AS artista_nombre,
                    c.nombre AS cliente_nombre
                FROM encargos e
                LEFT JOIN usuarios u ON e.cod_usuario = u.cod_usuario
                LEFT JOIN clientes c ON e.cod_cliente = c.cod_cliente
                WHERE " . ($mostrarEliminados ? "e.borrado = 1" : "e.borrado = 0");

        $condiciones = [];

        if (!empty($busquedaNombre)) {
            $condiciones[] = "e.nombre LIKE '%" . CGeneral::addSlashes($busquedaNombre) . "%'";
        }
        if (!empty($busquedaArtista)) {
            $condiciones[] = "(u.nick LIKE '%" . CGeneral::addSlashes($busquedaArtista) . "%' OR u.nombre LIKE '%" . CGeneral::addSlashes($busquedaArtista) . "%')";
        }
        if ($filtroEstado > 0) {
            $condiciones[] = "e.estado = " . $filtroEstado;
        }
        if (!empty($condiciones)) {
            $sql .= " AND " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY " . $arrayOrdenCombo[$ordenSeleccionado];

        // _______ PAGINACIÓN _______

        $totalEncargos = count($bd->crearConsulta($sql)->filas() ?? []);
        $filasPorPagina = 10;
        $totalPaginas = max(1, ceil($totalEncargos / $filasPorPagina));
        if ($pagina > $totalPaginas) $pagina = $totalPaginas;

        $sql .= " LIMIT " . intval(($pagina - 1) * $filasPorPagina) . ", " . intval($filasPorPagina);
        $filas = $bd->crearConsulta($sql)->filas() ?? [];

        // _______

        $urlBase = Sistema::app()->generaURL(["encargos", "gestion"]);
        $params  = array_filter([
            "nombre_busqueda" => $busquedaNombre,
            "artista_busqueda" => $busquedaArtista,
            "estado_filtro" => $filtroEstado ?: null,
            "orden" => $ordenSeleccionado !== "fecha_desc" ? $ordenSeleccionado : null,
            "borrado" => $mostrarEliminados ? 1 : null, // → Mantener el filtro de eliminados en la paginación
        ]);
        $urlPaginador = $urlBase . (!empty($params) ? "&" . http_build_query($params) : "");

        $opcPaginador = [
            "URL" => $urlPaginador,
            "TOTAL_REGISTROS" => $totalEncargos,
            "PAGINA_ACTUAL" => $pagina,
            "REGISTROS_PAGINA" => $filasPorPagina,
            "PAGINAS_MOSTRADAS" => 5,
        ];

        // Nombres de estados para mostrar en la tabla

        $nombresEstado = [
            1 => "Lluvia de ideas", 2 => "Pruebas de diseño", 3 => "Bocetado",
            4 => "Pendiente revisión", 5 => "Corrección errores", 6 => "Desarrollo",
            7 => "Detallado", 8 => "Finalizado"
        ];

        $cabecera = [
            [
                "ETIQUETA" => "Encargo",  
                "CAMPO" => "nombre"
            ],
            [
                "ETIQUETA" => "Artista",  
                "CAMPO" => "artista_nick"
            ],
            [
                "ETIQUETA" => "Cliente",  
                "CAMPO" => "cliente_nombre"
            ],
            [
                "ETIQUETA" => "Estado",  
                "CAMPO" => "estado"
            ],
            [
                "ETIQUETA" => "Precio",  
                "CAMPO" => "precio_total"
            ],
            [
                "ETIQUETA" => "F. Alta",  
                "CAMPO" => "fecha_alta"
            ],
            [
                "ETIQUETA" => "F. Límite",
                "CAMPO" => "fecha_limite"
            ],
            [
                "ETIQUETA" => "Acciones",  
                "CAMPO" => "operaciones"
            ],
        ];

        // Procesamos las filas para mostrar los datos formateados y las operaciones disponibles
        foreach ($filas as &$fila) {
            $cod = intval($fila["cod_encargo"]);

            $fila["estado"] = htmlspecialchars($nombresEstado[$fila["estado"]] ?? "-");

            $fila["precio_total"] = number_format(floatval($fila["precio_total"]), 2, ",", ".") . " €";

            $fila["artista_nick"]    = htmlspecialchars($fila["artista_nick"] ?? "-");
            $fila["cliente_nombre"]  = htmlspecialchars($fila["cliente_nombre"] ?? "Sin cliente");

            $urlVer       = Sistema::app()->generaURL(["encargos", "gestionVer"])       . "?" . http_build_query(["cod_encargo" => $cod]);
            $urlModificar = Sistema::app()->generaURL(["encargos", "gestionModificar"]) . "?" . http_build_query(["cod_encargo" => $cod]);
            $urlBorrar    = Sistema::app()->generaURL(["encargos", "gestionBorrar"])    . "?" . http_build_query(["cod_encargo" => $cod]);

            $fila["operaciones"] =
                CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/eye.svg", "Ver", ["class" => "icono-pequeño invertir-color"]),
                    $urlVer, []) . "&nbsp;" .
                CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/pencil-square.svg", "Editar", ["class" => "icono-pequeño"]),
                    $urlModificar, []) . "&nbsp;" .
                CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/trash3-fill.svg", "Borrar", ["class" => "icono-pequeño"]),
                    $urlBorrar, []);
        }
        
        unset($fila);

        // Dibujamos la vista
        $this->dibujaVista("gestionEncargos/listar", [
            "filas" => $filas,
            "cabecera" => $cabecera,
            "opcPaginador" => $opcPaginador,
            "totalEncargos" => $totalEncargos,
            "busquedaNombre" => $busquedaNombre,
            "busquedaArtista" => $busquedaArtista,
            "filtroEstado" => $filtroEstado,
            "mostrarEliminados" => $mostrarEliminados,
            "ordenSeleccionado" => $ordenSeleccionado,
            "nombresEstado" => $nombresEstado,
            "barraUbi" => $this->barraUbi,
            "barraMenu" => $this->barraMenu,
        ], "Gestión de Encargos");
    }

    // ═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    //                                       ACCIONES PARA ARTISTAS (PERMISO 8)
    // ═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // Los artstas pueden gestionar sus propios encargos:
    //  - accionIndex() → Listar mis encargos
    //  - accionCrear() → Crear nuevo encargo
    //  - accionVer()   → Ver detalle de mi encargo
    //  - accionModificar() → Editar mi encargo
    //  - accionBorrar() → Borrar mi encargo



    // —————————————————————————————————————————————————————————————
    //                    ACCIÓN - INDEX (LISTAR ENCARGOS)
    // —————————————————————————————————————————————————————————————

    public function accionIndex()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mis Encargos",
                "enlace" => ["encargos", "index"]
            ]
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)
        if (!Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["encargos", "index"];
            }
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        
        if (!$tienePermiso8) {
            Sistema::app()->paginaError(403, "No tienes permisos para acceder a encargos");
            return;
        }

        // Obtenemos el código del usuario conectado para mostrar solo sus encargos
        $nick = Sistema::app()->acceso()->getNick();
        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);

        // Consultamos a la BD los encargos del usuario ordenados por estado y fecha de alta
        $bd = Sistema::app()->BD();
        $sql = "SELECT e.*, 
                c.nombre as cliente_nombre, 
                c.email as cliente_email,
                c.direccion as cliente_direccion,
                c.presupuesto as cliente_presupuesto
                FROM encargos e 
                LEFT JOIN clientes c ON e.cod_cliente = c.cod_cliente
                WHERE e.cod_usuario = " . intval($codUsuario) . " AND e.borrado = 0 
                ORDER BY e.estado ASC, e.fecha_alta DESC";

        $cmd = $bd->crearConsulta($sql);
        $encargos = $cmd->filas() ?? [];

        // Dibujamos la vista
        $this->dibujaVista("index", [
            "encargos" => $encargos,
            "barraUbi" => $this->barraUbi,
            "barraMenu" => $this->barraMenu
        ], "Mis Encargos");
    }

    // —————————————————————————————————————————————————————————————
    //                    ACCIÓN - CREAR ENCARGO
    // —————————————————————————————————————————————————————————————
    // La idea era crear una acción de creación clientes para que si un artista o un administrador quería crear un encargo 
    // para un cliente que no existiera, pudiera crear el cliente desde la misma gestión de encargos. Pero al final mirando 
    // los requisitos me di cuenta de que no se podian hacer controladores complejos  y que tenia que haber "un controlador 
    // por cada elemento gestionado", por lo que no podía hacer una accion de creacion de clientes dentro del controlador 
    // de encargos ( y he de decir que me funcionaba y quedaba muy chulo pero bueno). Por eso al final lo he borrado y he 
    // dejado que la creación de clientes solo se haga desde el controlador de clientes (clienteAPIControlador).

    public function accionCrear()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mis Encargos",
                "enlace" => ["encargos", "index"]
            ],
            [
                "texto" => "Crear Encargo",
                "enlace" => ["encargos", "crear"]
            ]
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["encargos", "crear"];
            }
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        
        if (!$tienePermiso8) {
            Sistema::app()->paginaError(403, "No tienes permisos para crear encargos");
            return;
        }

        // Obtenemos el codigo del usuario conectado para asignarle el encargo
        $nick = Sistema::app()->acceso()->getNick();
        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);

        if ($_POST) {
            $encargo = new Encargos();

            // Le metemos los datos al encargo
            $encargo->cod_usuario = $codUsuario;
            $encargo->cod_cliente = intval($_POST["Encargo"]["cod_cliente"] ?? 0);
            $encargo->nombre = $_POST["Encargo"]["nombre"] ?? "";
            $encargo->descripcion = $_POST["Encargo"]["descripcion"] ?? "";
            $encargo->estado = 1; // → Estado inicial: "En proceso"
            $encargo->precio_base = floatval($_POST["Encargo"]["precio_base"] ?? 0);
            $encargo->iva = floatval($_POST["Encargo"]["iva"] ?? 0);
            $encargo->precio_total = $encargo->precio_base + ($encargo->precio_base * ($encargo->iva / 100));
            $encargo->fecha_alta = date("Y-m-d");
            $encargo->fecha_limite = $_POST["Encargo"]["fecha_limite"] ?? null;
            $encargo->borrado = 0;

            // ________________________________________________________
            // _______ VALIDACIÓN PRESUPUESTO CLIENTE (VÍA API) _______

            $errorPresupuesto = "";

            if ($encargo->cod_cliente) {

                $baseURL  = Sistema::app()->generaURL(["api", "clientes"]);

                $urlAPI   = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;
                $cabecera = ["Cookie: " . session_name() . "=" . session_id()];

                session_write_close();

                $respPres  = petCURLGet($urlAPI, "cod_cliente=" . intval($encargo->cod_cliente), $cabecera);

                $datosPres = json_decode($respPres, true);

                session_start();

                // Validamos que el cliente pertenezca al artista
                $codClienteAPI = intval($datosPres["datos"]["cod_cliente"] ?? 0);
                $codUsuarioAPI = intval($datosPres["datos"]["cod_usuario"] ?? 0);
                
                if ($codClienteAPI !== $encargo->cod_cliente || $codUsuarioAPI !== $encargo->cod_usuario) {
                    $errorPresupuesto = "El cliente seleccionado no pertenece a este artista";
                    $encargo->setError("cod_cliente", $errorPresupuesto);
                }

                $presupuesto = floatval($datosPres["datos"]["presupuesto"] ?? 0);

                if ($presupuesto > 0 && $encargo->precio_total > $presupuesto) {
                    $errorPresupuesto = "El precio total (" . number_format($encargo->precio_total, 2, ',', '.') . " €) supera el presupuesto del cliente (" . number_format($presupuesto, 2, ',', '.') . " €)";
                    $encargo->setError("precio_base", $errorPresupuesto);
                }
            }

            // Si no hay errores de presupuesto y el encargo se guarda correctamente, redirigimos al index.
            // Si hay errores, se los mostramnos en la vista.

            if (empty($errorPresupuesto) && $encargo->guardar()) {
                
                // —————————————————————————————————————————————
                //    CREAR CARPETA PARA IMÁGENES DEL ENCARGO
                // —————————————————————————————————————————————
                
                // Generamos nombre de carpeta:
                //  - Nombre del encargo en minúsculas y sin espacios
                $nombreCarpeta = strtolower(str_replace([' ', '/', '\\', '"', "'"], '_', $encargo->nombre));
                $rutaCarpeta = RUTA_BASE . "/imagenes/encargos/" . $nombreCarpeta;
                
                // Si no existe la carpeta, la creamos con permisos 0755 (lectura y escritura)
                if (!is_dir($rutaCarpeta)) {
                    mkdir($rutaCarpeta, 0755, true);  // → Creamos la carpeta recursivamente
                }
                
                Sistema::app()->irAPagina(["encargos", "index"]);
                return;
            } else {
                $errores = $encargo->getErrores() ?? [];
                if (!empty($errorPresupuesto)) {
                    $errores["precio_base"] = $errorPresupuesto;
                }
            }
        }

        // _______ OBTENEMOS LOS CLIENTES (por la API) _______

        $clientesCombo = []; // → Array para el combo de cliente

        $baseURL  = Sistema::app()->generaURL(["api", "clientes"]);

        $urlAPI   = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;
        $cabecera = ["Cookie: " . session_name() . "=" . session_id()];
        
        $params   = http_build_query(["ordenar_por" => "nombre", "orden" => "ASC", "pagina" => 1, "registros_pagina" => 50]);

        session_write_close(); // → Cerramos la sesión para evitar bloqueos al llamar a la API

        // Llamamos a la API para obtener los clientes y los preparamos para el combo
        $respuestaAPI = petCURLGet($urlAPI, $params, $cabecera); 
        $datosAPI     = json_decode($respuestaAPI, true);

        // Volvemos a abrir la sesión para poder usarla en la vista
        session_start();

        if ($datosAPI && !empty($datosAPI["correcto"])) {
            foreach ($datosAPI["datos"]["clientes"] ?? [] as $cli) {
                $etiqueta = $cli["nombre"];
                if (!empty($cli["email"])) {
                    $etiqueta .= " (" . $cli["email"] . ")";
                }
                $clientesCombo[$cli["cod_cliente"]] = $etiqueta;
            }
        }

        // Dibujamos la vista
        $this->dibujaVista("crear", [
            "clientesCombo"         => $clientesCombo,
            "clienteIdSeleccionado" => "",
            "encargo"               => $encargo ?? new Encargos(),
            "barraUbi"              => $this->barraUbi,
            "barraMenu"             => $this->barraMenu,
            "errores"               => $errores ?? []
        ], "Crear Encargo");
    }

    // —————————————————————————————————————————————————————————————
    //                    ACCIÓN - MODIFICAR ENCARGO
    // —————————————————————————————————————————————————————————————

    public function accionModificar()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mis Encargos",
                "enlace" => ["encargos", "index"]
            ],
            [
                "texto" => "Modificar Encargo",
                "enlace" => ["encargos", "modificar"]
            ]
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["encargos", "modificar"];
            }
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        
        if (!$tienePermiso8) {
            Sistema::app()->paginaError(403, "No tienes permisos para modificar encargos");
            return;
        }

        // _______ OBTENER USUARIO Y ENCARGO _______

        $nick = Sistema::app()->acceso()->getNick();
        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
        $codEncargo = $_GET["cod_encargo"] ?? null;

        if (!$codEncargo) {
            Sistema::app()->paginaError(400, "Encargo no especificado");
            return;
        }

        // Buscamos el encargo y verificamos que pertenezca al usuario
        $encargo = new Encargos();
        if (!$encargo->buscarPor(["where" => "cod_encargo = " . intval($codEncargo) . " AND cod_usuario = " . intval($codUsuario)])) {
            Sistema::app()->paginaError(404, "Encargo no encontrado");
            return;
        }

        // Si la imagen está vacía, asignamos la de por defecto que es una con un dibujito por defecto
        if (empty($encargo->imagen_proceso)) {
            $encargo->imagen_proceso = "EncargoDefault.png";
        }

        // Comprobamos que vengan los datos por POST para modificar el encargo
        if ($_POST) {
            $encargo->nombre = $_POST["Encargo"]["nombre"] ?? $encargo->nombre;
            $encargo->descripcion = $_POST["Encargo"]["descripcion"] ?? $encargo->descripcion;
            $encargo->estado = $_POST["Encargo"]["estado"] ?? $encargo->estado;
            $encargo->precio_base = floatval($_POST["Encargo"]["precio_base"] ?? $encargo->precio_base);
            $encargo->iva = floatval($_POST["Encargo"]["iva"] ?? $encargo->iva);
            $encargo->precio_total = $encargo->precio_base + ($encargo->precio_base * ($encargo->iva / 100));
            
            //  Convertimos la fecha de Y-m-d a d/m/Y 

            if (!empty($_POST["Encargo"]["fecha_limite"])) {
                $fechaObj = DateTime::createFromFormat('Y-m-d', $_POST["Encargo"]["fecha_limite"]);
                if ($fechaObj) {
                    $encargo->fecha_limite = $fechaObj->format('d/m/Y');
                } else {
                    $encargo->fecha_limite = $encargo->fecha_limite;
                }
            } else {
                $encargo->fecha_limite = $encargo->fecha_limite;
            }
            
            $encargo->comentarios = $_POST["Encargo"]["comentarios"] ?? $encargo->comentarios; // → Guardamos los comentarios/notas
            
            if (!empty($_FILES["imagen_proceso"]["name"])) { // → Si se sube una imagen nueva
                $rutaDestino = "imagenes/encargos/". str_replace(' ', '_', mb_strtolower($encargo->nombre)); // → Ruta donde guardar la imagen (carpeta dedicada a encargos)
                
                // Creamos la carpeta si no existe
                if (!is_dir($rutaDestino)) {
                    mkdir($rutaDestino, 0755, true); // → Creamos la carpeta con permisos 755
                }
                
                $nombreArchivo = $_FILES["imagen_proceso"]["name"]; // → Nombre original del archivo
                $tiposPermitidos = ["image/jpeg", "image/png"]; // → Tipos permitidos
                $tamMaximo = 5 * 1024 * 1024; // → de 5MB máximo
                
                // Array con los nombres de los estados
                $estadosNombres = [
                    1 => "lluvia_ideas",
                    2 => "pruebas_diseno",
                    3 => "bocetado",
                    4 => "pendiente_revision",
                    5 => "correccion_errores",
                    6 => "desarrollo",
                    7 => "detallado",
                    8 => "finalizado"
                ];
                
                // validamos los tipos de archivo
                if (!in_array($_FILES["imagen_proceso"]["type"], $tiposPermitidos)) {

                    // Si el tipo no es válido, mantener imagen actual o le asignamos la de por defecto si no tenía
                    $encargo->imagen_proceso = $encargo->imagen_proceso ?? "EncargoDefault.png";

                } else if ($_FILES["imagen_proceso"]["size"] > $tamMaximo) { // → Validamos el tamaño
                    
                    // Si el tamaño se pasa del límite, mantener imagen actual o le asignamos la de por defecto si no tenía
                    $encargo->imagen_proceso = $encargo->imagen_proceso ?? "EncargoDefault.png";

                } else {

                    // Incrementamos la versión cada vez que se sube una imagen
                    $encargo->version = intval($encargo->version) + 1; // → Incrementamos la versión
                    
                    // Generamos el nombre con formato: nombreObra_nombreEstado_version.extension
                    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

                    $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $encargo->nombre)); // → Limpiamos el nombre
                    $estadoNombre = $estadosNombres[$encargo->estado] ?? "desconocido"; // → Obtenemos el nombre del estado
                    $version = intval($encargo->version); // → Versión actualizada
                    
                    $nombreUnico = mb_strtolower($nombreLimpio) . "_" . mb_strtolower($estadoNombre) . "_" . $version . "." . $extension;
                    $rutaCompleta = $rutaDestino . "/" . $nombreUnico;
                    
                    // Mover archivo a la carpeta de destino
                    if (move_uploaded_file($_FILES["imagen_proceso"]["tmp_name"], $rutaCompleta)) {
                        $encargo->imagen_proceso = $nombreUnico; // → Guardar nombre del archivo con formato correcto

                    } else {
                        // Si falla la subida, echamos para atras la versión y mantenemos la imagen que tenia o le asignamos la de por defecto si no tenía
                        $encargo->version = intval($encargo->version) - 1; // → Revertimos la versión
                        $encargo->imagen_proceso = $encargo->imagen_proceso ?? "EncargoDefault.png";
                    }
                }
            } else { // → Si no se sube imagen, le dejamos la que tenia o la de por defecto si no tenia
                $encargo->imagen_proceso = $encargo->imagen_proceso ?? "EncargoDefault.png"; // → Mantener imagen actual o usar por defecto
            }

            if ($encargo->validar()) {
                if ($encargo->guardar()) {
                    Sistema::app()->irAPagina(["encargos", "index"]);
                    return;
                }
            }
            $errores = $encargo->getErrores();
        }

        // _______ OBTENER DATOS DEL CLIENTE DEL ENCARGO (por la API) _______

        $clienteActual = [];

        if (!empty($encargo->cod_cliente)) {

            $baseURL = Sistema::app()->generaURL(["api", "clientes"]);

            $urlAPI  = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;
            $cabecera = ["Cookie: " . session_name() . "=" . session_id()];

            session_write_close();

            $respCliente  = petCURLGet($urlAPI, "cod_cliente=" . intval($encargo->cod_cliente), $cabecera);

            $datosCliente = json_decode($respCliente, true);

            session_start();

            if ($datosCliente && !empty($datosCliente["correcto"])) {
                $clienteActual = $datosCliente["datos"] ?? [];
            }
        }

        // Dibujamos la vista
        $this->dibujaVista("modificar", [
            "encargo"      => $encargo,
            "clienteActual" => $clienteActual,
            "barraUbi"     => $this->barraUbi,
            "barraMenu"    => $this->barraMenu,
            "errores"      => $errores ?? []
        ], "Modificar Encargo");
    }

    // —————————————————————————————————————————————————————————————
    //                    ACCIÓN - VER ENCARGO
    // —————————————————————————————————————————————————————————————

    public function accionVer()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mis Encargos",
                "enlace" => ["encargos", "index"]
            ],
            [
                "texto" => "Ver Encargo",
                "enlace" => ["encargos", "ver"]
            ]
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["encargos", "ver"];
            }
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        
        if (!$tienePermiso8) {
            Sistema::app()->paginaError(403, "No tienes permisos para ver los encargos");
            return;
        }

        // Obtenemos el código del usuario conectado y el código del encargo a mostrar
        $nick = Sistema::app()->acceso()->getNick();
        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
        $codEncargo = $_GET["cod_encargo"] ?? null; // → Obtenemos el código del encargo de la URL

        if (!$codEncargo) {
            Sistema::app()->paginaError(400, "Encargo no especificado");
            return;
        }

        // Buscamos el encargo
        $encargo = new Encargos();
        if (!$encargo->buscarPor(["where" => "cod_encargo = " . intval($codEncargo) . " AND cod_usuario = " . intval($codUsuario)])) {
            Sistema::app()->paginaError(404, "Encargo no encontrado");
            return;
        }

        // Obtenemos los datos del encargo y del cliente como arrays

        $bd = Sistema::app()->BD();
        
        // Obtenemos el encargo completo y lo convertimos en un array
        $sqlEncargo = "SELECT * FROM encargos WHERE cod_encargo = " . intval($codEncargo);

        $cmdEncargo = $bd->crearConsulta($sqlEncargo);

        $encargoData = $cmdEncargo->filas();

        $encargoArray = $encargoData[0] ?? [];

        // Obtenemos el cliente asociado al encargo (si tiene) y lo convertimos en un array

        $cliente = [];

        if ($encargo->cod_cliente) {
            $sql = "SELECT cod_cliente, nombre, email, direccion, pais, presupuesto FROM clientes WHERE cod_cliente = " . intval($encargo->cod_cliente) . " AND cod_usuario = " . intval($codUsuario) . " AND borrado = 0";
            $cmd = $bd->crearConsulta($sql);
            $clienteData = $cmd->filas();
            $cliente = $clienteData[0] ?? [];
        }

        // Dibujamos la vista
        $this->dibujaVista("ver", [
            "encargo" => $encargoArray,
            "cliente" => $cliente,
            "barraUbi" => $this->barraUbi,
            "barraMenu" => $this->barraMenu
        ], "Ver Encargo");
    }

    // —————————————————————————————————————————————————————————————
    //                    ACCIÓN - BORRAR ENCARGO
    // —————————————————————————————————————————————————————————————

    public function accionBorrar()
    {
        // Validamos que el usuario tenga permiso 8 (usuario)
        if (!Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["encargos", "index"];
            }
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        
        if (!$tienePermiso8) {
            Sistema::app()->paginaError(403, "No tienes permisos para borrar encargos");
            return;
        }

        // Obtenemos el código del usuario conectado y el código del encargo a mostrar
        $nick = Sistema::app()->acceso()->getNick();

        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
        $codEncargo = $_POST["cod_encargo"] ?? $_GET["cod_encargo"] ?? null;

        if (!$codEncargo) {
            Sistema::app()->irAPagina(["encargos", "index"]);
            return;
        }

        // Buscamos el encargo
        $encargo = new Encargos();
        if (!$encargo->buscarPor(["where" => "cod_encargo = " . intval($codEncargo) . " AND cod_usuario = " . intval($codUsuario)])) {
            Sistema::app()->paginaError(404, "Encargo no encontrado");
            return;
        }

        // Comprobamos si vienen datos por POST para marcar el encargo como borrado
        if ($_POST) {

            // Marcamos el encargo como borrado
            $encargo->borrado = 1;

            if ($encargo->guardar()) {
                Sistema::app()->irAPagina(["encargos", "index"]);
                return;
            }
        }

        // _______ MOSTRAR LA CONFIRMACIÓN _______

        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mis Encargos",
                "enlace" => ["encargos", "index"]
            ],
            [
                "texto" => "Confirmar Borrado",
                "enlace" => ["encargos", "borrar"]
            ]
        ];

        // Dibujamos la vista
        $this->dibujaVista("borrar", [
            "encargo" => $encargo,
            "barraUbi" => $this->barraUbi,
            "barraMenu" => $this->barraMenu
        ], "Confirmar Borrado de Encargo");
    }

    // —————————————————————————————————————————————————————————————
    //                  ACCIÓN - LIMPIAR FILTROS
    // —————————————————————————————————————————————————————————————

    public function accionLimpiarFiltros()
    {
        $_SESSION["nombre_busqueda"] = "";
        $_SESSION["estado_busqueda"] = "";
        Sistema::app()->irAPagina(["encargos", "index"]);
    }


    // ═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    //                                    ACCIONES PARA ADMINISTRADORES (PERMISO 9)
    // ═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    // Administradores pueden gestionar TODOS los encargos de todos los artistas:
    // - accionGestion() → Listar todos los encargos existentes con filtros de busqueda y ordenación
    // - accionGestionCrear() → Crear nuevo encargo asignando artista y cliente
    // - accionGestionVer() → Ver detalle de cualquier encargo
    // - accionGestionModificar() → Editar cualquier encargo
    // - accionGestionBorrar() → Borrar cualquier encargo
    // ═══════════════════════════════════════════════════════════════════════════════════════════════════════════════════

    // —————————————————————————————————————————————————————————————
    //                    ACCIÓN - VER ENCARGO
    // —————————————————————————————————————————————————————————————

    public function accionGestionVer()
    {
        $this->barraUbi = [
            ["texto" => "Inicio",            "enlace" => ["inicial"]],
            ["texto" => "Gestión Encargos",  "enlace" => ["encargos", "gestion"]],
            ["texto" => "Ver Encargo",       "enlace" => ["encargos", "gestionVer"]],
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }
        if (!Sistema::app()->acceso()->puedePermiso(9)) {
            Sistema::app()->paginaError(403, "Solo los administradores pueden acceder aquí");
            return;
        }

        // Obtenemos el código del encargo a mostrar

        $codEncargo = intval($_GET["cod_encargo"] ?? 0);
        if (!$codEncargo) {
            Sistema::app()->paginaError(400, "Encargo no especificado");
            return;
        }

        // Sacamos todos los encargos con su usuario (artista) asociado.
        $bd  = Sistema::app()->BD();
        $sql = "SELECT e.*, u.nick AS artista_nick, u.nombre AS artista_nombre, u.email AS artista_email
                FROM encargos e
                LEFT JOIN usuarios u ON e.cod_usuario = u.cod_usuario
                WHERE e.cod_encargo = " . $codEncargo . " AND e.borrado = 0";
        $filas = $bd->crearConsulta($sql)->filas();

        if (empty($filas)) {
            Sistema::app()->paginaError(404, "Encargo no encontrado");
            return;
        }

        $encargoArray = $filas[0];

        // Buscamos el cliente asociado (si es que tiene)
        $cliente = [];
        if (!empty($encargoArray["cod_cliente"])) {
            $sqlCli = "SELECT cod_cliente, nombre, email, direccion, pais, presupuesto
                        FROM clientes
                        WHERE cod_cliente = " . intval($encargoArray["cod_cliente"]) . " AND borrado = 0";
            $filaCli = $bd->crearConsulta($sqlCli)->filas();
            $cliente = $filaCli[0] ?? [];
        }

        // Dibujamos la vista
        $this->dibujaVista("gestionEncargos/ver", [
            "encargo"   => $encargoArray,
            "cliente"   => $cliente,
            "barraUbi"  => $this->barraUbi,
            "barraMenu" => $this->barraMenu,
        ], "Ver Encargo");
    }

    // —————————————————————————————————————————————————————————————
    //                 ACCIÓN - MODIFICAR ENCARGO
    // —————————————————————————————————————————————————————————————

    public function accionGestionModificar()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",            
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Encargos",  
                "enlace" => ["encargos", "gestion"]
            ],
            [
                "texto" => "Modificar Encargo", 
                "enlace" => ["encargos", "gestionModificar"]
            ],
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }
        if (!Sistema::app()->acceso()->puedePermiso(9)) {
            Sistema::app()->paginaError(403, "Solo los administradores pueden modificar encargos de otros artistas");
            return;
        }

        // Obtenemos el código del encargo a modificar
        $codEncargo = intval($_GET["cod_encargo"] ?? 0);
        if (!$codEncargo) {
            Sistema::app()->paginaError(400, "Encargo no especificado");
            return;
        }

        $encargo = new Encargos();

        if (!$encargo->buscarPor(["where" => "cod_encargo = " . $codEncargo . " AND borrado = 0"])) {
            Sistema::app()->paginaError(404, "Encargo no encontrado");
            return;
        }

        if (empty($encargo->imagen_proceso)) {
            $encargo->imagen_proceso = "EncargoDefault.png";
        }

        // Comprobamos que vengan datos por POST para modificar el encargo
        if ($_POST) {
            $encargo->nombre = $_POST["Encargo"]["nombre"] ?? $encargo->nombre;
            $encargo->descripcion = $_POST["Encargo"]["descripcion"]  ?? $encargo->descripcion;
            $encargo->estado = $_POST["Encargo"]["estado"] ?? $encargo->estado;
            $encargo->precio_base = floatval($_POST["Encargo"]["precio_base"] ?? $encargo->precio_base);
            $encargo->iva = floatval($_POST["Encargo"]["iva"] ?? $encargo->iva);
            $encargo->precio_total = $encargo->precio_base + ($encargo->precio_base * ($encargo->iva / 100));
            
            // Convertimos la fecha de Y-m-d a d/m/Y
            
            if (!empty($_POST["Encargo"]["fecha_limite"])) {
                $fechaObj = DateTime::createFromFormat('Y-m-d', $_POST["Encargo"]["fecha_limite"]);
                if ($fechaObj) {
                    $encargo->fecha_limite = $fechaObj->format('d/m/Y');
                } else {
                    $encargo->fecha_limite = $encargo->fecha_limite;
                }
            } else {
                $encargo->fecha_limite = $encargo->fecha_limite;
            }
            
            $encargo->comentarios = $_POST["Encargo"]["comentarios"] ?? $encargo->comentarios;

            // Comprobamos si se ha subido una imagen nueva para el proceso y la guardamos en:
            // - Una carpeta con su nombre
            // - Con un nombre único basado en el nombre del encargo, su estado y una versión 
            //   que se irá incrementando cada vez que se suba una imagen nueva para ese encargo.
            
            if (!empty($_FILES["imagen_proceso"]["name"])) {

                // Generamos la ruta de destino: "imagenes/encargos/nombre_encargo/"
                $rutaDestino = "imagenes/encargos/" . str_replace(' ', '_', mb_strtolower($encargo->nombre));
                
                // Si la carpeta no existe, la creamos con permisos 0755 (lectura/escritura)
                if (!is_dir($rutaDestino)) {
                    mkdir($rutaDestino, 0755, true);
                }

                $tiposPermitidos = ["image/jpeg", "image/png"];
                $tamMaximo = 5 * 1024 * 1024;

                // Validamos que el archivo subido sea una imagen válida y no supere el tamaño máximo
                if (in_array($_FILES["imagen_proceso"]["type"], $tiposPermitidos) && $_FILES["imagen_proceso"]["size"] <= $tamMaximo) {
                    
                    // Array con los nombres de los estados para generar el nombre del archivo
                    $estadosNombres = [
                        1 => "lluvia_ideas", 2 => "pruebas_diseno", 3 => "bocetado",
                        4 => "pendiente_revision", 5 => "correccion_errores", 6 => "desarrollo",
                        7 => "detallado", 8 => "finalizado"
                    ];

                    // Incrementamos la versión del encargo cada vez que se sube una imagen nueva para ese encargo
                    $encargo->version = intval($encargo->version) + 1;

                    // Generamos el nombre del archivo con formato: nombreEncargo_estado_version.extension
                    $extension    = strtolower(pathinfo($_FILES["imagen_proceso"]["name"], PATHINFO_EXTENSION));

                    // Limpiamos el nombre del encargo
                    $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $encargo->nombre));

                    // Obtenemos el nombre del estado actual del encargo para incluirlo en el nombre del archivo
                    $estadoNombre = $estadosNombres[$encargo->estado] ?? "desconocido";

                    // Montamos el nombre del archivo
                    $nombreUnico  = mb_strtolower($nombreLimpio) . "_" . $estadoNombre . "_" . $encargo->version . "." . $extension;
                    
                    // Intentamos mover el archivo subido a la ruta de destino con el nombre único generado
                    if (move_uploaded_file($_FILES["imagen_proceso"]["tmp_name"], $rutaDestino . "/" . $nombreUnico)) {
                        $encargo->imagen_proceso = $nombreUnico;
                    } else {
                        // Si no se puede mover el archivo, revertimos la versión del encargo
                        $encargo->version = intval($encargo->version) - 1;
                    }
                }
            }

            if ($encargo->validar()) {
                if ($encargo->guardar()) {
                    Sistema::app()->irAPagina(["encargos", "gestion"]);
                    return;
                }
            }
            $errores = $encargo->getErrores();
        }

        // Buscamos el cliente asociado al encargo para mostrarlo a través de la API
        $clienteActual = [];

        if (!empty($encargo->cod_cliente)) {
            $baseURL  = Sistema::app()->generaURL(["api", "clientes"]);

            // Para llamar a la API necesitamos pasar la cookie de sesión para que nos devuelva solo los clientes del usuario 
            // conectado (o el cliente concreto si se pasa el cod_cliente)

            $urlAPI   = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;
            $cabecera = ["Cookie: " . session_name() . "=" . session_id()];

            session_write_close(); // → Cerramos la sesión para evitar bloqueos mientras hacemos la petición a la API

            // Hacemos la petición a la API para obtener los datos del cliente asociado al encargo
            $respCliente  = petCURLGet($urlAPI, "cod_cliente=" . intval($encargo->cod_cliente), $cabecera);

            $datosCliente = json_decode($respCliente, true);

            session_start(); // → Volvemos a iniciar la sesión después de la petición a la API

            if ($datosCliente && !empty($datosCliente["correcto"])) {
                $clienteActual = $datosCliente["datos"] ?? [];
            }
        }

        // Buscamos el artista del encargo para mostrarlo
        $bd  = Sistema::app()->BD();
        $sqlArtista = "SELECT nick, nombre, email FROM usuarios WHERE cod_usuario = " . intval($encargo->cod_usuario);
        $filaArtista = $bd->crearConsulta($sqlArtista)->filas();
        $artista = $filaArtista[0] ?? [];

        // Dibujamos la vista
        $this->dibujaVista("gestionEncargos/modificar", [
            "encargo" => $encargo,
            "clienteActual" => $clienteActual,
            "artista" => $artista,
            "barraUbi" => $this->barraUbi,
            "barraMenu" => $this->barraMenu,
            "errores" => $errores ?? [],
        ], "Modificar Encargo");
    }

    // —————————————————————————————————————————————————————————————
    //                  ACCIÓN - BORRAR ENCARGO 
    // —————————————————————————————————————————————————————————————

    public function accionGestionBorrar()
    {
        // Validamos que el usuario tenga permiso 8 (usuario)

        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }
        if (!Sistema::app()->acceso()->puedePermiso(9)) {
            Sistema::app()->paginaError(403, "Solo los administradores pueden borrar encargos de otros artistas");
            return;
        }

        // Obtenemos el código del encargo a borrar
        $codEncargo = intval($_POST["cod_encargo"] ?? $_GET["cod_encargo"] ?? 0);
        if (!$codEncargo) {
            Sistema::app()->irAPagina(["encargos", "gestion"]);
            return;
        }

        $encargo = new Encargos();
        if (!$encargo->buscarPor(["where" => "cod_encargo = " . $codEncargo . " AND borrado = 0"])) {
            Sistema::app()->paginaError(404, "Encargo no encontrado");
            return;
        }

        // Comprobamos si vienen datos por POST para marcar el encargo como borrado
        if ($_POST) {
            $encargo->borrado = 1;
            if ($encargo->guardar()) {
                Sistema::app()->irAPagina(["encargos", "gestion"]);
                return;
            }
        }

        // Mostramos la confirmación de borrado
        $this->barraUbi = [
            [
                "texto" => "Inicio",            
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Encargos",  
                "enlace" => ["encargos", "gestion"]
            ],
            [
                "texto" => "Confirmar Borrado", 
                "enlace" => ["encargos", "gestionBorrar"]
            ],
        ];

        // Dibujamos la vista
        $this->dibujaVista("gestionEncargos/borrar", [
            "encargo" => $encargo,
            "barraUbi" => $this->barraUbi,
            "barraMenu" => $this->barraMenu,
        ], "Confirmar Borrado");
    }

    // —————————————————————————————————————————————————————————————
    //                      ACCIÓN - CREAR ENCARGO 
    // —————————————————————————————————————————————————————————————

    // La idea era crear una acción de creación clientes para que si un artista o un administrador quería crear un encargo 
    // para un cliente que no existiera, pudiera crear el cliente desde la misma gestión de encargos. Pero al final mirando 
    // los requisitos me di cuenta de que no se podian hacer controladores complejos  y que tenia que haber "un controlador 
    // por cada elemento gestionado", por lo que no podía hacer una accion de creacion de clientes dentro del controlador 
    // de encargos ( y he de decir que me funcionaba y quedaba muy chulo pero bueno). Por eso al final lo he borrado y he 
    // dejado que la creación de clientes solo se haga desde el controlador de clientes (clienteAPIControlador).
    
    public function accionGestionCrear()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",            
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Encargos",  
                "enlace" => ["encargos", "gestion"]
            ],
            [
                "texto" => "Crear Encargo",     
                "enlace" => ["encargos", "gestionCrear"]
            ],
        ];

        // Validamos que el usuario tenga permiso 8 (usuario)
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }
        if (!Sistema::app()->acceso()->puedePermiso(9)) {
            Sistema::app()->paginaError(403, "Solo los administradores pueden crear encargos para otros artistas");
            return;
        }

        $errores = [];
        $encargo = new Encargos();

        // Comprobamos que vengan datos por POST para crear el encargo
        if ($_POST) {
            $encargo->cod_usuario = intval($_POST["Encargo"]["cod_usuario"] ?? 0); // → Artista seleccionado en el combo
            $encargo->cod_cliente = intval($_POST["Encargo"]["cod_cliente"] ?? 0); // → Cliente del artista seleccionado en el combo
            $encargo->nombre = $_POST["Encargo"]["nombre"] ?? "";
            $encargo->descripcion = $_POST["Encargo"]["descripcion"] ?? "";
            $encargo->estado = 1;
            $encargo->precio_base = floatval($_POST["Encargo"]["precio_base"] ?? 0);
            $encargo->iva = floatval($_POST["Encargo"]["iva"] ?? 0);
            $encargo->precio_total = $encargo->precio_base + ($encargo->precio_base * ($encargo->iva / 100));
            $encargo->fecha_alta = date("Y-m-d");
            $encargo->fecha_limite = $_POST["Encargo"]["fecha_limite"] ?? null;
            $encargo->borrado = 0;

            if (!$encargo->cod_usuario) {
                $errores["cod_usuario"] = "Debes seleccionar un artista";
            }

            // _______ VALIDACIÓN PRESUPUESTO CLIENTE (por la API) _______

            if ($encargo->cod_cliente) {

                // Para validar el presupuesto del cliente, necesitamos llamar a la API para obtener los datos del cliente 
                // y comparar su presupuesto con el precio total del encargo.
                $baseURL  = Sistema::app()->generaURL(["api", "clientes"]);

                // Para llamar a la API necesitamos pasar la cookie de sesión para que nos devuelva solo los clientes del 
                // usuario conectado (o el cliente concreto si se pasa el cod_cliente)
                $urlAPI   = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;
                $cabecera = ["Cookie: " . session_name() . "=" . session_id()];

                session_write_close(); // → Cerramos la sesión para evitar bloqueos mientras hacemos la petición a la API

                // Hacemos la petición a la API para obtener los datos del cliente asociado al encargo y validar su presupuesto
                $respPres  = petCURLGet($urlAPI, "cod_cliente=" . intval($encargo->cod_cliente), $cabecera);

                // Decodificamos la respuesta de la API para obtener el presupuesto del cliente
                $datosPres = json_decode($respPres, true);

                session_start(); // → Volvemos a iniciar la sesión después de la petición a la API

                // _______ VALIDACIÓN DE QUE EL CLIENTE PERTENEZCA AL ARTISTA _______

                $codClienteAPI = intval($datosPres["datos"]["cod_cliente"] ?? 0);
                $codUsuarioAPI = intval($datosPres["datos"]["cod_usuario"] ?? 0);
                
                if ($codClienteAPI !== $encargo->cod_cliente || $codUsuarioAPI !== $encargo->cod_usuario) {
                    $errores["cod_cliente"] = "El cliente seleccionado no pertenece al artista indicado";
                    $encargo->setError("cod_cliente", $errores["cod_cliente"]);
                }

                $presupuesto = floatval($datosPres["datos"]["presupuesto"] ?? 0); // → Presupuesto del cliente obtenido de la API

                // _______ VALIDACIÓN DE QUE EL PRECIO TOTAL NO SUPERE EL PRESUPUESTO DEL CLIENTE _______
                if ($presupuesto > 0 && $encargo->precio_total > $presupuesto) {
                    $mensajeErrorPresu = "El precio total (" . number_format($encargo->precio_total, 2, ',', '.') . " €) supera el presupuesto del cliente (" . number_format($presupuesto, 2, ',', '.') . " €)";
                    $errores["precio_base"] = $mensajeErrorPresu;
                    $encargo->setError("precio_base", $mensajeErrorPresu);
                }
            }

            // Si no hay errores, guardamos el encargo y redirigimos a la gestión de encargos.
            if (empty($errores) && $encargo->guardar()) {

                // Creamos la carpeta del encargo
                $nombreCarpeta = strtolower(str_replace([' ', '/', '\\', '"', "'"], '_', $encargo->nombre));
                $rutaCarpeta   = RUTA_BASE . "/imagenes/encargos/" . $nombreCarpeta;

                if (!is_dir($rutaCarpeta)) {
                    mkdir($rutaCarpeta, 0755, true);
                }

                Sistema::app()->irAPagina(["encargos", "gestion"]);
                return;
            } else {
                $errores = array_merge($errores, $encargo->getErrores() ?? []);
            }
        }

        // Obtenemos la lista de artistas para mostrarla en un combo
        $bd = Sistema::app()->BD();

        $sqlArtistas = "SELECT u.cod_usuario, u.nick, u.nombre
                        FROM usuarios u
                        INNER JOIN acl_usuarios au ON au.nick = u.nick AND au.borrado = 0
                        INNER JOIN acl_roles ar    ON ar.cod_acl_role = au.cod_acl_role
                        WHERE ar.perm8 = 1 AND u.borrado = 0
                        ORDER BY u.nick ASC";

        // Ejecutamos la consulta y obtenemos las filas de artistas
        $filasArtistas = $bd->crearConsulta($sqlArtistas)->filas();

        if (!is_array($filasArtistas)) $filasArtistas = [];

        $artistasCombo = [];
        
        // Recorremos las filas de artistas para montar el array que se usará en el combo (cod_usuario => "nick (nombre)")
        foreach ($filasArtistas as $a) {
            $etiqueta = $a["nick"];
            if (!empty($a["nombre"])) {
                $etiqueta .= " (" . $a["nombre"] . ")";
            }
            $artistasCombo[$a["cod_usuario"]] = $etiqueta;
        }

        // Obtenemos la lista de clientes para mostrarla en un combo (por la API)
        $clientesCombo = [];

        // Para obtener la lista de clientes, necesitamos llamar a la API para obtener solo los clientes del usuario conectado y 
        // mostrarlos en el combo.
        $baseURL  = Sistema::app()->generaURL(["api", "clientes"]);

        $urlAPI   = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;

        $cabecera = ["Cookie: " . session_name() . "=" . session_id()];

        $params   = http_build_query(["ordenar_por" => "nombre", "orden" => "ASC", "pagina" => 1, "registros_pagina" => 50]);

        session_write_close(); // → Cerramos la sesión para evitar bloqueos mientras hacemos la petición a la API

        // Hacemos la petición a la API para obtener la lista de clientes del usuario conectado
        $respuestaAPI = petCURLGet($urlAPI, $params, $cabecera);

        // Decodificamos la respuesta de la API para obtener los datos de los clientes
        $datosAPI = json_decode($respuestaAPI, true);

        session_start(); // → Volvemos a iniciar la sesión después de la petición a la API

        // Recorremos los clientes obtenidos de la API para montar el array que se usará en el combo 
        // (cod_cliente => "nombre (email)")
        if ($datosAPI && !empty($datosAPI["correcto"])) {
            foreach ($datosAPI["datos"]["clientes"] ?? [] as $cli) {
                $etiqueta = $cli["nombre"];
                if (!empty($cli["email"])) {
                    $etiqueta .= " (" . $cli["email"] . ")";
                }
                $clientesCombo[$cli["cod_cliente"]] = $etiqueta;
            }
        }

        // Dibujamos la vista
        $this->dibujaVista("gestionEncargos/crear", [
            "encargo"           => $encargo,
            "artistasCombo"     => $artistasCombo,
            "clientesCombo"     => $clientesCombo,
            "artistaIdSel"      => $_POST["Encargo"]["cod_usuario"] ?? "",
            "clienteIdSel"      => $_POST["Encargo"]["cod_cliente"] ?? (intval($_GET["cod_cliente_nuevo"] ?? 0) ?: ""),
            "barraUbi"          => $this->barraUbi,
            "barraMenu"         => $this->barraMenu,
            "errores"           => $errores,
        ], "Crear Encargo");
    }
}
?>
