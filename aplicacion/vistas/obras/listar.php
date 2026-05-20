<?php
// —————————————————————————————————————————————
//           FORMULARIO DE FILTROS
// —————————————————————————————————————————————

echo CHTML::dibujaEtiqueta("div", ["class" => "form-filtrado"],

    CHTML::dibujaEtiqueta("form", ["method" => "GET", "class" => "grupo-filtros"],
        
        // Contenedor 2 columnas para fieldsets
        CHTML::dibujaEtiqueta("div", ["class" => "filtro-contenedor-cols"],
            
            // COLUMNA 1 - FILTRAR POR
            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],
                CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Filtrar por:") .
                    
                    // Búsqueda por nombre
                    CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::dibujaEtiqueta("label", [], "Nombre:") .
                            CHTML::campoText("nombre_busqueda", $_SESSION["nombre_busqueda"] ?? "", ["placeholder" => "Buscar por nombre...", "class" => "input-filtro"])
                        ) .
                        
                        // Categoría
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                            CHTML::dibujaEtiqueta("label", [], "Categoría:") .
                            CHTML::campoListaDropDown("categoria", $_REQUEST["categoria"] ?? "", $categoriasList ?? [], // → Array de categorías para el dropdown
                                ["class" => "filtro-cat", "linea" => false]
                            )
                        ) .

                        // Checkbox para mostrar eliminadas
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-checkbox"],
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-chb"],
                                CHTML::campoCheckBox("borrado", (isset($_REQUEST["borrado"]) && $_REQUEST["borrado"] == 1), 
                                    ["value" => "1", "id" => "check-eliminadas"]
                                ) .
                                CHTML::campoLabel("Mostrar eliminadas", "check-eliminadas", ["class" => "label-checkbox"])
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
                            CHTML::campoListaDropDown("orden", $ordenSeleccionado ?? "fecha_desc", $arrayOrdenacion ?? [], // → Opciones de ordenación
                                ["class" => "filtro-orden", "linea" => false]
                            )
                        )
                    )
                )
            )
        ) .

        // Botones de acción
        CHTML::dibujaEtiqueta("div", ["class" => "filtro-botones-accion"],
            CHTML::boton("Buscar", ["class" => "boton btn-buscar ", "type" => "submit"]) .
            CHTML::link("↻ Limpiar filtros", ["obras", "limpiarFiltros"], ["class" => "boton btn-limpiar btn-cancelar"]) .
            CHTML::link("+ Nueva obra", ["obras", "crear"], ["class" => "boton btn-crear btn-cancelar"])
        )

    )
);

// ________ PAGINADOR SUPERIOR ________

if (isset($paginador)) {
	$pagWidget = new CPager($paginador);
	echo $pagWidget->dibujate();
}

// ________ TABLA DE OBRAS (CGRID) ________

if (isset($obras) && isset($cabecera) && count($obras) > 0) {
	// Usamos directamente la cabecera del controlador
	$grid = new CGrid($cabecera, $obras, ["class" => "tabla-obras"]);
	echo $grid->dibujate();
} else {
	echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No hay obras para mostrar");
}

// ________ PAGINADOR INFERIOR ________

if (isset($paginador)) {
	$pagWidget = new CPager($paginador);
	echo $pagWidget->dibujate();
}
