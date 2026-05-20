<?php

// —————————————————————————————————————————————————————————————
//                       API REST - CLIENTES
// —————————————————————————————————————————————————————————————

class apiControlador extends CControlador
{
    // —————————————————————————————————————————————————————————————
    //                 ACCIÓN PRINCIPAL - API REST
    // —————————————————————————————————————————————————————————————

	/**
	 * Acción principal que maneja todas las operaciones CRUD
	 * Detecta el método HTTP y ejecuta la operación correspondiente
	 * 
	 * GET → Listar todos o obtener uno específico
	 * POST → Crear cliente
	 * PUT → Actualizar cliente
	 * DELETE → Eliminar cliente
	 */
	public function accionClientes()
	{
		// _______ VERIFICAMOS ACCESO Y AUTENTICACIÓN _______

		$this->verificarAcceso();

		// _______ PROCESAMOS SEGÚN MÉTODO HTTP _______

		$metodo = $_SERVER["REQUEST_METHOD"];

		if ($metodo === "GET") {
			if (isset($_GET["cod_cliente"])) {
				// Obtenemos un cliente específico
				$this->obtenerCliente();
			} else {
				// Obtenemos el listado de los clientes (con filtros y paginación)
				$this->listarClientes();
			}
			return;
		}

		if ($metodo === "POST") {
			$this->crearCliente();
			return;
		}

		if ($metodo === "PUT") {
			$this->actualizarCliente();
			return;
		}

		if ($metodo === "DELETE") {
			$this->eliminarCliente();
			return;
		}

		// Si el método no es ninguno de los anteriores, devolvemos error 405
		$this->respuestaJSON(false, ["metodo" => "Método no permitido. Use GET, POST, PUT o DELETE"], 405);
	}

    // —————————————————————————————————————————————————————————————
    //                   MÉTODOS PRIVADOS - CRUD
    // —————————————————————————————————————————————————————————————

