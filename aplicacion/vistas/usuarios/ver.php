<?php
// ————————————————————————————————————————————————————————————
//         VISTA VER USUARIO (con todos los detalles)
// ————————————————————————————————————————————————————————————

// Preparamos la ruta de la imagen de perfil (si no tiene, mostraremos la imagen por defecto)
$rutaImg = "/imagenes/perfiles/" . htmlspecialchars($usuario->img_perfil ?? "ImgPerfilDefault.jpg");

// ____________ CONTENEDOR PRINCIPAL ____________

echo CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-contenedor"],

    // Columna Izq. Imagen
    CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-col-izq"], 
        CHTML::imagen($rutaImg, htmlspecialchars($usuario->nick), ["class" => "ver-obra-img"])
    ) .

    // Columna Der. Info Usuario
    CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-col-der"],

        // Info usuario
        CHTML::dibujaEtiqueta("div", ["class" => "bloque-info-obra"],

            CHTML::dibujaEtiqueta("div", ["class" => "obra-header-container"],
                CHTML::dibujaEtiqueta("h2", ["class" => "obra-nombre"], htmlspecialchars($usuario->nombre)).
                CHTML::dibujaEtiqueta("span", ["class" => "obra-categoria"], htmlspecialchars($usuario->email ?? ""))
            ) .
            
            CHTML::dibujaEtiqueta("p", ["class" => "obra-descripcion"], htmlspecialchars($usuario->descripcion ?? "")) .

            CHTML::dibujaEtiqueta("div", ["class" => "obra-footer-info"],
                CHTML::dibujaEtiqueta("span", ["class" => "obra-fecha"], htmlspecialchars($usuario->fecha_alta)) .
                CHTML::dibujaEtiqueta("span", ["class" => "obra-categoria"], htmlspecialchars($usuario->pais ?? ""))
            )
        ) .

        // Botones de acción
        CHTML::dibujaEtiqueta("div", ["class" => "filtro-botones-accion"],
            CHTML::link("Modificar", Sistema::app()->generaURL(["usuarios", "modificar"]) . "?" . http_build_query(["cod_usuario" => $usuario->cod_usuario]), ["class" => "boton btn-mod"]) .
            CHTML::link("↩ Volver", Sistema::app()->generaURL(["usuarios", "index"]), ["class" => "boton btn-mod"])
        )
    )
);
