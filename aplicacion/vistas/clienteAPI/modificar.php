<?php

// —————————————————————————————————————————————
//        VISTA DE EDICIÓN DE CLIENTE
// —————————————————————————————————————————————
echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-crear-cliente"],
        CHTML::dibujaEtiqueta("div", ["class" => "cliente-formulario"],
            CHTML::dibujaEtiqueta("h1", [], "Editar Cliente") .

            // Mostrar errores de validación si existen
            (isset($errores) && !empty($errores) ? 
                CHTML::dibujaEtiqueta("div", ["class" => "error"], CHTML::dibujaEtiqueta("strong", [], "Errores de validación:") .
                    
                    CHTML::dibujaEtiqueta("ul", [], 

                        // Recorremos el array de errores para mostrar cada campo con sus respectivos errores
                        implode("", array_map(function ($campo, $erroresDelCampo) {
                            
                            return CHTML::dibujaEtiqueta("li", [], "<strong>" . ucfirst($campo) . ":</strong> " . implode(", ", (array)$erroresDelCampo));
                        
                        }, array_keys($errores), array_values($errores)))
                    )
                ) : "") .

            // Formulario de edición/modificación
            CHTML::dibujaEtiqueta("form", ["method" => "POST"],

                // Campo oculto con cod_cliente
                CHTML::campoHidden("cod_cliente", $cliente["cod_cliente"] ?? "") .

                // Nombre
                CHTML::dibujaEtiqueta("div",["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "nombre"], "Nombre *") .
                    CHTML::campoText("nombre", $cliente["nombre"] ?? "", [
                        "placeholder" => "Nombre del cliente",
                        "required" => "required"
                    ])
                ) .

                // Email
                CHTML::dibujaEtiqueta("div",["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "email"], "Email *") .
                    CHTML::campoEmail("email", $cliente["email"] ?? "", [
                        "placeholder" => "correo@ejemplo.com",
                        "required" => "required"
                    ])
                ) .

                // Dirección y País
                CHTML::dibujaEtiqueta("div", ["class" => "form-grid-dos"],
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                        CHTML::dibujaEtiqueta("label", ["for" => "direccion"], "Dirección") .
                        CHTML::campoText("direccion", $cliente["direccion"] ?? "", ["placeholder" => "Dirección del cliente"])
                    ) .
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                        CHTML::dibujaEtiqueta("label", ["for" => "pais"], "País") .
                        CHTML::campoListaDropDown("pais", $cliente["pais"] ?? "", 
                            [
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
                    CHTML::campoNumber("presupuesto", $cliente["presupuesto"] ?? "", [
                        "step" => "0.01",
                        "placeholder" => "0.00"
                        ]
                    )
                ) .

                // Botones de acción
                CHTML::dibujaEtiqueta("div", ["class" => "campo-boton"],
                    CHTML::boton("Guardar Cambios", ["type" => "submit", "class" => "boton"]) .
                    CHTML::link("Cancelar", Sistema::app()->generaURL(["clienteAPI", "index"]), ["class" => "boton btn-cancelar"])
                )
            )
        )
    );
