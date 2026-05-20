<?php
// ———————————————————————————————————————————————————————————
//       VISTA VER ENCARGO (ADMIN - CUALQUIER ARTISTA)
// ———————————————————————————————————————————————————————————

$encargo = $encargo ?? [];
$cliente = $cliente ?? [];

// Array de estados para mostrar el nombre y color correspondiente
$estados = [
    1 => "Lluvia de ideas",
    2 => "Pruebas de diseño",
    3 => "Bocetado",
    4 => "Pendiente de revisión",
    5 => "Corrección de errores",
    6 => "Desarrollo",
    7 => "Detallado",
    8 => "Finalizado"
];

$coloresEstado = [
    1 => "#ebbefd",
    2 => "#d07cf1",
    3 => "#bc46eb",
    4 => "#8937e7",
    5 => "#6952eb",
    6 => "#6696fd",
    7 => "#9dd8ff",
    8 => "#adfcff"
];

$estadoActual = intval($encargo['estado'] ?? 1);
$estadoTexto  = $estados[$estadoActual] ?? "-";
$estadoColor  = $coloresEstado[$estadoActual] ?? "#ccc";

// ________________ CABECERA ________________

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-ver-encargo"], null, false);

    echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-detalle"], null, false);

        echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-header"], null, false);

            echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-titulo"], null, false);
                echo CHTML::dibujaEtiqueta("h1", [], "✦ " . htmlspecialchars($encargo['nombre'] ?? 'Sin nombre') . " ✦");
                echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-estado-badge", "style" => "background-color: " . $estadoColor],
                    $estadoTexto
                );
            echo CHTML::dibujaEtiquetaCierre("div");

            echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-acciones-header"],
                CHTML::link("↩ Volver", ["encargos", "gestion"], ["class" => "boton btn-volver"]) .
                CHTML::link(
                    CHTML::imagen("/imagenes/iconos_propios/svg/pencil-square.svg", "Editar", ["class" => "icono-pequeño invertir-color"]) . " Editar",
                    Sistema::app()->generaURL(["encargos", "gestionModificar"]) . "?cod_encargo=" . intval($encargo['cod_encargo'] ?? 0),
                    ["class" => "boton btn-volver"]
                )
            );

        echo CHTML::dibujaEtiquetaCierre("div");

        // ________________ CONTENIDO EN GRID ________________

        echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-contenido-grid"], null, false);

            // Bloque artista
            echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion"], null, false);
                echo CHTML::dibujaEtiqueta("h2", [], "Artista");
                echo CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],
                    CHTML::dibujaEtiqueta("p", [], "<strong>Nick:</strong> "    . htmlspecialchars($encargo['artista_nick']    ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Nombre:</strong> "  . htmlspecialchars($encargo['artista_nombre']  ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Email:</strong> "   . htmlspecialchars($encargo['artista_email']   ?? '-'))
                );
            echo CHTML::dibujaEtiquetaCierre("div");

            // Bloque cliente
            echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion"], null, false);
                echo CHTML::dibujaEtiqueta("h2", [], "Cliente");
                echo CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],
                    CHTML::dibujaEtiqueta("p", [], "<strong>Nombre:</strong> "      . htmlspecialchars($cliente['nombre']     ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Email:</strong> "       . htmlspecialchars($cliente['email']      ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Dirección:</strong> "   . htmlspecialchars($cliente['direccion']  ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>País:</strong> "        . htmlspecialchars($cliente['pais']       ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Presupuesto:</strong> " . number_format(floatval($cliente['presupuesto'] ?? 0), 2, ',', '.') . " €")
                );
            echo CHTML::dibujaEtiquetaCierre("div");

        echo CHTML::dibujaEtiquetaCierre("div"); // .encargo-contenido-grid

        // ________________ DETALLES + IMAGEN ________________

        $imagenProceso = $encargo['imagen_proceso'] ?? 'EncargoDefault.png';
        if ($imagenProceso === 'EncargoDefault.png') {
            $imgPath = "/imagenes/encargos/EncargoDefault.png";
        } else {
            $nombreCarpeta = strtolower(str_replace([' ', '/', '\\'], '_', $encargo['nombre'] ?? ''));
            $imgPath = "/imagenes/encargos/" . $nombreCarpeta . "/" . $imagenProceso;
        }

        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-detalles-imagen"], null, false);
            echo CHTML::dibujaEtiqueta("h2", [], "Detalles del Encargo");
            echo CHTML::dibujaEtiqueta("div", ["class" => "detalles-imagen-grid"], null, false);
                echo CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],
                    CHTML::dibujaEtiqueta("p", [], "<strong>Estado:</strong> " . $estadoTexto) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Versión:</strong> " . intval($encargo['version'] ?? 0)) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Fecha Alta:</strong> "   . date('d/m/Y', strtotime($encargo['fecha_alta']   ?? 'now'))) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Fecha Límite:</strong> " . (!empty($encargo['fecha_limite']) ? date('d/m/Y', strtotime($encargo['fecha_limite'])) : '-'))
                );
                echo CHTML::dibujaEtiqueta("div", ["class" => "imagen-actual-wrapper"],
                    CHTML::imagen($imgPath, "Imagen actual del encargo", ["class" => "imagen-proceso-ver"])
                );
            echo CHTML::dibujaEtiquetaCierre("div");
        echo CHTML::dibujaEtiquetaCierre("div");

        // ________________ DESCRIPCIÓN ________________

        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-descripcion"], null, false);
            echo CHTML::dibujaEtiqueta("h2", [], "Descripción");
            echo CHTML::dibujaEtiqueta("div", ["class" => "descripcion-texto"],
                str_replace("\n", "<br>", htmlspecialchars($encargo['descripcion'] ?? '')) // → Convertimos saltos de línea a <br>
            );
        echo CHTML::dibujaEtiquetaCierre("div");

        // ________________ PRECIOS ________________

        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-precios"], null, false);
            echo CHTML::dibujaEtiqueta("h2", [], "Información de Precios");
            echo CHTML::dibujaEtiqueta("div", ["class" => "precios-tabla"], null, false);
                echo CHTML::dibujaEtiqueta("div", ["class" => "precio-fila"],
                    CHTML::dibujaEtiqueta("span", [], "Precio Base:") .
                    CHTML::dibujaEtiqueta("strong", [], number_format(floatval($encargo['precio_base'] ?? 0), 2, ',', '.') . " €")
                );
                echo CHTML::dibujaEtiqueta("div", ["class" => "precio-fila"],
                    CHTML::dibujaEtiqueta("span", [], "IVA:") .
                    CHTML::dibujaEtiqueta("strong", [], floatval($encargo['iva'] ?? 0) . " %")
                );
                echo CHTML::dibujaEtiqueta("div", ["class" => "precio-fila precio-total"],
                    CHTML::dibujaEtiqueta("span", [], "Precio Total:") .
                    CHTML::dibujaEtiqueta("strong", [], number_format(floatval($encargo['precio_total'] ?? 0), 2, ',', '.') . " €")
                );
            echo CHTML::dibujaEtiquetaCierre("div");
        echo CHTML::dibujaEtiquetaCierre("div");

        // ________________ COMENTARIOS ________________

        if (!empty($encargo['comentarios'])) {
            echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion"], null, false);
                echo CHTML::dibujaEtiqueta("h2", [], "Notas");
                echo CHTML::dibujaEtiqueta("div", ["class" => "descripcion-texto"],
                    str_replace("\n", "<br>", htmlspecialchars($encargo['comentarios'])) // → Convertimos saltos de línea a <br>
                );
            echo CHTML::dibujaEtiquetaCierre("div");
        }

    echo CHTML::dibujaEtiquetaCierre("div"); // .encargo-detalle

echo CHTML::dibujaEtiquetaCierre("div"); // .contenedor-ver-encargo
