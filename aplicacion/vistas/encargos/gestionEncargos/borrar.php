<?php
// ————————————————————————————————————————————————————————————
//                CONFIRMACIÓN DE BORRADO
// ————————————————————————————————————————————————————————————

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

// Si el encargo no existe o no se ha encontrado, mostramos un mensaje de error
if (isset($encargo) && !empty($encargo)) {

    $nombreEncargo = htmlspecialchars($encargo->nombre);
    $codEncargo = intval($encargo->cod_encargo);
    $precioTotal = number_format($encargo->precio_total, 2, ',', '.');
    $fechaAlta = date('d/m/Y', strtotime($encargo->fecha_alta));
    $estadoNombre = $estadosNombres[$encargo->estado] ?? "Desconocido";
    $numVersiones = intval($encargo->version);

    echo CHTML::dibujaEtiqueta("div", ["class" => "contenedor-confirmacion"],

        // Cabecera de advertencia
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-header"],
            CHTML::dibujaEtiqueta("h2", ["class" => "titulo-advertencia"],
                CHTML::imagen("/imagenes/iconos_propios/icono-advertencia.png", "", ["class" => "icono-advertencia"]) . "Confirmar borrado"
            )
        ) .

        // Contenido
        CHTML::dibujaEtiqueta("div", ["class" => "confirmacion-content"],

            // Info del encargo a eliminar
            CHTML::dibujaEtiqueta("div", ["class" => "encargo-preview"],

                CHTML::dibujaEtiqueta("div", ["class" => "preview-info"],

                    CHTML::dibujaEtiqueta("h3", [], "Encargo a eliminar") .
                    CHTML::dibujaEtiqueta("p", ["class" => "nombre-encargo"], $nombreEncargo) .

                    CHTML::dibujaEtiqueta("div", ["class" => "encargo-detalles"],

                        CHTML::dibujaEtiqueta("p", [], "<strong>Precio Total:</strong> " . $precioTotal . " €") .
                        CHTML::dibujaEtiqueta("p", [], "<strong>Registrado el:</strong> " . $fechaAlta) .
                        CHTML::dibujaEtiqueta("p", [], "<strong>Estado:</strong> " . $estadoNombre) .
                        CHTML::dibujaEtiqueta("p", [], "<strong>Versiones:</strong> " . $numVersiones)
                    ) .
                    CHTML::dibujaEtiqueta("p", ["class" => "advertencia-texto"], "El encargo será marcado como eliminado en la base de datos y no se mostrará en la lista de encargos.")
                )
            ) .

            // Formulario de confirmación
            CHTML::iniciarForm(Sistema::app()->generaURL(["encargos", "gestionBorrar"]) . "?cod_encargo=" . $codEncargo,
                "POST", ["class" => "formulario-confirmacion"]
            ) .

            // Campo oculto con el código del encargo a eliminar
            CHTML::dibujaEtiqueta("input", [
                "type"  => "hidden",
                "name"  => "cod_encargo",
                "value" => $codEncargo
            ]) .

            // Botones de confirmación o cancelación
            CHTML::dibujaEtiqueta("div", ["class" => "botones-confirmacion"],
                '<button type="submit" class="boton btn-borrar">Sí, borrar encargo</button>' .
                CHTML::link("↩ Cancelar", ["encargos", "gestion"], ["class" => "boton btn-cancelar"])
            ) .
            
            CHTML::finalizarForm()
        )
    );

} else {
    echo CHTML::dibujaEtiqueta("p", ["class" => "error"], "No se ha encontrado el encargo a borrar.");
    echo CHTML::link("↩ Volver a Gestión", ["encargos", "gestion"], ["class" => "boton btn-cancelar"]);
}
