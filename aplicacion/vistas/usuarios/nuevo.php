<?php
// —————————————————————————————————————————————
//        VISTA CREAR USUARIO (FORMULARIO)
// —————————————————————————————————————————————

// ———————— TÍTULO ————————

echo CHTML::dibujaEtiqueta("h2", ["class" => "titulo-formulario"], "Crear Nuevo Usuario", true);

// ———————— CONTENEDOR DOS COLUMNAS ————————

echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-modificar"], null, false);

    // COLUMNA 1 - FORMULARIO
    echo CHTML::dibujaEtiqueta("div", ["class" => "mod-formulario-col"], null, false);
    
        echo CHTML::dibujaEtiqueta("form", ["method" => "POST", "class" => "mod-form", "enctype" => "multipart/form-data"],
            
            // Bloque 1 - Información Básica
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                CHTML::dibujaEtiqueta("legend", [], "Información Básica") .
                
                // Campo Nick
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($usuario, "nick") .
                    CHTML::modeloText($usuario, "nick", [
                        "class" => "campo-entrada",
                        "required" => "required",
                        "placeholder" => "Nombre de usuario (nick)"
                    ]) .
                    CHTML::modeloError($usuario, "nick", ["class" => "error"]) // → Si se ha producido un error en la validación del nick en el modelo lo mostramos
                ) .
                
                // Campo Nombre
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($usuario, "nombre") .
                    CHTML::modeloText($usuario, "nombre", [
                        "class" => "campo-entrada",
                        "required" => "required",
                        "placeholder" => "Nombre completo"
                    ]) .
                    CHTML::modeloError($usuario, "nombre", ["class" => "error"]) // → Error en la validación del nombre
                ) .
                
                // Campo Email
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($usuario, "email") .
                    CHTML::modeloEmail($usuario, "email", [
                        "class" => "campo-entrada",
                        "required" => "required"
                    ]) .
                    CHTML::modeloError($usuario, "email", ["class" => "error"]) // → Error en la validación del email
                ) .
                
                // Campo País
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($usuario, "pais") .
                    CHTML::modeloListaDropDown($usuario, "pais", [
                        "España" => "España",
                        "Francia" => "Francia",
                        "Italia" => "Italia",
                        "Reino Unido" => "Reino Unido",
                        "Portugal" => "Portugal",
                        "Bélgica" => "Bélgica",
                        "Estados Unidos" => "Estados Unidos",
                        "Canadá" => "Canadá",
                        "Japón" => "Japón",
                        "China" => "China",
                        "Otro" => "Otro"
                    ], ["class" => "campo-entrada", "linea" => false]) .
                    CHTML::modeloError($usuario, "pais", ["class" => "error"]) // → Error en la validación del país
                )
            ) .
            
            // Bloque 2 - Descripción
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                CHTML::dibujaEtiqueta("legend", [], "Acerca de ti") .
                
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($usuario, "descripcion") .
                    CHTML::modeloTextArea($usuario, "descripcion", [
                        "class" => "campo-entrada",
                        "rows" => "5",
                        "placeholder" => "Cuéntanos un poco sobre ti...(técnicas, gustos, intereses, etc.)"
                    ]) .
                    CHTML::modeloError($usuario, "descripcion", ["class" => "error"]) // → Error en la validación de la descripción
                )
            ) .

            // Bloque 3 - Foto de Perfil
            CHTML::dibujaEtiqueta("fieldset", ["class" => "mod-fieldset"],
                CHTML::dibujaEtiqueta("legend", [], "Foto de Perfil") .
                
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::dibujaEtiqueta("label", [], "Subir foto (jpg, jpeg, png):") .
                    CHTML::modeloFile($usuario, "img_perfil", [
                        "accept" => "image/jpeg,image/png"
                    ]) .
                    CHTML::modeloError($usuario, "img_perfil", ["class" => "error"]) // → Error en la validación de la imagen de perfil
                )
            ) .
            
            // Bloque 4 - Botones
            CHTML::dibujaEtiqueta("div", ["class" => "campo-boton"],
                    CHTML::boton("✓ Crear Usuario", ["class" => "boton", "type" => "submit"]) .
                    CHTML::link("↩ Cancelar", ["usuarios", "index"], ["class" => "boton btn-cancelar"])
            )
        );
        
    echo CHTML::dibujaEtiquetaCierre("div"); // Cierre .mod-formulario-col

    // ________________ COLUMNA 2 - VISTA PREVIA DE IMAGEN ________________

    // Si el usuario no tiene imagen de perfil, mostramos una imagen por defecto
    $rutaImagen = "/imagenes/perfiles/ImgPerfilDefault.jpg";

    echo CHTML::dibujaEtiqueta("div", ["class" => "mod-imagen-col"], 
    
        // Título
        CHTML::dibujaEtiqueta("h3", ["class" => "mod-img-titulo"], "Preview", true).
        
        // Imagen
        CHTML::dibujaEtiqueta("div", ["class" => "mod-img-preview", "style" => "background-image: url('" . $rutaImagen . "')"],
            CHTML::imagen($rutaImagen, "Foto de perfil", ["class" => "mod-img"])
        ).  
        
        // Info
        CHTML::dibujaEtiqueta("div", ["class" => "mod-img-info"],
            CHTML::dibujaEtiqueta("strong", [], "Foto por defecto") .
            CHTML::dibujaEtiqueta("p", ["class" => "mod-img-nombre"], "Se reemplazará con la subida")
        ).
        
        // HR
        CHTML::dibujaEtiqueta("hr", ["class" => "mod-separator"]).

        // Info
        CHTML::dibujaEtiqueta("h4", ["class" => "mod-img-subtitulo"], "Información", true) .
        
        CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
            CHTML::dibujaEtiqueta("label", [], "Estado:") .
            CHTML::dibujaEtiqueta("input", [
                "type" => "text",
                "value" => "Nuevo usuario",
                "disabled" => "disabled",
            ])
        )
        
    ); 

echo CHTML::dibujaEtiquetaCierre("div"); 
