<?php

// Verificamos que tenemos el modelo del formulario
if (!isset($usuario)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "Error al cargar el formulario de edición de perfil");
    return;
}

// —————————————————————————————————————————————
//         EDITAR PERFIL USUARIO
// —————————————————————————————————————————————

echo CHTML::dibujaEtiqueta("h1", ["class" => "titulo-editar"], "Editar Perfil", true);

echo CHTML::dibujaEtiqueta("form", [
    "method" => "POST",
    "enctype" => "multipart/form-data",
    "class" => "formulario-editar-perfil"
],
    // Foto de perfil
    CHTML::dibujaEtiqueta("fieldset", ["class" => "fieldset-editar"],
        CHTML::dibujaEtiqueta("legend", [], "Foto de Perfil") .
        
        CHTML::dibujaEtiqueta("div", ["class" => "campo-foto"],
            CHTML::dibujaEtiqueta("div", ["class" => "preview-foto-perfil"],
                CHTML::imagen("/imagenes/perfiles/" . htmlspecialchars($usuario->img_perfil ?? "ImgPerfilDefault.jpg"),
                    "Foto actual", ["class" => "img-preview"])
            ) .
            
            CHTML::dibujaEtiqueta("div", ["class" => "campo-archivo"],
                CHTML::modeloLabel($usuario, "img_perfil") .
                CHTML::modeloFile($usuario, "img_perfil", ["accept" => "image/*"]) .

                CHTML::modeloError($usuario, "img_perfil", ["class" => "error"]) // → Si hay algun error en la validacion de la imagen de perfil en el modelo lo mostramos
            )
        )
    ) .
    
    // Foto de banner
    CHTML::dibujaEtiqueta("fieldset", ["class" => "fieldset-editar"],
        CHTML::dibujaEtiqueta("legend", [], "Foto de Banner") .
        
        CHTML::dibujaEtiqueta("div", ["class" => "campo-foto"],
            CHTML::dibujaEtiqueta("div", ["class" => "preview-foto-banner"],
                CHTML::imagen("/imagenes/banners/" . htmlspecialchars($usuario->img_banner ?? "ImgBannerDefault.jpg"),
                    "Banner actual", ["class" => "img-preview-banner"])
            ) .
            
            CHTML::dibujaEtiqueta("div", ["class" => "campo-archivo"],
                CHTML::modeloLabel($usuario, "img_banner") .
                CHTML::modeloFile($usuario, "img_banner", ["accept" => "image/*"]) .

                CHTML::modeloError($usuario, "img_banner", ["class" => "error"]) // → Si hay algun error en la validacion de la imagen de banner en el modelo lo mostramos
            )
        )
    ) .
    
    // Datos personales
    CHTML::dibujaEtiqueta("fieldset", ["class" => "fieldset-editar"],
        CHTML::dibujaEtiqueta("legend", [], "Datos Personales") .
        
        CHTML::dibujaEtiqueta("div", ["class" => "fila-campos"],
            CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                CHTML::modeloLabel($usuario, "nick") .
                CHTML::modeloText($usuario, "nick", ["disabled" => "disabled"]) .
                CHTML::modeloError($usuario, "nick", ["class" => "error"])
            ) .
            
            CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                CHTML::modeloLabel($usuario, "email") .
                CHTML::modeloEmail($usuario, "email") .

                CHTML::modeloError($usuario, "email", ["class" => "error"]) // → Si hay algun error en la validacion del email en el modelo lo mostramos
            )
        ) .
        
        CHTML::dibujaEtiqueta("div", ["class" => "fila-campos"],
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
                ], ["linea" => false]) .

                CHTML::modeloError($usuario, "pais", ["class" => "error"]) // → Si hay algun error en la validacion del país en el modelo lo mostramos
            )
        )
    ) .
    
    // Descripción
    CHTML::dibujaEtiqueta("fieldset", ["class" => "fieldset-editar"],
        CHTML::dibujaEtiqueta("legend", [], "Acerca de ti") .
        
        CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
            CHTML::modeloLabel($usuario, "descripcion") .
            CHTML::modeloTextArea($usuario, "descripcion", ["rows" => "5"]) .
            CHTML::modeloError($usuario, "descripcion", ["class" => "error"])
        )
    ) .
    
    // Botones
    CHTML::dibujaEtiqueta("div", ["class" => "campo-boton"],
        CHTML::boton("✓ Guardar cambios", ["class" => "boton", "type" => "submit"]) .
        CHTML::link("✕ Cancelar", ["usuarios", "perfil"], ["class" => "boton btn-cancelar"])
    )
);

