<?php
// ———————————————————————————————————————————————————————————————————————————
//       VISTA DETALLE OBRA (Galería pública, sin permisos para entrar)
// ———————————————————————————————————————————————————————————————————————————

// Verificamos que tenemos los datos de la obra
if (!isset($obra)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "No se encontró la obra");
    return;
}

// Preparamos la ruta de la imagen principal
$rutaImg = "/imagenes/tablaObras/" . htmlspecialchars($obra->img_principal);

$usuario = new Usuario();// → Obtenemos el artista

$existeUsu = false;

if ($usuario->buscarPor(["where" => "cod_usuario = " . intval($obra->cod_usuario)])) {
    $existeUsu = $usuario;
}

$categoria = new Categorias(); // → Obtenemos la categoría

$existeCat = false;

if ($categoria->buscarPor(["where" => "cod_categoria = " . intval($obra->cod_categoria)])) {
    $existeCat = $categoria;
}

$valoracion = round($obra->valoracion); // → Generamos las estrellas según valoración

$estrellas = "";

for ($i = 0; $i < $valoracion; $i++) {
    $estrellas .= "★";
}
for ($i = $valoracion; $i < 5; $i++) {
    $estrellas .= "☆";
}

// _____________ Comprobamos el estado del like _____________

$usuarioLogueado = Sistema::app()->acceso()->hayUsuario();
$yaHaVotado = false;

if ($usuarioLogueado) {
    $nick = Sistema::app()->acceso()->getNick();
    $codUsuario = Sistema::app()->acl()->getCodUsuario($nick);
    
    // Comprobamos si ya ha votado esta obra (no borrado)
    $favorito = new ObrasFavoritas();
    $yaHaVotado = $favorito->buscarPor([
        "where" => "cod_obra = " . intval($obra->cod_obra) . " AND cod_usuario = " . intval($codUsuario) . " AND borrado = 0"
    ]);
}

// ____________ CONTENEDOR PRINCIPAL ____________

echo CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-contenedor"],

    // Columna Izquierda - Imagen
    CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-col-izq"], 
        CHTML::imagen($rutaImg, htmlspecialchars($obra->nombre), ["class" => "ver-obra-img"])
    ) .

    // Columna Derecha - Info Obra y Artista
    CHTML::dibujaEtiqueta("div", ["class" => "ver-obra-col-der"],

        // BLOQUE 1 - Información de la Obra
        CHTML::dibujaEtiqueta("div", ["class" => "bloque-info-obra"],

            CHTML::dibujaEtiqueta("div", ["class" => "obra-header-container"],
                CHTML::dibujaEtiqueta("h2", ["class" => "obra-nombre"], htmlspecialchars($obra->nombre)) .
                
                // Formulario para dar/quitar like
                ($usuarioLogueado ?
                    CHTML::iniciarForm(
                        Sistema::app()->generaURL(["obras", $yaHaVotado ? "quitarLike" : "darLike"]),
                        "POST", ["id" => "form-like-" . intval($obra->cod_obra), "style" => "display:inline;"]
                    ) .
                    CHTML::campoHidden("cod_obra", (string) $obra->cod_obra) .
                    CHTML::campoBotonSubmit("★", ["class" => "btn-me-gusta" . ($yaHaVotado ? " votada" : "")]) .
                    
                    CHTML::finalizarForm() 

                    : // Si no hay usuario logueado, mostramos el botón de like deshabilitado

                    CHTML::dibujaEtiqueta("button", [
                        "class" => "btn-me-gusta",
                        "disabled" => "disabled",
                        "type" => "button"
                    ], "★")
                )
            ) .
            CHTML::dibujaEtiqueta("div", ["class" => "obra-valoracion"], $estrellas) .
            CHTML::dibujaEtiqueta("p", ["class" => "obra-descripcion"], htmlspecialchars($obra->descripcion)) .

            CHTML::dibujaEtiqueta("div", ["class" => "obra-footer-info"], 
                CHTML::dibujaEtiqueta("span", ["class" => "obra-fecha"], htmlspecialchars($obra->fecha_alta)) .
                CHTML::dibujaEtiqueta("span", ["class" => "obra-categoria"], ucfirst(htmlspecialchars($existeCat->descripcion ?? "Sin categoría")))
            )
        ) .

        // BLOQUE 2 - Información del Artista
        ($existeUsu ?
            CHTML::dibujaEtiqueta("div", ["class" => "bloque-info-artista"],
                
                CHTML::dibujaEtiqueta("div", ["class" => "artista-col-foto"],
                    CHTML::imagen("/imagenes/perfiles/" . htmlspecialchars($existeUsu->img_perfil ?? "ImgPerfilDefault.jpg"),
                        htmlspecialchars($existeUsu->nick ?? "Artista"),
                        ["class" => "foto-artista"]
                    )
                ) .
                CHTML::dibujaEtiqueta("div", ["class" => "artista-col-detalles"], 
                    CHTML::dibujaEtiqueta("h3", ["class" => "artista-nick"], htmlspecialchars($existeUsu->nick ?? "Artista")) .
                    CHTML::dibujaEtiqueta("p", ["class" => "artista-descripcion"], htmlspecialchars($existeUsu->descripcion ?? "")) .
                        
                    CHTML::dibujaEtiqueta("div", ["class" => "artista-footer-info"],
                        CHTML::dibujaEtiqueta("span", ["class" => "artista-email"], htmlspecialchars($existeUsu->email ?? "")) .
                        CHTML::dibujaEtiqueta("span", ["class" => "artista-pais"], htmlspecialchars($existeUsu->pais ?? ""))
                    )
                )
            ) : ""
        )
    )
);
