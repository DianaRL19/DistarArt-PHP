<?php

// —————————————————————————————————————————————
//        VISTA VER DETALLE DE CLIENTE
// —————————————————————————————————————————————

// Verificamos que tenemos los datos del cliente
if (!isset($cliente) || empty($cliente)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontraron datos del cliente");
    return;
}

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-ver-encargo"],
        CHTML::dibujaEtiqueta("div", ["class" => "encargo-detalle"],
            
            // Cabezera con título y acciones ( y un degradado morado de fondo)
            CHTML::dibujaEtiqueta("div", ["class" => "encargo-header cliente-header-azul"],
                
                CHTML::dibujaEtiqueta("div", ["class" => "encargo-titulo"],
                    CHTML::dibujaEtiqueta("h1", [], htmlspecialchars($cliente["nombre"] ?? "Cliente")) .
                    CHTML::dibujaEtiqueta("span", ["class" => "encargo-estado-badge", "style" => "background: var(--color-encargo-morado-oscuro)"], $cliente["pais"] ?? "Sin país")
                ).
                
                CHTML::dibujaEtiqueta("div", ["class" => "encargo-acciones-header"],
                    CHTML::link(
                        CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/svg/pencil-square.svg", "class" => "icono-pequeño ico-editar invertir-color", "alt" => "Icono de editar"]),
                        Sistema::app()->generaURL(["clienteAPI", "modificar"], ["cod_cliente" => $cliente["cod_cliente"]]), ["class" => "boton btn-editar"]) .
                    CHTML::link(
                        CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/svg/trash3-fill.svg", "class" => "icono-pequeño ico-borrar invertir-color", "alt" => "Icono de borrar"]),
                        Sistema::app()->generaURL(["clienteAPI", "borrar"], ["cod_cliente" => $cliente["cod_cliente"]]), ["class" => "boton btn-eliminar"]) .
                    CHTML::link(
                        CHTML::dibujaEtiqueta("img", ["src" => "/imagenes/iconos_propios/svg/arrow-left.svg", "class" => "icono-pequeño", "alt" => "Icono de volver"]),
                        Sistema::app()->generaURL(["clienteAPI", "index"]), ["class" => "boton btn-volver"])
                )
            ).

            // Contenido principal con los datos del cliente, dividido en secciones
            CHTML::dibujaEtiqueta("div", ["class" => "encargo-contenido-grid"],        

                CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-seccion-morado"], 

                    CHTML::dibujaEtiqueta("h2", [], "Contacto") .

                    CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],
                        CHTML::dibujaEtiqueta("p", [],
                            CHTML::dibujaEtiqueta("strong", [], "Email: ") . htmlspecialchars($cliente["email"] ?? "Sin especificar")
                        ) .
                        CHTML::dibujaEtiqueta("p", [],
                            CHTML::dibujaEtiqueta("strong", [], "Dirección: ") . htmlspecialchars($cliente["direccion"] ?? "Sin especificar")
                        ) .
                        CHTML::dibujaEtiqueta("p", [],
                            CHTML::dibujaEtiqueta("strong", [], "País: ") . htmlspecialchars($cliente["pais"] ?? "Sin especificar")
                        )
                    )
                )        
            ).   
            
            // Bloque datos económicos
            CHTML::dibujaEtiqueta("div", ["class" => "bloque-seccion bloque-seccion-morado", "style" => "margin: 0.5rem 2rem 2rem 2rem;"],
                CHTML::dibujaEtiqueta("h2", [], "Datos económicos").
                CHTML::dibujaEtiqueta("div", ["class" => "info-bloque"],
                    CHTML::dibujaEtiqueta("p", [],
                        CHTML::dibujaEtiqueta("strong", [], "Presupuesto: ") .
                        number_format($cliente["presupuesto"] ?? 0, 2) . " €"
                    ) .
                    CHTML::dibujaEtiqueta("p", [],
                        CHTML::dibujaEtiqueta("strong", [], "Fecha de alta: ") .
                        (isset($cliente["fecha_alta"]) ? date("d/m/Y", strtotime($cliente["fecha_alta"])) : "—")
                    )
                )
            )
        )
    );

