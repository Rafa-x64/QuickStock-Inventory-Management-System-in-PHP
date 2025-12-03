---
trigger: always_on
---

Estándar de Rutas del Proyecto QuickStock
Contexto del Proyecto
El proyecto QuickStock utiliza una arquitectura híbrida que combina:

MVC tradicional (Modelo-Vista-Controlador) con flujo centralizado a través de 
src/index.php
Cliente-Servidor con endpoints API independientes en la carpeta api/
Esta dualidad requiere dos estrategias de rutas diferentes según el contexto de ejecución.

Regla 1: Rutas Relativas desde 
src/index.php
 (MVC)
Ámbito de Aplicación
Esta regla aplica a todos los archivos que forman parte del flujo MVC principal:

Modelos (src/model/)
Controladores (src/controller/)
Vistas (src/view/html/)
Cualquier archivo incluido directamente o indirectamente desde src/index.php
Principio Fundamental
Todas las rutas deben ser relativas tomando como punto de referencia ÚNICO y ESTRICTO el archivo src/index.php.

Implementación
✅ Correcto:
php
// Desde src/index.php
include_once("model/core.sucursal.php");
include_once("controller/sucursales_añadir_C.php");
include_once("view/html/sucursales-listado-view.php");

// Desde src/controller/sucursales_añadir_C.php
include_once("model/core.sucursal.php");  // Relativo a index.php, NO al controlador

// Desde src/view/html/empleados-listado-view.php
include_once("controller/empleados_listado_C.php");  // Relativo a index.php
❌ Incorrecto:
php
// NO usar __DIR__
include_once(__DIR__ . "/../model/core.sucursal.php");

// NO usar rutas absolutas del sistema
include_once("/xampp/htdocs/DEV/PHP/QuickStock/src/model/core.sucursal.php");

// NO usar rutas relativas al archivo actual (fuera de index.php)
include_once("../model/core.sucursal.php");  // Desde un controlador
Restricción Crítica
🚫 PROHIBIDO usar __DIR__, __FILE__, o rutas absolutas del sistema de archivos en archivos MVC.

Justificación
Mantiene consistencia en todo el proyecto
Facilita el mantenimiento y debugging
Evita errores de inclusión cuando los archivos se mueven
Simplifica la comprensión del flujo de ejecución
Regla 2: Rutas Absolutas con __DIR__ (API Endpoints)
Ámbito de Aplicación
Esta regla aplica EXCLUSIVAMENTE a archivos dentro de la carpeta api/:

Endpoints del servidor (api/server/)
Scripts de cliente (api/client/)
Archivos de configuración accedidos desde la API
Principio Fundamental
Los endpoints API se ejecutan en un contexto independiente del flujo MVC, por lo tanto DEBEN usar rutas absolutas del sistema de archivos construidas con __DIR__ o __FILE__.

Implementación
✅ Correcto:
php
// Desde api/server/index.php
include_once(__DIR__ . "/index.functions.php");
include_once(__DIR__ . "/seguridad_acceso/usuario.php");
include_once(__DIR__ . "/../../config/conexion.php");

// Desde api/server/core/sucursal.php
require_once(__DIR__ . "/../index.functions.php");
❌ Incorrecto:
php
// NO usar rutas relativas a src/index.php desde API
include_once("api/server/index.functions.php");  // Fallará en contexto API

// NO omitir __DIR__
include_once("index.functions.php");  // Ambiguo y propenso a errores
Justificación
Los endpoints API se invocan directamente vía HTTP (no pasan por src/index.php)
El directorio de trabajo puede variar según la configuración del servidor
__DIR__ garantiza rutas absolutas confiables independientes del contexto de ejecución
Regla 3: Rutas en JavaScript (Cliente)
Ámbito de Aplicación
Scripts JavaScript que realizan peticiones a endpoints API:

api/client/*.js
view/js/*.js
Principio Fundamental
Usar rutas URL relativas o absolutas basadas en la estructura HTTP del proyecto, NO rutas del sistema de archivos.

Implementación
✅ Correcto:
javascript
// Importación de módulos (ruta URL absoluta desde la raíz del proyecto)
import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// Peticiones a endpoints (rutas relativas al dominio)
fetch('/DEV/PHP/QuickStock/src/api/server/index.php', {
    method: 'POST',
    body: JSON.stringify({ accion: 'obtener_sucursales' })
});
❌ Incorrecto:
javascript
// NO usar rutas del sistema de archivos
import { api } from "C:/xampp/htdocs/DEV/PHP/QuickStock/src/api/client/index.js";

// NO usar rutas relativas ambiguas
import { api } from "../api/client/index.js";
Resumen Visual
┌─────────────────────────────────────────────────────────────┐
│                    PROYECTO QUICKSTOCK                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  src/index.php (PUNTO DE REFERENCIA ÚNICO PARA MVC)         │
│      │                                                       │
│      ├─ model/           → Rutas relativas a index.php      │
│      ├─ controller/      → Rutas relativas a index.php      │
│      ├─ view/html/       → Rutas relativas a index.php      │
│      │                                                       │
│  api/ (CONTEXTO INDEPENDIENTE)                              │
│      │                                                       │
│      ├─ server/          → Rutas absolutas con __DIR__      │
│      │   └─ index.php    → include_once(__DIR__ . "/...")   │
│      │                                                       │
│      └─ client/          → Rutas URL (HTTP)                 │
│          └─ *.js         → import { } from "/DEV/PHP/..."   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
Checklist de Validación
Antes de crear o modificar archivos, verifica:

 ¿El archivo está en src/model/, src/controller/, o src/view/?
→ Usa rutas relativas a src/index.php sin __DIR__
 ¿El archivo está en api/server/?
→ Usa rutas absolutas con __DIR__ o __FILE__
 ¿Es un archivo JavaScript que importa módulos o hace peticiones?
→ Usa rutas URL (HTTP) relativas o absolutas
 ¿Estás incluyendo un archivo de configuración desde la API?
→ Usa __DIR__ para construir la ruta absoluta
Ejemplos Prácticos Completos
Ejemplo 1: Controlador MVC
php
<?php
// src/controller/sucursales_añadir_C.php

// ✅ Correcto: Ruta relativa a src/index.php
include_once "model/core.sucursal.php";

class sucursales_añadir_C extends mainModel {
    public static function agregarSucursal($formulario) {
        // Lógica del controlador
    }
}
?>
Ejemplo 2: Endpoint API
php
<?php
// api/server/core/sucursal.php

// ✅ Correcto: Ruta absoluta con __DIR__
require_once(__DIR__ . "/../index.functions.php");

function obtenerSucursales() {
    $conn = conectar_base_datos();
    // Lógica del endpoint
}
?>
Ejemplo 3: JavaScript Cliente
javascript
// api/client/sucursales-listado.js

// ✅ Correcto: Ruta URL absoluta
import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    api({ accion: "obtener_sucursales" })
        .then(res => console.log(res))
        .catch(err => console.error(err));
});

Este estándar debe ser seguido ESTRICTAMENTE en todas las modificaciones y creaciones de archivos para mantener la coherencia y evitar errores de rutas.