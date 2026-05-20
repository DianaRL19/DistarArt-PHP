<?php
// ————————————————————————————————————————————————————————————
//                   VISTA CREAR ENCARGO 
// ————————————————————————————————————————————————————————————

$encargo = $encargo ?? new Encargos();

$artistasCombo = $artistasCombo ?? [];
$clientesCombo = $clientesCombo ?? [];
$artistaIdSel = $artistaIdSel  ?? "";
$clienteIdSel = $clienteIdSel  ?? "";

$errores = $errores ?? [];

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-crear-encargo"],

    CHTML::dibujaEtiqueta("div", ["class" => "modificar-header"],
        CHTML::dibujaEtiqueta("h1", [], "Crear Encargo (Admin)") .
        CHTML::dibujaEtiqueta("p", ["class" => "subtitulo"], "Asigna el encargo a un artista y a un cliente")
    ) .

    // Parrafo para mostrar los errores generales
    (!empty($errores["cod_usuario"]) ? 
        CHTML::dibujaEtiqueta("p", ["class" => "error"], htmlspecialchars($errores["cod_usuario"]))
        : ""
    ) .

    // Div para mostrar el error de que el cliente no pertenece al artista
    (!empty($errores["cod_cliente"]) ? 
        CHTML::dibujaEtiqueta("div", ["class" => "error-mensaje-admin"],
            CHTML::dibujaEtiqueta("strong", [], "! ATENCIÓN - ") . htmlspecialchars($errores["cod_cliente"])
        )
        : ""
    ) .

    // Formulario de creación de encargo, dividido en dos columnas (datos del encargo a la izquierda, selección de artista y cliente a la derecha)
    CHTML::dibujaEtiqueta("form", ["method" => "POST", "class" => "form-encargo"],

        CHTML::dibujaEtiqueta("div", ["class" => "seccion-cliente-encargo"],

            // ________________ IZQUIERDA - DATOS DEL ENCARGO ________________

            CHTML::dibujaEtiqueta("div", ["class" => "bloque-encargo"],

                CHTML::dibujaEtiqueta("h2", [], "Datos del Encargo", true) .

                // Nombre
                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::modeloLabel($encargo, "nombre") .
                    CHTML::modeloText($encargo, "nombre", [
                        "class" => "campo-entrada",
                        "placeholder" => "Ej: Ilustración de portada para la novela 'El Bosque Encantado'",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($encargo, "nombre", ["class" => "error"]) // → Error de validación del nombre en el modelo 
                ) .

                // Descripción
                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::modeloLabel($encargo, "descripcion") .
                    CHTML::modeloTextArea($encargo, "descripcion", [
                        "class" => "campo-entrada taDescripcionEncargo",
                        "placeholder" => "Describe los detalles del encargo...",
                        "rows" => "4",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($encargo, "descripcion", ["class" => "error"]) // → Error de validación de la descripción en el modelo
                ) .

                // Precio e IVA
                CHTML::dibujaEtiqueta("div", ["class" => "form-grid-precios"],
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                        CHTML::modeloLabel($encargo, "precio_base") .
                        CHTML::modeloNumber($encargo, "precio_base", [
                            "class" => "campo-entrada",
                            "placeholder" => "0.00",
                            "step" => "0.01",
                            "required" => "required"
                        ]) .
                        CHTML::modeloError($encargo, "precio_base", ["class" => "error"]) // → Error de validación del precio base en el modelo
                    ) .
                    CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                        CHTML::modeloLabel($encargo, "iva") .
                        CHTML::modeloNumber($encargo, "iva", [
                            "class" => "campo-entrada",
                            "placeholder" => "0",
                            "step" => "0.01",
                            "value" => "21"
                        ]) .
                        CHTML::modeloError($encargo, "iva", ["class" => "error"]) // → Error de validación del IVA en el modelo
                    )
                ) .

                // Fecha límite
                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::modeloLabel($encargo, "fecha_limite") .
                    CHTML::modeloDate($encargo, "fecha_limite", [
                        "class" => "campo-entrada",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($encargo, "fecha_limite", ["class" => "error"]) // → Error de validación de la fecha límite en el modelo
                )
            ) .

            // ________________ DERECHA - ARTISTA y CLIENTE ________________

            CHTML::dibujaEtiqueta("div", ["class" => "bloque-cliente"],

                // Combo de artistas
                CHTML::dibujaEtiqueta("h2", [], "Artista", true) .

                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "encargo-cod-usuario"], "Asignar a artista:") .
                    CHTML::campoListaDropDown("Encargo[cod_usuario]", $artistaIdSel, $artistasCombo, [
                        "id" => "encargo-cod-usuario",
                        "class" => "campo-entrada cliente-select",
                        "required" => "required",
                        "linea" => "-- Selecciona un artista --"
                    ])
                ) .

                // Combo de clientes
                CHTML::dibujaEtiqueta("h2", [], "Cliente", true) .

                CHTML::dibujaEtiqueta("div", ["class" => "form-grupo"],
                    CHTML::dibujaEtiqueta("label", ["for" => "encargo-cod-cliente"], "Seleccionar cliente:") .
                    CHTML::campoListaDropDown("Encargo[cod_cliente]", $clienteIdSel, $clientesCombo, [
                        "id" => "encargo-cod-cliente",
                        "class" => "campo-entrada cliente-select",
                        "required" => "required",
                        "linea" => "-- Selecciona un cliente --"
                    ])
                ) .

                CHTML::dibujaEtiqueta("div", ["class" => "cliente-boton-grupo"],
                    CHTML::link("+ Crear cliente nuevo",
                        Sistema::app()->generaURL(["clienteAPI", "nuevo"], ["volver" => "encargos", "accion" => "gestionCrear"]),
                        ["class" => "boton btn-crear-cliente"]
                    )
                )
            )
        ) .

        // ________________ BOTONES ________________

        CHTML::dibujaEtiqueta("div", ["class" => "form-botones"],
            CHTML::boton("Guardar y Crear", ["class" => "boton btn-guardar", "type" => "submit"]) .
            CHTML::link("↩ Cancelar", ["encargos", "gestion"], ["class" => "btn-cancelar-encargo"])
        )
    )
);
