<?php
// ———————————————————————————————————————————————————
//     VISTA LISTAR GESTIÓN ENCARGOS (PERMISO 9)
// ———————————————————————————————————————————————————

$filas = $filas ?? [];
$cabecera = $cabecera ?? [];
$opcPaginador = $opcPaginador ?? [];
$totalEncargos = $totalEncargos ?? 0;
$busquedaNombre = $busquedaNombre ?? "";
$busquedaArtista = $busquedaArtista ?? "";
$filtroEstado = $filtroEstado ?? 0;
$mostrarEliminados = $mostrarEliminados ?? false;
$ordenSeleccionado = $ordenSeleccionado ?? "fecha_desc";
$nombresEstado = $nombresEstado ?? [];

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-gestion-encargos"],

    // ________________ BOTÓN CREAR ________________

    CHTML::dibujaEtiqueta("div", ["class" => "barra-acciones-superior"],
        CHTML::link("+ Crear Encargo", ["encargos", "gestionCrear"], ["class" => "boton btn-guardar", "style" => "margin-left: 100rem;"])
    ) .

    // ________________ FILTROS ________________

    CHTML::dibujaEtiqueta("div", ["class" => "form-filtrado"],
        CHTML::dibujaEtiqueta("form", ["method" => "GET", "class" => "grupo-filtros"],

            CHTML::dibujaEtiqueta("div", ["class" => "filtro-contenedor-cols"],

                // Columna 1 - Búsqueda por texto
                CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],
                    CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                        CHTML::dibujaEtiqueta("legend", [], "Filtrar por:") .
                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                                CHTML::dibujaEtiqueta("label", [], "Encargo:") .
                                CHTML::campoText("nombre_busqueda", $busquedaNombre, ["placeholder" => "Nombre del encargo...", "class" => "input-filtro input-filtro-sm"])
                            ) .
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                                CHTML::dibujaEtiqueta("label", [], "Artista:") .
                                CHTML::campoText("artista_busqueda", $busquedaArtista, ["placeholder" => "Nick o nombre...", "class" => "input-filtro input-filtro-sm"])
                            ) .
                            // Checkbox para mostrar eliminados
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-checkbox"],
                                CHTML::dibujaEtiqueta("div", ["class" => "filtro-chb"],
                                    CHTML::campoCheckBox("borrado", $mostrarEliminados, 
                                        ["value" => "1", "id" => "check-eliminados"]
                                    ) .
                                    CHTML::campoLabel("Mostrar eliminados", "check-eliminados", ["class" => "label-checkbox"])
                                )
                            )
                        )
                    )
                ) .

                // Columna 2 - Ordenar y estado
                CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-fieldset"],

                    CHTML::dibujaEtiqueta("fieldset", ["class" => "filtro-fieldset"],
                        CHTML::dibujaEtiqueta("legend", [], "Ordenar y estado:") .

                        CHTML::dibujaEtiqueta("div", ["class" => "filtro-row"],
                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col-orden"],
                                CHTML::campoListaDropDown("orden", $ordenSeleccionado, [
                                    "nombre_asc" => "Encargo (A-Z)",
                                    "nombre_desc" => "Encargo (Z-A)",
                                    "artista_asc" => "Artista (A-Z)",
                                    "artista_desc" => "Artista (Z-A)",
                                    "estado_asc" => "Estado ↑",
                                    "estado_desc" => "Estado ↓",
                                    "fecha_asc" => "Fecha alta ↑",
                                    "fecha_desc" => "Fecha alta ↓",
                                    "precio_asc" => "Precio ↑",
                                    "precio_desc" => "Precio ↓",
                                ], ["class" => "filtro-orden", "linea" => false])
                            ) .

                            CHTML::dibujaEtiqueta("div", ["class" => "filtro-col"],
                                CHTML::campoListaDropDown("estado_filtro", $filtroEstado,
                                    [0 => "Todos los estados"] + $nombresEstado,
                                    ["class" => "input-filtro input-filtro-sm", "linea" => false]
                                )
                            )
                        )
                    )
                )
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "filtro-botones-accion"],
                CHTML::boton("Buscar", ["class" => "boton btn-buscar", "type" => "submit"]) .
                CHTML::link("↻ Limpiar", ["encargos", "gestion"], ["class" => "boton btn-limpiar btn-cancelar"])
            )
        )
    ) .

    // ________________ PAGINADOR SUPERIOR ________________

    (function() use ($opcPaginador) {
        if (!empty($opcPaginador)) {
            $pag = new CPager($opcPaginador);
            return $pag->dibujate();
        }
        return "";
    })() . 

    // ________________ TABLA ________________

    (!empty($filas)
        ? (function() use ($cabecera, $filas) {
            $grid = new CGrid($cabecera, $filas, ["class" => "tabla-obras"]);
            return $grid->dibujate();
        })()
        : CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No hay encargos que coincidan con los filtros.")
    )
);
