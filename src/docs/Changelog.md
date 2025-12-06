# Changelog

Este archivo documenta los cambios y avances realizados en el desarrollo del sistema **QuickStock**. Actualmente se encuentra en fase de construcción de vistas, con enfoque en diseño responsivo, modularidad y cumplimiento del modelo entidad-relación (MER).

---

---

---

## [v0.3.0] - Implementación de Roles y Mejoras en UX

**Fecha:** 2025-12-06
**Estado:** Completado

### Añadido

- **Sistema de Roles (RBAC) Completo**:

  - Implementación de menús laterales específicos para: Encargado, Cajero, Vendedor, Depositario y Administrador.
  - Creación de `dashboard-empleado-view.php` simplificado para roles no gerenciales.
  - Lógica de redirección y carga de menús dinámicos en `plantilla.php`.

- **Mejoras en Gestión de Empleados**:
  - Opción de "Sucursal: Ninguna" para Administradores.
  - Validación dinámica en frontend para hacer opcional la sucursal solo si el rol es Admin.
  - Corrección en la población del select de roles (excluyendo "Gerente" en edición).

### Corregido

- **Edición de Productos**:
  - Solucionado bug donde el estado "Activo/Inactivo" siempre se mostraba como Inactivo debido a una conversión de tipos incorrecta de PostgreSQL a PHP (`t` vs `true`).
  - Corrección en la carga de valores booleanos desde la API.

---

## [v0.2.0] - Módulo de Reportes y Actualización de Dependencias

**Fecha:** 2025-12-01  
**Estado:** Completado

### Añadido

- **Módulo de Reportes Completo**:
  - Vista unificada de generación de reportes (`prueba-reporte-view.php`)
  - Controlador de reportes (`reportes_C.php`) con soporte para 4 tipos de reportes:
    - Reporte de Ventas
    - Reporte de Inventario
    - Reporte de Rotación de Productos
    - Reporte Financiero
  - Generación de reportes en formato HTML para visualización en pantalla
  - Exportación de reportes a PDF con diseño profesional
  - Filtrado por rango de fechas y sucursal (basado en sesión del usuario)

### Actualizado

- **Dependencias del Proyecto**:
  - Actualización de mPDF de `v6.1.3` a `v8.2.7` para compatibilidad con PHP 8.2
  - Instalación de dependencias adicionales requeridas por mPDF 8:
    - `psr/log: 3.0.2`
    - `psr/http-message: 2.0`
    - `paragonie/random_compat: v9.99.100`
    - `myclabs/deep-copy: 1.13.4`
    - `mpdf/psr-log-aware-trait: v3.0.0`
    - `mpdf/psr-http-message-shim: v2.0.1`
  - Actualización de `setasign/fpdi` de `1.6.2` a `v2.6.4`

### Corregido

- **Generación de PDF**:
  - Solucionado error "Data has already been sent to output" al generar PDFs
  - Implementada verificación temprana de solicitudes PDF en `plantilla.php` antes de enviar cualquier HTML
  - Añadido `ob_end_clean()` en `generarPDF()` para limpiar buffers de salida
  - Corrección de sintaxis de curly braces (`{}`) incompatible con PHP 8.0+

### Modificado

- **Arquitectura de Plantilla**:
  - `plantilla.php` ahora verifica solicitudes de PDF antes de incluir menús laterales
  - Flujo de generación de PDF optimizado para evitar conflictos con output buffering
  - Añadida entrada `prueba-reporte-view.php` al array `$paginas_existentes`

### Técnico

- **Namespace mPDF**: Migración de clase global `\mPDF` a namespace `\Mpdf\Mpdf`
- **Manejo de Excepciones**: Actualizado de `\Exception` genérica a `\Mpdf\MpdfException`
- **Configuración de mPDF**: Cambio de sintaxis de constructor de string a array asociativo

---

## [v0.1.0] - Estructura inicial y vistas base

**Fecha:** 2025-10-25  
**Estado:** En desarrollo

### Añadido

- Estructura de carpetas bajo patrón MVC:
  - `controller/`, `model/`, `view/`, `assets/`, `config/`, `shared/`, `docs/`
- Sistema de vistas con archivos `.php` organizados por módulo funcional:
  - Inventario, compras, clientes, empleados, proveedores, punto de venta, sesión de usuario
- Plantilla base (`plantilla.php`) para renderizado visual
- Archivos `.htaccess` e `index.php` configurados para flujo MVC

### Implementado en vistas

- Carruseles visuales para presentación de contenido destacado
- Formularios tipo wizard (por pasos) para procesos como registro, compras y gestión de productos
- Diseño responsivo compatible con Bootstrap y adaptable a múltiples resoluciones
- Validación estructural de formularios conforme al modelo entidad-relación (MER)
- Separación modular de componentes visuales (`<boton-accion>`, menús, cabeceras, pie de página)
- Inclusión de Web Components registrados globalmente
- Integración de iconografía SVG y logotipos en variantes estándar y circular

### Documentación

- [Manual del desarrollador](src/docs/manual.desarrollador.md)
- [Estructura técnica del proyecto](src/docs/estructura_proyecto.md)
- Diagramas del sistema actual y propuesto:
  - [current_system](src/docs/diagrams/current_system/)
  - [proposed_system](src/docs/diagrams/proposed_system/)
- [Registro de tareas](src/docs/todo.md)
- [Licencia de uso](src/docs/LICENSE.md)

---

## Próximos pasos

- Implementar lógica de controladores y modelos
- Adaptar conexión a base de datos (MySQL/PostgreSQL)
- Integrar validaciones dinámicas y seguridad en formularios
- Desarrollar flujo de autenticación y roles de usuario
- Extender documentación técnica y diagramas UML
