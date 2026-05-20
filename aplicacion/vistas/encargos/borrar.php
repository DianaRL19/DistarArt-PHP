<?php
// —————————————————————————————————————————————
//      CONFIRMACIÓN DE BORRADO DE ENCARGO
// —————————————————————————————————————————————

// Array de nombres de los estados
$estadosNombres = [
    1 => "Lluvia de ideas",
    2 => "Pruebas de diseño",
    3 => "Bocetado",
    4 => "Pendiente de revisión",
    5 => "Corrección de errores",
    6 => "Desarrollo",
    7 => "Detallado",
    8 => "Finalizado"
];

// Si el encargo no existe o no se ha encontrado, mostramos un mensaje de error (en el else)
if (isset($encargo) && !empty($encargo)) {
    
    $nombreEncargo = htmlspecialchars($encargo->nombre);
    $codEncargo = intval($encargo->cod_encargo);
    $precioTotal = number_format($encargo->precio_total, 2, ',', '.');
    $fechaAlta = date('d/m/Y', strtotime($encargo->fecha_alta));
    $estadoNombre = $estadosNombres[$encargo->estado] ?? "Desconocido";
    $numVersiones = intval($encargo->version);
    
    // Contenedor principal
    echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-confirmacion"],
        
        // Encabezado de advertencia
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-header"],
            CHTML::dibujaEtiqueta("h2", ["class" => "titulo-advertencia"], 
            CHTML::imagen("/imagenes/iconos_propios/icono-advertencia.png", "", ["class" => "icono-advertencia"]) . "Confirmar borrado")
        ) .
        
        // Contenido principal
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-content"],
            
            // ______ Información del encargo a eliminar ______

            CHTML::dibujaEtiqueta("div", ["class" => "encargo-preview"],
                CHTML::dibujaEtiqueta("div", ["class" => "preview-info"],
                    CHTML::dibujaEtiqueta("h3", [], "Encargo a eliminar") .
                    CHTML::dibujaEtiqueta("p", ["class" => "nombre-encargo"], $nombreEncargo) .
                    CHTML::dibujaEtiqueta("div", ["class" => "encargo-detalles"],
                        CHTML::dibujaEtiqueta("p", [], "<strong>Precio Total:</strong> " . $precioTotal . " €") .
                        CHTML::dibujaEtiqueta("p", [], "<strong>Registrado el día:</strong> " . $fechaAlta) .
                        CHTML::dibujaEtiqueta("p", [], "<strong>Estado:</strong> " . $estadoNombre) .
                        CHTML::dibujaEtiqueta("p", [], "<strong>Número de versiones:</strong> " . $numVersiones)
                    ) .
                    CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], 
                        "Esta acción es irreversible. El encargo será marcado como eliminado en la base de datos."
                    )
                )
            ) .
            
            // Formulario de confirmación
            CHTML::iniciarForm(Sistema::app()->generaURL(["encargos", "borrar"]), "POST", ["class" => "formulario-confirmacion"]) .
                
                // Campo oculto con el código de encargo
                CHTML::dibujaEtiqueta("input", ["type" => "hidden", "name" => "cod_encargo", "value" => $codEncargo]) .
                
                // Botones de acción
                CHTML::dibujaEtiqueta("div", ["class" => "botones-confirmacion"],
                    (intval($encargo->borrado ?? 0) === 1
                        ? CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], "Este encargo ya está eliminado.")
                        : CHTML::boton("Sí, borrar el encargo", ["class" => "boton btn-confirmar-borrar", "type" => "submit"])
                    ) .
                    CHTML::link("↩ Cancelar", ["encargos", "index"], ["class" => "boton btn-cancelar"]) 
                ) .
            
            CHTML::finalizarForm()
        )
    );

} else {
    echo CHTML::dibujaEtiqueta("p", ["class" => "error"], "Error: No se puede encontrar el encargo para borrar");
}