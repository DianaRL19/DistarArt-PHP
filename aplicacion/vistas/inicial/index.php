<?php
// —————————————————————————————————————————————
//        INICIO GALERÍA PÚBLICA
// —————————————————————————————————————————————

if (isset($opcionesOrden) && isset($ordenSeleccionado)) {
    echo CHTML::dibujaEtiqueta("div", ["class" => "galeria-orden-container clientes-orden-container"],

        // Lista desplegable de ordenación
        CHTML::dibujaEtiqueta("div", ["class" => "clientes-orden-izq"],

            CHTML::dibujaEtiqueta("label", ["for" => "galeria-orden"], 
                CHTML::dibujaEtiqueta("img",["src" => "/imagenes/iconos_propios/svg/filter-left.svg", "alt" => "Icono de orden", "class" => "icono-pequeño"]) . 
                " Ordenar por:"
            ) .
            CHTML::dibujaEtiqueta("form", ["method" => "GET", "class" => "galeria-orden-form"],
                
                CHTML::dibujaEtiqueta("select", [
                    "name" => "orden",
                    "id" => "galeria-orden",
                    "class" => "galeria-orden-select",
                    "onchange" => "this.form.submit();" // → Cuando el usuario selecciona una opción diferente en el combo, se envía automáticamente el formulario y se cambia la info mostrada
                ], 
                    CHTML::dibujaEtiqueta("option", ["value" => ""], "Seleccionar...") .

                    implode("", array_map(function($valor, $label) use ($ordenSeleccionado) {

                        $selected = ($valor === $ordenSeleccionado) ? ["selected" => "selected"] : [];

                        return CHTML::dibujaEtiqueta("option", array_merge(["value" => $valor], $selected), $label);
                    
                    }, array_keys($opcionesOrden), array_values($opcionesOrden)))
                )

            )
        ) .

        // Etiqueta con info de lo que se está buscando
        CHTML::dibujaEtiqueta("div", ["class" => "clientes-orden-der"],

        // Si hay un criterio de búsqueda activo lo mostramos, sino no mostramos nada
        (isset($busqueda) && $busqueda !== "" ? 
            CHTML::dibujaEtiqueta("span", ["class" => "clientes-busqueda-activa"], "Buscando: \"" . htmlspecialchars($busqueda) . "\"") 
            : "") .
                    
        // Botón para exportar PDF        
        CHTML::link("Exportar PDF " . 
            CHTML::dibujaEtiqueta("img",["src" => "/imagenes/iconos_propios/svg/filetype-pdf.svg", "alt" => "Icono PDF", "class" => "icono-pequeño invertir-color"]), 
            Sistema::app()->generaURL(["inicial", "exportarGaleria"]), ["class" => "boton boton-exportar"])
        )
    );
}

// —————————————————————————————————————————————
//         PAGINADOR (SUPERIOR)
// —————————————————————————————————————————————

if (isset($paginador)) {
    $pagWidget = new CPager($paginador);
    echo $pagWidget->dibujate();
}

// —————————————————————————————————————————————
//      GALERÍA DE OBRAS (TARJETAS)
// —————————————————————————————————————————————

if (isset($imagenes) && count($imagenes) > 0) {
    
    $galeriaContent = "";
    
    foreach ($imagenes as $imagen) {
        
        // Crear cada tarjeta
        $rutaImagen = "/imagenes/tablaObras/" . htmlspecialchars($imagen['img_principal']);
        $nombreObra = str_replace("_", " ", htmlspecialchars($imagen['nombre']));
        $artista = htmlspecialchars($imagen['nick_usuario']);
        $valoracion1 = htmlspecialchars($imagen['valoracion']);
        $valoracion = round(htmlspecialchars($imagen['valoracion']));
        $codObra = htmlspecialchars($imagen['cod_obra']);

        $estrellas = "";

        for($cont=$valoracion; $cont > 0; $cont--){
            $estrellas .= "★";
        } 
        
        if(mb_strlen($estrellas) < 5) {
            for($cont=mb_strlen($estrellas); $cont < 5; $cont++){
                $estrellas .= "☆";
            }
        }
        
        $tarjeta = CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-imagen"],
            
            CHTML::imagen($rutaImagen, $nombreObra, ["class" => "tarjeta-imagen-img"]) .  // → Imagen principal
                        
            CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-imagen-info"],  // → Info obra
                
                // Nombre y valoración
                CHTML::dibujaEtiqueta("div", ["class" => "nom_valoracion"],
                    CHTML::dibujaEtiqueta("h3", [], $nombreObra) .
                    CHTML::dibujaEtiqueta("h3", ["class" => "valoracion"], $estrellas)
                ) .
                
                // Datos artista
                CHTML::dibujaEtiqueta("div", ["class" => "tarjeta-imagen-datos"],
                    CHTML::dibujaEtiqueta("span", ["class" => "artista"], "Artista: " . $artista)
                )
            )
        );
        
        // Enlazamos la obra a la vista de detalle para mostrar sus detalles
        $urlVer = Sistema::app()->generaURL(["inicial", "verObra"]) . "?" . http_build_query(["cod_obra" => $codObra]);
        $tarjetaConLink = CHTML::link($tarjeta, $urlVer, ["class" => "enlace-tarjeta"]);
        
        $galeriaContent .= $tarjetaConLink;
    }
    
    // Mostrar galería
    echo CHTML::dibujaEtiqueta("div", ["class" => "galeria"], $galeriaContent);
    
} else {
    // Sin imágenes
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-imagenes"], "No hay imágenes en la galería");
}

