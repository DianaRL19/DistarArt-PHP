<?php

class obrasControlador extends CControlador
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

    // ————————————————————————————————————————————————————————————

    public function accionIndex()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Obras",
                "enlace" => ["obras", "index"]
            ],
        ];

        // Validamos los permisos

        if (!Sistema::app()->acceso()->hayUsuario()) {
            if (Sistema::app()->sesion()->haySesion()) {
                $_SESSION["pagina"] = ["obras", "index"];
            }
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        $tienePermiso9 = Sistema::app()->acceso()->puedePermiso(9);

        if (!$tienePermiso8 && !$tienePermiso9) {
            Sistema::app()->paginaError(403, "No tienes permisos para acceder al CRUD");
            return;
        }

        // Si no tiene permiso 9, solo puede ver sus propias obras
        $tienePermisoAdmin = $tienePermiso9;
        $codUsuarioActual = null;

        if (!$tienePermisoAdmin) {
            // Obtenemos el código del usuario para filtrar solamente sus obras
            $nick = Sistema::app()->acceso()->getNick();
            $codUsuarioActual = Sistema::app()->acl()->getCodUsuario($nick);
        }

        // _____________________________________________________________

        $galeria = new Galeria();

        // _____ Cargamos las categorías para el filtro _____

        $categoriasModel = new Categorias();

        $categoriasList = ["" => "-- Todas las categorías --"]; // → Lo ponemos así como más profesional 

        foreach ($categoriasModel->buscarTodos() as $cat) {

            $categoriasList[$cat["cod_categoria"]] = ucfirst($cat["descripcion"]); // → ucfirst para poner la primera letra en mayus
        }

        // ____ CREAMOS UN ARRAY DE OBRAS UNICAMENTE DEL USUARIO (solo del usuario si no es un admin) ____

        // Obtenemos todas las obras del usuario (si es admin pillamos todas)
        $whereSeguridad = !$tienePermisoAdmin ? "cod_usuario = " . $codUsuarioActual : "";

        $opcionesSeguridad = $whereSeguridad ? ["where" => $whereSeguridad] : [];

        $obrasSeguras = $galeria->buscarTodos($opcionesSeguridad); // → Array de obras del usuario

        // ____________ APLICAMOS LOS FILTROS ____________

        $obrasFiltradas = $obrasSeguras; // → Trabajamos sobre una copia

        // Búsqueda por nombre
        if (isset($_GET["nombre_busqueda"]) && empty($_GET["nombre_busqueda"])) {
            unset($_SESSION["nombre_busqueda"]); // → Limpiamos el filtro de busqueda si el campo está vacío
        }

        if (isset($_GET["nombre_busqueda"]) && !empty($_GET["nombre_busqueda"])) {
            $_SESSION["nombre_busqueda"] = htmlspecialchars($_GET["nombre_busqueda"]);
        }

        $busquedaActiva = $_SESSION["nombre_busqueda"] ?? "";

        if (!empty($busquedaActiva)) {

            // Filtramos por nombre o nick_usuario (pero OJO solo sobre las obras del usuario)
            $obrasFiltradas = array_filter($obrasFiltradas, function ($obra) use ($busquedaActiva) {

                $busquedaLower = mb_strtolower($busquedaActiva);
                $nombreLower = mb_strtolower($obra["nombre"]);
                $nickLower = mb_strtolower($obra["nick_usuario"]);

                return strpos($nombreLower, $busquedaLower) !== false ||
                    strpos($nickLower, $busquedaLower) !== false;
            });

            $obrasFiltradas = array_values($obrasFiltradas); // → Reseteamos los índices
        }

        // Filtro por CATEGORÍA

        if (!empty($_REQUEST["categoria"])) {

            $categoriaFiltro = intval($_REQUEST["categoria"]);

            $obrasFiltradas = array_filter($obrasFiltradas, function ($obra) use ($categoriaFiltro) {

                return $obra["cod_categoria"] == $categoriaFiltro;
            });

            $obrasFiltradas = array_values($obrasFiltradas); // → Reseteamos los índices
        }

        // Filtro por BORRADO

        $mostrarBorradas = isset($_REQUEST["borrado"]) && $_REQUEST["borrado"] == 1;

        $obrasFiltradas = array_filter($obrasFiltradas, function ($obra) use ($mostrarBorradas) {

            return $mostrarBorradas ? $obra["borrado"] == 1 : $obra["borrado"] == 0;
        });

        $obrasFiltradas = array_values($obrasFiltradas); // → Reseteamos los índices

        // _____________ CRITERIOS DE ORDENACIÓN _____________

        // Si el usuario selecciona un criterio para ordenar
        if (isset($_GET["orden"]) && !empty($_GET["orden"])) {
            $_SESSION["orden"] = $_GET["orden"];
        }

        // Usamos el critorio de ordenación, si no hay, usamos por defecto "fecha_desc"
        $ordenSeleccionado = $_SESSION["orden"] ?? "fecha_desc";

        // Aplicamos la ordenación al array filtrado
        $obrasFiltradas = $this->ordenaObras($obrasFiltradas, $ordenSeleccionado);

        // _____________ PAGINACIÓN SOBRE EL ARRAY _____________

        $obrasFiltradas = (array) $obrasFiltradas; // → Nos aseguramos de que es un array

        $total = count($obrasFiltradas); // → Total de obras después de filtrar
        $regPag = 10; // → Obras por página
        $paginas = ceil($total / $regPag); // → Total de páginas

        $pag = 1;
        if (isset($_GET["pag"])) {
            $pag = intval($_GET["pag"]);
        }

        // Validar que la página sea válida
        if ($pag < 1 || $pag > $paginas)
            $pag = 1;

        $inicioPaginador = ($pag - 1) * $regPag;
        if ($inicioPaginador < 0)
            $inicioPaginador = 0;

        // De aquí sacamos solo los registros de esta página del array filtrado
        $registros = array_slice($obrasFiltradas, $inicioPaginador, $regPag);

        // _____________ CONFIGURAR LA CABECERA DEL CGRID _____________

        $cabecera = [
            [
                "ETIQUETA" => "Código",
                "CAMPO" => "cod_obra"
            ],
            [
                "ETIQUETA" => "Imagen",
                "CAMPO" => "img_principal"
            ],
            [
                "ETIQUETA" => "Nombre",
                "CAMPO" => "nombre"
            ],
            [
                "ETIQUETA" => "Categoría",
                "CAMPO" => "descripcion_categoria"
            ],
            [
                "ETIQUETA" => "Artista",
                "CAMPO" => "nick_usuario"
            ],
            [
                "ETIQUETA" => "Valoración",
                "CAMPO" => "valoracion"
            ],
            [
                "ETIQUETA" => "Fecha Alta",
                "CAMPO" => "fecha_alta"
            ],
            [
                "ETIQUETA" => "Estado",
                "CAMPO" => "borrado"
            ],
            [
                "ETIQUETA" => "Operaciones",
                "CAMPO" => "operaciones"
            ],
        ];

        // _________ FILAS PARA CGRID _________

        $filas = [];

        if (!empty($registros)) {

            foreach ($registros as &$fila) {

                $cod_obra = $fila["cod_obra"];

                // Pillamos y procesamos la imagen 
                $rutaImg = "/imagenes/tablaObras/" . htmlspecialchars($fila["img_principal"]);

                $fila["img_principal"] = CHTML::imagen($rutaImg, htmlspecialchars($fila["nombre"]), ["class" => "tabla-imagen"]);

                // Pasamos el borrado a texto legible
                $fila["borrado"] = ($fila["borrado"] == 1) ? "Eliminada" : "Registrada";

                //  http_build_query → Funcion de PHP que convierte un array en una cadena de parámetros URL

                $urlVer = Sistema::app()->generaURL(["obras", "ver"]) . "?" . http_build_query(["cod_obra" => $cod_obra]);
                $urlModificar = Sistema::app()->generaURL(["obras", "modificar"]) . "?" . http_build_query(["cod_obra" => $cod_obra]);
                $urlBorrar = Sistema::app()->generaURL(["obras", "borrar"]) . "?" . http_build_query(["cod_obra" => $cod_obra]);

                $fila["operaciones"] =
                    CHTML::link(
                        CHTML::imagen("/imagenes/iconos_propios/svg/eye.svg", "Ver obra", ["class" => "icono-op invertir-color"]),
                        $urlVer
                    ) . " " .
                    CHTML::link(
                        CHTML::imagen("/imagenes/iconos_propios/svg/pencil-square.svg", "Modificar obra", ["class" => "icono-op"]),
                        $urlModificar
                    ) . " " .
                    CHTML::link(
                        CHTML::imagen("/imagenes/iconos_propios/svg/trash3-fill.svg", "Borrar obra", ["class" => "icono-op"]),
                        $urlBorrar
                    );
            }

            $filas = $registros;
        }

        // _________ PAGINADOR _________

        // Guardamos los parámetros del filtro para seguir teniendolos en paginación
        $filtrosURL = [];

        if (isset($_SESSION["nombre_busqueda"])) {
            $filtrosURL["nombre_busqueda"] = $_SESSION["nombre_busqueda"];
        }
        if (!empty($_REQUEST["categoria"])) {
            $filtrosURL["categoria"] = $_REQUEST["categoria"];
        }
        if (isset($_REQUEST["borrado"]) && $_REQUEST["borrado"] == 1) {
            $filtrosURL["borrado"] = 1;
        }
        //Mantener la ordenación en el paginadpr
        if (isset($_SESSION["orden"])) {
            $filtrosURL["orden"] = $_SESSION["orden"];
        }

        $urlPaginador = Sistema::app()->generaURL(["obras", "index"]);
        if (!empty($filtrosURL)) {
            $urlPaginador .= "?" . http_build_query($filtrosURL); // → Función de PHP que convierte un array en una cadena de parámetros URL
        }

        $opcPaginador = [
            "URL" => $urlPaginador,
            "TOTAL_REGISTROS" => $total,
            "PAGINA_ACTUAL" => $pag,
            "REGISTROS_PAGINA" => $regPag,
            "PAGINAS_MOSTRADAS" => 5,
        ];

        // Dibujamos la vista
        $this->dibujaVista("listar", [
            "cabecera" => $cabecera,
            "obras" => $filas,
            "paginador" => $opcPaginador,
            "categoriasList" => $categoriasList,
            "ordenSeleccionado" => $ordenSeleccionado,
            "arrayOrdenacion" => [
                "nombre_asc" => "Nombre (A-Z)",
                "nombre_desc" => "Nombre (Z-A)",
                "fecha_asc" => "Más antiguas",
                "fecha_desc" => "Más recientes",
                "valoracion_asc" => "Valoración ↓",
                "valoracion_desc" => "Valoración ↑"
            ]
        ], "Gestión de Obras");
    }

    // —————————————————————————————————————————————
    //           ACCIÓN - VER OBRA
    // —————————————————————————————————————————————

    public function accionVer()
    {
        // Verificamos los permisos
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        // Validamos que tenga permisos 8 u 9
        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        $tienePermiso9 = Sistema::app()->acceso()->puedePermiso(9);

        if (!$tienePermiso8 && !$tienePermiso9) {
            Sistema::app()->paginaError(403, "No tienes permisos para ver obras");
            return;
        }

        // Pillamos el cod_obra del GET
        $cod_obra = intval($_REQUEST["cod_obra"] ?? 0);

        if ($cod_obra <= 0) {
            Sistema::app()->paginaError(404, "Código de obra inválido");
            return;
        }

        // Buscamos la obra usando el modelo
        $obra = new Obras();

        // Con buscarPor accedemos a la tabla directamente
        if (!$obra->buscarPor(["where" => "cod_obra = $cod_obra"])) {
            Sistema::app()->paginaError(404, "Obra no encontrada (cod: $cod_obra)");
            return;
        }

        // Validamos los permisos: Si NO tiene permiso 9, solo puede ver sus propias obras
        if (!$tienePermiso9) {
            $nick = Sistema::app()->acceso()->getNick();
            $codUsuarioActual = Sistema::app()->acl()->getCodUsuario($nick);

            // Validamos los permisos: Si la obra no es de este usuario, rechazamos el acceso
            if ($obra->cod_usuario != $codUsuarioActual) {
                Sistema::app()->paginaError(403, "Esta obra no te pertenece");
                return;
            }
        }

        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Obras",
                "enlace" => ["obras", "index"]
            ],
            [
                "texto" => "Ver Obra → " . htmlspecialchars($obra->nombre ?? "Sin nombre"), // → Mostramos el nombre de la obra en la barraubi
                "enlace" => ""
            ]
        ];

        // Dibujamos la vista
        $this->dibujaVista("ver", ["obra" => $obra], "Ver Obra - " . htmlspecialchars($obra->nombre ?? "Obra"));
    }

    // —————————————————————————————————————————————
    //           ACCIÓN - CREAR OBRA
    // —————————————————————————————————————————————

    public function accionCrear()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Obras",
                "enlace" => ["obras", "index"]
            ],
            [
                "texto" => "Nueva obra",
                "enlace" => ""
            ],
        ];

        // Verificamos los permisos: necesita permiso 8 (artista) o 9 (admin)
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        $tienePermiso9 = Sistema::app()->acceso()->puedePermiso(9);

        if (!$tienePermiso8 && !$tienePermiso9) {
            Sistema::app()->paginaError(403, "No tienes permisos para crear obras");
            return;
        }

        $obra = new Obras();

        // Si se han enviado datos por POST guardamos la info de la nueva obra
        if (!empty($_POST)) {

            // Los componentes modeloText() generan nombres como Obra[nombre], Obra[descripcion], etc.
            // Con mayúscula porque fijarNombre() devuelve 'Obra'
            $datosObra = $_POST["Obra"] ?? [];

            $obra->nombre = $datosObra["nombre"] ?? "";
            $obra->descripcion = $datosObra["descripcion"] ?? "";
            $obra->cod_categoria = intval($datosObra["cod_categoria"] ?? 0);
            $obra->valoracion = floatval($_POST["valoracion"] ?? 0);

            // _____ Asignamos el cod_usuario ANTES de validar ______
            // - Si el admin (permiso 9) selecciona un usuario en el formulario, lo usamos
            // - Si no, asignamos el usuario actual
            if ($tienePermiso9 && isset($datosObra["cod_usuario"]) && !empty($datosObra["cod_usuario"])) {
                $obra->cod_usuario = intval($datosObra["cod_usuario"]);
            } else {
                $nick = Sistema::app()->acceso()->getNick();
                $obra->cod_usuario = Sistema::app()->acl()->getCodUsuario($nick);
            }

            // _____ Asignamos la fecha ANTES de validar y convertirla al formato correcto d/m/Y ______
            // Para CREAR, siempre usamos la de hoy. En el formulario lo tienemos como disabled, 
            // esto es para proteger las obras del plagio, es decir, de esta forma si alguien copia un dibujo 
            // no puede poner una fecha anterior al original y decir que era suyo antes.

            $hoy = new DateTime();
            $obra->fecha_alta = $hoy->format('d/m/Y');

            // Intentamos subir imagen, si no hay archivo subido usar la por defecto
            $imgNueva = $this->subirImagen($obra->nombre);
            $obra->img_principal = $imgNueva ?? "ImgDefault.jpg";

            if ($obra->validar()) {
                if ($obra->guardar()) {
                    Sistema::app()->irAPagina(["obras", "index"]);
                }
            }
        }

        // Cargamos las categorías para el select
        $categorias = new Categorias();
        $categorias_lista = $categorias->buscarTodos();

        // Cargamos todos los usuarios para que el admin pueda asignar la obra a un artista
        $usuariosList = [];
        if ($tienePermiso9) {
            $usuario = new Usuario();
            $usuariosList = $usuario->buscarTodos(["where" => "borrado = 0"]) ?? [];
        }

        $this->dibujaVista("nuevo", [
            "obra" => $obra,
            "categoriasList" => $categorias_lista,
            "usuariosList" => $usuariosList,
            "tienePermiso9" => $tienePermiso9,
            "accion" => "crear"
        ], "Nueva Obra");
    }

    // —————————————————————————————————————————————
    //           ACCIÓN - MODIICAR OBRA
    // —————————————————————————————————————————————

    public function accionModificar()
    {
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Gestión Obras",
                "enlace" => ["obras", "index"]
            ],
            [
                "texto" => "Modificar obra",
                "enlace" => ""
            ],
        ];

        // Verificamos los permisos
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        // Validamos que tenga permisos 8 u 9
        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        $tienePermiso9 = Sistema::app()->acceso()->puedePermiso(9);

        if (!$tienePermiso8 && !$tienePermiso9) {
            Sistema::app()->paginaError(403, "No tienes permisos para modificar obras");
            return;
        }

        // Pillamos el cod_obra del GET
        $cod_obra = intval($_REQUEST["cod_obra"] ?? 0);

        if ($cod_obra <= 0) {
            Sistema::app()->paginaError(404, "Código de obra inválido");
            return;
        }

        // ________ Buscar la obra a modificar ________
        $obra = new Obras();

        // Permitimos buscar obras borradas para poder editarlas después
        if (!$obra->buscarPor(["where" => "cod_obra = " . $cod_obra])) {
            Sistema::app()->paginaError(404, "Obra no encontrada (cod: $cod_obra)");
            return;
        }

        // Validamos de permisos: Si NO tiene permiso 9, solo puede modificar sus propias obras
        if (!$tienePermiso9) {
            $nick = Sistema::app()->acceso()->getNick();
            $codUsuarioActual = Sistema::app()->acl()->getCodUsuario($nick);

            // Si la obra no es de este usuario, rechazas su  acceso
            if ($obra->cod_usuario != $codUsuarioActual) {
                Sistema::app()->paginaError(403, "Esta obra no te pertenece");
                return;
            }
        }

        if (!empty($_POST)) {

            // Los componentes modeloText(), modeloDate(), etc. generan nombres como Obra[nombre], Obra[fecha_alta], etc.
            // Con mayúscula porque fijarNombre() devuelve 'Obra'
            $datosObra = $_POST["Obra"] ?? [];

            // Modificar los datos
            $obra->nombre = $datosObra["nombre"] ?? $obra->nombre;
            $obra->descripcion = $datosObra["descripcion"] ?? $obra->descripcion;
            $obra->cod_categoria = intval($datosObra["cod_categoria"] ?? $obra->cod_categoria);
            $obra->valoracion = floatval($datosObra["valoracion"] ?? $obra->valoracion);

            // Si el admin (permiso 9) selecciona un usuario en el formulario, lo usamos para cambiar el propietario
            if ($tienePermiso9 && isset($datosObra["cod_usuario"])) {
                $codUsuarioNuevo = intval($datosObra["cod_usuario"]);
                if ($codUsuarioNuevo > 0) {
                    $obra->cod_usuario = $codUsuarioNuevo;
                }
            }

            // Convertir fecha de Y-m-d a d/m/Y
            if (isset($datosObra["fecha_alta"]) && !empty($datosObra["fecha_alta"])) {
                $fechaFormulario = DateTime::createFromFormat('Y-m-d', $datosObra["fecha_alta"]);
                if ($fechaFormulario) {
                    $obra->fecha_alta = $fechaFormulario->format('d/m/Y');
                }
            }

            // Si la fecha sigue en Y-m-d (de BD), convertir a d/m/Y para validación
            $fechaObj = DateTime::createFromFormat('Y-m-d', $obra->fecha_alta);
            if ($fechaObj) {
                $obra->fecha_alta = $fechaObj->format('d/m/Y');
            }

            // Si hay una imagen nueva, subirla y actualizar
            // Si NO hay imagen nueva, mantener la actual que es lo más lógico 
            // (en los requisitos pone volver a la default pero para mi proyecto tiene mas sentido asi, si un artista solo quiere cambiar el 
            // nombre de la obra, no tiene sentido volver a tener que subir la imagen y que si no la vuelve a subir se le ponga la de por defecto)
            $imgNueva = $this->subirImagen($obra->nombre);
            if ($imgNueva !== null) {
                $obra->img_principal = $imgNueva;
            }

            if ($obra->validar()) {
                if ($obra->guardar()) {
                    Sistema::app()->irAPagina(["obras", "index"]);
                }
            }
        }

        //  ________ Cargamos las categorías para el select ________

        $categorias = new Categorias();

        $categorias_lista = $categorias->buscarTodos();

        // Cargamos todos los usuarios para que el admin pueda reasignar la obra a otro artista
        $usuariosList = [];
        if ($tienePermiso9) {
            $usuario = new Usuario();
            $usuariosList = $usuario->buscarTodos(["where" => "borrado = 0"]) ?? [];
        }

        //  ________ Preparar fecha para la vista (Y-m-d) ________

        $fechaFormato = 'd/m/Y';
        $fechaHTML = $obra->fecha_alta;

        $fechaObj = DateTime::createFromFormat($fechaFormato, $obra->fecha_alta);
        if ($fechaObj) {
            $fechaHTML = $fechaObj->format('Y-m-d');
        }

        //  ________ Dibujar vista ________

        $this->dibujaVista("modificar", [
            "obra" => $obra,
            "categoriasList" => $categorias_lista,
            "usuariosList" => $usuariosList,
            "tienePermiso9" => $tienePermiso9,
            "accion" => "modificar",
            "fechaHTML" => $fechaHTML
        ], "Modificar Obra");
    }

    // —————————————————————————————————————————————
    //           ACCIÓN - BORRAR OBRA
    // —————————————————————————————————————————————

    public function accionBorrar()
    {
        // ______ Verificamos permisos _______
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        // ______ Validamos que tenga permisos 8 u 9 _______
        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        $tienePermiso9 = Sistema::app()->acceso()->puedePermiso(9);

        if (!$tienePermiso8 && !$tienePermiso9) {
            Sistema::app()->paginaError(403, "No tienes permisos para borrar obras");
            return;
        }

        // Pillamos el cod_obra de GET y lo verificamos para que no me de más errores 
        $cod_obra = intval($_REQUEST["cod_obra"] ?? 0);

        if ($cod_obra <= 0) {
            Sistema::app()->paginaError(404, "Código de obra inválido");
            return;
        }

        // Buscamos la obra para mostrar la confirmación y que quede profesional
        $obra = new Obras();

        if (!$obra->buscarPor(["where" => "cod_obra = " . $cod_obra])) {
            Sistema::app()->paginaError(404, "Obra no encontrada");
            return;
        }

        // Validamos los permisos, si NO tiene permiso 9, solo puede borrar sus propias obras
        if (!$tienePermiso9) {
            $nick = Sistema::app()->acceso()->getNick();
            $codUsuarioActual = Sistema::app()->acl()->getCodUsuario($nick);

            // Si la obra no es de este usuario, rechazar acceso
            if ($obra->cod_usuario != $codUsuarioActual) {
                Sistema::app()->paginaError(403, "Esta obra no te pertenece");
                return;
            }
        }

        // Actualizamos la barra de ubicación
        $this->barraUbi = [
            [
                "texto" => "Inicio",
                "enlace" => ["inicial"]
            ],
            [
                "texto" => "Obras",
                "enlace" => ["obras", "index"]
            ],
            [
                "texto" => "Confirmar borrado",
                "enlace" => ""
            ]
        ];

        // Pasar los datos a la vista
        $obraArray = [
            "cod_obra"      => $obra->cod_obra,
            "nombre"        => $obra->nombre,
            "img_principal" => $obra->img_principal,
            "borrado"       => intval($obra->borrado), // → Necesario para deshabilitar el botón si ya está eliminada
        ];

        $this->dibujaVista("borrar", ["obra" => $obraArray], "Confirmar borrado de obra");
    }

    // —————————————————————————————————————————————
    //           CONFIRMAR BORRADO 
    // —————————————————————————————————————————————

    public function accionConfirmarBorrado()
    {
        if (!Sistema::app()->acceso()->hayUsuario()) {
            Sistema::app()->irAPagina(["registro", "login"]);
            return;
        }

        // ______ Validamos que tenga permisos 8 u 9 ______
        $tienePermiso8 = Sistema::app()->acceso()->puedePermiso(8);
        $tienePermiso9 = Sistema::app()->acceso()->puedePermiso(9);

        if (!$tienePermiso8 && !$tienePermiso9) {
            Sistema::app()->paginaError(403, "No tienes permisos para borrar obras");
            return;
        }

        // Pillamos otra vez el cod_obra del POST
        $cod_obra = intval($_POST["cod_obra"] ?? 0);

        if ($cod_obra <= 0) {
            Sistema::app()->paginaError(400, "Código de obra inválido");
            return;
        }

        // Buscamos la obra para asegurarnos de que existe y de que el usuario tiene permisos para borrarla
        $obra = new Obras();

        if (!$obra->buscarPor(["where" => "cod_obra = " . $cod_obra])) {
            Sistema::app()->paginaError(404, "Obra no encontrada");
            return;
        }

        // Validamos los permisos, si no tiene permiso 9, solo puede borrar sus propias obras
        if (!$tienePermiso9) {
            $nick = Sistema::app()->acceso()->getNick();
            $codUsuarioActual = Sistema::app()->acl()->getCodUsuario($nick);

            // Si la obra no es de este usuario, rechazar acceso
            if ($obra->cod_usuario != $codUsuarioActual) {
                Sistema::app()->paginaError(403, "Esta obra no te pertenece");
                return;
            }
        }

        // Y hacemos el borrado lógico usando el modelo 
        $obra->borrado = 1;

        if ($obra->guardar()) {
            // Redirigimos al listar
            Sistema::app()->irAPagina(["obras", "index"]);
        } else {
            Sistema::app()->paginaError(500, "Error al eliminar la obra");
        }

        return;
    }

    // ———————————————————————————————————————————————————————————
    //                       ACCIÓN - DAR LIKE
    // ———————————————————————————————————————————————————————————

    public function accionDarLike()
    {
        // Validamos el POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'index']));
            exit;
        }

        // Validamos que el usuario tenga la sesion iniciada
        if (!Sistema::app()->acceso()->hayUsuario()) {
            header("Location: " . Sistema::app()->generaURL(['principal', 'index']));
            exit;
        }

        // Extraemos datos del POST
        $cod_obra = intval($_POST["cod_obra"] ?? 0);

        if ($cod_obra <= 0) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'index']));
            exit;
        }

        // _____________ OBTENEMOS LOS DATOS DEL USUARIO _____________

        $nick = Sistema::app()->acceso()->getNick();
        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);

        // Si ya se ha dado like a esta obra, no hacemos nada
        $favorito = new ObrasFavoritas();
        $existeFavorito = $favorito->buscarPor([
            "where" => "cod_obra = $cod_obra AND cod_usuario = $codUsuario"
        ]);

        if ($existeFavorito && $favorito->borrado == 0) { // → Si ya existe y está activo, no hacemos nada
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // Si existe pero está marcado como borrado, lo reactivamos. Si no existe, lo creamos nuevo. 
        // En ambos casos, actualizamos la valoración de la obra y del artista. Si falla alguna operación, redirigimos sin hacer cambios.

        if ($existeFavorito && $favorito->borrado == 1) { // → Reutilizamos el registro existente
            $favorito->borrado = 0;
            $favorito->fecha_alta = date('Y-m-d'); // → Actualizamos la fecha

            if (!$favorito->validar()) {
                header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
                exit;
            }

            if (!$favorito->guardar()) {
                header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
                exit;
            }

            // Actualizamos valoración
            $obra = new Obras();
            if (!$obra->buscarPor(["where" => "cod_obra = $cod_obra AND borrado = 0"])) {
                header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
                exit;
            }

            $obra->valoracion = $obra->valoracion + 0.5;

            if (!$obra->guardar()) { // → Si falla, revertimos el cambio
                $favorito->borrado = 1;
                $favorito->guardar();
                header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
                exit;
            }

            // _____________ ACTUALIZAMOS VALORACIÓN DEL ARTISTA _____________

            $artista = new Usuario();
            if ($artista->buscarPor(["where" => "cod_usuario = " . intval($obra->cod_usuario)])) {
                $artista->actualizarValoracionDesdeObras();
            }

            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // _____________ SI NO EXISTE, CREAMOS UN NUEVO REGISTRO _____________

        $favorito = new ObrasFavoritas(); // → Creamos una nueva instancia para wl Insert

        $favorito->cod_obra = intval($cod_obra); // → Nos aseguramos de que es entero
        $favorito->cod_usuario = intval($codUsuario); // → Nos aseguramos de que es entero
        $favorito->fecha_alta = date('Y-m-d'); // → Fecha actual en el formato correcto
        $favorito->borrado = 0;

        // Validamos antes de guardar
        if (!$favorito->validar()) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        if (!$favorito->guardar()) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // _____________ OBTENEMOS Y ACTUALIZAMOS LA VALORACIÓN _____________

        $obra = new Obras();
        if (!$obra->buscarPor(["where" => "cod_obra = $cod_obra AND borrado = 0"])) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        $obra->valoracion = $obra->valoracion + 0.5;

        if (!$obra->guardar()) {  // → Si falla, marcamos favorito como borrado
            $favorito->borrado = 1;
            $favorito->guardar();
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // _____________ ACTUALIZAMOS LA VALORACIÓN DEL ARTISTA _____________

        $artista = new Usuario();
        if ($artista->buscarPor(["where" => "cod_usuario = " . intval($obra->cod_usuario)])) {
            $artista->actualizarValoracionDesdeObras();
        }

        header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
        exit;
    }

    // ———————————————————————————————————————————————————————————
    //                      ACCIÓN - QUITAR LIKE
    // ———————————————————————————————————————————————————————————

    public function accionQuitarLike()
    {
        // Validamos el POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'index']));
            exit;
        }

        // Validamos que el usuario tenga la sesión iniciada
        if (!Sistema::app()->acceso()->hayUsuario()) {
            header("Location: " . Sistema::app()->generaURL(['principal', 'index']));
            exit;
        }

        // Extraemos datos
        $cod_obra = intval($_POST["cod_obra"] ?? 0);

        if ($cod_obra <= 0) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'index']));
            exit;
        }

        // Obtenemos los datos del usuario para buscar el favorito correspondiente

        $nick = Sistema::app()->acceso()->getNick();
        $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);

        // Buscamos el favorito para esta obra y usuario. Si no existe o ya está borrado, no hacemos nada
        $favorito = new ObrasFavoritas();
        if (!$favorito->buscarPor([
            "where" => "cod_obra = $cod_obra AND cod_usuario = $codUsuario AND borrado = 0"
        ])) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // Obtenemos la obra correspondiente

        $obra = new Obras();
        if (!$obra->buscarPor(["where" => "cod_obra = $cod_obra"])) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // Borramos el favorito (borrado lógico, no físico)

        $favorito->borrado = 1; // → Borrado lógico, no físico

        if (!$favorito->guardar()) {
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // Actualizamos la valoración
        $obra->valoracion = max(0, $obra->valoracion - 0.5);

        if (!$obra->guardar()) {    
            $favorito->borrado = 0; // → Deshacemos el borrado si falla
            $favorito->guardar();
            header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
            exit;
        }

        // _____________ ACTUALIZAMOS LA VALORACIÓN DEL ARTISTA _____________

        $artista = new Usuario();
        if ($artista->buscarPor(["where" => "cod_usuario = " . intval($obra->cod_usuario)])) {
            $artista->actualizarValoracionDesdeObras();
        }

        header("Location: " . Sistema::app()->generaURL(['inicial', 'verObra']) . "?" . http_build_query(["cod_obra" => $cod_obra]));
        exit;
    }


    // —————————————————————————————————————————————
    //                 FUNCIONES 
    // —————————————————————————————————————————————

    /**
     * Limpia los filtros de busqueda y ordenación de la sesión
     *
     * @return void
     */
    public function accionLimpiarFiltros()
    {
        // Borramos el filtro de busqueda de la sesión
        if (isset($_SESSION["nombre_busqueda"])) {
            unset($_SESSION["nombre_busqueda"]);
        }

        // Borramos la ordenación guardada (vuelve a estar por defecto)
        if (isset($_SESSION["orden"])) {
            unset($_SESSION["orden"]);
        }

        // Mandamos a la página de obras sin filtros
        Sistema::app()->irAPagina(["obras", "index"]);
    }

    /**
     * Función que maneja la subida de los archivos de imagen
     *  - Valida extensión (jpg, jpeg, png)
     *  - Mueve archivo a /imagenes/tablaObras/
     * 
     * @return string|null Nombre del archivo si sale bien, null si falla
     */
    private function ordenaObras(array &$obras, string $criterio): array
    {
        // Creamos un array con las funciones de comparación para cada criterio de ordenación
        $arrayFunOrden = [
            "nombre_asc" => function ($a, $b) {
                return strcmp($a["nombre"], $b["nombre"]);
            },
            "nombre_desc" => function ($a, $b) {
                return strcmp($b["nombre"], $a["nombre"]);
            },
            "fecha_asc" => function ($a, $b) {
                return strtotime($a["fecha_alta"]) - strtotime($b["fecha_alta"]);
            },
            "fecha_desc" => function ($a, $b) {
                return strtotime($b["fecha_alta"]) - strtotime($a["fecha_alta"]);
            },
            "valoracion_asc" => function ($a, $b) {
                return $a["valoracion"] - $b["valoracion"];
            },
            "valoracion_desc" => function ($a, $b) {
                return $b["valoracion"] - $a["valoracion"];
            }
        ];

        // Obtenemos función de ordenación, por defecto fecha descendente
        $funcionOrden = $arrayFunOrden[$criterio] ?? $arrayFunOrden["fecha_desc"];

        // Ordenamos array y devolver
        usort($obras, $funcionOrden);
        return $obras;
    }


    /**
     * Función que controla la subida de los archivos de imagen para las obras. 
     *  - Valida la extensión
     *  - Valida el tamaño
     *  - Genera un nombre único
     *  - Mueve el archivo al directorio correspondiente.
     *
     * @param string $nombreObra
     * @return string|null
     */
    private function subirImagen(string $nombreObra = "imagen"): ?string
    {

        // Validamos que se ha subido un archivo sin errores
        // El formulario envía con name="Obra[img_principal]" porque así lo genera modeloFile()
        if (!isset($_FILES["Obra"]) || !isset($_FILES["Obra"]["error"]["img_principal"])) {
            return null;
        }

        $error = $_FILES["Obra"]["error"]["img_principal"];
        if ($error !== UPLOAD_ERR_OK) {
            return null;
        }

        // Definimos las extensiones permitidas
        $extensionesPermitidas = ["jpg", "jpeg", "png"];

        $nombreArchivo = basename($_FILES["Obra"]["name"]["img_principal"]);

        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION)); // → Obtenemos la extensión del archivo y la convertimos a minúsculas

        // Validamos la extensión
        if (!in_array($ext, $extensionesPermitidas)) {
            return null;
        }

        // Validamos el tamaño máximo (5MB)
        if ($_FILES["Obra"]["size"]["img_principal"] > 5 * 1024 * 1024) {
            return null;
        }

        // Generamos un nombre único para evitar conflictos que será: nombre_obra + num aleatorio de 3 cifras + extensión
        // Limpiamos el nombre: convertimos a minúsculas, reemplazamos espacios por guiones bajos, eliminamos caracteres especiales
        $nombreLimpio = strtolower($nombreObra);
        $nombreLimpio = preg_replace('/[^a-z0-9_-]/', '', str_replace(" ", "_", $nombreLimpio));

        // Num. aleatorio de 3 cifras
        $numeroAleatorio = rand(100, 999);

        // Nombre final → nombre_obra_999.ext
        $nombreNuevo = $nombreLimpio . "_" . $numeroAleatorio . "." . $ext;
        $rutaDestino = __DIR__ . "/../../imagenes/tablaObras/" . $nombreNuevo;

        // Creamos un directorio si no existe
        $dirDestino = dirname($rutaDestino);
        if (!is_dir($dirDestino)) {
            mkdir($dirDestino, 0755, true);
        }

        // Movemos el archivo
        if (move_uploaded_file($_FILES["Obra"]["tmp_name"]["img_principal"], $rutaDestino)) {
            return $nombreNuevo;
        }

        return null;
    }
}