	/**
	 * Método qie verifica que el usuario esté autentificado y tenga permisos
	 *
	 * @return void
	 */
	private function verificarAcceso(): void
	{
		// Validamos que hay un usuario autentificado
		if (!Sistema::app()->acceso()->hayUsuario()) {
			http_response_code(401);
			header("Content-Type: application/json");

			// Devolvemos un mensaje de error en formato JSON indicando que no está autentificado
			echo json_encode(["datos" => ["acceso" => "No autentificado. Debe iniciar sesión"], "correcto" => false], JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Validamos los permisos (8 = usuario(artista), 9 = admin)
		if (!Sistema::app()->acceso()->puedePermiso(8) && !Sistema::app()->acceso()->puedePermiso(9)) {
			http_response_code(403);
			header("Content-Type: application/json");

			// Devolvemos un mensaje de error en formato JSON indicando que no tiene permisos suficientes
			echo json_encode(["datos" => ["acceso" => "No autorizado. Permisos insuficientes"], "correcto" => false], JSON_UNESCAPED_UNICODE);
			exit;
		}
	}

	/**
	 * Método que lista todos los clientes (con filtros y paginación)
	 *
	 * @return void
	 */
	private function listarClientes(): void
	{
		$nick = Sistema::app()->acceso()->getNick();
		$codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
		$esAdmin = Sistema::app()->acceso()->puedePermiso(9);
		$bd = Sistema::app()->BD();

		// Construimos where segun los permisos que tenga el usuario conetcado
		if ($esAdmin) {
			$whereBase = "1=1";
		} else {
			$whereBase = "cod_usuario = " . intval($codUsuario);
		}

		// Filtro por BORRADO
		$mostrarBorrados = isset($_GET["borrado"]) && intval($_GET["borrado"]) === 1; // → Mostrar solo eliminados
		if (isset($_GET["borrado"]) && in_array($_GET["borrado"], ["0", "1"])) {
			$whereBase .= " AND borrado = " . intval($_GET["borrado"]);
		} else {
			// Por defecto, solo mostrar no eliminados
			$whereBase .= " AND borrado = 0";
		}

		$whereFiltros = "";

		// Hacemos una busqueda general (nombre o email)
		if (!empty($_GET["busqueda"])) {
			$busqueda = CGeneral::addSlashes($_GET["busqueda"]);
			$whereFiltros .= " AND (nombre LIKE '%$busqueda%' OR email LIKE '%$busqueda%')";
		} else {
			// Filtros individuales (compatibilidad con vista gestion)
			if (!empty($_GET["nombre"])) {
				$nombre = CGeneral::addSlashes($_GET["nombre"]);
				$whereFiltros .= " AND nombre LIKE '%$nombre%'";
			}
			if (!empty($_GET["email"])) {
				$email = CGeneral::addSlashes($_GET["email"]);
				$whereFiltros .= " AND email LIKE '%$email%'";
			}
		}

		// Contamos el total de registros
		$sqlCount = "SELECT COUNT(*) as total FROM clientes WHERE $whereBase $whereFiltros";
		$cmdCount = $bd->crearConsulta($sqlCount);
		$filasCount = $cmdCount->filas();
		$totalRegistros = $filasCount[0]["total"] ?? 0;

		// Configuramos la paginación
		$registrosPagina = intval($_GET["registros_pagina"] ?? 10);
		$paginaActual = intval($_GET["pagina"] ?? 1);
		$totalPaginas = ceil($totalRegistros / $registrosPagina);

		if ($paginaActual < 1) {
			$paginaActual = 1;
		}
		if ($paginaActual > $totalPaginas && $totalPaginas > 0) {
			$paginaActual = $totalPaginas;
		}

		$registroDesde = ($paginaActual - 1) * $registrosPagina;

		// Construimos el order by teniendo en cuenta los campos y la inyección SQL
		$camposOrdenables = ["nombre", "email", "pais", "presupuesto", "fecha_alta"];
		$ordenarPor = in_array($_GET["ordenar_por"] ?? "fecha_alta", $camposOrdenables)
			? $_GET["ordenar_por"]
			: "fecha_alta";
		$orden = in_array($_GET["orden"] ?? "DESC", ["ASC", "DESC"])
			? $_GET["orden"]
			: "DESC";

		// Consulta para obtener datos paginados
		$sql = "SELECT * FROM clientes 
                WHERE $whereBase $whereFiltros 
                ORDER BY $ordenarPor $orden 
                LIMIT $registroDesde, $registrosPagina";

		$cmd = $bd->crearConsulta($sql);
		$clientes = $cmd->filas() ?? [];

		// Preparamos respuesta
		$this->respuestaJSON(true, [
			"clientes" => $clientes,
			"total" => $totalRegistros,
			"pagina" => $paginaActual,
			"registros_pagina" => $registrosPagina,
			"total_paginas" => $totalPaginas
		]);
	}

	/**
	 * Método que obtiene un cliente específico
	 *
	 * @return void
	 */
	private function obtenerCliente(): void
	{
		// Validamos que se ha pasado un cod_cliente válido
		$nick = Sistema::app()->acceso()->getNick();
		$codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
		$esAdmin = Sistema::app()->acceso()->puedePermiso(9);
		$bd = Sistema::app()->BD();

		$codCliente = intval($_GET["cod_cliente"]);

		// Construimos el where segun los permisos
		$whereSecurity = $esAdmin ? "" : "AND cod_usuario = " . intval($codUsuario) . " ";

		$sql = "SELECT * FROM clientes WHERE cod_cliente = " . $codCliente . " 
                $whereSecurity AND borrado = 0";

		$cmd = $bd->crearConsulta($sql);
		$filas = $cmd->filas();

		if (empty($filas)) {
			$this->respuestaJSON(false, ["error" => "Cliente no encontrado"], 404);
			return;
		}

		$cliente = $filas[0];
		$this->respuestaJSON(true, $cliente);
	}

	/**
	 * Método que crea un nuevo cliente
	 *
	 * @return void
	 */
	private function crearCliente(): void
	{
		// Obtenemos el usuario actual para asignar el cod_usuario al cliente
		$nick = Sistema::app()->acceso()->getNick();
		$codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
		$bd = Sistema::app()->BD();

		// Obtenemos los datos del post
		$datos = [
			"nombre" => trim($_POST["nombre"] ?? ""),
			"email" => trim($_POST["email"] ?? ""),
			"direccion" => trim($_POST["direccion"] ?? ""),
			"pais" => trim($_POST["pais"] ?? ""),
			"presupuesto" => floatval($_POST["presupuesto"] ?? 0)
		];

		// Validamos los datos

		$errores = [];

		if (empty($datos["nombre"])) {
			$errores["nombre"][] = "El nombre es obligatorio";
		}
		// Validación de largo máximo para nombre
		else if (strlen($datos["nombre"]) > 100) {
			$errores["nombre"][] = "El nombre no puede exceder 100 caracteres";
		}
		if (empty($datos["email"])) {
			$errores["email"][] = "El email es obligatorio";
		} else if (!filter_var($datos["email"], FILTER_VALIDATE_EMAIL)) {
			$errores["email"][] = "El email no es válido";
		}
		if ($datos["presupuesto"] < 0) {
			$errores["presupuesto"][] = "El presupuesto no puede ser negativo";
		}

		// Si hay errores, devolvemos un array de errores
		if (!empty($errores)) {
			$this->respuestaJSON(false, $errores, 400);
			return;
		}

		// Insertamos el nuevo cliente en la BD
		$sql = "INSERT INTO clientes (cod_usuario, nombre, email, direccion, pais, presupuesto, fecha_alta, borrado) 
                VALUES (" . intval($codUsuario) . ", 
                        '" . CGeneral::addSlashes($datos["nombre"]) . "', 
                        '" . CGeneral::addSlashes($datos["email"]) . "', 
                        '" . CGeneral::addSlashes($datos["direccion"]) . "', 
                        '" . CGeneral::addSlashes($datos["pais"]) . "', 
                        " . floatval($datos["presupuesto"]) . ", 
                        NOW(), 0)";

		$cmd = $bd->crearConsulta($sql);

		if ($cmd->error() == 0) {
			// Devolvemos el cod_cliente generado para usarlo en el combo de crear encargos
			$this->respuestaJSON(true, [
				"mensaje"     => "Cliente creado exitosamente",
				"cod_cliente" => intval($cmd->idGenerado())
			], 201);
		} else {
			$this->respuestaJSON(false, ["error" => "Error al crear cliente"], 500);
		}
	}

	/**
	 * Método que actualiza/modifica un cliente
	 *
	 * @return void
	 */
	private function actualizarCliente(): void
	{
		// Obtenemos el usuario actual para verificar permisos
		$nick = Sistema::app()->acceso()->getNick();
		$codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
		$esAdmin = Sistema::app()->acceso()->puedePermiso(9);
		$bd = Sistema::app()->BD();

		// Obtenemos los datos del PUT
		parse_str(file_get_contents("php://input"), $PUT);
		$codCliente = intval($PUT["cod_cliente"] ?? 0);

		if ($codCliente == 0) {
			$this->respuestaJSON(false, ["error" => "Código de cliente no válido"], 400);
			return;
		}

		// Verificamos que el usuario existe y tiene permisos
		$whereSecurity = $esAdmin ? "" : "AND cod_usuario = " . intval($codUsuario) . " ";
		$sql = "SELECT * FROM clientes WHERE cod_cliente = " . $codCliente . " $whereSecurity AND borrado = 0";
		$cmd = $bd->crearConsulta($sql);
		$filas = $cmd->filas();

		if (empty($filas)) {
			$this->respuestaJSON(false, ["error" => "Cliente no encontrado"], 404);
			return;
		}

		$datos = [
			"nombre" => trim($PUT["nombre"] ?? ""),
			"email" => trim($PUT["email"] ?? ""),
			"direccion" => trim($PUT["direccion"] ?? ""),
			"pais" => trim($PUT["pais"] ?? ""),
			"presupuesto" => floatval($PUT["presupuesto"] ?? 0)
		];

		// Validamos los datos

		$errores = []; // → Array para almacenar los errores

		// Nombre
		if (empty($datos["nombre"])) {
			$errores["nombre"][] = "El nombre es obligatorio";
		} else if (strlen($datos["nombre"]) > 100) {
			$errores["nombre"][] = "El nombre no puede exceder 100 caracteres";
		}

		// Email
		if (empty($datos["email"])) {
			$errores["email"][] = "El email es obligatorio";
		} else if (!filter_var($datos["email"], FILTER_VALIDATE_EMAIL)) {
			$errores["email"][] = "El email no es válido";
		}

		// Presupuesto
		if ($datos["presupuesto"] < 0) {
			$errores["presupuesto"][] = "El presupuesto no puede ser negativo";
		}

		// Si hay errores, devolvemos un array de errores
		if (!empty($errores)) {
			$this->respuestaJSON(false, $errores, 400);
			return;
		}

		// Actualizamos los datos en la BD
		$sql = "UPDATE clientes SET 
                nombre = '" . CGeneral::addSlashes($datos["nombre"]) . "',
                email = '" . CGeneral::addSlashes($datos["email"]) . "',
                direccion = '" . CGeneral::addSlashes($datos["direccion"]) . "',
                pais = '" . CGeneral::addSlashes($datos["pais"]) . "',
                presupuesto = " . floatval($datos["presupuesto"]) . "
                WHERE cod_cliente = " . $codCliente;

		$cmd = $bd->crearConsulta($sql);

		if ($cmd->error() == 0) {
			$this->respuestaJSON(true, ["mensaje" => "Cliente actualizado exitosamente"]);
		} else {
			$this->respuestaJSON(false, ["error" => "Error al actualizar cliente"], 500);
		}
	}

	/**
	 * Método que elimina un cliente
	 *
	 * @return void
	 */
	private function eliminarCliente(): void
	{
		$nick = Sistema::app()->acceso()->getNick();
		$codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
		$esAdmin = Sistema::app()->acceso()->puedePermiso(9);
		$bd = Sistema::app()->BD();

		parse_str(file_get_contents("php://input"), $DELETE);
		$codCliente = intval($DELETE["cod_cliente"] ?? 0);

		if ($codCliente == 0) {
			$this->respuestaJSON(false, ["error" => "Código de cliente no válido"], 400);
			return;
		}

		// Comprobamos que el cliente existe y que el usuario tiene permisos para borrarlo
		$whereSecurity = $esAdmin ? "" : "AND cod_usuario = " . intval($codUsuario) . " ";
		$sql = "SELECT * FROM clientes WHERE cod_cliente = " . $codCliente . " $whereSecurity AND borrado = 0";
		$cmd = $bd->crearConsulta($sql);
		$filas = $cmd->filas();

		if (empty($filas)) {
			$this->respuestaJSON(false, ["error" => "Cliente no encontrado"], 404);
			return;
		}

		// Comprobamos que no tenga encargos asociados
		$sqlEncargos = "SELECT COUNT(*) as total FROM encargos WHERE cod_cliente = " . $codCliente . " AND borrado = 0";
		$cmdEncargos = $bd->crearConsulta($sqlEncargos);
		$filasEncargos = $cmdEncargos->filas();
		$totalEncargos = $filasEncargos[0]["total"] ?? 0;

		if ($totalEncargos > 0) {
			$this->respuestaJSON(false, ["error" => "No se puede borrar un cliente con encargos asociados"], 409);
			return;
		}

		// Hacemos un borrado lógico
		$sql = "UPDATE clientes SET borrado = 1 WHERE cod_cliente = " . $codCliente;
		$cmd = $bd->crearConsulta($sql);

		if ($cmd->error() == 0) {
			$this->respuestaJSON(true, ["mensaje" => "Cliente eliminado exitosamente"]);
		} else {
			$this->respuestaJSON(false, ["error" => "Error al eliminar cliente"], 500);
		}
	}

    // —————————————————————————————————————————————————————————————
    //                MÉTODO AUXILIAR - RESPUESTA JSON
    // —————————————————————————————————————————————————————————————

	/**
	 * Devuelve respuesta JSON en formato estándar
	 * 
	 * Estructura de la respuesta:
	 * - Éxito: {"correcto": true, "datos": {...}}
	 * - Error validación: {"correcto": false, "datos": {"campo": ["error1", "error2"]}}
	 * - Error general: {"correcto": false, "datos": {"error": "mensaje"}}
	 * 
	 * @param bool $correcto → Indica si la operación fue exitosa
	 * @param mixed $datos → Datos a devolver (array con datos o con errores)
	 * @param int $codigoHTTP → Código HTTP a devolver (200, 201, 400, 404, 500, etc)
	 */
	private function respuestaJSON(bool $correcto, mixed $datos, int $codigoHTTP = 200): void
	{
		http_response_code($codigoHTTP);
		header("Content-Type: application/json; charset=utf-8");

		// Decodificamos los datos para evitar problemas con caracteres especiales y devolvemos la respuesta en formato JSON
		echo json_encode([
			"datos" => $datos,
			"correcto" => $correcto
		], JSON_UNESCAPED_UNICODE);
		exit;
	}
}
