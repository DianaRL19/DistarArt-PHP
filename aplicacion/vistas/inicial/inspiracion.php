<?php
// ——————————————————————————————————————————————————————————————————————————
//     INSPIRACIÓN - OBRAS DEL MUSEO METROPOLITANO DE ARTE DE NUEVA YORK
// ——————————————————————————————————————————————————————————————————————————

if (!empty($mensajeError)) { // → Si hay un mensaje de error, lo mostramos
    
    echo CHTML::dibujaEtiqueta("div", ["class" => "error-mensaje"],
        CHTML::dibujaEtiqueta("p", [], htmlspecialchars($mensajeError))
    );
    
} else if (!empty($obrasInspiracion) && count($obrasInspiracion) > 0) {

    echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-inspiracion"],
        CHTML::dibujaEtiqueta("h1", [], "Inspiración del Museo Metropolitano de Arte de Nueva York") .
        CHTML::dibujaEtiqueta("p", ["class" => "subtitulo-inspiracion"], 
            "Descubre obras maestras de museos alrededor del mundo. Datos obtenidos de la API pública del Museo Metropolitano de Arte de Nueva York."
        ) .
        CHTML::dibujaEtiqueta("div", ["class" => "galeria-api-externa"],

        // Recorremos las obras obtenidas de la API y creamos una tarjeta para cada una
            implode("", array_map(function ($obra) {

                return CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-api-externa"],
                    
                // Imagen de la obra
                    CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-api-img-container"],
                        CHTML::dibujaEtiqueta("img", [
                            "src" => htmlspecialchars($obra["imagen"]),
                            "alt" => htmlspecialchars($obra["titulo"]),
                            "class" => "tarjeta-api-img"
                        ])
                    ) .

                    // Información de la obra
                    CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-api-info"],
                        CHTML::dibujaEtiqueta("h3", ["class" => "api-titulo"], htmlspecialchars($obra["titulo"])) .
                        CHTML::dibujaEtiqueta("p", ["class" => "api-artista"], 
                            CHTML::dibujaEtiqueta("strong", [], "Artista: ") . htmlspecialchars($obra["artista"])
                        ) .
                        CHTML::dibujaEtiqueta("p", ["class" => "api-fecha"],
                            CHTML::dibujaEtiqueta("strong", [], "Fecha: ") . htmlspecialchars($obra["fecha"])
                        ) .

                        // Botón enlace al museo
                        (!empty($obra["enlaceMuseo"]) ?
                            CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-api-botones"],
                                CHTML::link("Ver en el Met →", $obra["enlaceMuseo"], [
                                    "target" => "_blank",
                                    "rel" => "noopener noreferrer",
                                    "class" => "boton btn-api-museo"
                                ])
                            )
                            : ""
                        )
                    )
                );
            }, $obrasInspiracion))
        )
    );
} else {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"],
        "No se pudieron cargar las obras de inspiración. Por favor, intenta de nuevo más tarde."
    );
}
