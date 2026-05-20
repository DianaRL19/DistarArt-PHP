<?php

// —————————————————————————————————————————————
//     CONFIRMACIÓN DE BORRADO DE CLIENTE
// —————————————————————————————————————————————

echo CHTML::dibujaEtiqueta(
    "div",
    ["class" => "contenedor-confirmacion"],

    CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-header"],
        CHTML::dibujaEtiqueta("h2", ["class" => "titulo-advertencia"], 
            CHTML::imagen("/imagenes/iconos_propios/icono-advertencia.png", "", ["class" => "icono-advertencia"]) . "Confirmar borrado")
    ) .

    CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-content"],

        // Datos del cliente a borrar
        CHTML::dibujaEtiqueta("div", ["class" => "encargo-preview"],
            CHTML::dibujaEtiqueta("div", ["class" => "encargo-detalles"],
                CHTML::dibujaEtiqueta("p", [], "<strong>Nombre:</strong> " . ($cliente["nombre"] ?? "")) .
                CHTML::dibujaEtiqueta("p", [], "<strong>Email:</strong> " . ($cliente["email"] ?? "")) .
                CHTML::dibujaEtiqueta("p", [], "<strong>País:</strong> " . ($cliente["pais"] ?? "Sin especificar")) .
                CHTML::dibujaEtiqueta("p", [], "<strong>Presupuesto:</strong> " . number_format($cliente["presupuesto"] ?? 0, 2) . "€")
            )
        ) .

        CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], 
            "¿Estás seguro de que deseas borrar al cliente <strong>" . ($cliente["nombre"] ?? "") . "</strong>? Esta acción no se puede deshacer."
        ) .

        // Formulario de confirmación
        CHTML::dibujaEtiqueta("div", ["class" => "formulario-confirmacion"],
            CHTML::dibujaEtiqueta("form", ["method" => "POST"],
                CHTML::campoHidden("cod_cliente", $cliente["cod_cliente"] ?? "") .
                CHTML::dibujaEtiqueta("div", ["class" => "botones-confirmacion"], 
                    (intval($cliente["borrado"] ?? 0) === 1 ? 
                        CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], "Este cliente ya está eliminado.") : 
                        CHTML::boton("Confirmar Borrado", ["type" => "submit", "class" => "btn-confirmar-borrar"])
                    ) .
                    CHTML::link("Cancelar", Sistema::app()->generaURL(["clienteAPI", "index"]), ["class" => "btn-cancelar-encargo"])
                )
            )
        )
    )
);

?>
