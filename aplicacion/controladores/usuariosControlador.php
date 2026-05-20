<?php

class usuariosControlador extends CControlador
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
    //                   CRUD USUARIOS
    // —————————————————————————————————————————————————————————————

    public function accionIndex()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Usuarios",
                "enlace" => ["usuarios", "index"]
            ],
        ];

        // Verificamos los permisos
        if (!Sistema::app()->acceso()->hayUsuario()) {

            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["usuarios", "index"];
            }

            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso10 = Sistema::app()->acceso()->puedePermiso(10); // → Solo el permiso 10 (admin) puede gestionar usuarios

        if (!$tienePermiso10) {
            Sistema::app()->paginaError(403, "No tienes permisos para acceder a la gestión de usuarios");
            return;
        }

        // _______ FILTROS Y BÚSQUEDA _______

        $nombre_busqueda = $_GET["nombre_busqueda"] ?? "";
        $nick_busqueda = $_GET["nick_busqueda"] ?? "";
        $categoria_filtro = intval($_GET["categoria_filtro"] ?? 0);
        $mostrar_eliminados = isset($_REQUEST["mostrar_eliminados"]) && $_REQUEST["mostrar_eliminados"] == 1;
        $pagina = max(1, intval($_GET["pag"] ?? 1));

        // Validamos el campo de ordenación contra whitelist
        $ordenesPermitidos = [
            "nombre_asc", "nombre_desc",
            "nick_asc", "nick_desc",
            "pais_asc", "pais_desc",
            "fecha_asc", "fecha_desc",
            "valoracion_asc", "valoracion_desc",
        ];
        $ordenSeleccionado = in_array($_GET["orden"] ?? "", $ordenesPermitidos) ? $_GET["orden"] : "fecha_desc";

        $mapaOrden = [
            "nombre_asc" => "u.nombre ASC",
            "nombre_desc" => "u.nombre DESC",
            "nick_asc" => "u.nick ASC",
            "nick_desc" => "u.nick DESC",
            "pais_asc" => "u.pais ASC",
            "pais_desc" => "u.pais DESC",
            "fecha_asc" => "u.fecha_alta ASC",
            "fecha_desc" => "u.fecha_alta DESC",
            "valoracion_asc" => "u.valoracion ASC",
            "valoracion_desc"  => "u.valoracion DESC",
        ];
        $orderBy = $mapaOrden[$ordenSeleccionado];

        // Guardamos los filtros en sesión para mantenerlos al paginar

        $_SESSION["nombre_busqueda"] = $nombre_busqueda;
        $_SESSION["nick_busqueda"] = $nick_busqueda;
        $_SESSION["mostrar_eliminados"] = $mostrar_eliminados;
        $_SESSION["orden_usuarios"] = $ordenSeleccionado;

        // Construimos la consulta SQL
        $bd = Sistema::app()->BD();

        // Si hay filtro por categoría necesitamos JOIN con obras
        $usaJoin = $categoria_filtro > 0;

        if ($usaJoin) {
            $sql = "SELECT DISTINCT u.* FROM usuarios u
                    INNER JOIN obras o ON o.cod_usuario = u.cod_usuario AND o.borrado = 0";
        } else {
            $sql = "SELECT u.* FROM usuarios u";
        }

        // Construimos las condiciones del where
        $condiciones = [];

        $condiciones[] = $mostrar_eliminados ? "u.borrado = 1" : "u.borrado = 0";

        if (!empty($nombre_busqueda)) {
            $condiciones[] = "u.nombre LIKE '%" . CGeneral::addSlashes($nombre_busqueda) . "%'";
        }
        if (!empty($nick_busqueda)) {
            $condiciones[] = "u.nick LIKE '%" . CGeneral::addSlashes($nick_busqueda) . "%'";
        }
        if ($usaJoin) {
            $condiciones[] = "o.cod_categoria = " . $categoria_filtro;
        }

        $sql .= " WHERE " . implode(" AND ", $condiciones);
        $sql .= " ORDER BY " . $orderBy;

        // _______ PAGINACIÓN _______

        $cmd = $bd->crearConsulta($sql);
        $totalUsuarios = count($cmd->filas() ?? []);

        $filasPorPagina = 6;
        $totalPaginas = ceil($totalUsuarios / $filasPorPagina);

        if ($pagina > $totalPaginas && $totalPaginas > 0) $pagina = $totalPaginas;

        $offset = ($pagina - 1) * $filasPorPagina;

        $sql .= " LIMIT " . intval($offset) . ", " . intval($filasPorPagina);

        $cmd = $bd->crearConsulta($sql);
        $filas = $cmd->filas() ?? [];

        // _______ PAGINADOR _______

        $urlPaginador = Sistema::app()->generaURL(["usuarios", "index"]);

        $parametrosPaginador = [];

        if (!empty($nombre_busqueda)) {
            $parametrosPaginador["nombre_busqueda"] = $nombre_busqueda;
        }
        if (!empty($nick_busqueda)) {
            $parametrosPaginador["nick_busqueda"] = $nick_busqueda;
        }
        if ($categoria_filtro > 0) {
            $parametrosPaginador["categoria_filtro"] = $categoria_filtro;
        }
        if ($mostrar_eliminados) {
            $parametrosPaginador["mostrar_eliminados"] = 1;
        }
        if ($ordenSeleccionado !== "fecha_desc") {
            $parametrosPaginador["orden"] = $ordenSeleccionado;
        }

        if (!empty($parametrosPaginador)) {
            $urlPaginador .= "&" . http_build_query($parametrosPaginador);
        }

        $opcPaginador = [
            "URL" => $urlPaginador,
            "TOTAL_REGISTROS" => $totalUsuarios,
            "PAGINA_ACTUAL" => $pagina,
            "REGISTROS_PAGINA" => $filasPorPagina,
            "PAGINAS_MOSTRADAS" => 5,
        ];

        // _______ CABECERA DE LA TABLA _______

        $cabecera = [
            [
                "ETIQUETA" => "Foto",
                "CAMPO" => "img_perfil"
            ],
            [
                "ETIQUETA" => "Nombre",
                "CAMPO" => "nombre"
            ],
            [
                "ETIQUETA" => "Nick",
                "CAMPO" => "nick"
            ],
            [
                "ETIQUETA" => "Email",
                "CAMPO" => "email"
            ],
            [
                "ETIQUETA" => "País",
                "CAMPO" => "pais"
            ],
            [
                "ETIQUETA" => "Fecha Alta",
                "CAMPO" => "fecha_alta"
            ],
            [
                "ETIQUETA" => "Operaciones",
                "CAMPO" => "operaciones"
            ]
        ];

        // Añadimos imagen de perfil y botones de operaciones
        foreach ($filas as &$usuario) {
            $codUsuario = $usuario["cod_usuario"];

            if (!empty($usuario["img_perfil"])) {
                $rutaImagen = "/imagenes/perfiles/" . htmlspecialchars($usuario["img_perfil"]);
            } else {
                $rutaImagen = "/imagenes/perfiles/ImgDefault.jpg";
            }

            $usuario["img_perfil"] = CHTML::dibujaEtiqueta("img", [
                "src" => $rutaImagen,
                "alt" => htmlspecialchars($usuario["nick"]),
                "class" => "tabla-imagen",
                "onerror" => "this.src='/imagenes/perfiles/ImgDefault.jpg'"
            ]);

            $urlVer = Sistema::app()->generaURL(["usuarios", "ver"]) . "?" . http_build_query(["cod_usuario" => $codUsuario]);
            $urlModificar = Sistema::app()->generaURL(["usuarios", "modificar"]) . "?" . http_build_query(["cod_usuario" => $codUsuario]);
            $urlBorrar = Sistema::app()->generaURL(["usuarios", "borrar"]) . "?" . http_build_query(["cod_usuario" => $codUsuario]);

            $usuario["operaciones"] =
                CHTML::link(
                    CHTML::imagen("/imagenes/iconos_propios/svg/eye.svg", "Ver usuario", ["class" => "icono-op invertir-color"]),
                    $urlVer
                ) . " " .
                CHTML::link(
                    CHTML::imagen("/imagenes/iconos_propios/svg/pencil-square.svg", "Modificar usuario", ["class" => "icono-op"]),
                    $urlModificar
                ) . " " .
                CHTML::link(
                    CHTML::imagen("/imagenes/iconos_propios/svg/trash3-fill.svg", "Borrar usuario", ["class" => "icono-op"]),
                    $urlBorrar
                );
        }

        // Cargamos categorías para el dropdown de filtro
        $catModel = new Categorias();
        $listaCat = $catModel->buscarTodos() ?? [];
        $categoriasDropdown = ["" => "Todas las categorías"];
        foreach ($listaCat as $cat) {
            $categoriasDropdown[$cat["cod_categoria"]] = $cat["descripcion"];
        }

        // Dibujamos la vista
        $this->dibujaVista("listar", [
            "usuarios"            => $filas,
            "cabecera"            => $cabecera,
            "paginador"           => $opcPaginador,
            "mostrar_eliminados"  => $mostrar_eliminados,
            "ordenSeleccionado"   => $ordenSeleccionado,
            "categorias"          => $categoriasDropdown,
            "categoriaSeleccionada" => $categoria_filtro,
            "barraUbi"            => $this->barraUbi,
            "barraMenu"           => $this->barraMenu
        ], "Gestión Usuarios");
    }

    // ____________________________________________________________

    public function accionCrear()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Usuarios",
                "enlace" => ["usuarios", "index"]
            ],
            [
                "texto" => "Crear Usuario",
                "enlace" => ""
            ]
        ];

        // Verificamos el permiso
        if (!Sistema::app()->acceso()->hayUsuario() || !Sistema::app()->acceso()->puedePermiso(10)) {
            Sistema::app()->paginaError(403, "No tienes permisos para crear usuarios");
            return;
        }

        $usuario = new Usuario();

        // Si es POST, creamos el nuevo usuario
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Recibimos los datos del formulario
            $datosUsuario = $_POST["Usuario"] ?? [];

            // Asignamos los datos al modelo (asegurándonos de escapar para evitar inyección SQL)
            $usuario->nombre = CGeneral::addSlashes($datosUsuario["nombre"] ?? "");
            $usuario->nick = CGeneral::addSlashes($datosUsuario["nick"] ?? "");
            $usuario->email = CGeneral::addSlashes($datosUsuario["email"] ?? "");
            $usuario->pais = CGeneral::addSlashes($datosUsuario["pais"] ?? "");
            $usuario->descripcion = CGeneral::addSlashes($datosUsuario["descripcion"] ?? "");
            $usuario->direccion = CGeneral::addSlashes($datosUsuario["direccion"] ?? "");
            $usuario->borrado = 0;

            // Asignamos la fecha ANTES de validar en formato d/m/Y
            $hoy = new DateTime();
            $usuario->fecha_alta = $hoy->format('d/m/Y');

            // Controlamos la subida de img_perfil
            if (isset($_FILES["Usuario"]) && isset($_FILES["Usuario"]["tmp_name"]["img_perfil"]) && $_FILES["Usuario"]["size"]["img_perfil"] > 0) {
                $rutaDestino = "/imagenes/perfiles/";
                $extensionOriginal = pathinfo($_FILES["Usuario"]["name"]["img_perfil"], PATHINFO_EXTENSION);
                $nombreArchivo = "perfil_" . $usuario->nick . "." . $extensionOriginal;
                $rutaCompleta = $_SERVER["DOCUMENT_ROOT"] . $rutaDestino . $nombreArchivo;

                if (move_uploaded_file($_FILES["Usuario"]["tmp_name"]["img_perfil"], $rutaCompleta)) {
                    $usuario->img_perfil = $nombreArchivo;
                }
            }

            if ($usuario->validar() && $usuario->guardar()) {
                Sistema::app()->irAPagina(["usuarios", "index"]);
                return;
            }
        }

        // Dibujamos la vista
        $this->dibujaVista("nuevo", ["usuario" => $usuario], "Crear Usuario");
    }

    // ____________________________________________________________

    public function accionVer()
    {
        // Usamos $_REQUEST para aceptar GET y POST
        $codUsuario = intval($_REQUEST["cod_usuario"] ?? 0);

        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Usuarios",
                "enlace" => ["usuarios", "index"]
            ],
            [
                "texto" => "Ver Usuario",
                "enlace" => ""
            ]
        ];

        // Verificamos el permiso
        if (!Sistema::app()->acceso()->hayUsuario() || !Sistema::app()->acceso()->puedePermiso(10)) {
            Sistema::app()->paginaError(403, "No tienes permisos");
            return;
        }

        $usuario = new Usuario();
        // Buscamos el usuario por cod_usuario y que no esté borrado
        if (!$usuario->buscarPor(["where" => "cod_usuario = " . $codUsuario . " AND borrado = 0"])) {
            Sistema::app()->paginaError(404, "Usuario no encontrado");
            return;
        }

        $this->dibujaVista("ver", ["usuario" => $usuario], "Ver Usuario");
    }

    // ____________________________________________________________

    public function accionModificar()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Usuarios",
                "enlace" => ["usuarios", "index"]
            ],
            [
                "texto" => "Modificar Usuario",
                "enlace" => ""
            ]
        ];

        //  Verificamos los permisos
        if (!Sistema::app()->acceso()->hayUsuario() || !Sistema::app()->acceso()->puedePermiso(10)) {
            Sistema::app()->paginaError(403, "No tienes permisos para modificar usuarios");
            return;
        }

        //  Obtenemos y validamos el cod_usuario
        $cod_usuario = intval($_REQUEST["cod_usuario"] ?? 0);

        if ($cod_usuario <= 0) {
            Sistema::app()->paginaError(404, "Código de usuario inválido");
            return;
        }

        //  Buscamos el usuario a modificar
        $usuario = new Usuario();

        if (!$usuario->buscarPor(["where" => "cod_usuario = " . $cod_usuario])) {
            Sistema::app()->paginaError(404, "Usuario no encontrado");
            return;
        }

        //  Si viene por POST, actualizamos el usuario
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $datosUsuario = $_POST["Usuario"] ?? [];

            //  Actualizamos los campos (asegurándonos de escapar para evitar inyección SQL)
            $usuario->nombre = CGeneral::addSlashes($datosUsuario["nombre"] ?? $usuario->nombre);
            $usuario->nick = CGeneral::addSlashes($datosUsuario["nick"] ?? $usuario->nick);
            $usuario->email = CGeneral::addSlashes($datosUsuario["email"] ?? $usuario->email);
            $usuario->descripcion = CGeneral::addSlashes($datosUsuario["descripcion"] ?? $usuario->descripcion);
            $usuario->direccion = CGeneral::addSlashes($datosUsuario["direccion"] ?? $usuario->direccion);
            $usuario->pais = CGeneral::addSlashes($datosUsuario["pais"] ?? $usuario->pais);
            $usuario->valoracion = floatval($datosUsuario["valoracion"] ?? $usuario->valoracion);

            // Convertimos la fecha Y-m-d (de BD) a d/m/Y para que validaFecha() la acepte
            $fechaObj = DateTime::createFromFormat('Y-m-d', $usuario->fecha_alta);
            if ($fechaObj) {
                $usuario->fecha_alta = $fechaObj->format('d/m/Y');
            }

            //  Controlamos la subida de img_perfil
            if (isset($_FILES["Usuario"]) && isset($_FILES["Usuario"]["tmp_name"]["img_perfil"]) && $_FILES["Usuario"]["size"]["img_perfil"] > 0) {
                
                $rutaDestino = "/imagenes/perfiles/";
                $extensionOriginal = pathinfo($_FILES["Usuario"]["name"]["img_perfil"], PATHINFO_EXTENSION);
                $nombreArchivo = "perfil_" . $usuario->nick . "." . $extensionOriginal;
                $rutaCompleta = $_SERVER["DOCUMENT_ROOT"] . $rutaDestino . $nombreArchivo;

                if (move_uploaded_file($_FILES["Usuario"]["tmp_name"]["img_perfil"], $rutaCompleta)) {
                    $usuario->img_perfil = $nombreArchivo;
                }
            }

            //  Controlamos la subida de img_banner (opcional)
            if (isset($_FILES["Usuario"]) && isset($_FILES["Usuario"]["tmp_name"]["img_banner"]) && $_FILES["Usuario"]["size"]["img_banner"] > 0) {
                $rutaDestino = "/imagenes/banners/";
                if (!is_dir($_SERVER["DOCUMENT_ROOT"] . $rutaDestino)) {
                    mkdir($_SERVER["DOCUMENT_ROOT"] . $rutaDestino, 0777, true);
                }

                $extensionOriginal = pathinfo($_FILES["Usuario"]["name"]["img_banner"], PATHINFO_EXTENSION);
                $nombreArchivo = "banner_" . $usuario->nick . "." . $extensionOriginal;
                $rutaCompleta = $_SERVER["DOCUMENT_ROOT"] . $rutaDestino . $nombreArchivo;

                if (move_uploaded_file($_FILES["Usuario"]["tmp_name"]["img_banner"], $rutaCompleta)) {
                    $usuario->img_banner = $nombreArchivo;
                }
            }

            //  Validamos y guardamos
            if ($usuario->validar() && $usuario->guardar()) {
                Sistema::app()->irAPagina(["usuarios", "index"]);
                return;
            }
        }

        //  Dibujamos la vista
        $this->dibujaVista("modificar", ["usuario" => $usuario], "Modificar Usuario");
    }

    // ____________________________________________________________

    public function accionBorrar()
    {
        //  Verificamos los permisos
        if (!Sistema::app()->acceso()->hayUsuario() || !Sistema::app()->acceso()->puedePermiso(10)) {
            Sistema::app()->paginaError(403, "No tienes permisos para borrar usuarios");
            return;
        }

        //  Si viene por GET, mostrar la confirmación
        if ($_SERVER["REQUEST_METHOD"] == "GET") {

            $this->barraUbi = [
                [
                    "texto" => "Inicio",
                    "enlace" => ["inicial"]
                ],
                [
                    "texto" => "Gestión Usuarios",
                    "enlace" => ["usuarios", "index"]
                ],
                [
                    "texto" => "Confirmar borrado",
                    "enlace" => ""
                ]
            ];

            // Obtenemos y validamos el cod_usuario
            $cod_usuario = intval($_GET["cod_usuario"] ?? 0);

            if ($cod_usuario <= 0) {
                Sistema::app()->paginaError(404, "Código de usuario inválido");
                return;
            }

            // Buscamos el usuario
            $usuario = new Usuario();

            if (!$usuario->buscarPor(["where" => "cod_usuario = " . $cod_usuario])) {
                Sistema::app()->paginaError(404, "Usuario no encontrado");
                return;
            }

            // Dibujamos la vista de confirmación
            $this->dibujaVista("borrar", ["usuario" => $usuario], "Confirmar borrado de usuario");
        }

        //  Si viene por POST, procesamos el borrado
        else if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Obtenemos el cod_usuario desde POST
            $cod_usuario = intval($_POST["cod_usuario"] ?? 0);
            $confirmar = intval($_POST["confirmar"] ?? 0);

            if ($cod_usuario <= 0 || $confirmar != 1) {
                Sistema::app()->paginaError(400, "Solicitud inválida");
                return;
            }

            // Buscamos el usuario
            $usuario = new Usuario();

            if (!$usuario->buscarPor(["where" => "cod_usuario = " . $cod_usuario])) {
                Sistema::app()->paginaError(404, "Usuario no encontrado");
                return;
            }

            // Realizamos el borrado lógico
            $usuario->borrado = 1;

            if ($usuario->guardar()) {
                Sistema::app()->irAPagina(["usuarios", "index"]);
            } else {
                Sistema::app()->paginaError(500, "Error al eliminar el usuario");
            }

            return;
        }
    }

    // —————————————————————————————————————————————
    //         VISTA PERFIL USUARIO
    // —————————————————————————————————————————————

    public function accionPerfil()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mi Perfil",
                "enlace" => ""
            ]
        ];

        // Verificamos que hay usuario con sesión iniciada
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        // Los administradores (permiso 9) no tienen perfil de artista
        if (Sistema::app()->acceso()->puedePermiso(9)) {
            Sistema::app()->paginaError(403, "Los administradores no tienen perfil de artista");
            return;
        }

        // Obtenemos el nick del usuario conectado
        $nick = Sistema::app()->acceso()->getNick();
        $usuario = new Usuario();

        // Buscamos el usuario por nick
        if (!$usuario->buscarPor(["where" => "nick = '" . CGeneral::addSlashes($nick) . "'"])) {
            Sistema::app()->paginaError(404, "Usuario no encontrado");
            return;
        }

        // Obtenemos las obras del usuario (máximo 4 para la galería)
        $obras = new Obras();
        $obrasUsuario = $obras->buscarTodos([
            "where" => "cod_usuario = " . $usuario->cod_usuario . " AND borrado = 0",
            "limit" => "4",
            "order" => "fecha_alta DESC"
        ]);

        // Obtenemos TODAS las categorías únicas del usuario (de TODAS sus obras, no solo las 4 primeras)
        $todasLasObrasDelUsuario = new Obras();
        $todasLasObrasDelUsuario = $todasLasObrasDelUsuario->buscarTodos([
            "where" => "cod_usuario = " . $usuario->cod_usuario . " AND borrado = 0"
        ]);

        $categoriasArray = [];
        $categoriasIds = [];

        if (is_array($todasLasObrasDelUsuario) && count($todasLasObrasDelUsuario) > 0) {
            foreach ($todasLasObrasDelUsuario as $obra) {
                $codCategoria = isset($obra["cod_categoria"]) ? $obra["cod_categoria"] : null;

                if ($codCategoria && !in_array($codCategoria, $categoriasIds)) {
                    $categoriasIds[] = $codCategoria;

                    $categoria = new Categorias();
                    if ($categoria->buscarPor(["where" => "cod_categoria = " . (int)$codCategoria])) {
                        $categoriasArray[] = $categoria;
                    }
                }
            }
        }

        $categoriasUsuario = $categoriasArray;

        // Pasamos los datos a la vista y la dibujamos
        $this->dibujaVista("perfil/perfil", [
            "usuario" => $usuario,
            "obras" => $obrasUsuario,
            "categorias" => $categoriasUsuario
        ], "Mi Perfil");
    }

    // —————————————————————————————————————————————
    //         GALERIA PRIVADA DEL USUARIO
    // —————————————————————————————————————————————

    public function accionGaleriaPrivada()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mi Perfil",
                "enlace" => ["usuarios", "perfil"]
            ],
            [
                "texto" => "Mis Obras",
                "enlace" => ""
            ]
        ];

        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $nick = Sistema::app()->acceso()->getNick();
        $usuario = new Usuario();

        if (!$usuario->buscarPor(["where" => "nick = '" . CGeneral::addSlashes($nick) . "'"])) {
            Sistema::app()->paginaError(404, "Usuario no encontrado");
            return;
        }

        // Obtenemos TODAS las obras del usuario
        $obras = new Obras();

        $obrasUsuario = $obras->buscarTodos([
            "where" => "cod_usuario = " . $usuario->cod_usuario . " AND borrado = 0",
            "order" => "fecha_alta DESC"
        ]);

        $this->mostrarBuscador = true; // → Activamos buscador en galería privada

        $this->dibujaVista("perfil/galeria-privada", ["obras" => $obrasUsuario], "Mis Obras");
    }

    // —————————————————————————————————————————————
    //         EDITAR PERFIL DEL USUARIO
    // —————————————————————————————————————————————

    public function accionEditar()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mi Perfil",
                "enlace" => ["usuarios", "perfil"]
            ],
            [
                "texto" => "Editar",
                "enlace" => ""
            ]
        ];

        // Verificamos que hay usuario con sesión iniciada
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $nick = Sistema::app()->acceso()->getNick();
        $usuario = new Usuario();

        if (!$usuario->buscarPor(["where" => "nick = '" . CGeneral::addSlashes($nick) . "'"])) {
            Sistema::app()->paginaError(404, "Usuario no encontrado");
            return;
        }

        // Si viene por POST, guardamos los cambios

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $datosUsuario = $_POST["Usuario"] ?? [];

            $usuario->email = CGeneral::addSlashes($datosUsuario["email"] ?? $usuario->email);
            $usuario->pais = CGeneral::addSlashes($datosUsuario["pais"] ?? $usuario->pais);
            $usuario->descripcion = CGeneral::addSlashes($datosUsuario["descripcion"] ?? $usuario->descripcion);

            // Convertimos la fecha Y-m-d (de BD) a d/m/Y para que validaFecha() la acepte
            $fechaObj = DateTime::createFromFormat('Y-m-d', $usuario->fecha_alta);
            if ($fechaObj) {
                $usuario->fecha_alta = $fechaObj->format('d/m/Y');
            }

            // Controlamos la subida de img_perfil
            if (isset($_FILES["Usuario"]) && isset($_FILES["Usuario"]["tmp_name"]["img_perfil"]) && $_FILES["Usuario"]["size"]["img_perfil"] > 0) {
                $rutaDestino = "/imagenes/perfiles/";

                $extensionOriginal = pathinfo($_FILES["Usuario"]["name"]["img_perfil"], PATHINFO_EXTENSION);
                $nombreArchivo = "perfil_" . $usuario->nick . "." . $extensionOriginal;
                $rutaCompleta = $_SERVER["DOCUMENT_ROOT"] . $rutaDestino . $nombreArchivo;

                if (move_uploaded_file($_FILES["Usuario"]["tmp_name"]["img_perfil"], $rutaCompleta)) {
                    $usuario->img_perfil = $nombreArchivo;
                }
            }

            // Controlamos la subida de img_banner
            if (isset($_FILES["Usuario"]) && isset($_FILES["Usuario"]["tmp_name"]["img_banner"]) && $_FILES["Usuario"]["size"]["img_banner"] > 0) {
                $rutaDestino = "/imagenes/banners/";
                if (!is_dir($_SERVER["DOCUMENT_ROOT"] . $rutaDestino)) {
                    mkdir($_SERVER["DOCUMENT_ROOT"] . $rutaDestino, 0777, true);
                }

                $extensionOriginal = pathinfo($_FILES["Usuario"]["name"]["img_banner"], PATHINFO_EXTENSION);
                $nombreArchivo = "banner_" . $usuario->nick . "." . $extensionOriginal;
                $rutaCompleta = $_SERVER["DOCUMENT_ROOT"] . $rutaDestino . $nombreArchivo;

                if (move_uploaded_file($_FILES["Usuario"]["tmp_name"]["img_banner"], $rutaCompleta)) {
                    $usuario->img_banner = $nombreArchivo;
                }
            }

            // Validamos y guardamos
            if ($usuario->validar() && $usuario->guardar()) {
                Sistema::app()->irAPagina(["usuarios", "perfil"]);
                return;
            }
        }

        $this->dibujaVista("perfil/editar", ["usuario" => $usuario], "Editar Perfil");
    }

    // —————————————————————————————————————————————
    //         OBRAS FAVORITAS DEL USUARIO
    // —————————————————————————————————————————————

    public function accionFavoritos()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Mi Perfil",
                "enlace" => ["usuarios", "perfil"]
            ],
            [
                "texto" => "Favoritos",
                "enlace" => ""
            ]
        ];

        // Verificamos que hay usuario con sesión iniciada
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        // ———————————————————————————————————————————
        //     OBTENER OBRAS FAVORITAS DEL USUARIO
        // ———————————————————————————————————————————

        // Obtenemos el nick del usuario conectado
        $nick = Sistema::app()->acceso()->getNick();
        $usuario = new Usuario();

        // Buscamos el usuario en la base de datos
        if (!$usuario->buscarPor(["where" => "nick = '" . CGeneral::addSlashes($nick) . "'"])) {
            Sistema::app()->paginaError(404, "Usuario no encontrado");
            return;
        }

        // Convertimos el código del usuario a número entero por seguridad
        $cod_usuario = intval($usuario->cod_usuario);

        // Hacemos una consulta para obtener obras favoritas activas
        $sql = "SELECT o.* FROM obras o
                INNER JOIN obras_favoritas of ON o.cod_obra = of.cod_obra
                WHERE of.cod_usuario = $cod_usuario
                AND of.borrado = 0
                AND o.borrado = 0
                ORDER BY of.fecha_alta DESC";

        // Ejecutamos la consulta
        $bd = Sistema::app()->BD();
        $comando = $bd->crearConsulta($sql);

        // Obtenemos todas las filas como un array
        $obras = $comando->filas();
        if ($obras === false) {
            $obras = [];
        }

        // Pasamos los datos a la vista para mostrar la galería de favoritos
        $this->mostrarBuscador = true; // → Activamos buscador en favoritos

        $this->dibujaVista("perfil/favoritos", ["obras" => $obras], "Mis Favoritos");
    }

    // —————————————————————————————————————————————
    //         LIMPIAR FILTROS DE BÚSQUEDA
    // —————————————————————————————————————————————

    public function accionLimpiarFiltros()
    {
        // Borramos los filtros de búsqueda de la sesión
        foreach (["nombre_busqueda", "nick_busqueda", "mostrar_eliminados", "orden_usuarios"] as $clave) {
            if (isset($_SESSION[$clave])) unset($_SESSION[$clave]);
        }

        // Volvemos al listado sin filtros
        Sistema::app()->irAPagina(["usuarios", "index"]);
    }
}
