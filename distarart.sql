-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 07-05-2026 a las 11:14:05
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `distarart`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_roles`
--

CREATE TABLE `acl_roles` (
  `cod_acl_role` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `perm1` tinyint(1) NOT NULL,
  `perm2` tinyint(1) NOT NULL,
  `perm3` tinyint(1) NOT NULL,
  `perm4` tinyint(1) NOT NULL,
  `perm5` tinyint(1) NOT NULL,
  `perm6` tinyint(1) NOT NULL,
  `perm7` tinyint(1) NOT NULL,
  `perm8` tinyint(1) NOT NULL,
  `perm9` tinyint(1) NOT NULL,
  `perm10` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `acl_roles`
--

INSERT INTO `acl_roles` (`cod_acl_role`, `nombre`, `perm1`, `perm2`, `perm3`, `perm4`, `perm5`, `perm6`, `perm7`, `perm8`, `perm9`, `perm10`) VALUES
(2, 'artista', 0, 0, 0, 0, 0, 0, 1, 1, 0, 0),
(3, 'administrador', 0, 0, 0, 0, 0, 0, 0, 1, 1, 1),
(4, 'administrativo', 0, 0, 0, 0, 0, 0, 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl_usuarios`
--

CREATE TABLE `acl_usuarios` (
  `cod_acl_usuario` int(11) NOT NULL,
  `cod_acl_role` int(11) NOT NULL,
  `nick` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `contrasenia` varchar(65) NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `acl_usuarios`
--

INSERT INTO `acl_usuarios` (`cod_acl_usuario`, `cod_acl_role`, `nick`, `nombre`, `contrasenia`, `borrado`) VALUES
(1, 3, 'Administrador', 'Administrador', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(2, 4, 'Administrativo', 'Administrativo', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(3, 2, 'Diwin', 'Diana Romero', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(4, 2, 'Feefal', 'Linnea Kikuchi', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(5, 2, 'carles_dalmau', 'Carles Dalmau', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(6, 2, 'noe', 'Noemí', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(7, 2, 'alejandra_pls', 'Alejandra  Veloso', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(8, 2, 'JelArts', 'JelArts', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(9, 2, 'colorful_and_wild', 'Jess', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(10, 2, 'joyie_hayve', 'Joyie Hay Ve', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(11, 2, 'Karo.line.art', 'Karolina', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(12, 2, 'Eufonia', 'Eufonia', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(13, 2, 'niilopez', 'Nil López', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(14, 2, 'thefireseal', 'The fire seal', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0),
(28, 2, 'aikee', 'Aikee', 'f1575b49081d7abe41fd7f150f4a2d0a7c22a96b', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `cod_categoria` int(11) NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `borrado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`cod_categoria`, `descripcion`, `borrado`) VALUES
(1, 'digital', 0),
(2, 'tradicional', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `cod_cliente` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `direccion` varchar(150) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `presupuesto` float NOT NULL,
  `fecha_alta` date NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`cod_cliente`, `cod_usuario`, `nombre`, `email`, `direccion`, `pais`, `presupuesto`, `fecha_alta`, `borrado`) VALUES
(1, 3, 'Lucía Lagos Conejo', 'lucialc@gmail.com', 'C/ Lucía', 'España', 20, '2026-04-15', 0),
(2, 3, 'Ana Mar Flores Sevilla', 'anita04@gmail.com', 'C/Ana n3', 'España', 20, '2026-04-13', 0),
(5, 4, 'Alexy Black', 'alexy23@gmail.com', 'C/ Alexy', 'Estados Unidos', 45, '2026-04-08', 0),
(6, 3, 'Néstor De La Vega', 'nestordlv14@gmail.com', 'C/ Néstor', 'España', 15, '2026-04-23', 0),
(7, 3, 'José Antonio López', 'joseantoniolp@gmail.com', 'C/ José', 'España', 23, '2026-04-23', 0),
(8, 3, 'Rebeca Romero', 'rebromleo@gmail.com', 'C/ Rebeca', 'España', 35, '2026-04-27', 0),
(9, 1, 'Diana Romero', 'dromleo1909@g.educaand.es', 'C/ Diana', 'España', 20, '2026-04-27', 0),
(10, 1, 'Alejandro Valverde', 'alevalver@gmail.com', 'C/ Alejandro', 'España', 100, '2026-04-27', 0),
(11, 5, 'Ignacio', 'ignacioic@gmai.com', 'C/ IC', 'España', 23, '2026-04-27', 0),
(12, 5, 'Claudia Jurado', 'claustudio@gmail.com', 'C/ Claudia', 'España', 45.17, '2026-04-28', 0),
(13, 5, 'Brian', 'brian@gmail.com', 'C/ Brian', 'Estados Unidos', 198, '2026-04-28', 0),
(14, 10, 'Guillermo Valencia', 'guillevalca@gmail.com', 'C/ Guille', 'Francia', 34, '2026-04-28', 0),
(15, 10, 'Paulino Jimenez', 'paujim@gmail.com', 'C/ Paulino', 'España', 10, '2026-04-28', 0),
(17, 28, 'David Valverde', 'davidvalerde@gmail.com', 'C/ David', 'España', 25, '2026-05-06', 0),
(18, 28, 'Diana Romero', 'dromleo1909@g.educaand.es', 'C/ Diana', 'España', 15, '2026-05-06', 0),
(19, 28, 'Pepe García', 'pepitogarci@gmail.com', 'C/ Pepito', 'Portugal', 34.34, '2026-05-06', 1),
(20, 28, 'Luis', 'luisito@gmail.com', 'C/ Luis', 'Japón', 37.01, '2026-05-06', 1),
(21, 28, 'Juanma', 'juanmaperez@gmail.com', 'C/ Juanma', 'Francia', 123, '2026-05-06', 0),
(22, 28, 'Jairo', 'jairo@gmail.com', 'C/ Jairo', 'Bélgica', 45, '2026-05-06', 0);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `datos_obras`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `datos_obras` (
`cod_obra` int(11)
,`cod_usuario` int(11)
,`cod_categoria` int(11)
,`nombre` varchar(100)
,`descripcion` varchar(1000)
,`img_principal` varchar(60)
,`valoracion` float
,`fecha_alta` date
,`borrado` tinyint(1)
,`nick_usuario` varchar(30)
,`descripcion_categoria` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encargos`
--

CREATE TABLE `encargos` (
  `cod_encargo` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(1000) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `estado` int(11) NOT NULL,
  `precio_base` float NOT NULL,
  `iva` float NOT NULL,
  `precio_total` float NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_limite` date NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `imagen_proceso` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'EncargoDefault.png',
  `comentarios` text CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT '\'\'',
  `borrado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `encargos`
--

INSERT INTO `encargos` (`cod_encargo`, `cod_usuario`, `cod_cliente`, `nombre`, `descripcion`, `estado`, `precio_base`, `iva`, `precio_total`, `fecha_alta`, `fecha_limite`, `version`, `imagen_proceso`, `comentarios`, `borrado`) VALUES
(1, 3, 1, 'Gato Real', 'Gato atigrado naranja con estilo de la realeza antigua con bastón de oro, corona de oro y joyas rojas y una capa típica de rey, sobre un cojín azul oscuro con borlas doradas.', 7, 14, 21, 16.94, '2026-04-16', '2026-04-17', 3, 'gato_real_detallado_3.png', 'Añadirle un collar de zafiros.', 1),
(2, 3, 8, 'Ilustración arquitectónica', 'Ilustración arquitectónica de un instituto con estética gótica-moderna para una novela de magia oscura, con un embarcadero, forma abstracta y zonas peculiares.', 1, 30, 21, 36.3, '2026-04-27', '2026-05-21', 2, 'EncargoDefault.png', '', 0),
(4, 3, 6, 'Logo para aplicación de plantas', 'Logo con el nombre de la aplicación WaterBuddy, una aplicación para el cuidado de plantas.', 8, 15, 21, 18.15, '2026-04-27', '2025-04-17', 2, 'logo_para_aplicacin_de_plantas_finalizado_2.png', '', 0),
(5, 9, 10, 'Ilustración pareja en Paris', 'Ilustración de una pareja (una chica y un chico) mirándose felices por la noche en París frente al Louvre. La chica debe tener el pelo oscuro y suelto, con un par de mechones cogidos por detrás con un lazo rosa y dos mechones en el flequillo, un abrigo marrón a juego con sus botas y una falda rosa. El chico, con una chaqueta estilo deportiva americana y vaqueros y con gafas y el pelo medio corto oscuro y un poco de barba.', 1, 69.99, 21, 84.6879, '2026-04-27', '2026-05-23', 1, 'EncargoDefault.png', '', 0),
(8, 5, 12, 'Ilustración chica con zumos en el puerto', 'Una chica con un zumo y ropa de verano en un puerto mirando el mar.', 1, 30, 21, 36.3, '2026-04-28', '2026-05-22', 1, 'EncargoDefault.png', '', 0),
(9, 5, 13, 'Pegatinas avatar', 'Pegatinas y diseño de un avatar para una marca.', 1, 50, 21, 60.5, '2026-04-28', '2026-07-24', 1, 'EncargoDefault.png', '', 0),
(11, 10, 14, 'Pintura de Taylor Swift', 'Una pintura con tecnica guoache de Taylor Swift en el Eras Tour.', 1, 28, 21, 33.88, '2026-04-28', '2026-06-30', 1, 'EncargoDefault.png', '', 0),
(14, 28, 18, 'Drawing of a kitten', 'Drawing of a kitten looking at its reflection\r\n', 3, 10, 21, 12.1, '2026-05-06', '2026-06-18', 2, 'drawing_of_a_kitten_bocetado_2.jpg', 'The cat must be black.', 0),
(15, 28, 21, 'Cat looking in the door', 'Illustration of a cat looking from the other side of a slightly open door.', 8, 23, 21, 27.83, '2026-05-06', '2026-07-10', 2, 'cat_looking_in_the_door_finalizado_2.jpg', 'Play with shades of blue and yellow.\r\nAdd shadows.\r\nA cat staring adorably.', 0),
(16, 28, 22, 'Cat on a wall', 'Cat on a wall, looking up at dawn', 7, 25, 21, 30.25, '2026-05-06', '2026-07-09', 2, 'cat_on_a_wall_detallado_2.jpg', '', 0),
(17, 3, 1, 'Gamba Mariachi', 'Una gamba con sombrero de mariachi', 4, 15, 21, 18.15, '2026-05-06', '2026-05-14', 2, 'gamba_mariachi_pendiente_revision_2.jpg', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obras`
--

CREATE TABLE `obras` (
  `cod_obra` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_categoria` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(1000) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `img_principal` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'ImgDefault.jpg',
  `valoracion` float NOT NULL,
  `fecha_alta` date NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `obras`
--

INSERT INTO `obras` (`cod_obra`, `cod_usuario`, `cod_categoria`, `nombre`, `descripcion`, `img_principal`, `valoracion`, `fecha_alta`, `borrado`) VALUES
(1, 4, 1, 'AsiaClio', 'Hello everyone! I can finally share this piece. For this design, I aimed to fuse a Neo-Japanese aesthetic with an air of mystery, focusing primarily on the contrast of silhouettes: the geometric rigidity of the hairstyle versus the almost liquid fluidity of those monumental sleeves.\r\n\r\nAs for the technique, I decided to work with a graphite texture brush to preserve that hand-drawn essence even in the digital realm. I played with a palette of desaturated, cool tones in the background, which allowed the warm accents of the sun and the fabric patterns to resonate much more powerfully. For the composition, I used layers of traditional patterns and transparencies in the smoke, seeking to dynamically guide the eye without disrupting the flat aesthetic that I love so much.', 'AsiaClio_129.jpg', 3.5, '2026-03-24', 1),
(2, 8, 2, 'Autum vibes', 'Cozy Owl and Autum vibes.\r\nLately, I\'ve been feeling super inspired by the small details of nature and that touch of cozy fantasy (yes, I\'m totally into cottagecore!).\r\n\r\nThis page is the result of combining the softness of watercolor with the precision of colored pencils, a mixed media technique that allows me to create such detailed textures in the plumage and vegetation over fluid color bases. The whole piece is supported by waterproof ink line art to maintain a clean structure, finishing each illustration with subtle touches of white gouache to bring the highlights and water droplets to life.', 'autum_vibes_895.jpg', 3.1, '2026-03-29', 0),
(3, 4, 1, 'Fragmented', 'In this illustration, I wanted to explore duality and the loss of self-identity through a deconstructed central figure. I was very interested in playing with the idea of ​​multiplicity, represented by the floating skulls that seem to gaze in different directions, while limbs detach from the main body to create a dynamic and somewhat unsettling composition.\r\n\r\nFor the technique, I worked with a fairly loose yet defined digital line art style, aiming for the strokes to retain a certain organic energy. The key to this piece lies in the color palette. I used a base of dark and deep tones for the background and clothing, which allowed me to apply gradients in vibrant pastel shades, especially lilacs, cyans, and pinks, using layer blending modes. This creates that ethereal luminescence effect and that slight touch of chromatic aberration that I love to accentuate the dreamlike and surreal atmosphere of the whole.', 'fragmented_255.jpg', 3.1, '2026-03-27', 0),
(4, 8, 2, 'Forest Cozy Bear', 'Finalmente puedo compartir con ustedes esta pieza en la que he estado trabajando.\n\nQuería capturar ese sentimiento mágico del bosque en pleno otoño, pero con un toque de fantasía. En esta obra, nuestro amigo el oso no solo está pescando, sino que parece estar en medio de un \"vuelo de salmones\". ????????', 'forest_cozy_bear_605.jpg', 4.5, '2026-04-01', 0),
(5, 8, 2, 'Cozy Cat', 'For this piece, I wanted to experiment with a loose and vibrant brushstroke technique, using layers of acrylic gouache. My main focus was capturing the interplay of light and shadow.', 'cozy_cat_658.jpg', 2.5, '2026-03-28', 0),
(6, 4, 1, 'Dark Deer', 'Esta pieza nació de una idea un poco loca: ¿qué pasaría si mezclamos la elegancia de un ciervo con la inquietante anatomía de una araña?. Me encanta explorar ese límite entre lo lindo y lo espeluznante (cute and spooky), que es donde más cómodo me siento creando. ', 'dark_deer_431.jpg', 3.8, '2026-03-30', 0),
(7, 4, 1, 'Head Of Angel', 'Hello everyone! I\'ve finally finished this illustration inspired by a modern seraph aesthetic that I\'ve been obsessed with lately.\r\nFor this piece, I worked entirely digitally in Procreate, focusing on using brushes with a subtle texture that mimics the finish of traditional graphite on canvas. I aimed for a strong contrast between the deep blue background and the vibrant orange accents, drawing the viewer\'s eye directly to the eyes on the wings and the halo. I applied the shading using soft multiply layers to maintain that ethereal and somewhat nostalgic atmosphere that I love to explore in my characters.', 'head_of_angel_189.jpg', 3.5, '2026-03-25', 0),
(8, 4, 1, 'Lunexus', 'Nadie sabe si es un espíritu, un recuerdo… o algo que nunca fue humano. La figura permanece inmóvil, envuelta en un silencio casi sagrado, con una máscara de calavera blanca coronada por cuernos que oculta cualquier rastro de identidad. Bajo ella, su cuerpo revela una caja torácica expuesta, como si la vida y la muerte convivieran en el mismo lugar.', 'lunexus_982.jpg', 4.4, '2026-04-24', 0),
(9, 4, 1, 'Celestial body', 'La figura flota en la oscuridad del cosmos, su piel y cabello teñidos de azul profundo, como si estuviera hecha del propio cielo nocturno. En el centro de su pecho brilla un motivo solar, un pequeño sol que irradia calor y luz en medio de la vastedad estrellada. Detrás de su cabeza, un halo dorado repite esa forma, convirtiéndola en un faro silencioso en la noche.\r\n\r\nEl contraste entre el azul frío de su cuerpo y la calidez de los símbolos solares crea una sensación de equilibrio entre lo etéreo y lo poderoso. Cada estrella del fondo parece girar a su alrededor, envolviéndola en un aura de misterio y tranquilidad. Es como si el universo entero se detuviera para contemplarla, testigo silencioso de su presencia luminosa.', 'celestial_body_359.jpg', 5, '2026-04-22', 0),
(10, 4, 1, 'Tofu', 'Here\'s Tofu, my little mix between a Don Sphynx cat and an alien baby. For this design, I aimed to play with clean lines and a soft color palette that contrasts with his \"eternally stinky\" nature. The main technique is based on precise outlining with fine-tipped pens, where the secret lies in varying the pressure to give dynamism to the strokes. I worked on the color in layers, applying a flat base and then adding those characteristic bluish shadows that help separate the character from the background and give his eyes that ethereal, otherworldly touch.', 'tofu_741.jpg', 2.6, '2026-03-27', 0),
(12, 28, 1, 'END', 'This piece was born from an exploration of nocturnal mysticism and the sense of finality evoked by the word \"END.\" I wanted the cat to function as a silent guardian in an environment that feels both familiar and unsettling, using the red moon to tint the entire atmosphere a crimson hue that contrasts with the depth of the forest.\r\nTechnically, I worked with digital illustration, focusing on the use of halftone screens to add texture and a retro feel to the shadows. I used a limited color palette to unify the composition, applying soft gradients in the sky and bolder brushstrokes in the foreground to create volume without losing the minimalist graphic style that characterizes my work.', 'end_597.jpg', 4.2, '2026-03-25', 0),
(13, 14, 1, 'Jirafa', 'Here\'s a little critter I drew last night while I was seriously procrastinating. I don\'t know if it\'s contemplating the meaning of life or if it just realized it left the oven on, but that vacant stare totally reflects how I feel today. I dabbled in pixel art, but without overcomplicating things; I just wanted those giant eyes to be the main focus.', 'jirafa_775.jpg', 4.5, '2026-03-28', 0),
(14, 8, 2, 'Bear', 'Hoy quería probar algo más orgánico, así que hice este oso combinando rotuladores y lápices de color. Empecé marcando las sombras con rotulador para darle fuerza y contraste, y luego suavicé todo con capas de lápiz para añadir textura en el pelaje.\r\n\r\nMe interesaba que no se viera \"perfecto\", sino que se notaran los trazos y el proceso, sobre todo en la cara y las patas. Creo que ahí es donde más vida gana el dibujo.\r\n\r\nSigo experimentando con mezclar técnicas tradicionales… todavía hay cosas que pulir ✏️', 'bear_488.jpg', 5, '2026-04-28', 0),
(15, 12, 1, 'Eufonia', '', 'Eufonia_614.jpg', 5, '2026-03-11', 0),
(16, 12, 1, 'Magic_witch', 'Aquí les comparto por fin esta pieza en la que estuve trabajando.\r\nLa idea nació de una mezcla entre estética cyberpunk y ese misticismo de las runas antiguas. Quería jugar con el contraste de los azules profundos y ese neón turquesa que parece que tiene vida propia. ✨', 'magic_witch_431.jpg', 5, '2026-03-30', 0),
(17, 12, 1, 'Spectral', '', 'Spectral_267.jpg', 5, '2026-03-28', 0),
(18, 10, 2, 'Cat painting', 'Drawing of a kitten in the rain with gouache', 'cat_painting_904.jpg', 4.5, '2026-03-31', 0),
(19, 10, 2, 'Little Sorcerer on a branch', 'Hi everyone! I\'ve finally finished this double-page spread in my sketchbook, and I love how these little inhabitants of the magical forest turned out.\r\n\r\nFor this piece, I focused on creating a strong contrast using a solid black background, which makes the neon tones really pop. I used a mixed media technique that combines the opacity of gouache for the base layers with the vibrant detail of Posca markers. To add depth and those grainy textures you see on the mushrooms and vegetation, I applied several layers of colored pencils over the dried paint, which allowed me to play with the light transitions and give the illustration such a tactile feel.', 'little_sorcerer_on_a_branch_769.jpg', 5, '2026-03-25', 0),
(20, 5, 2, 'PurpleFox', 'Today I\'m sharing this little botanical cat that just came out of my sketchbook. I love exploring that blend of animal and organic forms, letting nature sprout directly from them.\r\n\r\nFor this piece, I used my Dr. Ph. Martin\'s Radiant liquid watercolors, specifically the Slate Blue shade you see in the photo. The technique involves working wet-on-wet to achieve those soft gradations between blue and violet, allowing the pigments to blend seamlessly into the paper. Once the base was dry, I added the details of the leaves and fur with a fine liner to give contrast and depth to the composition.', 'purplefox_548.jpg', 3.5, '2026-03-24', 0),
(22, 13, 1, 'Visita al fondo del oceano', 'Un gatito aventurero, se adentro en una aventura submarina. Su submarino tiene forma de pez, con ojos de cristal que brillan suavemente en la oscuridad del océano profundo. \r\n\r\nSu estructura metálica imita las curvas de un ser vivo, y cuando se mueve, lo hace con la elegancia de una criatura marina más. \r\n\r\nDentro, el gato observa en silencio, como si siempre hubiera pertenecido a ese lugar.', 'visita_al_fondo_del_oceano_397.jpg', 4.8, '2026-03-24', 0),
(23, 13, 1, 'A hidden world', 'This piece explores the duality between human fragility and the immensity of the unknown. I was interested in capturing that moment of stillness where curiosity triumphs over fear, using the golden light of the flora to guide the eye directly to the central encounter.\r\nAs for the execution, I applied a digital painting technique based on contrasting values ​​and atmospheric depth. I worked primarily with soft-edged brushes for the color transitions in the background and others with more defined textures for the foreground vegetation. The key was the use of adjustment layers and blending modes to achieve that ethereal glow emanating from the ground, integrating the light particles to unify the entire composition.', 'a_hidden_world_572.jpg', 3, '2026-03-24', 0),
(24, 7, 1, 'Llamada espacial', 'Dicen que nadie sabe exactamente cuánto tiempo lleva flotando ahí arriba. Algunos aseguran que fue enviado en una misión rutinaria; otros, que simplemente se perdió. Pero lo que todos coinciden es en lo extraño de su costumbre: siempre está al teléfono.\r\n\r\nEl astronauta, atrapado en la inmensidad del espacio, nunca se separa de su viejo teléfono de cable. No es un dispositivo moderno ni tiene explicación lógica en un entorno sin gravedad. \r\n\r\nNadie sabe quién está al otro lado de la línea.\r\n', 'llamada_espacial_322.jpg', 4.5, '2026-03-31', 0),
(25, 12, 2, 'Black and white crow', 'This piece was an interesting challenge for last year\'s Inktober. I wanted to play with the contrast between death and a more tender figure, focusing the composition on this little raven guarding its treasures.\r\nAs for the execution, I worked primarily with India ink and watercolor washes in shades of gray to create volume without losing the classic illustration aesthetic. The finishing touch, and what truly brings the illustration to life, are the details in liquid gold, applied with a fine brush to highlight the cracks in the skull and the textures of the bones under direct light.', 'black_and_white_crow_506.jpg', 4, '2026-03-21', 0),
(26, 5, 2, 'Caracol', 'Cada trazo a mano captura la textura rugosa del caparazón, las vetas de la calabaza y la delicadeza del ratón, creando un contraste entrañable entre lo gigantesco y lo diminuto. Hay calma en el movimiento pausado del caracol, una sensación de viaje sereno, casi mágico, como si el mundo entero pudiera observarse desde ese diminuto observatorio ambulante.\r\n\r\nEs un instante de fantasía cotidiana, donde lo cotidiano se convierte en aventura, y lo enorme y lo pequeño conviven en perfecta armonía. ', 'caracol_377.jpg', 4, '2026-04-24', 1),
(27, 5, 2, 'Willow', 'Hi everyone! I\'ve finally finished this piece in my sketchbook and I\'m really happy with how it turned out. I wanted to capture a magical, flowing energy, almost as if the hair had a life of its own, playing with the contrast between the vibrant turquoise tones and the warm pinks of the skin.\r\nAs for the technique, I used a mixed media approach. First, I applied a solid, opaque base of gouache for the main color masses, and then I used colored pencils on top to create that grainy texture effect. Finally, I added the highlights and sharper lines using Posca markers to really make the design pop.', 'willow_311.jpg', 5, '2026-03-26', 0),
(28, 7, 2, 'Tortuga_Express', 'Abrí mi cuaderno y apareció esta tortuga gigante con su pequeño mundo sobre el caparazón: una estructura como un barco, con un gato tranquilo en la cima y una escalera de madera que invita a subir.\r\n\r\nMe divertí combinando tinta negra con toques de lápiz rojo para resaltar detalles y darle profundidad, manteniendo un estilo de cómic que sigue vivo en cada línea. La página izquierda muestra el boceto original, un pequeño recordatorio de cómo nace la idea antes de cobrar vida en tinta.', 'tortuga_express_101.jpg', 5, '2026-04-14', 0),
(29, 10, 2, 'Lizard', 'Today I played with shades of green and textures, trying to capture every scale of this little lizard.\r\n\r\nI wanted it to feel alive, for each leg and every detail to convey its subtle, almost imperceptible movement on the page. Pencils and markers allowed me to blend softness and definition, highlighting the light on its skin and the shadow beneath its body.\r\n\r\nSometimes the small has as much personality as the large… and this lizard definitely does.', 'lagarto_373.jpg', 3, '2026-03-22', 0),
(30, 10, 2, 'Little frogs', 'Hiii everyone! I\'ve finally finished today\'s little scene. It\'s a miniature oil painting on canvas, a piece that aims to capture the idyllic calm of a pond.\r\n\r\nTo achieve this finish, I used a thick, wet-on-wet brushstroke technique. Because of the small size, I focused on simplifying the shapes of the frogs and water lilies, letting the paint\'s texture suggest volume without excessive detail. The layers of green and blue blend directly on the canvas to create the illusion of moving water and filtered light.', 'little_frogs_781.jpg', 1, '2026-04-08', 0),
(31, 10, 2, 'Misifu', '¡Hola a todos! ✨ Finalmente he terminado esta pieza y no puedo estar más feliz con el resultado.\r\n\r\nQuería capturar ese momento perfecto de paz absoluta. ¿Saben esa sensación cuando el sol te calienta la cara y el mundo simplemente se detiene? Eso es exactamente lo que este pequeño amigo está sintiendo entre las margaritas. ????????\r\n\r\nTrabajar con esta paleta de azules vibrantes y naranjas cálidos fue un proceso casi terapéutico. Me encanta cómo las nubes estilizadas abrazan la figura del gato, dándole un aire casi de ensueño. Aunque el estilo tiene líneas muy marcadas, creo que la expresión de felicidad del gato logra transmitir mucha suavidad.\r\n\r\nEs un recordatorio para todos nosotros: a veces, lo único que necesitamos es un momento para respirar y disfrutar del jardín. ☀️', 'misifu_395.jpg', 3, '2026-04-09', 0),
(32, 3, 2, 'Virdrath', 'Esta es una nueva especie de dragón llamada Viridrath; su gruesa piel lo hace muy resistente ante muchos tipos de ataque. Es una especie agresiva y territorial ubicada en la zona alta de Dronnen.\r\n\r\nEste dibujo es el boceto de una de las criaturas que encontraremos próximamente en mi cómic Valdrys.', 'viridrath_437.jpg', 0.5, '2026-04-09', 0),
(33, 3, 2, 'Rey de la sabana', 'Nadie en la sabana se acerca a él, no por miedo únicamente, sino por respeto. El león no ruge como los demás, su presencia basta para imponer silencio.\r\n\r\nDicen que alguna vez fue el más feroz de todos, un rey indiscutible que dominaba cada rincón de su territorio. Pero el tiempo, paciente y silencioso, fue dejando su huella. ', 'rey_de_la_sabana_441.jpg', 0.5, '2026-04-09', 0),
(34, 5, 1, 'Sea Monster', 'En un bote de madera, una pequeña figura con capucha de gato pesca con calma, ajena a lo que acecha debajo. De las profundidades emerge un monstruo marino colosal de escamas turquesa, ojos rojos y mandíbulas llenas de dientes afilados, acercando el peligro silencioso. El contraste entre la inocencia y la amenaza crea una tensión poética, resaltada por detalles en el agua y la criatura.\r\n\r\nEsta ilustración independiente puede encontrarse en JOURNEY: The Art of Carles Dalmau, que recopila sus mejores obras y procesos de diseño, y comparte universo visual con obras como Soma y Brain Rot, publicadas por Planeta Cómic.', 'sea_monster_808.jpg', 1, '2026-04-09', 0),
(35, 3, 1, 'El chico del mapa', 'Este joven aventurero nació de un boceto rápido mientras imaginaba historias de tesoros perdidos y mapas antiguos. Decidí dibujarlo en pleno movimiento, escapando de algún peligro inminente con un pergamino que parece ser la clave de todo su viaje.\r\n\r\nEl diseño de su ropa, con esas vendas en el brazo y los detalles desgastados en los pantalones, busca reflejar una vida de constantes desafíos y poco descanso. Me interesaba capturar esa expresión de determinación mezclada con la adrenalina del momento. Siento que este chico esconde una historia profunda, quizás es el último de su linaje o simplemente un soñador que se arriesgó a dejarlo todo por una leyenda.\r\n\r\nAún estoy definiendo los detalles de su mundo, pero me gusta pensar que este es solo el comienzo de su gran travesía.', 'el_chico_del_mapa_833.jpg', 0.5, '2026-04-12', 0),
(36, 3, 2, 'Chica a lapiz', '', 'chica_a_lapiz_436.jpg', 1, '2026-04-13', 0),
(37, 3, 2, 'Hombre a lápiz', '', 'hombre_a_lpiz_446.jpg', 0.5, '2026-04-13', 0),
(38, 6, 1, 'limo Hydro de Genshin Impact', 'Este dibujo en estilo pixel art muestra un limo Hydro de Genshin Impact. El personaje es predominantemente azul, con diferentes tonos que simulan agua o gelatina, y tiene una textura pixelada. Sobre su cabeza redonda, que parece un hongo, lleva una gran venda blanca en forma de cruz. \r\n\r\nEl limo está \"vistiendo\" lo que parece ser un pequeño traje blanco con orejas puntiagudas, similar a un gato o un espíritu Seelie, que está parcialmente sumergido en su cuerpo gelatinoso. El fondo es de un color azul claro sólido y hay una pequeña gota o burbuja azul flotando a la derecha del personaje.', 'honguito_genshin_impact_328.png', 2, '2026-04-14', 0),
(39, 6, 1, 'Bichito rosa', 'La imagen presenta una ilustración en pixel art de una adorable criatura regordeta, de color predominantemente rosa claro. Este ser, que evoca a un personaje de videojuego, posee grandes ojos morados brillantes y una boca pequeña y abierta, con un rubor sutil en sus mejillas. Sus pequeñas extremidades están levantadas con entusiasmo mientras se encuentra cómodamente ubicado dentro de un recipiente o maceta de un tono morado más oscuro. Dicho contenedor está adornado con algunos símbolos gráficos simples en blanco, y todo el diseño se sitúa sobre un fondo azul pálido y uniforme.', 'bichito_rosa_173.png', 2, '2026-04-14', 0),
(40, 6, 1, 'That Sea, the Gambler', 'Velero utilizando la técnica de pixel art. Esta obra es una pieza de pixel art que destaca por su vibrante colorido y una composición dinámica contenida en una esfera. En el centro, un velero estilizado navega sobre aguas de un azul intenso, donde se aprecian reflejos dorados que provienen de un sol brillante situado justo detrás del mástil.\r\n\r\nEste dibujo está insparado el la obra de un artista llamado u/TheLastSymphony.', 'that_sea_the_gambler_979.png', 0.5, '2026-04-14', 0),
(41, 11, 2, 'Esencia y Hueso', 'Esta ilustración presenta una composición circular y orgánica donde dos zorros parecen flotar en un espacio onírico, formando una especie de yin y yang visual que explora la dualidad entre la vida y la muerte. En la parte superior, un zorro de pelaje naranja vibrante duerme plácidamente, representando la calidez y la vitalidad del mundo físico. En contraste, la parte inferior muestra a un zorro de tonos azules profundos y gélidos, cuyo cuerpo revela una estructura ósea delicada y estrellas diminutas en su cola, sugiriendo una naturaleza espiritual, astral o la permanencia del ser más allá de la vida.\r\nAmbas figuras se curvan una hacia la otra, casi tocándose con sus hocicos, lo que crea una sensación de equilibrio y continuidad infinita. El fondo, con sus pinceladas sueltas en tonos verdes, rosados y cremas, refuerza esa atmósfera de sueño o de un reino intermedio donde lo tangible y lo etéreo coexisten en perfecta armonía.', 'esencia_y_hueso_778.jpg', 2, '2026-04-14', 0),
(42, 11, 2, 'Caminante de Colores', 'Hoy quiero compartir con ustedes una de mis páginas favoritas del sketchbook. He decidido titular esta pieza  porque mi intención era capturar un momento de pura fantasía y luz.\r\n\r\nEn esta ilustración, quise jugar con la idea de que un pequeño Shiba Inu camina sobre un charco que, en lugar de agua común, refleja un universo vibrante de neones. Si observan de cerca, el pelaje no sigue los tonos naturales; utilicé sombras en azul y lavanda para que el perrito se integrara totalmente con esa explosión de cian y magenta que tiene bajo sus patas. ', 'caminante_de_colores_917.jpg', 1.5, '2026-04-14', 0),
(43, 11, 2, 'Reflejo espectral', 'Tenía muchas ganas de participar en este desafío artístico porque sentía que necesitaba explorar algo con cráneos y anatomía. Decidí trabajar con esta dualidad entre la forma física del gato y su estructura ósea, usando tonos azules y violetas para darle ese aire místico. Me encanta cómo el contraste de las tintas oscuras resalta la fragilidad de los huesos flotando sobre la silueta. Espero que les guste este pequeño estudio en mi cuaderno de bocetos.', 'reflejo_espectral_628.jpg', 1, '2026-04-14', 0),
(44, 3, 2, 'Cicatrices Invisibles', 'A veces las heridas más profundas no se ven a simple vista, aunque intentemos cubrirlas con lo que tenemos a mano. Este boceto nació de un momento de introspección, dejando que el grafito capturara esa sensación de vulnerabilidad que todos sentimos en algún punto.\r\n\r\nHay silencios que dicen más que cualquier palabra y miradas que no pueden ocultar el cansancio. Dibujar esto fue una forma de dejar salir un poco de esa carga.', 'cicatrices_invisibles_607.jpg', 0.5, '2026-04-14', 0),
(45, 3, 1, 'Gambita', 'Gambita era una pequeña habitante del arrecife que decidió ignorar las leyes de la biología para seguir su pasión por la música mexicana. Mientras el resto de los crustáceos se burlaba de sus aspiraciones y cuestionaba cómo una criatura tan diminuta podría cargar un sombrero o tocar un guitarrón, ella fabricó sus propios instrumentos con desechos marinos y ensayó sin descanso. \r\n\r\nSu determinación demostró que el alma de un mariachi no depende del tamaño de las patitas ni bigotes, sino de la fuerza del sentimiento.\r\n\r\nHoy, su música resuena en las profundidades como un recordatorio de que la audacia es el único requisito para transformar la propia naturaleza y alcanzar lo imposible.', 'gambita_104.png', 2, '2026-04-15', 0),
(46, 3, 2, 'Esencia del bosque', 'Finalmente logré terminar este trazo que tenía en mente hace días. Mi intención con esta pieza fue explorar la serenidad en la postura y la fuerza que reside en la simplicidad de la línea.\r\n\r\nMe enfoqué especialmente en el contraste entre el espacio en blanco y los patrones geométricos del vestuario, buscando un equilibrio visual que resaltara la identidad del personaje. Las plumas no son solo un adorno, sino un símbolo de esa ligereza y libertad que busco transmitir en mi trabajo actual.', 'esencia_del_bosque_693.jpg', 1, '2026-04-16', 0),
(48, 3, 2, 'Los Mil Peldaños', 'Hoy quería perderme un poco entre estas paredes de roca y casas colgantes. Este dibujo nació de la idea de una civilización que no teme a las alturas, donde la vida fluye como una cascada desde los picos nevados hasta el mar.\r\nMe enfoqué mucho en el contraste de las líneas verticales de los acantilados para dar esa sensación de vértigo, mientras que los pequeños detalles en los techos y las escaleras cuentan la historia de quienes habitan este lugar. ¿Se imaginan vivir en la casa más alta? ¡El camino al puerto sería un buen ejercicio! ', 'el_descenso_de_los_mil_peldaos_402.jpg', 0.5, '2026-04-16', 0),
(50, 3, 2, 'Mosntruo Seta', 'Prueba', 'mosntruo_seta_450.jpg', 0.5, '2026-04-16', 0),
(51, 3, 2, 'Entrelazado', '', 'entrelazado_354.jpg', 1, '2026-04-16', 0),
(52, 3, 2, 'Determinación', '', 'determinacin_123.jpg', 0.5, '2026-04-16', 0),
(55, 28, 1, 'Aikee', 'New year new me + some of my fav assets from the seasons of love post.', 'aikee_463.jpg', 0, '2026-05-06', 0),
(56, 28, 1, 'The reflection of a cat - Prueba ', 'I\'d like to be myself again, but I\'m still trying to find it.\r\n\r\nSome of my drafts from last year; I never finished them until now. I just got back from Vietnam! An inspiring trip! More art on the way!', 'sd_669.jpg', 0, '2026-05-06', 1),
(57, 28, 1, 'The reflection of a cat', 'I\'d like to be myself again, but I\'m still trying to find it.\r\n\r\nSome of my drafts from last year; I never finished them until now. I just got back from Vietnam! An inspiring trip! More art on the way!', 'the_reflection_of_a_cat_976.jpg', 0, '2026-05-06', 0),
(58, 28, 1, 'Hungry kitten', '', 'hungry_kitten_276.jpg', 0, '2026-05-06', 0),
(59, 28, 1, 'Autumn cat', '', 'autumn_cat_712.jpg', 0, '2026-05-06', 0),
(60, 3, 2, 'Eneightborg', 'Este dibujo es el diseño de una bestia para el cómic que tengo pensado hacer en las vacaciones de verano, y que además he usado como logo para mi aplicación web :P', 'eneightborg_788.jpeg', 0, '2026-05-06', 0),
(61, 3, 2, 'Cría de Eneightborg ', 'Esta es la primera versión que hice del Eneightbor, la verdad es que me acabó gustando más la segunda, pero me daba pena desechar la idea, por lo que pensé que así se podrían ver sus crías.', 'cra_de_eneightborg__533.jpeg', 0, '2026-05-06', 0),
(62, 3, 1, 'El super Informatico', 'Hice este dibujo cuando estaba en grado medio. Pensé: \"Si yo fuera un tipo de informático/a, sería este, ajajajaj\". ', 'el_super_informatico_301.jpg', 0, '2026-05-06', 0),
(63, 3, 1, 'Pelusin', 'Un bichito-monstruito peludo', 'pelusin_584.jpg', 0, '2026-05-06', 0),
(64, 3, 2, 'Ardilla a grafito', 'Ardilla dibujada con grafito', 'ardilla_a_grafito_745.jpg', 0, '2026-05-06', 0),
(65, 3, 2, 'Estela', 'Personaje original creado para el cómic que tengo pensado hacer en estas vacaciones de verano. :D', 'estela_561.jpg', 0, '2026-05-06', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `obras_favoritas`
--

CREATE TABLE `obras_favoritas` (
  `cod_obra_favorita` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_obra` int(11) NOT NULL,
  `fecha_alta` date NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `obras_favoritas`
--

INSERT INTO `obras_favoritas` (`cod_obra_favorita`, `cod_usuario`, `cod_obra`, `fecha_alta`, `borrado`) VALUES
(1, 8, 4, '2026-03-26', 0),
(2, 5, 14, '2026-03-30', 0),
(3, 8, 6, '2026-03-28', 0),
(4, 8, 17, '2026-04-02', 0),
(5, 3, 14, '2026-04-14', 0),
(6, 3, 9, '2026-04-14', 0),
(7, 3, 38, '2026-04-14', 1),
(8, 3, 41, '2026-04-14', 0),
(12, 3, 42, '2026-04-14', 0),
(13, 3, 39, '2026-04-14', 0),
(14, 11, 42, '2026-04-14', 0),
(15, 11, 14, '2026-04-14', 1),
(16, 11, 38, '2026-04-14', 0),
(17, 11, 39, '2026-04-14', 0),
(18, 11, 43, '2026-04-14', 0),
(19, 11, 8, '2026-04-14', 0),
(20, 11, 2, '2026-04-14', 0),
(21, 6, 14, '2026-04-14', 0),
(22, 6, 8, '2026-04-14', 0),
(23, 6, 9, '2026-04-14', 0),
(24, 6, 28, '2026-04-14', 0),
(25, 6, 38, '2026-04-14', 0),
(26, 6, 39, '2026-04-14', 0),
(27, 6, 40, '2026-04-14', 0),
(28, 6, 41, '2026-04-14', 0),
(29, 6, 42, '2026-04-14', 0),
(30, 6, 43, '2026-04-14', 0),
(31, 6, 36, '2026-04-14', 0),
(32, 6, 37, '2026-04-14', 0),
(33, 6, 35, '2026-04-14', 0),
(34, 6, 7, '2026-04-14', 0),
(35, 6, 24, '2026-04-14', 0),
(36, 6, 30, '2026-04-14', 0),
(37, 6, 4, '2026-04-14', 0),
(38, 6, 32, '2026-04-14', 0),
(39, 6, 34, '2026-04-14', 0),
(40, 6, 16, '2026-04-14', 0),
(41, 6, 19, '2026-04-14', 0),
(42, 6, 5, '2026-04-14', 0),
(43, 11, 41, '2026-04-14', 0),
(44, 11, 28, '2026-04-14', 0),
(45, 11, 9, '2026-04-14', 0),
(46, 3, 45, '2026-04-15', 0),
(47, 6, 45, '2026-04-15', 0),
(48, 3, 4, '2026-04-15', 0),
(49, 3, 36, '2026-04-15', 0),
(50, 1, 8, '2026-04-16', 1),
(51, 7, 9, '2026-04-16', 0),
(52, 7, 39, '2026-04-16', 0),
(53, 10, 45, '2026-04-16', 0),
(54, 10, 41, '2026-04-16', 0),
(55, 3, 46, '2026-04-16', 0),
(56, 3, 8, '2026-04-21', 0),
(57, 3, 51, '2026-04-21', 0),
(58, 3, 28, '2026-04-27', 0),
(59, 22, 54, '2026-04-29', 0),
(60, 3, 44, '2026-04-29', 0),
(61, 3, 33, '2026-04-29', 0),
(62, 3, 48, '2026-04-29', 0),
(63, 28, 8, '2026-05-06', 0),
(64, 28, 46, '2026-05-06', 0),
(65, 28, 51, '2026-05-06', 0),
(66, 28, 45, '2026-05-06', 0),
(67, 28, 38, '2026-05-06', 0),
(68, 28, 50, '2026-05-06', 0),
(69, 28, 30, '2026-05-06', 0),
(70, 28, 34, '2026-05-06', 0),
(71, 28, 52, '2026-05-06', 0),
(72, 28, 20, '2026-05-06', 0),
(73, 28, 27, '2026-05-06', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cod_usuario` int(11) NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nick` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(500) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `direccion` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `pais` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `img_perfil` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `img_banner` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `valoracion` float NOT NULL,
  `fecha_alta` date NOT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cod_usuario`, `nombre`, `nick`, `email`, `descripcion`, `direccion`, `pais`, `img_perfil`, `img_banner`, `valoracion`, `fecha_alta`, `borrado`) VALUES
(3, 'Diana Romero', 'Diwin', 'diwin.art@gmail.com', 'Dibujo a lápiz y arte digital.\r\nExplorando expresiones y pequeñas historias a través del dibujo.\r\nMe gusta dibujar cualquier cosa que se me pasa por la cabez pero en su mayoria OCs que me inspiran historias increibles.\r\nIG → @diwin_art\r\n', 'C/Prueba N1', 'España', 'perfil_Diwin.jpg', 'diwin_art.png', 0, '2026-03-12', 0),
(4, 'Linnea Kikuchi', 'Feefal', 'feefal@gmail.com', 'Artist based in Sweden I\'m into biology, natural decomposers', 'C/Prueba N2', 'Suecia', 'Feefal.jpg', 'Feefal_banner.jpg', 0, '2026-03-22', 0),
(5, 'Carles Dalmau', 'carles_dalmau', 'carles_dalmau@gmail.com', 'BRAINROT, Lucid Lucy, Calamari Kebab, Soma and Pilgrim!, Art', 'C/Prueba N7', 'Estados Unidos', 'carles_dalmau.png', 'carles_dalmau_banner.png', 0, '2026-03-23', 0),
(6, 'Noemí', 'Noe', 'noemipp@gmail.com', 'Probando por no se cuanta vez', 'C/Mollina ', 'España', 'perfil_Noe.PNG', 'banner_Noe.jpg', 0, '2026-04-14', 0),
(7, 'Alejandra Veloso', 'alejandra_pls', 'alejandraveloso59@gmail.com', 'Ig: @alejandra_pls', 'C/Prueba N4', 'España', 'Alejandra_pls.jpg', 'Alejandra_pls_banner.jpg', 0, '2026-03-24', 0),
(8, 'JelArts', 'JelArts', 'jelarts@gmail.com', 'I like drawing cozy animals, full-time canadian artist.', 'C/Prueba N10', 'Canada', 'JelArts.jpg', 'JelArts_banner.png', 4.025, '2026-03-26', 0),
(9, 'Jess', 'colorful_and_wild', 'colorful.and.wild@gmail.com', 'Digital Artist (CSP) Character Design and Illustrations', 'C/Prueba N11', 'Estados Unidos', 'colorful_and_wild.png', 'colorful_and_wild_banner.png', 4.5, '2026-03-16', 0),
(10, 'Joyie Hay Ve', 'joyie_hayve', 'joyiie.art@gmailcom', 'Acrylic marker, gouache, watercolor', 'C/Prueba N8', 'China', 'joyie_hayve.jpg', 'joyie_hayve_banner.png', 0, '2026-02-28', 0),
(11, 'Karolina', 'Karo.line.art', 'karolineart@gmail.com', 'Freelance belgium artist, ig @karo.line.art @karro.art', 'C/Prueba N3', 'España', 'perfil_Karo.line.art.png', 'banner_Karo.line.art.png', 0, '2026-03-24', 0),
(12, 'Eufonia', 'Eufonia', 'eufonia@gmail.com', 'London, UK |  INTP', 'C/Eufonia', 'Reino Unido', 'eufonia.jpg', 'banner_Eufonia.jpg', 4.875, '2026-04-02', 1),
(13, 'Nil López', 'niilopez', 'nilrogriguezart@gmail.com', 'Illustrator and comic artist', 'C/Prueba N5', 'España', 'niilopez.jpg', 'niilopez_banner.jpg', 4.8, '2026-03-18', 0),
(14, 'The fire seal', 'thefireseal', 'thefireseal@gmail.com', 'Home of the Corndog lord | Ig: @thefireseal', 'C/Prueba N6', 'Estados Unidos', 'thefireseal.jpg', 'thefireseal_banner.jpg', 4.6, '2026-03-21', 0),
(28, 'Aikee', 'aikee', 'aikee.art@gmail.com', 'art ✦ design ✦ cats\r\n→ back from hiatus (April 2026)\r\n→ aikee.art@gmail.com for inquiries\r\n→ all my other links and prints below\r\n→ no reposting', 'C/Filipinas 12', 'Otro', 'perfil_aikee.jpg', 'banner_aikee.jpg', 0, '2026-05-06', 0);

-- --------------------------------------------------------

--
-- Estructura para la vista `datos_obras`
--
DROP TABLE IF EXISTS `datos_obras`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `datos_obras`  AS SELECT `o`.`cod_obra` AS `cod_obra`, `o`.`cod_usuario` AS `cod_usuario`, `o`.`cod_categoria` AS `cod_categoria`, `o`.`nombre` AS `nombre`, `o`.`descripcion` AS `descripcion`, `o`.`img_principal` AS `img_principal`, `o`.`valoracion` AS `valoracion`, `o`.`fecha_alta` AS `fecha_alta`, `o`.`borrado` AS `borrado`, `u`.`nick` AS `nick_usuario`, `c`.`descripcion` AS `descripcion_categoria` FROM ((`obras` `o` left join `usuarios` `u` on(`o`.`cod_usuario` = `u`.`cod_usuario`)) left join `categorias` `c` on(`o`.`cod_categoria` = `c`.`cod_categoria`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acl_roles`
--
ALTER TABLE `acl_roles`
  ADD PRIMARY KEY (`cod_acl_role`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `acl_usuarios`
--
ALTER TABLE `acl_usuarios`
  ADD PRIMARY KEY (`cod_acl_usuario`),
  ADD UNIQUE KEY `nick` (`nick`),
  ADD KEY `cod_acl_role` (`cod_acl_role`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`cod_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cod_cliente`),
  ADD KEY `cod_usuario` (`cod_usuario`);

--
-- Indices de la tabla `encargos`
--
ALTER TABLE `encargos`
  ADD PRIMARY KEY (`cod_encargo`),
  ADD KEY `cod_usuario` (`cod_usuario`,`cod_cliente`);

--
-- Indices de la tabla `obras`
--
ALTER TABLE `obras`
  ADD PRIMARY KEY (`cod_obra`),
  ADD KEY `cod_usuario` (`cod_usuario`,`cod_categoria`);

--
-- Indices de la tabla `obras_favoritas`
--
ALTER TABLE `obras_favoritas`
  ADD PRIMARY KEY (`cod_obra_favorita`),
  ADD KEY `cod_usuario` (`cod_usuario`,`cod_obra`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cod_usuario`),
  ADD UNIQUE KEY `nick` (`nick`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acl_roles`
--
ALTER TABLE `acl_roles`
  MODIFY `cod_acl_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `acl_usuarios`
--
ALTER TABLE `acl_usuarios`
  MODIFY `cod_acl_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `cod_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cod_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `encargos`
--
ALTER TABLE `encargos`
  MODIFY `cod_encargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `obras`
--
ALTER TABLE `obras`
  MODIFY `cod_obra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `obras_favoritas`
--
ALTER TABLE `obras_favoritas`
  MODIFY `cod_obra_favorita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `cod_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
