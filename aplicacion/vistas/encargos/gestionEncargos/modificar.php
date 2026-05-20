<?php
// ———————————————————————————————————————————————————————————
//    VISTA MODIFICAR ENCARGO (ADMIN - CUALQUIER ARTISTA)
// ———————————————————————————————————————————————————————————

$encargo = $encargo ?? new Encargos();
$clienteActual = $clienteActual ?? [];
$artista = $artista ?? [];
$errores = $errores ?? [];

$estadosDisponibles = [
    1 => ["nombre" => "Lluvia de ideas", "color" => "#ebbefd"],
    2 => ["nombre" => "Pruebas de diseño", "color" => "#d07cf1"],
    3 => ["nombre" => "Bocetado", "color" => "#bc46eb"],
    4 => ["nombre" => "Pendiente de revisión", "color" => "#8937e7"],
    5 => ["nombre" => "Corrección de errores", "color" => "#6952eb"],
    6 => ["nombre" => "Desarrollo", "color" => "#6696fd"],
    7 => ["nombre" => "Detallado", "color" => "#9dd8ff"],
    8 => ["nombre" => "Finalizado", "color" => "#adfcff"],
];

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-modificar-encargo"], null, false);

    echo CHTML::dibujaEtiqueta("div", ["class" => "seccion-modificar-encargo"], null, false);

        // ________________ COLUMNA IZQUIERDA - INFO ARTISTA Y CLIENTE ________________

        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-cliente-mod"], null, false);

            // Artista
            echo CHTML::dibujaEtiqueta("h2", [], "Artista");
            if (!empty($artista)) {
                echo CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],
                    CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"], "<strong>Nick:</strong> "   . htmlspecialchars($artista['nick']   ?? '-')) .
                    CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"], "<strong>Nombre:</strong> " . htmlspecialchars($artista['nombre'] ?? '-')) .
                    CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"], "<strong>Email:</strong> "  . htmlspecialchars($artista['email']  ?? '-'))
                );
            } else {
                echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod sin-cliente"], "Sin artista asignado");
            }

            echo CHTML::dibujaEtiqueta("h2", [], "Cliente");
            if (!empty($clienteActual)) {
                echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"], "<strong>Nombre:</strong> "     . htmlspecialchars($clienteActual['nombre']    ?? '-'));
                echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"], "<strong>Email:</strong> "      . htmlspecialchars($clienteActual['email']     ?? '-'));
                echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"], "<strong>Dirección:</strong> "  . htmlspecialchars($clienteActual['direccion'] ?? '-'));
                echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod presupuesto"], "<strong>Presupuesto:</strong> " .
                    number_format(floatval($clienteActual['presupuesto'] ?? 0), 2, ',', '.') . " €"
                );
            } else {
                echo CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod sin-cliente"], "Sin cliente asignado");
            }

        echo CHTML::dibujaEtiquetaCierre("div"); // .bloque-cliente-mod

        // ________________ COLUMNA DERECHA - FORMULARIO ________________

        echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-encargo-mod"], null, false);

            echo CHTML::dibujaEtiqueta("h2", [], "Datos del Encargo");

            // —————————————————————————————————————————————
            //           MOSTRAR ERRORES DE VALIDACIÓN
            // —————————————————————————————————————————————

            $errores = $errores ?? [];
            
            if (!empty($errores)) {
                $bloqueErrores = "";
                
                // Si los errores son un array de strings (errores simples)
                if (is_array($errores) && count($errores) > 0 && is_string(reset($errores))) {
                    $bloqueErrores = CHTML::dibujaEtiqueta("div", ["class" => "error-mensaje-admin"],
                        CHTML::dibujaEtiqueta("strong", [], 
                        CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/icono-advertencia-malva.png", "class" => "icono-advertencia"]) . "Error de validación:") .
                        CHTML::dibujaEtiqueta("ul", [],
                            implode("", array_map(function ($error) {
                                return CHTML::dibujaEtiqueta("li", [], $error);
                            }, $errores))
                        )
                    );
                } 
                // Si los errores vienen del modelo (estructura campo => error)
                else if (is_array($errores) && count($errores) > 0) {
                    $bloqueErrores = CHTML::dibujaEtiqueta("div", ["class" => "error-mensaje-admin"],
                        CHTML::dibujaEtiqueta("strong", [], 
                        CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/icono-advertencia-malva.png", "class" => "icono-advertencia"]) . "Error de validación:") .
                        CHTML::dibujaEtiqueta("ul", [],
                            implode("", array_map(function ($campo, $error) {
                                return CHTML::dibujaEtiqueta("li", [], "<strong>" . ucfirst($campo) . ":</strong> " . $error);
                            }, array_keys($errores), array_values($errores)))
                        )
                    );
                }
                
                if (!empty($bloqueErrores)) {
                    echo $bloqueErrores;
                }
            }

            echo CHTML::dibujaEtiqueta("form", ["method" => "POST", "enctype" => "multipart/form-data", "class" => "form-encargo-mod"], null, false);

                // ______ Nombre ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                    CHTML::modeloLabel($encargo, "nombre") .
                    CHTML::modeloText($encargo, "nombre", [
                        "class" => "campo-entrada-mod",
                        "placeholder" => "Nombre del encargo",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($encargo, "nombre", ["class" => "error"])
                );

                // ______ Descripción ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                    CHTML::modeloLabel($encargo, "descripcion") .
                    CHTML::modeloTextArea($encargo, "descripcion", [
                        "class" => "campo-entrada-mod",
                        "placeholder" => "Describe los detalles del encargo...",
                        "rows" => "3",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($encargo, "descripcion", ["class" => "error"])
                );

                // ______ Estado (radio buttons con color pero ocultos para que el diseño quede muy chulo y solo se pueda elegir uno) ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"], null, false);
                    echo CHTML::dibujaEtiqueta("label", [], "<strong>Estado Actual:</strong>");
                    echo CHTML::dibujaEtiqueta("div", ["class" => "selector-estados-mod"], null, false);
                        for ($i = 1; $i <= 8; $i++) {
                            $isChecked   = ($i == $encargo->estado) ? "checked" : "";
                            $colorEstado = $estadosDisponibles[$i]['color'];
                            $nombreEstado = $estadosDisponibles[$i]['nombre'];
                            $inputId     = "estado_" . $i;

                            echo CHTML::dibujaEtiqueta("label",
                                ["class" => "estado-opcion", "style" => "--estado-color: " . $colorEstado],
                                CHTML::dibujaEtiqueta("input", [
                                    "type" => "radio",
                                    "id" => $inputId,
                                    "name" => "Encargo[estado]",
                                    "value" => $i,
                                    "hidden" => "hidden",
                                    $isChecked => $isChecked
                                ]) .
                                CHTML::dibujaEtiqueta("span", [], htmlspecialchars($nombreEstado))
                            );
                        }
                    echo CHTML::dibujaEtiquetaCierre("div"); // .selector-estados-mod
                echo CHTML::dibujaEtiquetaCierre("div");

                // ______ Precios ______

                echo CHTML::dibujaEtiqueta("fieldset", ["class" => "form-grid-precios-mod"],
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                        CHTML::modeloLabel($encargo, "precio_base") .
                        CHTML::modeloText($encargo, "precio_base", [
                            "class" => "campo-entrada-mod",
                            "placeholder" => "0.00",
                            "step" => "0.01",
                            "required" => "required"
                        ]) .
                        CHTML::modeloError($encargo, "precio_base", ["class" => "error"])
                    ) .
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                        CHTML::modeloLabel($encargo, "iva") .
                        CHTML::modeloText($encargo, "iva", [
                            "class" => "campo-entrada-mod",
                            "placeholder" => "21",
                            "step" => "0.01"
                        ]) .
                        CHTML::modeloError($encargo, "iva", ["class" => "error"])
                    )
                );

                // ______ Fecha límite ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                    CHTML::modeloLabel($encargo, "fecha_limite") .
                    CHTML::modeloDate($encargo, "fecha_limite", [
                        "class" => "campo-entrada-mod",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($encargo, "fecha_limite", ["class" => "error"])
                );

                // ______ Imagen del proceso ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                    CHTML::dibujaEtiqueta("label", [], "<strong>Imagen del Proceso:</strong>") .
                    CHTML::dibujaEtiqueta("input", [
                        "type"   => "file",
                        "name"   => "imagen_proceso",
                        "class"  => "campo-entrada-mod",
                        "accept" => "image/*"
                    ]) .
                    CHTML::dibujaEtiqueta("small", [], "Formatos soportados: JPG, PNG.&nbsp;Máx. 5 MB")
                );

                // ______ Comentarios / Notas ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                    CHTML::dibujaEtiqueta("label", [], "<strong>Notas:</strong>") .
                    CHTML::modeloTextArea($encargo, "comentarios", [
                        "class"       => "campo-entrada-mod",
                        "placeholder" => "Notas internas sobre el encargo...",
                        "rows"        => "3"
                    ])
                );

                // ______ Botones ______

                echo CHTML::dibujaEtiqueta("div", ["class" => "form-botones-mod"], null, false);
                    echo '<button type="submit" class="boton btn-guardar-mod">Guardar Cambios</button>';
                    echo CHTML::link("↩ Volver a Gestión", ["encargos", "gestion"], ["class" => "boton btn-cancelar-mod"]);
                echo CHTML::dibujaEtiquetaCierre("div");

            echo CHTML::dibujaEtiquetaCierre("form");

        echo CHTML::dibujaEtiquetaCierre("div"); // .bloque-encargo-mod

    echo CHTML::dibujaEtiquetaCierre("div"); // .seccion-modificar-encargo

echo CHTML::dibujaEtiquetaCierre("div"); // .contenedor-modificar-encargo
