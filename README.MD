# QuickStock – Sistema de Gestión de Inventario en PHP (MVC)

QuickStock es una aplicación web modular desarrollada bajo el patrón **Modelo-Vista-Controlador (MVC)**, diseñada para facilitar la gestión eficiente de inventario en entornos escalables, seguros y mantenibles. El sistema permite separar la lógica de negocio, la presentación y el control de flujo, garantizando una arquitectura clara, reutilizable y preparada para evolución funcional.

---

## Tareas

Para ver la **Estructura Completa de Tareas y Pendientes**, visita nuestro [Tablero Kanban aquí ➡️](.kanbn/index.md).

---

## Inicio rápido

Este repositorio corresponde al sistema QuickStock, una aplicación web modular desarrollada en PHP bajo el patrón MVC. Su estructura está diseñada para facilitar la mantenibilidad, escalabilidad y claridad técnica en entornos de desarrollo profesional.

Para conocer el flujo de trabajo, convenciones internas y recomendaciones de implementación, consulta el [manual del desarrollador](/src/docs/manual_desarrollador.md).

---

## Características Principales

QuickStock ofrece funcionalidades completas para la gestión de inventario y ventas:

### 📊 Módulo de Reportes

- **Generación de Reportes Dinámicos**: Ventas, Inventario, Rotación de Productos y Financiero
- **Exportación a PDF**: Reportes profesionales con mPDF 8.2
- **Filtrado Avanzado**: Por rango de fechas y sucursal
- **Visualización HTML**: Vista previa en pantalla antes de exportar

### 🏪 Gestión de Inventario

- Control de stock por sucursal
- Categorización de productos
- Alertas de stock mínimo
- Historial de movimientos

### 💰 Punto de Venta

- Registro de ventas rápido
- Múltiples métodos de pago
- Gestión de clientes
- Facturación automática

### 👥 Gestión de Personal

- Control de empleados por sucursal
- Roles y permisos
- Historial de actividades

---

## Tecnologías Principales

El proyecto está construido con un stack moderno y enfocado en la seguridad y la modularidad.

| Componente     | Versión / Propósito                   |
| :------------- | :------------------------------------ |
| **PHP**        | 8.0+ (POO, modularidad)               |
| **PostgreSQL** | 12+ (Base de datos relacional)        |
| **Composer**   | Gestión de dependencias y autoloading |
| **Bootstrap**  | 5 (Estilos y diseño responsivo)       |
| **mPDF**       | 8.2+ (Generación de PDFs)             |
| **Apache**     | Reescritura de URLs con `.htaccess`   |
| **OpenSSL**    | Encriptación de datos sensibles       |

---

## 🚀 Instalación y Configuración

Sigue estos pasos detallados para desplegar el proyecto en tu entorno local.

### Prerrequisitos

- **XAMPP / LAMP / WAMP**: Servidor web Apache y PHP 8.0+.
- **PostgreSQL 12+**: Motor de base de datos.
- **Composer**: Gestor de dependencias de PHP.
- **Git**: Control de versiones.

---

### 1. Preparación del Entorno (Windows/XAMPP)

1.  Navega a la carpeta pública de tu servidor web (usualmente `htdocs` en XAMPP).
2.  Crea la estructura de carpetas recomendada para mantener el orden:

```powershell
cd C:\xampp\htdocs
mkdir DEV\PHP
cd DEV\PHP
```

---

### 2. Clonar el Proyecto

Clona el repositorio dentro de la carpeta creada:

```powershell
git clone https://ruta-a-tu-repo/QuickStock.git
cd QuickStock
```

---

### 3. Gestión de Dependencias (Composer)

QuickStock utiliza **Composer** para gestionar librerías críticas como `mPDF` (generación de reportes) y para el autocarga de clases (autoloading).

**Es obligatorio instalar las dependencias antes de ejecutar el proyecto:**

1.  Abre una terminal en la raíz del proyecto (`.../QuickStock/`).
2.  Ejecuta el comando de instalación:

```bash
composer install
```

> **¿Qué hace esto?**
>
> - Descarga las librerías definidas en `composer.json` a la carpeta `vendor/`.
> - Genera el archivo `vendor/autoload.php`, necesario para que PHP encuentre las clases del proyecto automáticamente. sin esto, el sistema arrojará errores de "Class not found".

---

### 4. Configuración de Base de Datos (PostgreSQL)

El sistema utiliza PostgreSQL. Debes crear la base de datos e importar el esquema inicial.

#### Paso A: Crear la Base de Datos

Puedes usar **pgAdmin 4** o la línea de comandos (`psql`).

**Opción Terminal (psql):**

```bash
# Inicia sesión como superusuario (generalmente 'postgres')
psql -U postgres
```

```sql
-- Dentro de la consola psql:
CREATE DATABASE "QuickStock";
\q
```

#### Paso B: Importar el Esquema

