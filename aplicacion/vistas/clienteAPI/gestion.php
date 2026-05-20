<?php

// —————————————————————————————————————————————
//     GESTIÓN DE CLIENTES: FILTROS Y TABLA
// —————————————————————————————————————————————

echo CHTML::dibujaEtiqueta("div", ["class" => "form-filtrado"],

    CHTML::dibujaEtiqueta("form", ["method" => "GET", "class" => "grupo-filtros"],

        CHTML::dibujaEtiqueta("div", ["class" => "filtro-contenedor-cols"],

            // COLUMNA 1 - FILTRAR POR
            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],
                CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Filtrar por:") .
                    CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::dibujaEtiqueta("label", [], "Nombre:") .
                            CHTML::campoText("nombre", $nombreFiltro ?? "", ["placeholder" => "Buscar por nombre...", "class" => "input-filtro"])
                        ) .
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::dibujaEtiqueta("label", [], "Email:") .
                            CHTML::campoText("email", $emailFiltro ?? "", ["placeholder" => "Buscar por email...", "class" => "input-filtro"])
                        ) .
                        // Checkbox para mostrar eliminados
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-checkbox"],
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-chb"],
                                CHTML::campoCheckBox("borrado", (isset($mostrarEliminados) && $mostrarEliminados), 
                                    ["value" => "1", "id" => "check-eliminados"]
                                ) .
                                CHTML::campoLabel("Mostrar eliminados", "check-eliminados", ["class" => "label-checkbox"])
                            )
                        )
                    )
                )
            ) .

            // COLUMNA 2 - ORDENAR POR
            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],
                CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Ordenar por:") .
                    CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-orden"],
                            CHTML::campoListaDropDown("ordenar_por", $ordenarPor ?? "fecha_alta", [
                                "nombre" => "Nombre",
                                "email" => "Email",
                                "pais" => "País",
                                "presupuesto" => "Presupuesto",
                                "fecha_alta" => "Fecha Alta",
                            ], ["class" => "filtro-orden", "linea" => false])
                        ) .
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-orden"],
                            CHTML::campoListaDropDown("orden", $orden ?? "DESC", [
                                "DESC" => "Descendente",
                                "ASC"  => "Ascendente",
                            ], ["class" => "filtro-orden", "linea" => false])
                        )
                    )
                )
            )

        ) .

        // Botones de acción
        CHTML::dibujaEtiqueta("div", ["class" => "filtro-botones-accion"],
            CHTML::boton("Buscar", ["class" => "boton btn-buscar", "type" => "submit"]) .
            CHTML::link("↻ Limpiar filtros", Sistema::app()->generaURL(["clienteAPI", "gestion"]), ["class" => "boton btn-limpiar btn-cancelar"]) .
            CHTML::link("+ Nuevo cliente", Sistema::app()->generaURL(["clienteAPI", "nuevo"]), ["class" => "boton btn-crear btn-cancelar"]) .
            CHTML::link("← Volver", Sistema::app()->generaURL(["clienteAPI", "index"]), ["class" => "boton btn-cancelar"])
        )
    )
);

// ________ PAGINADOR SUPERIOR ________

if (isset($paginador)) {
    $pagWidget = new CPager($paginador);
    echo $pagWidget->dibujate();
}

// ________ TABLA DE CLIENTES (CGRID) ________

if (isset($cabecera) && isset($filas) && count($filas) > 0) {
    $grid = new CGrid($cabecera, $filas, ["class" => "tabla-obras"]);
    echo $grid->dibujate();
} else {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No hay clientes para mostrar");
}

