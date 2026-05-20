<?php
// Verificamos que tenemos los datos del error
$numError = $numError ?? 500; // → Código de error por defecto
$mensaje = $mensaje ?? 'Error'; // → Mensaje de error por defecto

header("HTTP/1.1 $numError $mensaje");
header("Status: $numError $mensaje");

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>ERROR</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/estilos/principal.css" />
	<link rel="icon" type="image/png" href="/imagenes/Logo-DistarArt.ico">
	
	<?php
		if (isset($this->textoHead))
		echo $this->textoHead;
	?>
</head>

<body>
	<header class="cabecera">
		<!-- SECCIÓN IZQUIERDA: Logo y Título -->
		<div class="cabecera-izq">
			<a href="/index.php" class="logo-url">
				<img class="logo-img" src="/imagenes/Logo-DistarArt.png" alt="Logo DistarArt" />
			</a>
			<a href="/index.php" class="titulo-url">
				<h1 class="titulo-cabecera">Distar<span class="lt-moradas">Art</span></h1>
			</a>
		</div>

		<!-- SECCIÓN CENTRO: Menú de navegación -->
		<div class="cabecera-centro">
			<?php

			if (isset($this->barraMenu) && !empty($this->barraMenu)) {
				foreach ($this->barraMenu as $opcion) {
					echo CHTML::dibujaEtiqueta(
						"p",
						array(),
						"",
						false
					);

					echo CHTML::link(
						$opcion["texto"],
						$opcion["enlace"]
					);

					echo CHTML::dibujaEtiquetaCierre("p");
				}
			} else {
				echo "<br>";
			}
			?>
		</div>

		<!-- SECCIÓN DERECHA: Búsqueda, Usuario, inicios de sesión e iconos -->
		<div class="cabecera-der">
			<!-- Formulario de búsqueda -->
			<form class="formulario-busqueda">

				<input type="text" name="q" placeholder="Buscar..." class="input-busqueda">

				<button class="boton-busqueda" title="Buscar">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="11" cy="11" r="8"></circle>
						<path d="m21 21-4.35-4.35"></path>
					</svg>
				</button>

			</form>

			<div class="usuario-info">
				<p class="usuario-conectado">Sin usuario conectado</p>
			</div>
			<div class="iconos-cabecera">

				<?php
				// —————————————————————————————————————————————
				//    IMAGEN INVITADO CON COOKIE (24 HORAS)
				// —————————————————————————————————————————————

				// Si NO hay usuario registrado, usar cookie para la imagen
				if (!Sistema::app()->acceso()->hayUsuario()) {
					$nombreCookie = "img_invitado_24h";

					// Si la cookie existe, usar esa imagen → Imagen consistente
					if (isset($_COOKIE[$nombreCookie])) {
						$numeroImagen = $_COOKIE[$nombreCookie];  // → Número almacenado (1-5)
					} else {
						// Cookie NO existe → Generar aleatoria y guardarla 24h
						$numeroImagen = rand(1, 5);
						setcookie($nombreCookie, $numeroImagen, time() + (24 * 60 * 60), "/"); // → Cookie 24 horas
					}

					$rutaIconoPerfil = "/imagenes/perfiles/default/ImgPerfil_" . $numeroImagen . ".png";
					$urlPerfil = Sistema::app()->generaURL(["registro", "login"]);
				} else {
					// Usuario registrado → Usar su imagen de BD
					$nick = Sistema::app()->acceso()->getNick();
					$usuario = new Usuario();

					if ($usuario->buscarPor(["where" => "nick = '" . CGeneral::addSlashes($nick) . "'"])) {
						// Si no tiene imagen, usar default
						$imgPerfil = !empty($usuario->img_perfil) ? htmlspecialchars($usuario->img_perfil) : "ImgDefault.jpg";
						$rutaIconoPerfil = "/imagenes/perfiles/" . $imgPerfil;
						$urlPerfil = Sistema::app()->generaURL(["usuarios", "perfil"]);
					} else {
						// Si no encuentra al usuario, usar default
						$rutaIconoPerfil = "/imagenes/perfiles/ImgDefault.jpg";
						$urlPerfil = Sistema::app()->generaURL(["usuarios", "perfil"]);
					}
				}

				echo CHTML::link(CHTML::imagen($rutaIconoPerfil, "Perfil", ["class" => "icono-circulo perfil"]), $urlPerfil);

				echo CHTML::link(CHTML::imagen("/imagenes/iconos_propios/icono-cerrar-sesion-blanco.png", "Salir", ["class" => "icono-circulo salir"]), Sistema::app()->generaURL(["registro", "logout"]));

				?>

			</div>

		</div>

	</header><!-- #header -->

	<div id="barraUbi">
		<div>
			<?php
			if (isset($this->barraUbi)) {
				foreach ($this->barraUbi as $opcion) {
					echo CHTML::dibujaEtiqueta(
						"p",
						array(),
						"",
						false
					);
					echo CHTML::link(
						$opcion["texto"] . " →",
						$opcion["enlace"]
					);
					echo CHTML::dibujaEtiquetaCierre("p");
				}
			}

			?>
		</div>
	</div>

	<article style="margin: 2rem;display:flex;justify-content: center;align-items: center;">
		<img id="logo_pag_error" src="/imagenes/error_320x320.png" alt="">
		<span id="mensaje_pag_error" style="padding-left: 1rem;"><?php echo $mensaje; ?></span>
	</article><!-- #content -->

	<footer>
		<h2><span>Copyright:</span> <?php echo Sistema::app()->autor ?></h2>

	</div><!-- #wrapper -->
</body>

</html>