<?php

// Verificamos que tenemos el modelo del formulario
if (!isset($modelo)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "Error al cargar el formulario de login");
    return;
}

echo CHTML::dibujaEtiqueta("div", ["class" => "auth-contenedor"],

    // ______ PANEL DEL FORMULARIO ______
    CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-form"],

        // Cabecera del panel
        CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-header"],
            CHTML::dibujaEtiqueta("img", [
                "src" => "/imagenes/iconos_propios/svg/person-circle.svg",
                "alt" => "DistarArt",
                "class" => "auth-logo invertir-color"
            ]) .
                CHTML::dibujaEtiqueta("h2", ["class" => "auth-titulo"], "Iniciar Sesión")
        ) .

            CHTML::iniciarForm(Sistema::app()->generaURL(["registro", "login"]), "POST", ["class" => "auth-form"]) .

            CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                CHTML::modeloLabel($modelo, "nick") .
                    CHTML::modeloText($modelo, "nick") .
                    CHTML::modeloError($modelo, "nick")
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                CHTML::modeloLabel($modelo, "contrasenia") .
                    CHTML::modeloPassword($modelo, "contrasenia") .
                    CHTML::modeloError($modelo, "contrasenia")
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "campo-boton"],
                CHTML::campoBotonSubmit("Iniciar Sesión", ["class" => "boton auth-boton"])
            ) .

            CHTML::finalizarForm()
    ) .

        CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-info"],

            CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-info-3"],
                CHTML::dibujaEtiqueta("img", [
                    "src" => "/imagenes/Logo-DistarArt.png",
                    "alt" => "DistarArt",
                    "class" => "auth-info-logo"
                ]) .

                    CHTML::dibujaEtiqueta("h3", ["class" => "auth-info-titulo"], "Bienvenido a DistarArt")
            ) .

                CHTML::dibujaEtiqueta("p", ["class" => "auth-info-texto"],
                    "Accede a tu cuenta para gestionar tus encargos, conectar con otros artistas y compartir tu creatividad."
                ) .

                CHTML::dibujaEtiqueta("div", ["class" => "auth-info-separador"]) .

                CHTML::dibujaEtiqueta("p", ["class" => "auth-info-texto"],
                    "¿No tienes cuenta? " . CHTML::link("Regístrate aquí", ["registro", "pedirDatosRegistro"], ["class" => "auth-link"])
                )
        )
);
