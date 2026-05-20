<?php
// —————————————————————————————————————————————
//      CONFIRMACIÓN DE BORRADO DE OBRA
// —————————————————————————————————————————————

if (isset($obra) && !empty($obra)) {
    
    $nombreObra = htmlspecialchars($obra['nombre']);
    $codObra = intval($obra['cod_obra']);
    
    $rutaImagen = "/imagenes/tablaObras/" . htmlspecialchars($obra['img_principal']);
    
    // Contenedor principal
    echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-confirmacion"],
        
        // Encabezado de advertencia
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-header"],
            CHTML::dibujaEtiqueta("h2", ["class" => "titulo-advertencia"], 
            CHTML::imagen("/imagenes/iconos_propios/icono-advertencia.png", "", ["class" => "icono-advertencia"]) . "Confirmar borrado")
        ) .
        
        // Contenido principal
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-content"],
            
            // ______ Información de la obra a eliminar ______
            CHTML::dibujaEtiqueta("div", ["class" => "obra-preview"],
                CHTML::imagen($rutaImagen, $nombreObra, ["class" => "preview-imagen"]) .
                CHTML::dibujaEtiqueta("div", ["class" => "preview-info"],
                    CHTML::dibujaEtiqueta("h3", [], "Obra a eliminar") .
                    CHTML::dibujaEtiqueta("p", ["class" => "nombre-obra"], $nombreObra) .
                    CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], 
                        "Esta acción es irreversible. La obra será marcada como eliminada en la base de datos."
                    )
                )
            ) .
            
            // Formulario de confirmación
            CHTML::iniciarForm(Sistema::app()->generaURL(["obras", "confirmarBorrado"]), "POST", ["class" => "formulario-confirmacion"]) .
                
                // Campo oculto con el código de obra
                CHTML::dibujaEtiqueta("input", ["type" => "hidden", "name" => "cod_obra", "value" => $codObra]) .
                
                // Botones de acción
                CHTML::dibujaEtiqueta("div", ["class" => "botones-confirmacion"],
                    (intval($obra['borrado'] ?? 0) === 1
                        ? CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], "Esta obra ya está eliminada.")
                        : CHTML::boton("Sí, borrar la obra", ["class" => "boton btn-confirmar-borrar", "type" => "submit"])
                    ) .
                    CHTML::link("↩ Cancelar", ["obras", "index"], ["class" => "boton btn-cancelar "]) 
                ) .
            
            CHTML::finalizarForm()
        )
    );

} else {
    echo CHTML::dibujaEtiqueta("p", ["class" => "error"], "Error: No se puede encontrar la obra para borrar");
}
