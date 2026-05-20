<?php
// —————————————————————————————————————————————
//          VISTA LISTA DE ENCARGOS
// —————————————————————————————————————————————

// Inicializamos variables
$encargos = $encargos ?? [];

// Array de estados con colores (Escala: Rosa → Morado → Azul)
$estados = [
    1 => ["nombre" => "Lluvia de ideas", "color" => "#ebbefd"],      
    2 => ["nombre" => "Pruebas de diseño", "color" => "#d07cf1"],    
    3 => ["nombre" => "Bocetado", "color" => "#bc46eb"],             
    4 => ["nombre" => "Pendiente de revisión", "color" => "#8937e7"], 
    5 => ["nombre" => "Corrección de errores", "color" => "#6952eb"], 
    6 => ["nombre" => "Desarrollo", "color" => "#6696fd"],            
    7 => ["nombre" => "Detallado", "color" => "#9dd8ff"],             
    8 => ["nombre" => "Finalizado", "color" => "#83ecff"]             
];

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-principal"], null, false);
    
    // ________________ HEADER CON TÍTULO Y BOTÓN ________________
    
    echo CHTML::dibujaEtiqueta("div", ["class" => "encargos-header"],
        CHTML::dibujaEtiqueta("h1", [], "Mis Encargos") .
        CHTML::link("+ Nuevo Encargo", ["encargos", "crear"], ["class" => "boton btn-crear"])
    );

    // ________________ CONTENEDOR DE TARJETAS ________________

    if (count($encargos) > 0) {
        echo CHTML::dibujaEtiqueta("div", ["class" => "encargos-contenedor-tarjetas"], null, false);
        
        foreach ($encargos as $encargo) {
            
            // Obtenemos el estado actual
            $estadoActual = $encargo['estado'] ?? 1;
            $estadoInfo = $estados[$estadoActual] ?? $estados[1];
            
            // Calculamos la imagen del estado basada en el número de estado
            // - Convertimos estado numérico en nombre para la ruta
            $estadosImagenes = [
                1 => 'lluvia_de_ideas',
                2 => 'pruebas_de_diseño',
                3 => 'bocetado',
                4 => 'pendiente_de_revision',
                5 => 'correccion_de_errores',
                6 => 'desarrollo',
                7 => 'detallado',
                8 => 'finalizado'
            ];
            
            // Limpiamos el nombre del encargo para la carpeta → /imagenes/encargos/{nombreEncargo}/
            $nombreEncargoCarpeta = strtolower(str_replace([' ', '/', '\\', '"', "'"], '_', $encargo['nombre']));
            $nombreEstadoImagen = $estadosImagenes[$estadoActual] ?? 'placeholder';
            
            // Obtenemos la versión actual del encargo (reinicia a 1 por cada estado)
            $versionActual = intval($encargo['version'] ?? 1);

            // Si es la imagen por defecto → está en /imagenes/encargos/
            // Si es una imagen propia → está en /imagenes/encargos/{nombreCarpeta}/
            $imagenProceso = $encargo['imagen_proceso'] ?? 'EncargoDefault.png';
            if ($imagenProceso === 'EncargoDefault.png') {
                $imgPath = "/imagenes/encargos/" . $imagenProceso;
            } else {
                $imgPath = "/imagenes/encargos/" . $nombreEncargoCarpeta . "/" . $imagenProceso;
            }

            $clasesTarjeta = "encargo-tarjeta" . ($estadoActual == 8 ? " encargo-finalizado" : "");
            echo CHTML::dibujaEtiqueta("div", ["class" => $clasesTarjeta, "style" => "--color-estado: " . ($estados[$estadoActual]['color'] ?? '#ccc')], null, false);

                // Etiqueta de estado en la esquina superior derecha
                echo CHTML::dibujaEtiqueta("div", [
                    "class" => "etiqueta-estado",
                    "style" => "--color-estado: " . ($estados[$estadoActual]['color'] ?? '#ccc')
                ], htmlspecialchars($estados[$estadoActual]['nombre'] ?? ''));
                
                // ________________ IZQUIERDA: IMAGEN ________________
                
                echo CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-izquierda"], null, false);
                    echo CHTML::dibujaEtiqueta("img", ["src" => $imgPath, "alt" => $encargo['nombre'], "class" => "imagen-encargo"]);
                echo CHTML::dibujaEtiquetaCierre("div");
                
                // ________________ CENTRO: INFORMACIÓN ________________
                
                echo CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-centro"], null, false);
                    
                    // _________ Nombre del encargo (ancho completo) _________
                    
                    echo CHTML::dibujaEtiqueta("div", ["class" => "nombre-encargo"],
                        CHTML::dibujaEtiqueta("h3", [], htmlspecialchars($encargo['nombre'] ?? 'Sin nombre'))
                    );
                    
                    // _________ Columna izquierda: Descripción + Info financiera _________
                    
                    echo CHTML::dibujaEtiqueta("div", ["class" => "col-izquierda"], null, false);
                        
                        echo CHTML::dibujaEtiqueta("p", ["class" => "descripcion"], 
                            htmlspecialchars(substr($encargo['descripcion'] ?? 'Sin descripción', 0, 150) . (strlen($encargo['descripcion'] ?? '') > 150 ? '...' : ''))
                        );
                        
                        echo CHTML::dibujaEtiqueta("div", ["class" => "info-financiera"], null, false);

                            echo CHTML::dibujaEtiqueta("span", [], 
                            (CHTML::imagen("/imagenes/iconos_propios/svg/calendar-week.svg", "Temporizador Icono", ["class" => "icono-pequeño ico-calendar"]))
                            . date('d/m/Y', strtotime($encargo['fecha_limite'] ?? '')));


                            echo CHTML::dibujaEtiqueta("span", ["class" => "precio"],
                            (CHTML::imagen("/imagenes/iconos_propios/euro.png", "Dinero Icono", ["class" => "icono-pequeño ico-monedas"]))
                            . number_format($encargo['precio_total'] ?? 0, 2, ',', '.') . " €");


                        echo CHTML::dibujaEtiquetaCierre("div");
                    
                    echo CHTML::dibujaEtiquetaCierre("div");
                    
                    // _________ Columna derecha: Datos del cliente _________
                    
                    echo CHTML::dibujaEtiqueta("div", ["class" => "col-derecha"], null, false);
                                                
                        echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente"], 
                            "<strong>Nombre:</strong> " . htmlspecialchars($encargo['cliente_nombre'] ?? 'Sin cliente')
                        );
                        
                        echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente"], 
                            "<strong>Email:</strong> " . htmlspecialchars($encargo['cliente_email'] ?? '-')
                        );
                        
                        echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente"], 
                            "<strong>Dirección:</strong> " . htmlspecialchars($encargo['cliente_direccion'] ?? '-')
                        );
                        
                        echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente presupuesto"], 
                            "<strong>Presupuesto:</strong> " . number_format($encargo['cliente_presupuesto'] ?? 0, 2, ',', '.') . " €"
                        );
                    
                    echo CHTML::dibujaEtiquetaCierre("div");
                
                echo CHTML::dibujaEtiquetaCierre("div");
                
                // ________________ DERECHA: ESTADOS ________________
                
                echo CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-derecha"], null, false);
                    
                    // _________ Estados visuales como radio buttons _________
                    
                    echo CHTML::dibujaEtiqueta("div", ["class" => "estados-visuales"], null, false);
                        for ($i = 1; $i <= 8; $i++) {
                            $isChecked = ($i == $estadoActual) ? "checked" : "";
                            $colorEstado = $estados[$i]['color'];
                            $nombreEstado = $estados[$i]['nombre'];
                            
                            echo CHTML::dibujaEtiqueta("label", ["class" => "radio-estado", "title" => $nombreEstado, "style" => "background-color: " . $colorEstado], 
                                CHTML::dibujaEtiqueta("input", ["type" => "radio", "name" => "estado_" . $encargo['cod_encargo'], "value" => $i, $isChecked => $isChecked])
                            );
                        }
                    echo CHTML::dibujaEtiquetaCierre("div");
                    
                    // _________ Botones de acción _________
                    
                    echo CHTML::dibujaEtiqueta("div", ["class" => "botones-tarjeta"], 
                        CHTML::link(
                            (CHTML::imagen("/imagenes/iconos_propios/svg/eye.svg", "Ver Icono", ["class" => "icono-pequeño"])), 
                            Sistema::app()->generaURL(["encargos", "ver"]) . "?" . http_build_query(["cod_encargo" => $encargo["cod_encargo"]]),
                            ["class" => "boton-accion btn-ver", "title" => "Ver"]
                        ).

                        CHTML::link(

                            (CHTML::imagen("/imagenes/iconos_propios/svg/pencil-square.svg", "Editar Icono", ["class" => "icono-pequeño"])), 
                            Sistema::app()->generaURL(["encargos", "modificar"]) . "?" . http_build_query(["cod_encargo" => $encargo["cod_encargo"]]),
                            ["class" => "boton-accion btn-editar", "title" => "Editar"]
                        ).

                        CHTML::link(
                            
                            (CHTML::imagen("/imagenes/iconos_propios/svg/trash3-fill.svg", "Eliminar Icono", ["class" => "icono-pequeño"])), 
                            Sistema::app()->generaURL(["encargos", "borrar"]) . "?" . http_build_query(["cod_encargo" => $encargo["cod_encargo"]]),
                            ["class" => "boton-accion btn-eliminar", "title" => "Eliminar"]
                        )
                    );
                
                echo CHTML::dibujaEtiquetaCierre("div");
            
            echo CHTML::dibujaEtiquetaCierre("div");
        }
        
        echo CHTML::dibujaEtiquetaCierre("div");

    } else {

        // Si no hay encargos, mostramos el mensaje de estado vacío
        
        echo CHTML::dibujaEtiqueta("div", ["class" => "sin-encargos"],
            CHTML::dibujaEtiqueta("p", [], "No tienes encargos aún.") .
            CHTML::link("Crea tu primer encargo", Sistema::app()->generaURL(["encargos", "crear"]), ["class" => "boton btn-crear"])
        );
    }

echo CHTML::dibujaEtiquetaCierre("div");
