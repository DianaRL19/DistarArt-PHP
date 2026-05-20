<div align="center">

<img src="imagenes/Logo-DistarArt.png" alt="Logo DistarArt" width="180"/>

# DistarArt

**Plataforma web para artistas digitales** · Gestión de obras, encargos y galería pública

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![TCPDF](https://img.shields.io/badge/TCPDF-PDF%20Export-E74C3C?style=for-the-badge&logo=adobeacrobatreader&logoColor=white)](https://tcpdf.org)
[![API REST](https://img.shields.io/badge/API-REST%20JSON-F39C12?style=for-the-badge&logo=fastapi&logoColor=white)](https://github.com/DianaRL19/DistarArt-PHP)
[![MVC](https://img.shields.io/badge/Arquitectura-MVC%20Pedrosa[propio]-6C39D8?style=for-the-badge&logo=blueprint&logoColor=white)](https://github.com/DianaRL19/DistarArt-PHP)

*Proyecto final para la asignatura de Desarrollo Web en Entorno Servidor.*

</div>

---

## ¿Qué es DistarArt?

DistarArt es una aplicación web desarrollada en PHP puro con el **framework PEDROSA**, pensada para que artistas digitales puedan publicar su trabajo, gestionar encargos de clientes y mostrar su portfolio al mundo.

La plataforma combina funcionalidades de red social artística (galería pública, favoritos, valoraciones) con herramientas de gestión profesional (encargos con presupuesto e IVA, clientes API, exportación PDF) y se conecta con la **API pública del Museo Metropolitano de Arte de Nueva York** para ofrecer una sección de inspiración.

---

## Características principales

| Módulo | Descripción |
|---|---|
| 🖼️ **Galería pública** | Muestra todas las obras con filtros, búsqueda, paginación y ordenación |
| 🎨 **Gestión de obras** | CRUD completo: subir imágenes, categorías, valoraciones y obras favoritas |
| 📋 **Encargos** | Sistema de pedidos personalizados con precio base, IVA y seguimiento de estado |
| 👥 **Usuarios** | Registro, login, perfiles con imagen y banner, roles y permisos |
| 🔌 **API REST** | Endpoints para gestión de clientes externos (GET, POST, PUT, DELETE) |
| 🌍 **Inspiración externa** | Integración con la API del Met Museum de Nueva York via cURL |
| 📄 **Exportar PDF** | Generación de informes de galería con TCPDF (con cabecera/pie personalizados) |
| 🔐 **Control de acceso** | ACL basado en base de datos con roles y hasta 10 permisos configurables |

---

## Tecnologías utilizadas

- **PHP 8+** — Backend y lógica de negocio
- **MySQL** — Base de datos relacional
- **Framework MVC propio** — Arquitectura en capas (Modelo, Vista, Controlador)
- **TCPDF** — Generación de documentos PDF
- **cURL** — Peticiones a APIs externas
- **Google Fonts** — Tipografías (Yellowtail, Agbalumo)
- **CSS puro** — Estilos personalizados sin dependencias externas

---

## Estructura del proyecto

```
DistarArt/
├── index.php                          # Punto de entrada de la aplicación
├── aplicacion/
│   ├── config/
│   │   └── config.php                 # Configuración: BD, rutas, ACL, sesión
│   ├── controladores/
│   │   ├── inicialControlador.php     # Página de inicio y galería pública
│   │   ├── obrasControlador.php       # CRUD de obras artísticas
│   │   ├── encargosControlador.php    # Gestión de encargos y pedidos
│   │   ├── usuariosControlador.php    # CRUD de usuarios y perfiles
│   │   ├── clienteAPIControlador.php  # Interfaz web para clientes API
│   │   ├── apiControlador.php         # API REST (endpoints JSON)
│   │   └── registroControlador.php    # Login y registro de nuevos usuarios
│   ├── modelos/
│   │   ├── Obras.php                  # Modelo de obras artísticas
│   │   ├── Encargos.php               # Modelo de encargos
│   │   ├── Usuario.php                # Modelo de usuarios
│   │   ├── Galeria.php                # Vista de datos de obras (JOIN)
│   │   ├── Categorias.php             # Categorías de obras
│   │   ├── ObrasFavoritas.php         # Likes / obras guardadas
│   │   ├── DatosRegistro.php          # Modelo de formulario de registro
│   │   └── Login.php                  # Modelo de autenticación
│   ├── vistas/
│   │   ├── plantillas/
│   │   │   └── main.php               # Layout HTML principal (header, nav, footer)
│   │   ├── inicial/                   # Galería pública e inspiración
│   │   ├── obras/                     # Vistas CRUD de obras
│   │   ├── encargos/                  # Vistas de encargos
│   │   ├── usuarios/                  # Vistas de usuarios y perfiles
│   │   ├── clienteAPI/                # Vistas del cliente API
│   │   └── registro/                  # Login y formulario de registro
│   └── auxiliares/
│       └── pdf.php                    # Clase para generación de PDFs personalizados
├── framework/
│   ├── Sistema.php                    # Núcleo del framework (autoload, bootstrap)
│   └── clases/
│       ├── mvc/
│       │   ├── CControlador.php       # Controlador base
│       │   └── CActiveRecord.php      # ORM propio (Active Record)
│       ├── bd/
│       │   ├── CBaseDatos.php         # Conexión a base de datos
│       │   └── CCommand.php           # Ejecución de consultas
│       ├── acceso/
│       │   ├── CAcceso.php            # Gestión de acceso y sesión
│       │   ├── CACLBase.php           # ACL base abstracta
│       │   └── CACLBD.php             # ACL implementada en BD
│       ├── forms/
│       │   └── CHTML.php              # Generador de HTML
│       ├── widget/
│       │   ├── CGrid.php              # Widget de tabla/grid
│       │   ├── CPager.php             # Widget de paginación
│       │   ├── CCaja.php              # Widget contenedor
│       │   └── CWidget.php            # Base de widgets
│       ├── base/
│       │   ├── CAplicacion.php        # Ciclo de vida de la aplicación
│       │   └── CSesion.php            # Manejo de sesiones PHP
│       └── general/
│           ├── CGeneral.php           # Utilidades generales
│           └── CValidaciones.php      # Validaciones de atributos
├── scripts/
│   ├── librerias/
│   │   └── peticionesCURL.php         # Funciones cURL (GET, POST, PUT, DELETE)
│   └── TCPDF/                         # Librería TCPDF para PDF
├── estilos/
│   └── principal.css                  # Estilos CSS de la aplicación
└── imagenes/                          # Imágenes: obras, perfiles, banners, iconos
```

---

## Sistema de permisos

El control de acceso está basado en **roles con permisos numerados** almacenados en base de datos:

| Permiso | Acceso |
|---|---|
| **7** | Artista — puede gestionar sus propias obras y ver sus clientes |
| **8** | Artista con encargos — puede recibir y gestionar encargos propios |
| **9** | Administrador de contenido — gestión global de obras, clientes y encargos |
| **10** | Superadministrador — gestión completa de usuarios |

---

## API REST

La aplicación expone una API REST interna para la gestión de clientes, accesible desde el controlador `api`:

```
GET    /index.php/api/clientes              → Lista todos los clientes (con filtros y paginación)
GET    /index.php/api/clientes?cod_cliente= → Obtiene un cliente específico
POST   /index.php/api/clientes              → Crea un nuevo cliente
PUT    /index.php/api/clientes              → Modifica un cliente existente
DELETE /index.php/api/clientes              → Elimina un cliente
```

Todas las respuestas son en formato **JSON**. Requiere autenticación activa (sesión iniciada).

---

## Instalación y puesta en marcha

### Requisitos

- PHP 8.0 o superior
- MySQL 8.x (o MariaDB compatible)
- Apache con `mod_rewrite` activado (para URLs amigables)
- Extensión `curl` de PHP habilitada

### Pasos

**1. Clonar el repositorio**
```bash
git clone https://github.com/DianaRL19/DistarArt-PHP.git
cd DistarArt-PHP
```

**2. Importar la base de datos**
```bash
mysql -u tu_usuario -p < distarart.sql
```

**3. Configurar la conexión**

Editar `aplicacion/config/config.php` con los datos de tu entorno:

```php
"BD" => array(
    "hay"        => true,
    "servidor"   => "localhost:3306",   // ajusta el puerto si es necesario
    "usuario"    => "tu_usuario",
    "contra"     => "tu_contraseña",
    "basedatos"  => "distarart"
),
```

**4. Configurar el servidor web**

Asegúrate de que el `DocumentRoot` apunta a la carpeta del proyecto y que `AllowOverride All` está activo para que funcionen las URLs amigables.

**5. Acceder a la aplicación**
```
http://localhost/
```

---

## Framework Pedrosa MVC 

DistarArt no usa ningún framework externo. El proyecto incluye un **mini-framework MVC** desarrollado por un profesor de desarrollo en entorno servidor con las siguientes características:

- **Autoload de clases** basado en convención de nombres
- **Enrutado automático** por URL: `/controlador/accion/param`
- **URLs amigables** configurables
- **Active Record** propio para mapeo objeto-relacional
- **Sistema de widgets** reutilizables (grid, paginador, caja)
- **ACL en base de datos** con roles y permisos booleanos
- **Generador HTML** (`CHTML`) para construir interfaces desde PHP

---

## Autora

**Diana Romero** · Proyecto de 2.º DAW

---

<div align="center">
  <sub>Hecho con ☕ y PHP puro · DistarArt 2026</sub>
</div>
