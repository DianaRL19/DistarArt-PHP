<?php
// —————————————————————————————————————————————
//           FORMULARIO DE FILTROS
// —————————————————————————————————————————————

echo CHTML::dibujaEtiqueta("div", ["class" => "form-filtrado"],

    CHTML::dibujaEtiqueta("form", ["method" => "GET", "class" => "grupo-filtros"],

        CHTML::dibujaEtiqueta("div", ["class" => "filtro-contenedor-cols"],

            // COLUMNA 1 - FILTRAR POR
            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],
                CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Filtrar por:") .

                    // Nombre, nick y checkbox
                    CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::dibujaEtiqueta("label", [], "Nombre:") .
                            CHTML::campoText("nombre_busqueda", $_SESSION["nombre_busqueda"] ?? "", ["placeholder" => "Buscar por nombre...", "class" => "input-filtro input-filtro-sm"])
                        ) .
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::dibujaEtiqueta("label", [], "Nick:") .
                            CHTML::campoText("nick_busqueda", $_SESSION["nick_busqueda"] ?? "", ["placeholder" => "Buscar por nick...", "class" => "input-filtro input-filtro-sm"])
                        ) .
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-checkbox filtro-col-checkbox-mid"],
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-chb"],
                                CHTML::campoCheckBox("mostrar_eliminados", ($mostrar_eliminados ?? false),
                                    ["value" => "1", "id" => "check-eliminados"]
                                ) .
                                CHTML::campoLabel("Mostrar eliminados", "check-eliminados", ["class" => "label-checkbox"])
                            )
                        )
                    )
                )
            ) .

            // COLUMNA 2 - ORDENAR POR y CATEGORÍA
            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],
                CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Ordenar por:") .
                    CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-orden"],
                            CHTML::campoListaDropDown("orden", $ordenSeleccionado ?? "fecha_desc", [
                                "nombre_asc" => "Nombre (A-Z)",
                                "nombre_desc" => "Nombre (Z-A)",
                                "nick_asc" => "Nick (A-Z)",
                                "nick_desc" => "Nick (Z-A)",
                                "valoracion_asc" => "Valoración ↑",
                                "valoracion_desc" => "Valoración ↓",
                                "pais_asc" => "País (A-Z)",
                                "pais_desc" => "País (Z-A)",
                                "fecha_asc" => "Fecha alta ↑",
                                "fecha_desc" => "Fecha alta ↓",
                            ], ["class" => "filtro-orden", "linea" => false])
                        ) .
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::campoListaDropDown("categoria_filtro", $categoriaSeleccionada ?? "", $categorias ?? ["" => "Todas"], ["class" => "input-filtro input-filtro-sm", "linea" => false])
                        )
                    )
                )
            )
        ) .

        // Botones de acción
        CHTML::dibujaEtiqueta("div", ["class" => "filtro-botones-accion"],
            CHTML::boton("Buscar", ["class" => "boton btn-buscar ", "type" => "submit"]) .
            CHTML::link("↻ Limpiar filtros", ["usuarios", "limpiarFiltros"], ["class" => "boton btn-limpiar btn-cancelar"]) .
            CHTML::link("+ Nuevo usuario", ["usuarios", "crear"], ["class" => "boton btn-crear btn-cancelar"])
        )
    )
);

// ________ PAGINADOR SUPERIOR ________

if (isset($paginador)) {
	$pagWidget = new CPager($paginador);
	echo $pagWidget->dibujate();
}

// ________ TABLA DE USUARIOS (CGRID) ________

if (isset($usuarios) && isset($cabecera) && count($usuarios) > 0) {
	// Usamos directamente la cabecera del controlador
	$grid = new CGrid($cabecera, $usuarios, ["class" => "tabla-obras"]);
	echo $grid->dibujate();
} else {
	echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No hay usuarios para mostrar");
}

// ________ PAGINADOR INFERIOR ________

if (isset($paginador)) {
	$pagWidget = new CPager($paginador);
	echo $pagWidget->dibujate();
}
