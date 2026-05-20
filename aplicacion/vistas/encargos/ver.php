<?php
// —————————————————————————————————————————————
//      VISTA VER ENCARGO (INFORMACIÓN COMPLETA)
// —————————————————————————————————————————————

// Inicializar variables
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
    1 => "#ebbefd", // → Rosa muy claro
    2 => "#d07cf1", // → Rosa claro
    3 => "#bc46eb", // → Rosa/Púrpura
    4 => "#8937e7", // → Púrpura
    5 => "#6952eb", // → Morado
    6 => "#6696fd", // → Azul medio
    7 => "#9dd8ff", // → Azul claro
    8 => "#adfcff" // → Azul verdoso claro
];

$estadoTexto = $estados[$encargo['estado'] ?? 1];
$estadoColor = $coloresEstado[$encargo['estado'] ?? 1];

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-ver-encargo"], null, false);
    
    echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-detalle"], null, false);
        
        // ________________ HEADER ________________
        
        echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-header"], null, false);
            
            echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-titulo"], null, false);

                echo CHTML::dibujaEtiqueta("h1", [], "✦ " . htmlspecialchars($encargo['nombre'] ?? 'Sin nombre'). " ✦");
                echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-estado-badge", "style" => "background-color: " . $estadoColor],
                    $estadoTexto
                );
            echo CHTML::dibujaEtiquetaCierre("div");
            
            echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-acciones-header"], 
                    CHTML::link("↩ Volver", ["encargos", "index"], ["class" => "boton btn-volver"])
            );
        
        echo CHTML::dibujaEtiquetaCierre("div");

        // ________________ CONTENIDO ________________

        echo CHTML::dibujaEtiqueta("div", ["class" => "encargo-contenido-grid"], null, false);
            
            // Sección Cliente
            echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion"], null, false);

                echo CHTML::dibujaEtiqueta("h2", [], "Información del Cliente", true);

                echo CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],

                    CHTML::dibujaEtiqueta("p", [], "<strong>Nombre:</strong> " . htmlspecialchars($cliente['nombre'] ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Email:</strong> ". htmlspecialchars($cliente['email'] ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Dirección:</strong> " . htmlspecialchars($cliente['direccion'] ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>País:</strong> " . htmlspecialchars($cliente['pais'] ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Presupuesto:</strong> " . number_format($cliente['presupuesto'] ?? 0, 2, ',', '.') . " €")
                );
            echo CHTML::dibujaEtiquetaCierre("div");

            // Sección Encargo
            echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion"], null, false);

                echo CHTML::dibujaEtiqueta("h2", [], "Detalles del Encargo", true);

                echo CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],

                    CHTML::dibujaEtiqueta("p", [], "<strong>Nombre:</strong> " . htmlspecialchars($encargo['nombre'] ?? '-')) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Estado:</strong> " . $estadoTexto) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Fecha Creación:</strong> " . date('d/m/Y', strtotime($encargo['fecha_alta'] ?? ''))) .
                    CHTML::dibujaEtiqueta("p", [], "<strong>Fecha Límite:</strong> " . date('d/m/Y', strtotime($encargo['fecha_limite'] ?? '')))
                );
            echo CHTML::dibujaEtiquetaCierre("div");

        echo CHTML::dibujaEtiquetaCierre("div");

        // Descripción
        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-descripcion"], null, false);

            echo CHTML::dibujaEtiqueta("h2", [], "Descripción", true);
            echo CHTML::dibujaEtiqueta("div", ["class" => "descripcion-texto"],
                str_replace("\n", "<br>", htmlspecialchars($encargo['descripcion'] ?? '')) // → Convertimos saltos de línea a <br>
            );
        echo CHTML::dibujaEtiquetaCierre("div");

        // Precios
        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-precios"], null, false);

            echo CHTML::dibujaEtiqueta("h2", [], "Información de Precios", true);

            echo CHTML::dibujaEtiqueta("div", ["class" => "precios-tabla"], null, false);
            
                echo CHTML::dibujaEtiqueta("div", ["class" => "precio-fila"],
                    CHTML::dibujaEtiqueta("span", [], "Precio Base:") .
                    CHTML::dibujaEtiqueta("strong", [], number_format($encargo['precio_base'] ?? 0, 2, ',', '.') . " €")
                );
                echo CHTML::dibujaEtiqueta("div", ["class" => "precio-fila"],
                    CHTML::dibujaEtiqueta("span", [], "IVA (" . ($encargo['iva'] ?? 0) . "%):") .
                    CHTML::dibujaEtiqueta("strong", [], number_format(($encargo['precio_base'] ?? 0) * ($encargo['iva'] ?? 0) / 100, 2, ',', '.') . " €")
                );
                echo CHTML::dibujaEtiqueta("div", ["class" => "precio-fila precio-total"],
                    CHTML::dibujaEtiqueta("span", [], "Precio Total:") .
                    CHTML::dibujaEtiqueta("strong", [], number_format($encargo['precio_total'] ?? 0, 2, ',', '.') . " €")
                );

            echo CHTML::dibujaEtiquetaCierre("div"); // → Cierre del .precios-tabla

        echo CHTML::dibujaEtiquetaCierre("div"); // → Cierre del bloque de precios

    echo CHTML::dibujaEtiquetaCierre("div"); // → Cierre del .encargo-detalle

echo CHTML::dibujaEtiquetaCierre("div"); // → Cierre del contenedor principal

?>
