<?php
// ————————————————————————————————————————————————————————————
//         VISTA VER OBRA (Con todos los detalles)
// ————————————————————————————————————————————————————————————

// Verificamos que tenemos los datos de la obra
if (!isset($obra)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontró la obra");
    return;
}

$rutaImg = "/imagenes/tablaObras/" . htmlspecialchars($obra->img_principal);

// ___ Obtenemos el artista ___
$usuario = new Usuario();

$existeUsu = false;

if ($usuario->buscarPor(["where" => "cod_usuario = " . intval($obra->cod_usuario)])) {
    $existeUsu = $usuario;
}

// ___ Obtenemos la categoría ___
$categoria = new Categorias();

$existeCat = false;

if ($categoria->buscarPor(["where" => "cod_categoria = " . intval($obra->cod_categoria)])) {
    $existeCat = $categoria;
}

// ___ Generamos las estrellas ___
$valoracion = round($obra->valoracion);

$estrellas = "";

for ($i = 0; $i < $valoracion; $i++) {
    $estrellas .= "★";
}
for ($i = $valoracion; $i < 5; $i++) {
    $estrellas .= "☆";
}

// ____________ CONTENEDOR PRINCIPAL ____________

echo CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-contenedor"],

    // Columna Izq. Imagen
    CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-col-izq"], CHTML::imagen($rutaImg, htmlspecialchars($obra->nombre), ["class" => "ver-obra-img"])) .

        // Columna Der. Info Obra y Artista
        CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-col-der"],

            // BLOQUE 1 - Info obra
            CHTML::dibujaEtiqueta("div", ["class" => "bloque-info-obra"],

                CHTML::dibujaEtiqueta("div", ["class" => "obra-header-container"],
                    CHTML::dibujaEtiqueta("h2", ["class" => "obra-nombre"], htmlspecialchars($obra->nombre)) .
                    CHTML::dibujaEtiqueta("div", ["class" => "obra-valoracion"], $estrellas)
                ) .
                    CHTML::dibujaEtiqueta("p", ["class" => "obra-descripcion"], htmlspecialchars($obra->descripcion)) .

                    CHTML::dibujaEtiqueta("div", ["class" => "obra-footer-info"], 
                        CHTML::dibujaEtiqueta("span", ["class" => "obra-fecha"], htmlspecialchars($obra->fecha_alta)) .
                        CHTML::dibujaEtiqueta("span", ["class" => "obra-categoria"], ucfirst(htmlspecialchars($existeCat->descripcion ?? "Sin categoría")))
                    )
            ) .

                // BLOQUE 2 - Info artista (2 columnas)
                ($existeUsu ?
                    CHTML::dibujaEtiqueta("div", ["class" => "bloque-info-artista"],
                        
                        CHTML::dibujaEtiqueta("div", ["class" => "artista-col-foto"],
                            CHTML::imagen("/imagenes/perfiles/" . htmlspecialchars($existeUsu->img_perfil ?? "default.jpg"),htmlspecialchars($existeUsu->nick ?? "Artista"),["class" => "foto-artista"])
                        ) .
                        CHTML::dibujaEtiqueta("div",["class" => "artista-col-detalles"], 
                            CHTML::dibujaEtiqueta("h3", ["class" => "artista-nick"], htmlspecialchars($existeUsu->nick ?? "Artista")) .
                            CHTML::dibujaEtiqueta("p", ["class" => "artista-descripcion"], htmlspecialchars($existeUsu->descripcion ?? "")) .
                                
                            CHTML::dibujaEtiqueta("div",["class" => "artista-footer-info"],
                                CHTML::dibujaEtiqueta("span", ["class" => "artista-email"], htmlspecialchars($existeUsu->email ?? "")) .
                                CHTML::dibujaEtiqueta("span", ["class" => "artista-pais"], htmlspecialchars($existeUsu->pais ?? ""))
                            )
                        )
                    ) : "") .

            // BLOQUE 3 - Botones de acción
            CHTML::dibujaEtiqueta("div", ["class" => "filtro-botones-accion"],
                CHTML::link("Modificar", Sistema::app()->generaURL(["obras", "modificar"]) . "?" . http_build_query(["cod_obra" => $obra->cod_obra]), ["class" => "boton btn-mod"]) .
                CHTML::link("↩ Volver", Sistema::app()->generaURL(["obras", "index"]), ["class" => "boton btn-mod"])
            )
        )
);
