<?php

// —————————————————————————————————————————————
//         VISTA PRINCIPAL DE CLIENTES
// —————————————————————————————————————————————

// Generamos opciones de ordenación
$opcionesHtml = "";
$ordenSel = $ordenSeleccionado ?? "fecha_desc";

if (isset($opcionesOrden)) {
    foreach ($opcionesOrden as $valor => $label) {
        $attrs = ["value" => $valor];
        if ($valor === $ordenSel) {
            $attrs["selected"] = "selected";
        }
        $opcionesHtml .= CHTML::dibujaEtiqueta("option", $attrs, $label);
    }
}

echo CHTML::dibujaEtiqueta("div", ["class" => "galeria-orden-container clientes-orden-container"],

    // Combo de opciones para ordenar ( con la opcion onchange => this.form.submit() para que se recargue la vista al cambiar la ordenación y quede mas chulo que con un boton)
    CHTML::dibujaEtiqueta("div", ["class" => "clientes-orden-izq"],
        CHTML::dibujaEtiqueta("label", ["for" => "clientes-orden"], "Ordenar por:") .
        CHTML::dibujaEtiqueta("form", ["method" => "GET", "class" => "galeria-orden-form"],
            CHTML::dibujaEtiqueta("select", [
                "name" => "orden",
                "id" => "clientes-orden",
                "class" => "galeria-orden-select",
                "onchange" => "this.form.submit();" // → Cuando el usuario selecciona una opción diferente en el combo, se envía automáticamente el formulario y se cambia la info mostrada
            ],
                $opcionesHtml
            )
        )
    ) .

    // Info de búsqueda activa y boton para ir a la gestión de clientes
    CHTML::dibujaEtiqueta("div", ["class" => "clientes-orden-der"],
        (isset($busqueda) && $busqueda !== "" ? 
            CHTML::dibujaEtiqueta("span", ["class" => "clientes-busqueda-activa"], "Buscando: \"" . htmlspecialchars($busqueda) . "\"") . "&nbsp;"
            : "") .
        CHTML::link("Gestión", Sistema::app()->generaURL(["clienteAPI", "gestion"]), ["class" => "boton btn-crear btn-cancelar"])
    )
);

// ________ PAGINADOR SUPERIOR ________

if (isset($paginador)) {
    $pagWidget = new CPager($paginador);
    echo $pagWidget->dibujate();
}

// ________TARJETAS DE CLIENTES ________

echo CHTML::dibujaEtiqueta("div", ["class" => "grid-clientes"],
    
    (isset($clientes) && $clientes) ? implode("", array_map(function ($cliente) {
        
        return CHTML::dibujaEtiqueta("div", ["class" => "cliente-card"],

            // Header de tarjeta con ícono
            CHTML::dibujaEtiqueta("div", ["class" => "cliente-header"],
                CHTML::dibujaEtiqueta("div", ["class" => "cliente-icon"],
                    CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/svg/person-fill.svg", "alt" => "Icono de cliente", "class" => "icono-svg-cliente invertir-color"])
                )
            ) .
            // Cuerpo de la tarjeta
            CHTML::dibujaEtiqueta("div", ["class" => "cliente-body"],
                CHTML::dibujaEtiqueta("h3", ["class" => "cliente-nombre"], $cliente["nombre"]) .
                CHTML::dibujaEtiqueta("p", ["class" => "cliente-email"], $cliente["email"]) .

                CHTML::dibujaEtiqueta("div", ["class" => "cliente-detalles"], 
                    CHTML::dibujaEtiqueta("span", ["class" => "detalle-pais"], ($cliente["pais"] ?? "Sin especificar")) .
                    CHTML::dibujaEtiqueta("span", ["class" => "detalle-presupuesto"], number_format($cliente["presupuesto"], 2) . "€") .
                    CHTML::dibujaEtiqueta("span", ["class" => "detalle-fecha"], (isset($cliente["fecha_alta"]) ? date("d/m/Y", strtotime($cliente["fecha_alta"])) : ""))
                )
            ).
            CHTML::dibujaEtiqueta("div",["class" => "cliente-footer"],
                CHTML::link(
                    CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/svg/tools.svg", "class" => "icono-pequeño ico-calendar invertir-color", "alt" => "Icono de gestión"])
                    , Sistema::app()->generaURL(["clienteAPI", "gestion"]), ["class" => "boton btn-crear btn-cancelar"])
                )
        );
        
    }, $clientes)) : CHTML::dibujaEtiqueta("div", ["class" => "sin-datos"], "No hay clientes registrados.")
);



