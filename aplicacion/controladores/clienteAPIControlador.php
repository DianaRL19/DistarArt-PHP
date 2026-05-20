<?php

// —————————————————————————————————————————————————————————————
//               CONTROLADOR CLIENTE API
// —————————————————————————————————————————————————————————————

require_once __DIR__ . "/../../scripts/librerias/peticionesCURL.php";

class clienteAPIControlador extends CControlador
{
	public array $barraUbi = [];
	public array $barraMenu = [];
	private string $urlAPI = ""; // → URL base de la API

	public function __construct()
	{
		// Generamos la URL base de la API
		$baseURL = Sistema::app()->generaURL(["api", "clientes"]);
		$this->urlAPI = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $baseURL;

		// Montamos la barra de menú
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

		// Añadimos "Clientes API" si tiene permiso
		if (Sistema::app()->acceso()->hayUsuario()) {
			if (Sistema::app()->acceso()->puedePermiso(9)) {
				$this->barraMenu[] = [
					"texto" => "Gestión Clientes API",
					"enlace" => ["clienteAPI", "index"]
				];
			} else if (Sistema::app()->acceso()->puedePermiso(8)) {
				$this->barraMenu[] = [
					"texto" => "Mis Clientes",
					"enlace" => ["clienteAPI", "index"]
				];
			}
		}

		// Añadimos "Encargos" si tiene permiso
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
	//              MÉTODO AUXILIAR PARA LA AUTENTICACIÓN
	// —————————————————————————————————————————————————————————————

	private function autentificarCabecera(): array
	{
		// Pasamos la cookie de sesión para que la API pueda autentificar el usuario.
		// - Esto es para que la API pueda validar los permisos del usuario en cada petición, ya que si no se pasa la cookie, 
		//   la API no reconoce al usuario y no puede validar permisos, y las peticiones se denegarían por falta de permisos.
		return ["Cookie: " . session_name() . "=" . session_id()];
	}

	// —————————————————————————————————————————————————————————————
	//                     ACCIÓN - LISTAR (INDEX)
	// —————————————————————————————————————————————————————————————

	public function accionIndex()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Clientes",
				"enlace" => ["clienteAPI", "index"]
			],
		];

		// Validamos los permisos

		if (!Sistema::app()->acceso()->hayUsuario()) {
			if (Sistema::app()->sesion()->haySesion()) {
				$_SESSION["pagina"] = ["clienteAPI", "index"];
			}
			Sistema::app()->irAPagina(["registro", "login"]);
			return;
		}

		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			Sistema::app()->paginaError(403, "No tienes permisos para acceder");
			return;
		}

		// —————————————————————————————————————————————
		//      BÚSQUEDA (buscador de la cabecera)
		// —————————————————————————————————————————————

		// Si el buscador se envía vacío, limpiamos el criterio de busqueda de la sesión
		if (isset($_GET["busqueda"]) && empty($_GET["busqueda"])) {
			unset($_SESSION["busqueda_clientes"]);
		}

		// Si el buscador se envía con contenido, lo guardamos en sesión para mantenerlo al navegar entre páginas
		if (isset($_GET["busqueda"]) && !empty($_GET["busqueda"])) {
			$_SESSION["busqueda_clientes"] = htmlspecialchars(trim($_GET["busqueda"]));
		}

		$busqueda = $_SESSION["busqueda_clientes"] ?? "";

		// —————————————————————————————————————————————————————
		//     ORDENACIÓN (lista desplegable en la vista)
		// —————————————————————————————————————————————————————

		$ordenesPermitidos = ["fecha_desc", "fecha_asc", "nombre_asc", "nombre_desc", "email_asc", "presupuesto_desc"];

		if (isset($_GET["orden"]) && in_array($_GET["orden"], $ordenesPermitidos)) {
			$_SESSION["orden_clientes"] = $_GET["orden"];
		}

		if (isset($_GET["orden"]) && empty($_GET["orden"])) {
			unset($_SESSION["orden_clientes"]);
		}

		$ordenSeleccionado = $_SESSION["orden_clientes"] ?? "fecha_desc";

		$mapaOrden = [
			"fecha_desc"       => ["ordenar_por" => "fecha_alta",  "orden" => "DESC"],
			"fecha_asc"        => ["ordenar_por" => "fecha_alta",  "orden" => "ASC"],
			"nombre_asc"       => ["ordenar_por" => "nombre",      "orden" => "ASC"],
			"nombre_desc"      => ["ordenar_por" => "nombre",      "orden" => "DESC"],
			"email_asc"        => ["ordenar_por" => "email",       "orden" => "ASC"],
			"presupuesto_desc" => ["ordenar_por" => "presupuesto", "orden" => "DESC"],
		];

		$ordenAPI = $mapaOrden[$ordenSeleccionado];

		// —————————————————————————————————————————————
		//                 PAGINACIÓN
		// —————————————————————————————————————————————

		$pag    = isset($_GET["pag"]) && is_numeric($_GET["pag"]) ? max(1, intval($_GET["pag"])) : 1;
		$regPag = isset($_GET["reg_pag"]) && is_numeric($_GET["reg_pag"]) ? intval($_GET["reg_pag"]) : 10;

		$esAdmin = Sistema::app()->acceso()->puedePermiso(9); // → Verificamos si es admin

		// Construimos parámetros para paginar los datps de la API
		$parametros = [
			"ordenar_por"      => $ordenAPI["ordenar_por"],
			"orden"            => $ordenAPI["orden"],
			"pagina"           => $pag,
			"registros_pagina" => $regPag,
		];

		if ($busqueda !== "") {
			$parametros["busqueda"] = $busqueda;
		}

		// Cerramos la sesión para evitar bloqueos mientras hacemos la petición a la API
		session_write_close();


		// Pasamos el array de parámetros a parametros de consulta para la API
		$cadenaSentencia = http_build_query($parametros);

		// Hacemos la petición a la API para obtener los clientes con los parámetros de ordenación, busqueda y paginación
		$respuestaAPI = petCURLGet($this->urlAPI, $cadenaSentencia, $this->autentificarCabecera());

		// Decodificamos la respuesta de la API
		$datosAPI = json_decode($respuestaAPI, true);

		// Si la respuesta no es correcta, mostramos error
		if (!$datosAPI || !isset($datosAPI["correcto"]) || !$datosAPI["correcto"]) {
			Sistema::app()->paginaError(500, "Error al consultar la API");
			return;
		}

		// Extraemos los datos de la respuesta de la API
		$datos          = $datosAPI["datos"] ?? [];
		$clientes       = $datos["clientes"] ?? [];
		$totalRegistros = $datos["total"] ?? 0;
		$paginaActual   = intval($datos["pagina"] ?? 1);
		$registrosPagina = intval($datos["registros_pagina"] ?? 10);
		$registroDesde  = ($paginaActual - 1) * $registrosPagina;

		// Montamos el paginador
		$paginador = [
			"URL"               => Sistema::app()->generaURL(["clienteAPI", "index"]),
			"TOTAL_REGISTROS"   => $totalRegistros,
			"PAGINA_ACTUAL"     => $paginaActual,
			"REGISTROS_PAGINA"  => $registrosPagina,
			"PAGINAS_MOSTRADAS" => 5,
		];

		// Montamos las opciones de ordenación para la vista ( la lista desplegable/combo)
		$opcionesOrden = [
			"fecha_desc"       => "Más recientes",
			"fecha_asc"        => "Más antiguos",
			"nombre_asc"       => "Nombre (A-Z)",
			"nombre_desc"      => "Nombre (Z-A)",
			"email_asc"        => "Email (A-Z)",
			"presupuesto_desc" => "Mayor presupuesto",
		];


		// Dibujamos la vista pasando los datos obtenidos de la API
		$this->dibujaVista("index", [
			"clientes"        => $clientes,
			"barraUbi"        => $this->barraUbi,
			"barraMenu"       => $this->barraMenu,
			"totalRegistros"  => $totalRegistros,
			"paginaActual"    => $paginaActual,
			"registrosPagina" => $registrosPagina,
			"registroDesde"   => $registroDesde + 1,
			"registroHasta"   => min($registroDesde + $registrosPagina, $totalRegistros),
			"paginador"       => $paginador,
			"opcionesOrden"   => $opcionesOrden,
			"ordenSeleccionado" => $ordenSeleccionado,
			"busqueda"        => $busqueda,
			"esAdmin"         => $esAdmin,
		]);
	}

	// —————————————————————————————————————————————————————————————
	//                       ACCIÓN - NUEVO 
	// —————————————————————————————————————————————————————————————

	public function accionNuevo()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Clientes",
				"enlace" => ["clienteAPI", "index"]
			],
			[
				"texto" => "Nuevo",
				"enlace" => ["clienteAPI", "nuevo"]
			],
		];

		// Validamos los permisos

		if (!Sistema::app()->acceso()->hayUsuario()) {
			Sistema::app()->irAPagina(["registro", "login"]);
			return;
		}

		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			Sistema::app()->paginaError(403, "No tienes permisos");
			return;
		}

		// Comprobamos el metodo por el que se realiza la petición, si es Get, mostramos el formulario.
		if ($_SERVER["REQUEST_METHOD"] === "GET") {
			$this->dibujaVista("nuevo", [
				"barraUbi" => $this->barraUbi,
				"barraMenu" => $this->barraMenu,
				"errores" => [],
				"datos" => []
			]);
			return;
		}

		// Y si es POST, procesamos el formulario para crear el cliente a través de la API
		if ($_SERVER["REQUEST_METHOD"] === "POST") {
			$datos = [
				"nombre" => trim($_POST["nombre"] ?? ""),
				"email" => trim($_POST["email"] ?? ""),
				"direccion" => trim($_POST["direccion"] ?? ""),
				"pais" => trim($_POST["pais"] ?? ""),
				"presupuesto" => floatval($_POST["presupuesto"] ?? 0)
			];

			// Cerramos la sesión para evitar bloqueos mientras hacemos la petición a la API
			session_write_close();

			// Pasamos el array de datos a parametros de consulta para la API
			$parametrosAPI = http_build_query($datos);

			// Hacemos la petición a la API para crear el cliente
			$respuestaAPI = petCURLPost($this->urlAPI, $parametrosAPI, $this->autentificarCabecera());

			// Decodificamos la respuesta de la API
			$datosAPI = json_decode($respuestaAPI, true);

			// Si hay errores de validación, mostramos formulario con errores
			if (!$datosAPI["correcto"]) {
				$errores = $datosAPI["datos"] ?? [];
				$this->dibujaVista("nuevo", [
					"barraUbi" => $this->barraUbi,
					"barraMenu" => $this->barraMenu,
					"errores" => $errores,
					"datos" => $datos
				]);
				return;
			}

			// Si todo ha ido bien, comprobamos desde donde se hizo la petición de crear un nuevo cliente para de esta forma redirigir al usuario 
			// a la página de la que vino.
			$volver  = $_GET["volver"] ?? "";
			$accion  = $_GET["accion"] ?? "crear"; // → Acción de encargos al volver (crear o gestionCrear)
			$nuevoId = intval($datosAPI["datos"]["cod_cliente"] ?? 0);

			if ($volver === "encargos" && $nuevoId > 0) { // → Redirigimos al crear de encargos (normal o admin) con el cliente preseleccionado
				$accionDestino = ($accion === "gestionCrear") ? "gestionCrear" : "crear";
				Sistema::app()->irAPagina(["encargos", $accionDestino], ["cod_cliente_nuevo" => $nuevoId]);
				return;
			}

			Sistema::app()->irAPagina(["clienteAPI", "index"]); // → Redirigimos a la lista por defecto
		}
	}

	// —————————————————————————————————————————————————————————————
	//                       ACCIÓN - VER
	// —————————————————————————————————————————————————————————————

	public function accionVer()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Clientes",
				"enlace" => ["clienteAPI", "index"]
			],
			[
				"texto" => "Ver",
				"enlace" => ["clienteAPI", "ver"]
			],
		];

		// Validamos los permisos

		if (!Sistema::app()->acceso()->hayUsuario()) {
			Sistema::app()->irAPagina(["registro", "login"]);
			return;
		}

		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			Sistema::app()->paginaError(403, "No tienes permisos");
			return;
		}

		// Obtenemos el cliente de la API
		$codCliente = intval($_GET["cod_cliente"] ?? 0);
		if ($codCliente == 0) {
			Sistema::app()->paginaError(400, "Código de cliente no válido");
			return;
		}

		session_write_close(); // → Cerramos la sesión

		//  Llamamos a la API para obtener el cliente
		$respuestaAPI = petCURLGet($this->urlAPI, "cod_cliente=" . $codCliente, $this->autentificarCabecera());
		$datosAPI = json_decode($respuestaAPI, true);

		if (!$datosAPI || !$datosAPI["correcto"]) {
			Sistema::app()->paginaError(404, "Cliente no encontrado");
			return;
		}

		$cliente = $datosAPI["datos"] ?? [];

		// Dibujamos la vista pasando los datos obtenidos de la API

		$this->dibujaVista("ver", [
			"cliente" => $cliente,
			"barraUbi" => $this->barraUbi,
			"barraMenu" => $this->barraMenu
		]);
	}

	// —————————————————————————————————————————————————————————————
	//                     ACCIÓN - MODIFICAR
	// —————————————————————————————————————————————————————————————

	public function accionModificar()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Clientes",
				"enlace" => ["clienteAPI", "index"]
			],
			[
				"texto" => "Editar",
				"enlace" => ["clienteAPI", "modificar"]
			],
		];

		// Validamos los permisos

		if (!Sistema::app()->acceso()->hayUsuario()) {
			Sistema::app()->irAPagina(["registro", "login"]);
			return;
		}

		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			Sistema::app()->paginaError(403, "No tienes permisos");
			return;
		}

		// Obtenemos el cliente de la API

		$codCliente = intval($_GET["cod_cliente"] ?? $_POST["cod_cliente"] ?? 0);
		if ($codCliente == 0) {
			Sistema::app()->paginaError(400, "Código de cliente no válido");
			return;
		}

		session_write_close(); // → Cerramos la sesión

		// Llamamos a la API para obtener el cliente
		$respuestaAPI = petCURLGet($this->urlAPI, "cod_cliente=" . $codCliente, $this->autentificarCabecera());
		$datosAPI = json_decode($respuestaAPI, true);

		if (!$datosAPI || !$datosAPI["correcto"]) {
			Sistema::app()->paginaError(404, "Cliente no encontrado");
			return;
		}

		$cliente = $datosAPI["datos"] ?? [];

		// Comprobamos el método de la petición, si es GET, mostramos el formulario con los datos del cliente para editar
		if ($_SERVER["REQUEST_METHOD"] === "GET") {
			$this->dibujaVista("modificar", [
				"cliente" => $cliente,
				"barraUbi" => $this->barraUbi,
				"barraMenu" => $this->barraMenu,
				"errores" => []
			]);
			return;
		}

		// Y si es POST, procesamos el formulario para modificar el cliente a través de la API
		if ($_SERVER["REQUEST_METHOD"] === "POST") {
			$datos = [
				"cod_cliente" => $codCliente,
				"nombre" => trim($_POST["nombre"] ?? ""),
				"email" => trim($_POST["email"] ?? ""),
				"direccion" => trim($_POST["direccion"] ?? ""),
				"pais" => trim($_POST["pais"] ?? ""),
				"presupuesto" => floatval($_POST["presupuesto"] ?? 0)
			];

			// Llamamos a la API para actualizar
			$parametrosAPI = http_build_query($datos);
			$respuestaAPI = petCURLPut($this->urlAPI, $parametrosAPI, $this->autentificarCabecera());
			$datosAPI = json_decode($respuestaAPI, true);

			// Si hay errores de validación, mostramos formulario con errores
			if (!$datosAPI["correcto"]) {
				$errores = $datosAPI["datos"] ?? [];
				$this->dibujaVista("modificar", [
					"cliente" => $datos,
					"barraUbi" => $this->barraUbi,
					"barraMenu" => $this->barraMenu,
					"errores" => $errores
				]);
				return;
			}

			// Si todo es correcto, redirigimos a index
			Sistema::app()->irAPagina(["clienteAPI", "index"]);
		}
	}

	// —————————————————————————————————————————————————————————————
	//                       ACCIÓN - BORRAR 
	// —————————————————————————————————————————————————————————————

	public function accionBorrar()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Clientes",
				"enlace" => ["clienteAPI", "index"]
			],
			[
				"texto" => "Borrar",
				"enlace" => ["clienteAPI", "borrar"]
			],
		];

		// Validamos los permisos

		if (!Sistema::app()->acceso()->hayUsuario()) {
			Sistema::app()->irAPagina(["registro", "login"]);
			return;
		}

		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			Sistema::app()->paginaError(403, "No tienes permisos");
			return;
		}

		// Obtenemos el cliente de la API

		$codCliente = intval($_GET["cod_cliente"] ?? $_POST["cod_cliente"] ?? 0);
		if ($codCliente == 0) {
			Sistema::app()->paginaError(400, "Código de cliente no válido");
			return;
		}

		session_write_close(); // → Cerramos la sesión

		// Llamamos a la API para obtener el cliente
		$respuestaAPI = petCURLGet($this->urlAPI, "cod_cliente=" . $codCliente, $this->autentificarCabecera());
		$datosAPI = json_decode($respuestaAPI, true);

		if (!$datosAPI || !$datosAPI["correcto"]) {
			Sistema::app()->paginaError(404, "Cliente no encontrado");
			return;
		}

		$cliente = $datosAPI["datos"] ?? [];

		// Comprobamos el método de la petición, si es GET, mostramos la confirmación para borrar
		if ($_SERVER["REQUEST_METHOD"] === "GET") {
			$this->dibujaVista("borrar", [
				"cliente" => $cliente,
				"barraUbi" => $this->barraUbi,
				"barraMenu" => $this->barraMenu
			]);
			return;
		}

		// Y si es POST, procesamos la confirmación para borrar el cliente a través de la API
		if ($_SERVER["REQUEST_METHOD"] === "POST") {
			$parametrosAPI = http_build_query(["cod_cliente" => $codCliente]);
			$respuestaAPI = petCURLDelete($this->urlAPI, $parametrosAPI, $this->autentificarCabecera());
			$datosAPI = json_decode($respuestaAPI, true);

			if (!$datosAPI["correcto"]) {
				Sistema::app()->paginaError(400, $datosAPI["datos"]["error"] ?? "Error al borrar cliente");
				return;
			}

			// Si todo es correcto, redirigimos a index
			Sistema::app()->irAPagina(["clienteAPI", "index"]);
		}
	}

	// —————————————————————————————————————————————————————————————
	//                 ACCIÓN - GESTIÓN (TABLA CRUD)
	// —————————————————————————————————————————————————————————————

	public function accionGestion()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Clientes",
				"enlace" => ["clienteAPI", "index"]
			],
			[
				"texto" => "Gestión",
				"enlace" => ["clienteAPI", "gestion"]
			],
		];

		// Validamos los permisos
		if (!Sistema::app()->acceso()->hayUsuario()) {
			Sistema::app()->irAPagina(["registro", "login"]);
			return;
		}

		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			Sistema::app()->paginaError(403, "No tienes permisos");
			return;
		}

		$esAdmin = Sistema::app()->acceso()->puedePermiso(9);

		// Establecemos los parámetros de busqueda, ordenación y paginación con validación y valores por defecto
		$pag = isset($_GET["pag"]) && is_numeric($_GET["pag"]) ? max(1, intval($_GET["pag"])) : 1;
		$regPag = isset($_GET["reg_pag"]) && is_numeric($_GET["reg_pag"]) ? intval($_GET["reg_pag"]) : 10;
		$nombreFiltro = trim($_GET["nombre"] ?? "");
		$emailFiltro = trim($_GET["email"] ?? "");
		$mostrarEliminados = isset($_GET["borrado"]) && $_GET["borrado"] == 1; // → Filtro para mostrar eliminados

		$camposPermitidos = ["nombre", "email", "pais", "presupuesto", "fecha_alta"];
		$ordenarPor = in_array($_GET["ordenar_por"] ?? "", $camposPermitidos) ? $_GET["ordenar_por"] : "fecha_alta";
		$orden = in_array($_GET["orden"] ?? "", ["ASC", "DESC"]) ? $_GET["orden"] : "DESC";

		// Construimos parámetros para la API
		$parametros = [
			"pagina" => $pag,
			"registros_pagina" => $regPag,
			"ordenar_por" => $ordenarPor,
			"orden" => $orden,
		];

		if ($nombreFiltro !== "") $parametros["nombre"] = $nombreFiltro; // → Filtro por nombre
		if ($emailFiltro !== "")  $parametros["email"]  = $emailFiltro;  // → Filtro por email
		if ($mostrarEliminados) $parametros["borrado"] = 1; // → Parámetro para mostrar solo eliminados

		session_write_close(); // → Cerramos la sesión

		// Pasamos el array de parámetros a parametros de consulta para la API
		$cadenaSentencia = http_build_query($parametros);
		$respuestaAPI = petCURLGet($this->urlAPI, $cadenaSentencia, $this->autentificarCabecera());
		$datosAPI = json_decode($respuestaAPI, true);

		if (!$datosAPI || !isset($datosAPI["correcto"]) || !$datosAPI["correcto"]) {
			Sistema::app()->paginaError(500, "Error al consultar la API");
			return;
		}

		$datos = $datosAPI["datos"] ?? [];
		$clientes = $datos["clientes"] ?? [];
		$totalRegistros = $datos["total"] ?? 0;

		// Cabecera para CGrid

		$cabecera = [
			[
				"ETIQUETA" => "Cód.",
				"CAMPO" => "cod_cliente",
				"ANCHO" => "60"
			],
			[
				"ETIQUETA" => "Nombre",
				"CAMPO" => "nombre"
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
				"ETIQUETA" => "Presupuesto",
				"CAMPO" => "presupuesto",
				"ALINEA" => "der"
			],
			[
				"ETIQUETA" => "Fecha Alta",
				"CAMPO" => "fecha_alta"
			],
			[
				"ETIQUETA" => "Operaciones",
				"CAMPO" => "operaciones",
				"ALINEA" => "cen"
			],
		];

		// Construimos las filas con operaciones
		$filas = [];

		foreach ($clientes as $cliente) {
			$cod = $cliente["cod_cliente"];

			$cliente["presupuesto"] = number_format($cliente["presupuesto"], 2) . "\u{20AC}";
			$cliente["fecha_alta"] = isset($cliente["fecha_alta"]) ? date("d/m/Y", strtotime($cliente["fecha_alta"])) : "";

			$urlVer = Sistema::app()->generaURL(["clienteAPI", "ver"]) . "?cod_cliente=" . $cod;
			$urlModificar = Sistema::app()->generaURL(["clienteAPI", "modificar"]) . "?cod_cliente=" . $cod;
			$urlBorrar = Sistema::app()->generaURL(["clienteAPI", "borrar"]) . "?cod_cliente=" . $cod;

			$cliente["operaciones"] =
				CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/eye.svg", "Ver", ["class" => "icono-op invertir-color"]), $urlVer) . " " .
				CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/pencil-square.svg", "Editar", ["class" => "icono-op"]), $urlModificar) . " " .
				CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/trash3-fill.svg", "Borrar", ["class" => "icono-op"]), $urlBorrar);

			$filas[] = $cliente;
		}

		// URL del paginador con todos los filtros activos para que se mantengan al cambiar de página
		$filtrosURL = ["ordenar_por" => $ordenarPor, "orden" => $orden];
		if ($nombreFiltro !== "") $filtrosURL["nombre"] = $nombreFiltro;
		if ($emailFiltro !== "")  $filtrosURL["email"]  = $emailFiltro;
		if ($mostrarEliminados) $filtrosURL["borrado"] = 1; // → Mantener el filtro de eliminados en la paginación

		$urlPaginador = Sistema::app()->generaURL(["clienteAPI", "gestion"]) . "?" . http_build_query($filtrosURL);

		$paginador = [
			"URL" => $urlPaginador,
			"TOTAL_REGISTROS" => $totalRegistros,
			"PAGINA_ACTUAL" => $pag,
			"REGISTROS_PAGINA" => $regPag,
			"PAGINAS_MOSTRADAS" => 5,
		];

		// Dibujamos la vista pasando los datos obtenidos de la API
		$this->dibujaVista("gestion", [
			"barraUbi" => $this->barraUbi,
			"barraMenu" => $this->barraMenu,
			"cabecera" => $cabecera,
			"filas" => $filas,
			"paginador" => $paginador,
			"totalRegistros" => $totalRegistros,
			"nombreFiltro" => $nombreFiltro,
			"emailFiltro" => $emailFiltro,
			"ordenarPor" => $ordenarPor,
			"orden" => $orden,
			"mostrarEliminados" => $mostrarEliminados,
			"esAdmin" => $esAdmin,
		]);
	}
}
