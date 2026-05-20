<?php
// —————————————————————————————————————————————
//           VISTA MODIFICAR OBRA
// —————————————————————————————————————————————

// Verificamos que tenemos los datos de la obra
if (!isset($obra)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontró la obra");
    return;
}

// Inicializamos variables que pueden no venir del controlador
$usuariosList = $usuariosList ?? [];
$tienePermiso9 = $tienePermiso9 ?? false;
$categoriasList = $categoriasList ?? [];
$fechaHTML = $fechaHTML ?? '';

// Array de categorías para el select
$catSelect = [];

if (is_array($categoriasList)) {
    foreach ($categoriasList as $cat) {
        $catSelect[$cat['cod_categoria']] = ucfirst(htmlspecialchars($cat['descripcion']));
    }
}

// Datos del artista
$usuario = new Usuario();

$usuarioEncontrado = false;
if ($usuario->buscarPor(["where" => "cod_usuario = " . intval($obra->cod_usuario)])) {
    $usuarioEncontrado = $usuario;
}

// _______ Titulo de la página _______
echo CHTML::dibujaEtiqueta("h2", ["class" => "titulo-formulario"], "Modificar Obra", true);

// _______ CONTENEDOR DOS COLUMNAS _______

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-modificar"], null, false);

    // COLUMNA 1 - FORMULARIO
    echo CHTML::dibujaEtiqueta("div", ["class" => "mod-formulario-col"], null, false);
    
        echo CHTML::dibujaEtiqueta("form",["method" => "POST", "class" => "mod-form", "enctype" => "multipart/form-data"],
            
            // Bloque 1 - Información Básica
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                CHTML::dibujaEtiqueta("legend", [], "Información Básica") .
                
                // Campo Nombre
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($obra, "nombre") .
                    CHTML::modeloText($obra, "nombre", [
                        "class" => "campo-entrada",
                        "required" => "required",
                        "placeholder" => "Nombre de la obra"
                    ]) .
                    CHTML::modeloError($obra, "nombre", ["class" => "error"])
                ) .
                
                // Campo Categoría
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($obra, "cod_categoria") .
                    CHTML::modeloListaDropDown($obra, "cod_categoria", $catSelect, ["class" => "campo-entrada", "linea" => false]) .
                    CHTML::modeloError($obra, "cod_categoria", ["class" => "error"])
                ) .

                // Campo Fecha (informativa, de solo lectura por motivos seguridad contra el plagio de obras)
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::dibujaEtiqueta("label", [], "Fecha de alta:") .
                    CHTML::dibujaEtiqueta("input", [
                        "type" => "date",
                        "id" => "fecha_alta_info",
                        "name" => "Obra[fecha_alta]",
                        "value" => htmlspecialchars($fechaHTML ?? $obra->fecha_alta),
                        "class" => "campo-entrada",
                        "disabled" => "disabled"
                    ])
                )
            ) .
            
            // Bloque 2 - Descripción
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                
            CHTML::dibujaEtiqueta("legend", [], "Detalles") .
                
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($obra, "descripcion") .
                    CHTML::modeloTextArea($obra, "descripcion", [
                        "class" => "campo-entrada",
                        "rows" => "5",
                        "placeholder" => "Describe la obra..."
                    ]) .
                    CHTML::modeloError($obra, "descripcion", ["class" => "error"])
                )
            ) .

            // Bloque 3 - Imagen
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                CHTML::dibujaEtiqueta("legend", [], "Imagen") .
                
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::dibujaEtiqueta("label", [], "Cambiar imagen (jpg, jpeg, png):") .
                    CHTML::modeloFile($obra, "img_principal", ["accept" => "image/jpeg,image/png"]) .
                    CHTML::modeloError($obra, "img_principal", ["class" => "error"])
                )
            ) .
            
            // Bloque 4 - Artista (solo si es admin)
            ($tienePermiso9 ?
                CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Asignado a Artista") .
                    CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                        CHTML::dibujaEtiqueta("label", ["for" => "cod_usuario_select"], "Cambiar artista:") .
                        (is_array($usuariosList) && !empty($usuariosList) ? 
                            CHTML::dibujaEtiqueta("select", [
                                "id" => "cod_usuario_select",
                                "name" => "Obra[cod_usuario]",
                                "class" => "campo-entrada"
                            ], 
                                CHTML::dibujaEtiqueta("option", ["value" => ""], "-- Selecciona un artista --") .

                                // Recorremos el array de usuarios para crear las opciones del select, marcando como seleccionado el artista actual de la obra
                                implode("", array_map(function($usuario) use ($obra) {

                                    $seleccionado = $usuario['cod_usuario'] == $obra->cod_usuario ? ' selected' : '';

                                    return "<option value='" . htmlspecialchars($usuario['cod_usuario']) . "'" . $seleccionado . ">" . 
                                            htmlspecialchars($usuario['nick'] . " (" . $usuario['nombre'] . ")") . 
                                            "</option>";

                                }, $usuariosList))
                            )
                        : CHTML::dibujaEtiqueta("p", ["class" => "error"], "No hay artistas disponibles"))
                    )
                )
            : "") .
            
            // Bloque 5 - Botones
            CHTML::dibujaEtiqueta("div", ["class" => "campo-boton"],
                    CHTML::boton("✓ Guardar cambios", ["class" => "boton", "type" => "submit"]) .
                    CHTML::link("↩ Cancelar", ["obras", "index"], ["class" => "boton btn-cancelar"])
            )
        );
        
    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre .mod-formulario-col

    $rutaImagen = "/imagenes/tablaObras/" . htmlspecialchars($obra->img_principal);

    // _________ COLUMNA 2 - VISTA PREVIA DE IMAGEN ___________

    echo CHTML::dibujaEtiqueta("div", ["class" => "mod-imagen-col"], 
    
        // Título
        CHTML::dibujaEtiqueta("h3", ["class" => "mod-img-titulo"], "Imagen Actual", true).
        
        // Imagen con background (la misma img) difuminado, que queda muy chulo para las imgs que no ocupan todo el ancho (que es lo normal en la mayoría)
        CHTML::dibujaEtiqueta("div", ["class" => "mod-img-preview", "style" => "background-image: url('" . $rutaImagen . "')"],
                CHTML::imagen($rutaImagen, htmlspecialchars($obra->nombre), ["class" => "mod-img"])
        ).  
        
        // Info imagen
        CHTML::dibujaEtiqueta("div", ["class" => "mod-img-info"],
                CHTML::dibujaEtiqueta("strong", [], "Archivo actual:") .
                CHTML::dibujaEtiqueta("p", ["class" => "mod-img-nombre"], htmlspecialchars($obra->img_principal))
        ).
        
        // HR para separar
        CHTML::dibujaEtiqueta("hr", ["class" => "mod-separator"]).

        // Valoración de solo lectura, es solo informativa
        CHTML::dibujaEtiqueta("h4", ["class" => "mod-img-subtitulo"], "Información de la Obra", true) .
        
        CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
            CHTML::dibujaEtiqueta("label", [], "Valoración actual:") .
            CHTML::dibujaEtiqueta("input", [
                "type" => "text",
                "value" => htmlspecialchars($obra->valoracion ?? 0) . "/5 ★",
                "disabled" => "disabled",
            ])
        ).
        
        // HR para separar
        CHTML::dibujaEtiqueta("hr", ["class" => "mod-separator"]).
        
        // _______ SECCIÓN ARTISTA _______
        // Si no es admin, mostrar info del artista actual (solo lectura)
        (!$tienePermiso9 && $usuarioEncontrado ? 
            CHTML::dibujaEtiqueta("h4", ["class" => "mod-img-subtitulo"], "Creador/a", true) .
            CHTML::dibujaEtiqueta("div", ["class" => "mod-artista-info"],
                CHTML::dibujaEtiqueta("div", ["class" => "mod-artista-foto-mini"],
                    CHTML::imagen(
                        "/imagenes/perfiles/" . htmlspecialchars($usuarioEncontrado->img_perfil ?? "ImgPerfilDefault.jpg"),
                        htmlspecialchars($usuarioEncontrado->nick ?? "Artista"),["class" => "mod-artista-img"]
                    )
                ) .
                CHTML::dibujaEtiqueta("div", ["class" => "mod-artista-nombre"],
                    CHTML::dibujaEtiqueta("p", [], htmlspecialchars($usuarioEncontrado->nick ?? "Artista"))
                )
            )
        : "")
        
    ); 
    

echo CHTML::dibujaEtiquetaCierre("div"); 

