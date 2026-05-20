<?php

// Verificamos que tenemos el modelo del formulario
if (!isset($modelo)) {
    echo CHTML::dibujaEtiqueta("p", ["class" => "sin-resultados"], "Error al cargar el formulario de registro");
    return;
}

echo CHTML::dibujaEtiqueta("div", ["class" => "auth-contenedor auth-contenedor-registro"],

    CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-form"],

        CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-header"],
            CHTML::dibujaEtiqueta("img", [
                "src"   => "/imagenes/iconos_propios/svg/brush.svg",
                "alt"   => "DistarArt",
                "class" => "auth-logo invertir-color"
            ]) .
            CHTML::dibujaEtiqueta("h2", ["class" => "auth-titulo"], "Crea tu cuenta en DistarArt")
        ) .

        CHTML::iniciarForm(Sistema::app()->generaURL(["registro", "pedirDatosRegistro"]), "POST", ["class" => "auth-form"]) .

            CHTML::dibujaEtiqueta("div", ["class" => "fila-campos"],
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($modelo, "nick") .
                    CHTML::modeloText($modelo, "nick") .
                    CHTML::modeloError($modelo, "nick")
                ) .
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($modelo, "nombre") .
                    CHTML::modeloText($modelo, "nombre") .
                    CHTML::modeloError($modelo, "nombre")
                )
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                CHTML::modeloLabel($modelo, "email") .
                CHTML::modeloText($modelo, "email") .
                CHTML::modeloError($modelo, "email")
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "fila-campos"],
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($modelo, "pais") .
                    CHTML::modeloListaDropDown($modelo, "pais", [
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
                        "Otro" => "Otro",
                    ], []) .
                    CHTML::modeloError($modelo, "pais")
                ) .
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($modelo, "direccion") .
                    CHTML::modeloText($modelo, "direccion") .
                    CHTML::modeloError($modelo, "direccion")
                )
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "fila-campos"],
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($modelo, "contrasenia") .
                    CHTML::modeloPassword($modelo, "contrasenia") .
                    CHTML::modeloError($modelo, "contrasenia")
                ) .
                CHTML::dibujaEtiqueta("div", ["class" => "campo-grupo"],
                    CHTML::modeloLabel($modelo, "confirmar_contrasenia") .
                    CHTML::modeloPassword($modelo, "confirmar_contrasenia") .
                    CHTML::modeloError($modelo, "confirmar_contrasenia")
                )
            ) .

            CHTML::dibujaEtiqueta("div", ["class" => "campo-boton"],
                CHTML::campoBotonSubmit("Crear cuenta", ["class" => "boton auth-boton"])
            ) .

        CHTML::finalizarForm()

    ) .

    CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-info"],
        CHTML::dibujaEtiqueta("div", ["class" => "auth-panel-info-2"],
            CHTML::dibujaEtiqueta("img", [
                "src" => "/imagenes/Logo-DistarArt.png",
                "alt" => "DistarArt",
                "class" => "auth-info-logo"
            ]) .

            CHTML::dibujaEtiqueta("h3", ["class" => "auth-info-titulo"], "Completa tu perfil") 
        ) .
        CHTML::dibujaEtiqueta("p", ["class" => "auth-info-texto"],
            "Una vez registrado podrás añadir imagen de perfil, banner y descripción desde tu perfil."
        ) .

        CHTML::dibujaEtiqueta("div", ["class" => "auth-info-separador"]) .

        CHTML::dibujaEtiqueta("p", ["class" => "auth-info-texto"],
            "¿Ya tienes cuenta? " . CHTML::link("Inicia sesión aquí", ["registro", "login"], ["class" => "auth-link"])
        )
    )
);