El archivo SQL se encuentra en `src/config/quickstock.sql`.

```powershell
# Desde PowerShell / Terminal en la raíz del proyecto
psql -U postgres -d QuickStock -f src/config/quickstock.sql
```

> **Nota:** Si tu usuario `postgres` tiene contraseña, el sistema te la solicitará.

#### Paso C: Configurar la Conexión (SERVER.php)

Debes decirle a la aplicación cómo conectarse a tu base de datos local.

1.  Abre el archivo `src/config/SERVER.php`.
2.  Localiza la definición de conexión y ajusta los valores (host, puerto, dbname, user, password).

```php
// Ejemplo de configuración típica en local
const DB_SERVER = "localhost";
const DB_NAME = "QuickStock";
const DB_USER = "postgres";
const DB_PASS = "tu_contraseña_aqui"; // Déjalo vacío "" si no tienes contraseña
```

---

### 5. Verificar Servidor Web (Apache)

Para que el enrutamiento amigable (URLs limpias) funcione, Apache debe tener habilitado el módulo `mod_rewrite` y permitir la lectura del archivo `.htaccess`.

- En XAMPP, asegúrate de iniciar el servicio **Apache**.
- Accede al proyecto desde tu navegador:
  `http://localhost/DEV/PHP/QuickStock/src/`

---

## Estructura del Proyecto (MVC)

El núcleo del sistema sigue el patrón **Modelo-Vista-Controlador (MVC)**, garantizando la separación de responsabilidades:

| Carpeta       | Rol dentro del MVC    | Descripción                                                                  |
| :------------ | :-------------------- | :--------------------------------------------------------------------------- |
| `controller/` | **Controlador (C)**   | Gestiona el flujo de la aplicación e invoca las vistas según la URL.         |
| `model/`      | **Modelo (M)**        | Lógica de negocio, conexión a la base de datos, encriptación y validaciones. |
| `view/`       | **Vista (V)**         | Archivos de presentación: HTML, CSS, JS y plantilla base.                    |
| `assets/`     | **Recursos visuales** | Bootstrap, componentes, imágenes, iconos y elementos PHP reutilizables.      |
| `shared/`     | **Lógica compartida** | Traits PHP reutilizables entre modelos y controladores.                      |
| `config/`     | **Configuración**     | Constantes globales, credenciales y script de base de datos.                 |
| `docs/`       | **Documentación**     | Manuales técnicos, changelog, estructura y diagramas del sistema.            |

### Referencia a estructura del proyecto

> [Estructura del proyecto](src/docs/estructura_proyecto.md)

---

## 🏗️ Arquitectura Híbrida (MVC + Client-Server)

QuickStock implementa dos patrones de arquitectura que conviven para ofrecer una experiencia moderna y robusta:

### 1. MVC Tradicional (Navegación)
Utilizado para la carga de vistas, gestión de sesiones y renderizado de plantillas HTML.
*   **Front Controller**: `src/index.php` gestiona todas las rutas amigables.
*   **Vistas**: Renderizadas desde el servidor con PHP.

### 2. Cliente-Servidor (API RESTful)
Utilizado para la interactividad dinámica sin recargas de página (AJAX/Fetch).

*   **Cliente (Frontend)**: Archivos JS en `src/api/client/` consumen la API.
*   **Servidor (Backend)**: Endpoints en `src/api/server/index.php` procesan solicitudes JSON y responden datos estructurados.

**Flujo de Datos:**
`Vista (JS)` ➡️ `Fetch (JSON)` ➡️ `API (PHP)` ➡️ `Base de Datos` ➡️ `Respuesta JSON` ➡️ `DOM Update`

---

## Contribución

**¡Las contribuciones son bienvenidas!** Para colaborar en el desarrollo:

1.  Clona el repositorio y crea una rama a partir de `master`:

```bash
git checkout -b feature/nombre-funcionalidad
```

2.  Sigue las **Convenciones de Desarrollo**.
3.  Asegúrate de que tus cambios pasen las pruebas.
4.  Realiza un `commit` descriptivo y abre un **Pull Request (PR)**.

```bash
git add .
git commit -m "commit descriptivo"
```

5. Borrar la rama que creaste luego de hacer pull request con:

```bash
git branch -D feature/nombre-funcionalidad
```

### Referencia a Guia de contribucion

> [Contributing.md](src/docs/Contributing.md)

---

## Seguridad

La seguridad es una prioridad. Por favor, asegúrate de:

- Usar siempre **consultas preparadas (PDO)** para interactuar con la base de datos.
- **Hashear contraseñas** con `password_hash`.
- **No modificar** archivos críticos como `config/SERVER` o `.htaccess`.

---

## Licencia

Este proyecto está bajo la Licencia **MIT** - ver el archivo [LICENSE.md](src/docs/LICENSE.MD) para más detalles.
