<?php
// —————————————————————————————————————————————
//      CONFIRMACIÓN DE BORRADO DE USUARIO
// —————————————————————————————————————————————

if (isset($usuario) && !empty($usuario)) {
    
    $nombreUsuario = htmlspecialchars($usuario->nombre ?? $usuario->nick);
    $codUsuario = intval($usuario->cod_usuario);
    $rutaImagen = "/imagenes/perfiles/" . htmlspecialchars($usuario->img_perfil ?? "ImgPerfilDefault.jpg");
    
    // Contenedor principal
    echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-confirmacion"],
        
        // Encabezado de advertencia
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-header"],
            CHTML::dibujaEtiqueta("h2", ["class" => "titulo-advertencia"], 
            CHTML::imagen("/imagenes/iconos_propios/icono-advertencia.png", "", ["class" => "icono-advertencia"]) . "Confirmar borrado")
        ) .
        
        // Contenido principal
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-content"],
            
            // Mostramos una tarjeta con la imagen y el nombre del usuario a eliminar
            CHTML::dibujaEtiqueta("div", ["class" => "obra-preview"],
                CHTML::imagen($rutaImagen, $nombreUsuario, ["class" => "preview-imagen"]) .
                CHTML::dibujaEtiqueta("div", ["class" => "preview-info"],
                    CHTML::dibujaEtiqueta("h3", [], "Usuario a eliminar") .
                    CHTML::dibujaEtiqueta("p", ["class" => "nombre-obra"], $nombreUsuario) .
                    CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], 
                        "Esta acción es irreversible. El usuario será marcado como eliminado en la base de datos."
                    )
                )
            ) .
            
            // Montamos el formulario de confirmación
            CHTML::iniciarForm(Sistema::app()->generaURL(["usuarios", "borrar"]), "POST", ["class" => "formulario-confirmacion"]) .
                
                // Campo oculto con el código de usuario
                CHTML::dibujaEtiqueta("input", ["type" => "hidden", "name" => "cod_usuario", "value" => $codUsuario]) .
                CHTML::dibujaEtiqueta("input", ["type" => "hidden", "name" => "confirmar", "value" => "1"]) .
                
                // Botones de acción
                CHTML::dibujaEtiqueta("div", ["class" => "botones-confirmacion"],
                    (intval($usuario->borrado ?? 0) === 1
                        ? CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], "Este usuario ya está eliminado.")
                        : CHTML::boton("Sí, borrar el usuario", ["class" => "boton btn-confirmar-borrar", "type" => "submit"])
                    ) .
                    CHTML::link("↩ Cancelar", ["usuarios", "index"], ["class" => "boton btn-cancelar "]) 
                ) .
            
            CHTML::finalizarForm()
        )
    );

} else {
    echo CHTML::dibujaEtiqueta("p", ["class" => "error"], "Error: No se puede encontrar el usuario para borrar");
}
