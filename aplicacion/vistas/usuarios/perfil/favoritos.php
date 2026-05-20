<?php

// —————————————————————————————————————————————
//           OBRAS FAVORITAS DEL USUARIO
// —————————————————————————————————————————————

// Verificamos que tenemos las obras favoritas
if (!isset($obras)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontraron obras favoritas");
    return;
}

echo CHTML::dibujaEtiqueta("div", ["class" => "container-galeria-privada"],
    
// Encabezado con título y contador de favoritos
    CHTML::dibujaEtiqueta("div", ["class" => "galeria-header"],

        CHTML::dibujaEtiqueta("h1", ["class" => "titulo-galeria"], "Mis Favoritos") .
        CHTML::dibujaEtiqueta("p", ["class" => "subtitulo-galeria"],
            "Total: " . count($obras) . " obra(s) marcada(s) como favorita(s)"
        )
    ) .
    
    // ____________________________________________________________
    //    Grid responsive- 3 columnas con efecto blur en fondo

    CHTML::dibujaEtiqueta("div", ["class" => "grid-galeria-privada"],
        (count($obras) > 0 ?

            // Recorremos el array y le ponemos a cada obra favorita a una tarjeta con enlace
            implode("", array_map(function($obra) {
                $imgRuta = "/imagenes/tablaObras/" . htmlspecialchars($obra["img_principal"] ?? "default.jpg");
                $nombre = htmlspecialchars($obra["nombre"] ?? "Sin título");
                $codObra = intval($obra["cod_obra"] ?? 0);
                
                return CHTML::link(
                    // Creamos tarjetas con la imagen de fondo + overlay con título
                    CHTML::dibujaEtiqueta("div", ["class" => "card-galeria-privada", "style" => "background-image: url('$imgRuta');"],
                        CHTML::imagen($imgRuta, $nombre, ["class" => "img-galeria"]) .

                        // Le ponemos un overlay oscuro degradado con texto con el nombre de la obra
                        CHTML::dibujaEtiqueta("div", ["class" => "overlay-imagen"],
                            CHTML::dibujaEtiqueta("span", ["class" => "texto-overlay"], $nombre)
                        )
                    ),
                    // Enlace a verObra (vista pública de la obra)
                    Sistema::app()->generaURL(["inicial", "verObra"]) . "?" . http_build_query(["cod_obra" => $codObra]),
                    ["class" => "enlace-galeria"]
                );
            }, $obras))

        // Si no hay favoritos mostramos el mensaje vacío
        : CHTML::dibujaEtiqueta("p", ["class" => "sin-contenido"], "Aún no tienes obras marcadas como favoritas")
        )
    ) .
    
    // ____________________________________________________________
    // Botón de navegación para volver al perfil
    CHTML::dibujaEtiqueta("div", ["class" => "botones-galeria"],
        CHTML::link("↩ Volver al perfil", ["usuarios", "perfil"], ["class" => "boton-volver"])
    )
);
