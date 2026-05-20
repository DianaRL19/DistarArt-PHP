<?php

class inicialControlador extends CControlador
{
	// ______ Propiedades para la vista ________
	public array $menuizq = [];
	public string $nombre = "2daw";

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

		// Añadimos "Inspiración (API Externa)" - Accesible para todos
		$this->barraMenu[] = [
			"texto" => "Inspiración (API Externa)",
			"enlace" => ["inicial", "inspiracion"]
		];

		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			]
		];
	}

	public function accionIndex()
	{

		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Galería",
				"enlace" => ["inicial"]
			]
		];

		// —————————————————————————————————————————————
		//            FILTRADO Y BUSQUEDA
		// —————————————————————————————————————————————

		$galeria = new Galeria();
		$where = ""; // → Aqui almacenaremos las condiciones where

		// Si se envía busqueda vacía, limpiamos la sesión
		if (isset($_GET["busqueda"]) && empty($_GET["busqueda"])) {
			unset($_SESSION["busqueda"]); // → Limpiamos busqueda anterior 
		}

		// Si se envía busqueda con contenido, la guardamos en sesión
		if (isset($_GET["busqueda"]) && !empty($_GET["busqueda"])) {
			$_SESSION["busqueda"] = htmlspecialchars($_GET["busqueda"]); // → Mantenemos la busqueda entre páginas
		}

		// Si hay alguna busqueda activa, montamos la condición where
		if (isset($_SESSION["busqueda"]) && !empty($_SESSION["busqueda"])) {

			$busqueda = $_SESSION["busqueda"]; // → Recuperamos busqueda guardada

			// Buscamos en nombre, categoría y artista sin distinguir mayúsculas
			$where = "LOWER(nombre) LIKE LOWER('%$busqueda%') OR 
					LOWER(descripcion_categoria) LIKE LOWER('%$busqueda%') OR
					LOWER(nick_usuario) LIKE LOWER('%$busqueda%')"; // → Usamos lower para ignorar mayúsculas
		}

		// _____________ ORDENACIÓN _____________

		// Capturamos criterio de orden si viene en GET
		if (isset($_GET["orden"]) && !empty($_GET["orden"])) {
			$_SESSION["orden_galeria"] = htmlspecialchars($_GET["orden"]);
		}

		// Limpiamos orden si viene vacío
		if (isset($_GET["orden"]) && empty($_GET["orden"])) {
			unset($_SESSION["orden_galeria"]);
		}

		// Opciones de ordenación disponibles
		$opcionesOrden = [
			"fecha_desc" => "Más recientes",
			"fecha_asc" => "Más antiguos",
			"valoracion_desc" => "Mejor valoradas",
			"valoracion_asc" => "Menor valoración",
			"nombre_asc" => "Nombre (A-Z)",
			"artista_asc" => "Artista (A-Z)"
		];

		// Determinar ORDER BY según sesión
		$ordenSeleccionado = $_SESSION["orden_galeria"] ?? "fecha_desc";
		
		$arrayOrden = [
			"fecha_desc" => "fecha_alta DESC",
			"fecha_asc" => "fecha_alta ASC",
			"valoracion_desc" => "valoracion DESC",
			"valoracion_asc" => "valoracion ASC",
			"nombre_asc" => "nombre ASC",
			"artista_asc" => "nick_usuario ASC"
		];

		$orderBy = $arrayOrden[$ordenSeleccionado] ?? "fecha_alta DESC";

		// _____________ APLICAR FILTROS _____________

		// Filtro para mostrar solo obras activas (no borradas)
		$condicionBorrado = "borrado = 0"; // → Excluimos obras marcadas como borradas
		$where = $where ? "($where) AND $condicionBorrado" : $condicionBorrado;

		$total = $galeria->buscarTodosNRegistros($where ? ["where" => $where] : []);

		// _____________ PAGINACIÓN _____________

		$regPag = 10; // → Registros por página

		$paginas = ($total / $regPag); // → Calculamos páginas totales

		// Si hay un resto en la division, añadimos una pagina más para los registros sobrantes
		if ($total % $regPag > 0)
			$paginas++;

		// ____________________________________________________________

		$pag = 1;
		// Si el usuario ha entrado en otra pagina, obtenemos ese numero
		if (isset($_GET["pag"])) {
			$pag = intval($_GET["pag"]);
		}
		// Validar que la pagina sea valida. Si no, volver a pagina 1
		if ($pag < 1 || $pag > $paginas)
			$pag = 1;

		// ____________________________________________________________

		$inicioPaginador = ($pag - 1) * $regPag; // → pagina inicial para que el limite de la consulta

		// Nos aseguramos de que nunca sea negativo
		if ($inicioPaginador < 0)
			$inicioPaginador = 0;

		$limit = "$inicioPaginador,$regPag"; // → Formato limit para la sentencia de la consulta

		// ____________________________________________________________

		$opciones = [
			"limit" => $limit,
			"order" => $orderBy
		];

		// Solo añadimos el where si hay un filtro activo, para no tener problemas si la variable está vacía
		if ($where) {
			$opciones["where"] = $where;
		}

		$obras = $galeria->buscarTodos($opciones);

		// _____________ PREPARAR PAGINADOR _____________

		// URL del paginador (busqueda manejada por sesión)
		$opcPaginador = [
			"URL" => Sistema::app()->generaURL(["inicial", "index"]),
			"TOTAL_REGISTROS" => $total,
			"PAGINA_ACTUAL" => $pag,
			"REGISTROS_PAGINA" => $regPag,
			"PAGINAS_MOSTRADAS" => 5,
		];

		$this->mostrarBuscador = true; // → Activamos el buscador de la cabecera

		// Dibujamos la vista 
		$this->dibujaVista("index", [
			"imagenes" => $obras, 
			"paginador" => $opcPaginador,
			"opcionesOrden" => $opcionesOrden,
			"ordenSeleccionado" => $ordenSeleccionado,
			"busqueda" => $busqueda ?? "",
		],"DistarArt");
	}

	// —————————————————————————————————————————————
	//      VER DETALLE DE OBRA (Desde galería)
	// —————————————————————————————————————————————

	public function accionVerObra()
	{
		// Pillamos el código de obra del GET
		$cod_obra = intval($_REQUEST["cod_obra"] ?? 0); 

		if ($cod_obra <= 0) {
			Sistema::app()->paginaError(404, "Código de obra inválido");
			return;
		}

		// Buscamos obra activa (que no esté borrada)
		$obra = new Obras();

		if (!$obra->buscarPor(["where" => "cod_obra = $cod_obra AND borrado = 0"])) {
			Sistema::app()->paginaError(404, "Obra no encontrada");
			return;
		}

		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Galería",
				"enlace" => ["inicial"]
			],
			[
				"texto" => htmlspecialchars($obra->nombre),
				"enlace" => ""
			]
		];

		// Dibujamos la vista
		$this->dibujaVista("detalle", ["obra" => $obra], "Detalle de Obra");
	}

	// —————————————————————————————————————————————
	//       EXPORTAR GALERÍA FILTRADA A PDF
	// —————————————————————————————————————————————

	public function accionExportarGaleria()
	{
		// Obtenemos todas las obras aplicando los filtros para exportar lo que se muestra en la galería.
		$galeria = new Galeria();
		$where = ""; 

		// Obtenemos los criterios de busqueda y ordenación de la sesión
		$busqueda = $_SESSION["busqueda"] ?? "";

		// Montamos el where igual que en accionIndex
		if (!empty($busqueda)) {
			$where = "LOWER(nombre) LIKE LOWER('%$busqueda%') OR 
					LOWER(descripcion_categoria) LIKE LOWER('%$busqueda%') OR
					LOWER(nick_usuario) LIKE LOWER('%$busqueda%')";
		}

		// Obtenemos el orden seleccionado en sesión
		$ordenSeleccionado = $_SESSION["orden_galeria"] ?? "fecha_desc";
		
		$arrayOrden = [
			"fecha_desc" => "fecha_alta DESC",
			"fecha_asc" => "fecha_alta ASC",
			"valoracion_desc" => "valoracion DESC",
			"valoracion_asc" => "valoracion ASC",
			"nombre_asc" => "nombre ASC",
			"artista_asc" => "nick_usuario ASC"
		];

		$orderBy = $arrayOrden[$ordenSeleccionado] ?? "fecha_alta DESC";

		// Filtro para mostrar solo obras activas (sin las borradas)
		$condicionBorrado = "borrado = 0";
		$where = $where ? "($where) AND $condicionBorrado" : $condicionBorrado;

		// Montamos las opciones de la consulta

		$opciones = ["order" => $orderBy];
		if ($where) {
			$opciones["where"] = $where;
		}

		$obras = $galeria->buscarTodos($opciones);
		$totalObras = count($obras);

		if ($totalObras === 0) {
			Sistema::app()->paginaError(400, "No hay obras para exportar con los filtros aplicados");
			return;
		}

		// —————————————————————————————————————————————
		//          CREAR INSTANCIA DE PDF
		// —————————————————————————————————————————————

		$pdf = new pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->AddPage(); // → Añadimos una página para empezar a escribir el contenido

		// Configuramos el título y los filtros
		$pdf->setTitulo("GALERÍAS DISTARART - DOCUMENTO DE OBRAS");
		
		$infoFiltros = "Total de obras: " . $totalObras;
		if (!empty($busqueda)) {
			$infoFiltros .= " | Búsqueda: \"" . htmlspecialchars($busqueda) . "\"";
		}
		$pdf->setInfoFiltros($infoFiltros);

		// —————————————————————————————————————————————
		//     CREAR ENCABEZADO DE TABLA
		// —————————————————————————————————————————————

		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->SetFillColor(81, 27, 198); // → Morado del logo de DistarArt 
		$pdf->SetTextColor(255, 255, 255); // → Texto blanco

		// Anchos de las columnas
		$anchoNombre = 45;
		$anchoArtista = 40;
		$anchoFecha = 40;
		$anchoValoracion = 35;
		$anchoCategoria = 35;

		$pdf->Cell($anchoNombre, 8, "Nombre", 1, 0, 'C', true);
		$pdf->Cell($anchoArtista, 8, "Artista", 1, 0, 'C', true);
		$pdf->Cell($anchoFecha, 8, "Fecha", 1, 0, 'C', true);
		$pdf->Cell($anchoCategoria, 8, "Categoría", 1, 0, 'C', true);
		$pdf->Cell($anchoValoracion, 8, "Valoración", 1, 1, 'C', true);

		// —————————————————————————————————————————————
		//        AGREGAR LAS FILAS DE DATOS
		// —————————————————————————————————————————————

		$pdf->SetFont('helvetica', '', 9);
		$pdf->SetTextColor(0, 0, 0);

		$alturaFila = 7;

		foreach ($obras as $obra) {
			
			// Formateamos los datos para que no sean demasiado largos y no se carguen el diseño del PDF
			$nombre = strlen($obra['nombre']) > 35 ? substr($obra['nombre'], 0, 32) . "..." : $obra['nombre'];
			$artista = strlen($obra['nick_usuario']) > 20 ? substr($obra['nick_usuario'], 0, 17) . "..." : $obra['nick_usuario'];
			
			// Formateamos fecha a formato dd/mm/yyyy
			$fecha = date('d/m/Y', strtotime($obra['fecha_alta']));
			
			// Acortamos categoría a 12 caracteres para que se vea bien aunque creo que no llega a ser tan largo, pero por si acaso
			$categoria = substr($obra['descripcion_categoria'], 0, 12);
			
			// Formateamos la valoración con un decimal (queria añadir el icono de la estrella pero se rayaba la codificacación del icono)
			$rating = number_format($obra['valoracion'], 1);

			$pdf->Cell($anchoNombre, $alturaFila, $nombre, 1, 0, 'L');
			$pdf->Cell($anchoArtista, $alturaFila, $artista, 1, 0, 'L');
			$pdf->Cell($anchoFecha, $alturaFila, $fecha, 1, 0, 'C');
			$pdf->Cell($anchoCategoria, $alturaFila, $categoria, 1, 0, 'C');
			$pdf->Cell($anchoValoracion, $alturaFila, $rating, 1, 1, 'C');
		}

		// —————————————————————————————————————————————
		//         GENERAR Y DESCARGAR PDF
		// —————————————————————————————————————————————

		// Limpiamos el buffer de salida para evitar conflictos con TCPDF
		if (ob_get_contents()) {
			ob_clean();
		}

		// Generamos el nombre del archivo
		$nombreArchivo = "distarart_obras_" . date('Y-m-d_H-i-s') . ".pdf";

		// Enviamos el PDF al navegador para que se descargue
		$pdf->Output($nombreArchivo, 'D');
		exit;
	}

	// ————————————————————————————————————————————————————————————————————————
	//       INSPIRACIÓN - API del Museo Metropolitano de Arte de Nueva York
	// ————————————————————————————————————————————————————————————————————————

	public function accionInspiracion()
	{
		$this->barraUbi = [
			[
				"texto" => "Inicio",
				"enlace" => ["inicial"]
			],
			[
				"texto" => "Inspiración (API Externa)",
				"enlace" => ["inicial", "inspiracion"]
			]
		];

		// ——————————————————————————————————————————————————————————————————————————————
		//       CONSULTAMOS LA API EXTERNA PARA OBTENER LAS OBRAS DE ARTE DEL MUSEO
		// ——————————————————————————————————————————————————————————————————————————————

		$obrasInspiracion = []; // → Aquí guardaremos las obras obtenidas de la API para pasarlas a la vista
		$mensajeError = ""; // → Si hay algun error, guardaremos un mensaje para mostrar en la vista en lugar de las obras

		try {
			// Endpoint de busqueda en la API del Museo Metropolitano de Arte para obtener IDs de obras públicas con imágenes
			
			// Buscamos las obras públicas con la url del endpoint
			$urlSearchAPI = "https://collectionapi.metmuseum.org/public/collection/v1/search?q=painting&isPublicDomain=true&limit=100";
			//                       ↑ Esta es la URL de la API del Met  
			//  
			// q=painting → Es para buscar las obras que sean "pinturas"        
			// isPublicDomain=true → Solo las obras de dominio público (sin copyright)
			// limit=100 → Devuelve máximo 100 resultados     

			// Hacemos una petición HTTP por GET a esa URL, a la API
			//  - file_get_contents() hace una petición GET a la URL devolviendo la respuesta en formato JSON
			//  - Y con @ si falla la petición, NO mostramos los warning/errores en la pantalla solo un FALSE
			$respuestaSearch = @file_get_contents($urlSearchAPI);

			if ($respuestaSearch === false) { // → Si hay un error, lanzamos una excepción
				throw new Exception("No se ha podido conectar con la API del Museo Metropolitano de Arte de Nueva York");
			}

			$datosBusqueda = json_decode($respuestaSearch, true); // → Decodificamos la respuesta JSON y la pasamos a un array

			// La API devuelve un JSON con una clave "objectIDs" que contiene un array de IDs de las obras encontradas
			// Por ello validamos que esa clave exista y tenga datos, si no, lanzamos una excepción para mostrar un mensaje de error en la vista
			if (!isset($datosBusqueda["objectIDs"]) || empty($datosBusqueda["objectIDs"])) {
				throw new Exception("No se encontraron obras en la API"); 
			}

			// Pillamos 12 IDs aleatorios de las obras disponibles para mostrar las obras variadas cada vez que se recarga la página estilo pinterest
			$idsDisponibles = $datosBusqueda["objectIDs"];

			$cantidadObras = min(12, count($idsDisponibles)); // → Nos aseguramos de no pedir más de las que haya
			$indicesAleatorios = array_rand($idsDisponibles, $cantidadObras); // → Obtenemos claves aleatorias para seleccionar los IDs de las obras

			if (!is_array($indicesAleatorios)) {
				$indicesAleatorios = [$indicesAleatorios];
			}

			$idsSeleccionados = [];
			
			foreach ($indicesAleatorios as $indice) {
				$idsSeleccionados[] = $idsDisponibles[$indice];
			}

			// Para obtener los detllaes de una obra específica del museo:
			// - Por cada ID en la lista (ej: 333, 555, 111)
			// - Construimos una URL dinámica (ej: .../objects/333)
			// - Hacemos otra petición file_get_contents() a esa URL
			// - Y la API devuelve los detalles de esa obra específica

			foreach ($idsSeleccionados as $objectId) {
				
				if (count($obrasInspiracion) >= 12) {
					break;  // → Cortamos el bucle si ya tenemos 12 obras por sia acaso.
				}
					
				$urlDetalleAPI = "https://collectionapi.metmuseum.org/public/collection/v1/objects/" . intval($objectId);

				$respuestaDetalle = @file_get_contents($urlDetalleAPI);

				if ($respuestaDetalle === false) {
					continue;  // Si falla una, continuamos con la siguiente
				}

				$obra = json_decode($respuestaDetalle, true);

				// Validar que tiene imagen y datos para mostrar, si no, pasamos a otra
				if (
					isset($obra["primaryImage"]) && !empty($obra["primaryImage"]) &&
					isset($obra["title"]) && !empty($obra["title"])
				) {
					// Saneamos los datos que vamos a mostrar para evitar problemas
					$obrasInspiracion[] = [
						"id" => htmlspecialchars($obra["objectID"] ?? ""),
						"titulo" => htmlspecialchars($obra["title"] ?? "Sin título"),
						"imagen" => htmlspecialchars($obra["primaryImage"] ?? ""),
						"artista" => htmlspecialchars($obra["artistDisplayName"] ?? "Artista desconocido"),
						"fecha" => htmlspecialchars($obra["objectDate"] ?? "Fecha desconocida"),
						"enlaceMuseo" => htmlspecialchars($obra["objectURL"] ?? "")
					];
				}
			}

			if (count($obrasInspiracion) === 0) {
				throw new Exception("No se han podido obtener obras con imágenes de la API");
			}

		} catch (Exception $e) {
			$mensajeError = "No se hay inspiración en este momento: " . $e->getMessage();
		}

		// Dibujamos la vista
		$this->dibujaVista("inspiracion", [
			"obrasInspiracion" => $obrasInspiracion,
			"mensajeError" => $mensajeError,
			"barraUbi" => $this->barraUbi,
			"barraMenu" => $this->barraMenu
		], "Inspiración - Museo Metropolitano de Arte de Nueva York");
	}
}
