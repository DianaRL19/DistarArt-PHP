<?php

// —————————————————————————————————————————————
//         GALERIA PRIVADA DEL ARTISTA
// —————————————————————————————————————————————

// Verificamos que tenemos las obras
if (!isset($obras)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontraron obras");
    return;
}

echo CHTML::dibujaEtiqueta("div", ["class" => "container-galeria-privada"],
    CHTML::dibujaEtiqueta("div", ["class" => "galeria-header"],
        CHTML::dibujaEtiqueta("h1", ["class" => "titulo-galeria"], "Mis Obras") .
        CHTML::dibujaEtiqueta("p", ["class" => "subtitulo-galeria"], "Total: " . count($obras) . " obra(s)")
    ) .
    
    // Grid de obras
    CHTML::dibujaEtiqueta("div", ["class" => "grid-galeria-privada"],
        (count($obras) > 0 ?

            // Recorremos el array y le ponemos a cada obra a una tarjeta con enlace
            implode("", array_map(function($obra) {
                $imgRuta = "/imagenes/tablaObras/" . htmlspecialchars($obra["img_principal"] ?? "default.jpg");
                $nombre = htmlspecialchars($obra["nombre"] ?? "Sin título");
                $codObra = intval($obra["cod_obra"] ?? 0);
                
                return CHTML::link(
                    CHTML::dibujaEtiqueta("div", ["class" => "card-galeria-privada", "style" => "background-image: url('$imgRuta');"],
                        CHTML::imagen($imgRuta, $nombre, ["class" => "img-galeria"]) .
                        CHTML::dibujaEtiqueta("div", ["class" => "overlay-imagen"],
                            CHTML::dibujaEtiqueta("span", ["class" => "texto-overlay"], $nombre)
                        )
                    ),
                    Sistema::app()->generaURL(["obras", "modificar"]) . "?" . http_build_query(["cod_obra" => $codObra]),
                    ["class" => "enlace-galeria"]
                );
            }, $obras))
        : CHTML::dibujaEtiqueta("p", ["class" => "sin-contenido"], "Sin obras registradas")
        )
    ) .
    
    // Botón Volver
    CHTML::dibujaEtiqueta("div", ["class" => "botones-galeria"],
        CHTML::link("↩ Volver al perfil", ["usuarios", "perfil"], ["class" => "boton-volver"])
    )
);
