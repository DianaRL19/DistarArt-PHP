    <?php
    // —————————————————————————————————————————————
    //      VISTA MODIFICAR ENCARGO (DOS SECCIONES)
    // —————————————————————————————————————————————

    $encargo = $encargo ?? new Encargos();
    $clienteActual = $clienteActual ?? [];

    // Creamos un array con los estados disponibles para mostrar en el formulario, con su nombre y color correspondiente
    $estadosDisponibles = [
        1 => ["nombre" => "Lluvia de ideas", "color" => "#ebbefd"],
        2 => ["nombre" => "Pruebas de diseño", "color" => "#d07cf1"],
        3 => ["nombre" => "Bocetado", "color" => "#bc46eb"],
        4 => ["nombre" => "Pendiente de revisión", "color" => "#8937e7"],
        5 => ["nombre" => "Corrección de errores", "color" => "#6952eb"],
        6 => ["nombre" => "Desarrollo", "color" => "#6696fd"],
        7 => ["nombre" => "Detallado", "color" => "#9dd8ff"],
        8 => ["nombre" => "Finalizado", "color" => "#adfcff"]
    ];

    echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-modificar-encargo"], null, false);

    // ________________________________

    echo CHTML::dibujaEtiqueta( "div", ["class" => "modificar-header"],
        CHTML::dibujaEtiqueta("h1", [], "Modificar Encargo") .
            CHTML::dibujaEtiqueta("p", ["class" => "subtitulo"], "✦ " . htmlspecialchars($encargo->nombre ?? 'Sin nombre') . " ✦")
    );

    // ________________ CONTENEDOR PRINCIPAL (DOS SECCIONES) ________________

    echo CHTML::dibujaEtiqueta("div", ["class" => "seccion-modificar-encargo"], null, false);

    // ________________ SECCIÓN IZQUIERDA - DATOS DEL CLIENTE ________________

    $bloqueCliente = "";

    if (!empty($clienteActual)) {
        $bloqueCliente .= CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"],
            "<strong>Nombre:</strong> " . htmlspecialchars($clienteActual['nombre'] ?? '-')
        );
        $bloqueCliente .= CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"],
            "<strong>Email:</strong> " . htmlspecialchars($clienteActual['email'] ?? '-')
        );
        $bloqueCliente .= CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod"],
            "<strong>Dirección:</strong> " . htmlspecialchars($clienteActual['direccion'] ?? '-')
        );
        $bloqueCliente .= CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod presupuesto"],
            "<strong>Presupuesto:</strong> " . number_format(floatval($clienteActual['presupuesto'] ?? 0), 2, ',', '.') . " €"
        );
    } else {
        $bloqueCliente = CHTML::dibujaEtiqueta("p", ["class" => "dato-cliente-mod sin-cliente"],
            "Sin cliente asignado"
        );
    }

    echo CHTML::dibujaEtiqueta("div", ["class" => "bloque-cliente-mod"], null, false);

    echo '<h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
            </svg> Datos del Cliente
        </h2>';

    echo $bloqueCliente;

    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre del .bloque-cliente-mod


    // ________________ SECCIÓN DERECHA - DATOS DEL ENCARGO ________________

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

    // ______ Nombre del encargo ___________________________________

    echo CHTML::dibujaEtiqueta(
        "div",
        ["class" => "form-grupo-mod"],
        CHTML::modeloLabel($encargo, "nombre") .
            CHTML::modeloText($encargo, "nombre", [
                "class" => "campo-entrada-mod",
                "placeholder" => "Nombre del encargo",
                "required" => "required"
            ]) .
            CHTML::modeloError($encargo, "nombre", ["class" => "error"]) // → Si hay error de validación del nombre en el modelo lo mostramos aquí
    );

    // ______ Descripción ________________________________________________________

    echo CHTML::dibujaEtiqueta(
        "div",
        ["class" => "form-grupo-mod"],
        CHTML::modeloLabel($encargo, "descripcion") .
            CHTML::modeloTextArea($encargo, "descripcion", [
                "class" => "campo-entrada-mod",
                "placeholder" => "Describe los detalles del encargo...",
                "rows" => "3",
                "required" => "required"
            ]) .
            CHTML::modeloError($encargo, "descripcion", ["class" => "error"]) // → Si hay error de validación de la descripción en el modelo lo mostramos aquí
    );

    // —————————————————————————————————————————————
    //                   ESTADOS
    // —————————————————————————————————————————————

    echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"], null, false);

        echo CHTML::dibujaEtiqueta("label", [], "<strong>Estado Actual:</strong>");

        /**
         * Se que esto es una locura que me ha dado pero es que lo pense por la noche y lo tenia que intentar y mola un motón. 
         * 
         * Se trata de generar radiobuttons con colores para cada estado, usando CSS variables para el color de fondo, y mostrando 
         * el nombre del estado al lado. El radio seleccionado es el estado que tendra el encargo al guardar. PERO OJO PIOJO pongo 
         * los radio buttons ocultos y el color lo aplico al label, asi que el usuario selecciona el estado por el nombre y como 
         * son radios solo puede pulsar 1, y el color del label cambia al pulsar. Pero no sabe el truquillo que hay detras jejeje
         */

        echo CHTML::dibujaEtiqueta("div", ["class" => "selector-estados-mod"], null, false);

            // Recorremos los estados disponibles para generar un radio button por cada uno
            for ($i = 1; $i <= 8; $i++) { // → Recorremos del 1 al 8 porque son los estados disponibles

                $isChecked = ($i == $encargo->estado) ? "checked" : ""; // → Marcamos seleccionado el estado actual del encargo
                $colorEstado = $estadosDisponibles[$i]['color']; // → Obtenemos el color del estado para ponerselo al label
                $nombreEstado = $estadosDisponibles[$i]['nombre']; // → Obtenemos el nombre del estado para mostrarlo al lado del radio button
                $inputId = "estado_" . $i; // → Le asignamos un id a cada radio, para engancharlo con su label

                echo CHTML::dibujaEtiqueta(
                    "label",
                    ["class" => "estado-opcion", "style" => "--estado-color: " . $colorEstado],
                    CHTML::dibujaEtiqueta("input", [
                        "type" => "radio",
                        "id" => $inputId,
                        "name" => "Encargo[estado]",
                        "value" => $i,
                        "hidden" => "hidden",
                        $isChecked => $isChecked // → Si se selecciona este estado, se marca el radio button
                    ]) .

                        CHTML::dibujaEtiqueta("span", [], htmlspecialchars($nombreEstado)) // → Mostramos el nombre del estado al lado del radio button
                );
            }

        echo CHTML::dibujaEtiquetaCierre("div"); // → Cierre del .selector-estados-mod

    echo CHTML::dibujaEtiquetaCierre("div"); // → Cierre del .form-grupo-mod

    // ____ Precios (Grid 2 columnas) ___________________________________________

    echo CHTML::dibujaEtiqueta("fieldset", ["class" => "form-grid-precios-mod"],

        CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
            CHTML::modeloLabel($encargo, "precio_base") .
                CHTML::modeloText($encargo, "precio_base", [
                    "class" => "campo-entrada-mod",
                    "placeholder" => "0.00",
                    "step" => "0.01",
                    "required" => "required"
                ]
            ) .
            CHTML::modeloError($encargo, "precio_base", ["class" => "error"])
        ) .

            CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
                CHTML::modeloLabel($encargo, "iva") .
                    CHTML::modeloText($encargo, "iva", [
                        "class" => "campo-entrada-mod",
                        "placeholder" => "21",
                        "step" => "0.01"
                    ]
                ) .
                CHTML::modeloError($encargo, "iva", ["class" => "error"])
            )
        );

    // ____ Fecha límite ___________________________________________

    echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
        CHTML::modeloLabel($encargo, "fecha_limite") .
            CHTML::modeloDate($encargo, "fecha_limite", [
                "class" => "campo-entrada-mod",
                "required" => "required"
            ]) .
            CHTML::modeloError($encargo, "fecha_limite", ["class" => "error"])
    );

    // ____ Imagen del proceso ___________________________________________

    echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],
        CHTML::dibujaEtiqueta("label", [], "<strong>Imagen del Proceso:</strong>") .
            CHTML::dibujaEtiqueta("input", [
                "type" => "file",
                "name" => "imagen_proceso",
                "class" => "campo-entrada-mod",
                "accept" => "image/*"
            ]) .
            CHTML::dibujaEtiqueta("small", [], "Formatos soportados: JPG, PNG, JPEG.&nbsp;Máx. 5MB")
    );

    // ____ Comentarios del artista ___________________________________________
    echo CHTML::dibujaEtiqueta("div", ["class" => "form-grupo-mod"],

        CHTML::dibujaEtiqueta("label", [], "<strong>Notas:</strong>") .

            CHTML::modeloTextArea($encargo, "comentarios", [
                "class" => "campo-entrada-mod",
                "placeholder" => "Notas internas sobre el encargo...",
                "rows" => "3"
            ])
            . CHTML::dibujaEtiquetaCierre("textarea")
    );


    // ____ Botones de acción ___________________________________________

    echo CHTML::dibujaEtiqueta("div", ["class" => "form-botones-mod"], null, false);

    echo '<button type="submit" class="boton btn-guardar-mod">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-brush" viewBox="0 0 16 16">
                <path d="M15.825.12a.5.5 0 0 1 .132.584c-1.53 3.43-4.743 8.17-7.095 10.64a6.1 6.1 0 0 1-2.373 1.534c-.018.227-.06.538-.16.868-.201.659-.667 1.479-1.708 1.74a8.1 8.1 0 0 1-3.078.132 4 4 0 0 1-.562-.135 1.4 1.4 0 0 1-.466-.247.7.7 0 0 1-.204-.288.62.62 0 0 1 .004-.443c.095-.245.316-.38.461-.452.394-.197.625-.453.867-.826.095-.144.184-.297.287-.472l.117-.198c.151-.255.326-.54.546-.848.528-.739 1.201-.925 1.746-.896q.19.012.348.048c.062-.172.142-.38.238-.608.261-.619.658-1.419 1.187-2.069 2.176-2.67 6.18-6.206 9.117-8.104a.5.5 0 0 1 .596.04M4.705 11.912a1.2 1.2 0 0 0-.419-.1c-.246-.013-.573.05-.879.479-.197.275-.355.532-.5.777l-.105.177c-.106.181-.213.362-.32.528a3.4 3.4 0 0 1-.76.861c.69.112 1.736.111 2.657-.12.559-.139.843-.569.993-1.06a3 3 0 0 0 .126-.75zm1.44.026c.12-.04.277-.1.458-.183a5.1 5.1 0 0 0 1.535-1.1c1.9-1.996 4.412-5.57 6.052-8.631-2.59 1.927-5.566 4.66-7.302 6.792-.442.543-.795 1.243-1.042 1.826-.121.288-.214.54-.275.72v.001l.575.575zm-4.973 3.04.007-.005zm3.582-3.043.002.001h-.002z"/>
            </svg>  Guardar Cambios
        </button>';
    echo CHTML::link("↩ Cancelar", ["encargos", "index"], ["class" => "boton btn-cancelar-mod"]);

    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre .form-botones-mod


    echo CHTML::dibujaEtiquetaCierre("form"); // Cierre del formulario

    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre .bloque-encargo-mod

    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre .seccion-modificar-encargo

    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre .contenedor-modificar-encargo

?>