<?php

// —————————————————————————————————————————————
//            VISTA PERFIL ARTISTA
// —————————————————————————————————————————————

// Verificamos que tenemos los datos del usuario
if (!isset($usuario)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontró el perfil del usuario");
    return;
}

// Preparamos las rutas de las imágenes (con los valores por defecto)
$rutaBanner = "/imagenes/banners/" . htmlspecialchars($usuario->img_banner ?? "ImgBannerDefault.jpg");
$rutaFoto = "/imagenes/perfiles/" . htmlspecialchars($usuario->img_perfil ?? "ImgPerfilDefault.jpg");

// Banner del artista (solo imagen)
echo CHTML::dibujaEtiqueta("div", ["class" => "banner-artista"],
    CHTML::dibujaEtiqueta("div", ["class" => "banner-header"],
        CHTML::imagen($rutaBanner, "Banner artista", ["class" => "img-banner"])
    )
) .


// ____________________________________________________________

// Contenedor principal
CHTML::dibujaEtiqueta("div", ["class" => "perfil-contenedor-nuevo"],

    // SECCION 1 - INFORMACIÓN DEL ARTISTA
    CHTML::dibujaEtiqueta("div", ["class" => "seccion-artista-principal"],
        CHTML::dibujaEtiqueta("div", ["class" => "card-artista"],
            CHTML::dibujaEtiqueta("div", ["class" => "foto-artista-grande"],
                CHTML::imagen($rutaFoto, htmlspecialchars($usuario->nick ?? "Perfil"), ["class" => "img-artista"])
            ) .
            
            CHTML::dibujaEtiqueta("div", ["class" => "info-artista-principal"],
                CHTML::dibujaEtiqueta("h2", ["class" => "nombre-artista"], htmlspecialchars($usuario->nick ?? "-")) .
                
                CHTML::dibujaEtiqueta("div", ["class" => "dato-artista"],
                    CHTML::dibujaEtiqueta("span", ["class" => "etiqueta"], "Email:") . htmlspecialchars($usuario->email ?? "")
                ) .
                
                CHTML::dibujaEtiqueta("div", ["class" => "dato-artista"],
                    CHTML::dibujaEtiqueta("span", ["class" => "etiqueta"], "País:") .
                    CHTML::dibujaEtiqueta("span", [], htmlspecialchars($usuario->pais ?? "-"))
                ) .
                
                CHTML::dibujaEtiqueta("div", ["class" => "descripcion-artista"],
                    CHTML::dibujaEtiqueta("p", [], htmlspecialchars($usuario->descripcion ?? "Sin descripción"))
                ) .
                
                // → Botones de acción
                CHTML::dibujaEtiqueta("div", ["class" => "botones-artista"],
                    CHTML::link("Volver", ["inicial"], ["class" => "boton boton-secundario"]) .
                    CHTML::link("Editar", ["usuarios", "editar"], ["class" => "boton boton-primario"])
                )
            )
        )
    ) .

    // ____________________________________________________________

    // SECCION 2 - MIS OBRAS
    CHTML::dibujaEtiqueta("div", ["class" => "seccion-obras-full"],
        CHTML::dibujaEtiqueta("h2", ["class" => "titulo-seccion-nuevo"], "Mis Obras") .
        
        CHTML::dibujaEtiqueta("div", ["class" => "grid-obras"],

            (isset($obras) && count($obras) > 0 ? // → Verificamos que $obras existe

                // Recorremos el array y le ponemos a cada obra a una tarjeta con enlace
                implode("", array_map(function($obra) {

                    $imgRuta = "/imagenes/tablaObras/" . htmlspecialchars($obra["img_principal"] ?? "ImgDefault.jpg");
                    $nombre = htmlspecialchars($obra["nombre"] ?? "Sin título");
                    $codObra = intval($obra["cod_obra"] ?? 0);
                    
                    return CHTML::link(
                        CHTML::dibujaEtiqueta("div", ["class" => "card-obra", "style" => "background-image: url('$imgRuta');"],
                            CHTML::imagen($imgRuta, $nombre, ["class" => "img-obra"]) .
                            CHTML::dibujaEtiqueta("div", ["class" => "overlay-obra"],
                                CHTML::dibujaEtiqueta("span", ["class" => "titulo-obra"], $nombre)
                            )
                        ),
                        Sistema::app()->generaURL(["obras", "modificar"]) . "?" . http_build_query(["cod_obra" => $codObra]),
                        ["class" => "enlace-obra"]
                    );
                }, $obras))

            : CHTML::dibujaEtiqueta("p", ["class" => "sin-contenido"], "Sin obras registradas")
            )
        ) .
        
        CHTML::link("Ver todas las obras →", ["usuarios", "galeriaPrivada"], ["class" => "boton-ver-mas-obras"])
    ) .

    // ____________________________________________________________
    // CONTENEDOR PARA CATEGORÍAS Y GESTIÓN EN 2 COLUMNAS

    CHTML::dibujaEtiqueta("div", ["class" => "seccion-categorias-gestion"],

        // SECCION 3 - CATEGORÍAS
        ((isset($categorias) && count($categorias) > 0) ?
        CHTML::dibujaEtiqueta("div", ["class" => "seccion-categorias-full"],
            CHTML::dibujaEtiqueta("h2", ["class" => "titulo-seccion-nuevo"], "Categorías") .
            
            CHTML::dibujaEtiqueta("div", ["class" => "subseccion subseccion-categorias"],
                CHTML::dibujaEtiqueta("div", ["class" => "lista-categorias"],

                    // Recorremos el array de categorías y las mostramos en una lista
                    implode("", array_map(function($cat) {
                        $nombre = ucfirst(strtolower($cat->descripcion ?? "Sin nombre"));
                        return CHTML::dibujaEtiqueta("div", ["class" => "item-categoria"],
                            htmlspecialchars($nombre)
                        );
                    }, $categorias))
                )
            )
        ) : "") .

        // ____________________________________________________________

        // SECCION 4 - GESTIÓN (solo para el artista propietario)
        ((Sistema::app()->acceso()->hayUsuario() && Sistema::app()->acceso()->puedePermiso(8)) ?
        CHTML::dibujaEtiqueta("div", ["class" => "seccion-gestion-full"],
            CHTML::dibujaEtiqueta("h2", ["class" => "titulo-seccion-nuevo"], "Gestión") .
            
            CHTML::dibujaEtiqueta("div", ["class" => "subseccion"],
                CHTML::dibujaEtiqueta("div", ["class" => "botones-gestion-flex"],

                    CHTML::link("+", ["obras", "crear"], ["class" => "icon-btn icon-crear", "title" => "Crear obra"]) . // → Icono de añadir obra
                    CHTML::link(CHTML::imagen("/imagenes/iconos_propios/svg/lienzo.svg", "Gestionar obras", ["class" => "icon-btn icono-pequeño-2"]), 
                        ["obras", "index"], ["class" => "icon-btn", "title" => "Gestionar obras"]) . // → Icono de gestión de obras

                    CHTML::link(CHTML::imagen("/imagenes/iconos_propios/encargos-blanco.png", "Gestionar encargos", ["class" => "icon-btn"]), 
                        ["encargos", "index"], ["class" => "icon-btn", "title" => "Gestionar encargos"]) . // → Icono de gestión de encargos

                    CHTML::link("★", ["usuarios", "favoritos"], ["class" => "icon-btn", "title" => "Ver favoritos"]) // → Icono de favoritos 
                )
            )
        ) : "")
    )
);


