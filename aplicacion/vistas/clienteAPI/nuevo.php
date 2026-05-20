<?php

// —————————————————————————————————————————————
//        VISTA CREAR UN NUEVO CLIENTE
// —————————————————————————————————————————————

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-crear-cliente"],

    CHTML::dibujaEtiqueta("div", ["class" => "cliente-formulario"],

        CHTML::dibujaEtiqueta("h1", [], "Crear Nuevo Cliente") .

        // Recorremos el array de errores para mostrarlos si existen
        (isset($errores) && !empty($errores) ? 
            CHTML::dibujaEtiqueta("div", ["class" => "error"],

                CHTML::dibujaEtiqueta("strong", [], "Errores de validación:") .
                CHTML::dibujaEtiqueta("ul", [],
                    implode("", array_map(function ($campo, $erroresDelCampo) {
                            return CHTML::dibujaEtiqueta("li", [], "<strong>" . ucfirst($campo) . ":</strong> " . implode(", ", (array)$erroresDelCampo)
                        );
                    }, array_keys($errores), array_values($errores)))
                )
            ) : "") .

            // Creamos el formulario con los campos necesarios para crear un nuevo cliente

            CHTML::dibujaEtiqueta("form", ["method" => "POST"],
                // Nombre
                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "nombre"], "Nombre *") .
                    CHTML::campoText("nombre", $datos["nombre"] ?? "", [
                        "placeholder" => "Nombre del cliente",
                        "required" => "required"
                    ])
                ) .

                // Email
                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "email"], "Email *") .
                    CHTML::campoEmail("email", $datos["email"] ?? "", [
                        "placeholder" => "correo@ejemplo.com",
                        "required" => "required"
                    ])
                ) .

                // Dirección y País en grid
                CHTML::dibujaEtiqueta("div", ["class" => "form-grid-dos"],
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                        CHTML::dibujaEtiqueta("label", ["for" => "direccion"], "Dirección") .
                            CHTML::campoText("direccion", $datos["direccion"] ?? "", [
                                "placeholder" => "Dirección del cliente"
                            ])
                    ) .
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                        CHTML::dibujaEtiqueta("label", ["for" => "pais"], "País") .
                        CHTML::campoListaDropDown("pais", $datos["pais"] ?? "", [
                            "" => "Seleccionar país...",
                            "España" => "España",
                            "Francia" => "Francia",
                            "Italia" => "Italia",
                            "Reino Unido" => "Reino Unido",
                            "Portugal" => "Portugal",
                            "Bélgica" => "Bélgica",
                            "Estados Unidos" => "Estados Unidos",
                            "Canadá" => "Canadá",
                            "Japón" => "Japón",
                            "China" => "China",
                            "Otro" => "Otro",
                            ], ["linea" => false]
                        )
                    )
                ) .

                // Presupuesto
                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "presupuesto"], "Presupuesto (€)") .
                    CHTML::campoNumber("presupuesto", $datos["presupuesto"] ?? "", [
                        "step" => "0.01",
                        "placeholder" => "0.00"
                    ])
                ) .

                // Botones de acción
                CHTML::dibujaEtiqueta("div", ["class" => "form-botones"],
                    CHTML::boton("Guardar", ["type" => "submit", "class" => "btn-guardar"]) .
                    CHTML::link("Cancelar", Sistema::app()->generaURL(["clienteAPI", "index"]), ["class" => "btn-cancelar-encargo"])
                )
            )
        )
    );
