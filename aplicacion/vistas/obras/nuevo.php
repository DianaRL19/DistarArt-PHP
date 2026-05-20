<?php
// —————————————————————————————————————————————
//        VISTA CREAR OBRA (FORMULARIO)
// —————————————————————————————————————————————

// Verificamos que tenemos los datos de la obra
if (!isset($obra)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontró la obra");
    return;
}

// Inicializamos las variables que pueden no venir del controlador
$usuariosList = $usuariosList ?? [];
$tienePermiso9 = $tienePermiso9 ?? false;

$categoriasList = $categoriasList ?? []; // → Array de categorías desde el controlador

$catSelect = []; // → Array de categorías para el select

if (is_array($categoriasList)) {
    foreach ($categoriasList as $cat) {
        $catSelect[$cat['cod_categoria']] = ucfirst(htmlspecialchars($cat['descripcion']));
    }
}

// Obtener datos del usuario registrado que ha iniciado sesión (el que está creando la obra)
// Solo los artistas pueden crear obras aunque haya administradores.

$nick = Sistema::app()->acceso()->getNick();

$usuario = new Usuario();
$usuarioEncontrado = false;

if ($usuario->buscarPor(["where" => "nick = '" . CGeneral::addSlashes($nick) . "'"])) {
    $usuarioEncontrado = $usuario;
}

// ———————— TÍTULO ————————

echo CHTML::dibujaEtiqueta("h2", ["class" => "titulo-formulario"], "Nueva Obra", true);

// ———————— CONTENEDOR DOS COLUMNAS ————————

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
                        "placeholder" => "Describe tu obra..."
                    ]) .
                    CHTML::modeloError($obra, "descripcion", ["class" => "error"])
                )
            ) .

            // Bloque 3 - Imagen (dentro del formulario)
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                CHTML::dibujaEtiqueta("legend", [], "Imagen") .
                
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::dibujaEtiqueta("label", [], "Selecciona archivo (jpg, jpeg, png):") .
                    CHTML::modeloFile($obra, "img_principal", [
                        "accept" => "image/jpeg,image/png"
                    ]) .
                    CHTML::modeloError($obra, "img_principal", ["class" => "error"])
                )
            ) .
            
            // Bloque 4 - Artista (solo si es admin, DENTRO DEL FORMULARIO)
            ($tienePermiso9 ?
                CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                    CHTML::dibujaEtiqueta("legend", [], "Asignar a Artista") .
                    CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                        CHTML::dibujaEtiqueta("label", ["for" => "cod_usuario_select"], "Selecciona un artista:") .
                        
                        (is_array($usuariosList) && !empty($usuariosList) ? 
                            CHTML::dibujaEtiqueta("select", [
                                "id" => "cod_usuario_select",
                                "name" => "Obra[cod_usuario]",
                                "class" => "campo-entrada",
                                "required" => "required"
                            ], 
                                CHTML::dibujaEtiqueta("option", ["value" => ""], "-- Selecciona un artista --") .

                                // Recorremos el array de usuarios para crear las opciones del select
                                implode("", array_map(function($usuario) {
                                    return "<option value='" . htmlspecialchars($usuario['cod_usuario']) . "'>" . 
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
                CHTML::boton("✓ Crear obra", ["class" => "boton", "type" => "submit"]) .
                CHTML::link("↩ Cancelar", ["obras", "index"], ["class" => "boton btn-cancelar"])
            )
        );
        
    echo CHTML::dibujaEtiquetaCierre("div"); 

    // _________ COLUMNA 2 - IMAGEN Y ARTISTA ___________

    echo CHTML::dibujaEtiqueta("div", ["class" => "mod-imagen-col"], 
    
        // Título
        CHTML::dibujaEtiqueta("h3", ["class" => "mod-img-titulo"], "Subir Imagen", true).
        
        // Preview placeholder
        CHTML::dibujaEtiqueta("div", ["class" => "mod-img-preview", "style" => "background-image: url('/imagenes/tablaObras/ImgDefault.jpg')"],
            CHTML::imagen("/imagenes/tablaObras/ImgDefault.jpg", "Imagen por defecto", ["class" => "mod-img"])
        ).
        
        // Info imagen
        CHTML::dibujaEtiqueta("div", ["class" => "mod-img-info"],
            CHTML::dibujaEtiqueta("strong", [], "Imagen predeterminada") .
            CHTML::dibujaEtiqueta("p", ["class" => "mod-img-nombre"], "ImgDefault.jpg")
        ).
        
        // Separador
        CHTML::dibujaEtiqueta("hr", ["class" => "mod-separator"]).

        // Valoración
        CHTML::dibujaEtiqueta("h4", ["class" => "mod-img-subtitulo"], "Información Automática", true) .
        CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
            CHTML::dibujaEtiqueta("label", [], "Valoración inicial:") .
            CHTML::dibujaEtiqueta("input", [
                "type" => "text",
                "value" => "0/5 ★",
                "disabled" => "disabled",
                "style" => "cursor: not-allowed;"
            ])
        ) .

        // Fecha
        CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
            CHTML::dibujaEtiqueta("label", [], "Fecha de alta:") .
            CHTML::dibujaEtiqueta("input", [
                "type" => "date",
                "value" => date('Y-m-d'),
                "disabled" => "disabled",
                "style" => "background-color: #f0f0f0; cursor: not-allowed;" // Lo pongo aqui porqie es un estilo muy especifico (no merece la pena crear una clase solo para esto)
            ])
        ).
        
        // HR para separar
        CHTML::dibujaEtiqueta("hr", ["class" => "mod-separator"]).
        
        // ————— SECCIÓN ARTISTA —————
        // Si no es admin, mostrar info del usuario actual (solo lectura)
        (!$tienePermiso9 && $usuarioEncontrado ? 
            CHTML::dibujaEtiqueta("h4", ["class" => "mod-img-subtitulo"], "Tu Perfil", true) .
            CHTML::dibujaEtiqueta("div", ["class" => "mod-artista-info"], 
                CHTML::dibujaEtiqueta("div", ["class" => "mod-artista-foto-mini"],
                    CHTML::imagen("/imagenes/perfiles/" . htmlspecialchars($usuarioEncontrado->img_perfil ?? "ImgPerfilDefault.jpg"),
                        htmlspecialchars($usuarioEncontrado->nick ?? "Artista"),
                        ["class" => "mod-artista-img"]
                    )
                ) .
                CHTML::dibujaEtiqueta("div",["class" => "mod-artista-nombre"],
                    CHTML::dibujaEtiqueta("p", [], htmlspecialchars($usuarioEncontrado->nick ?? "Artista"))
                )
            )
        : "")
        
    ); 
    

echo CHTML::dibujaEtiquetaCierre("div");
