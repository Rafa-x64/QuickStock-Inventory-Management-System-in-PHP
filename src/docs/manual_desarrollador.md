<!-- Manual de desarrollador completo para QuickStock -->

# Manual de Desarrollador — QuickStock

> Versión: 1.0
> Ruta base del proyecto: `c:\xampp\htdocs\DEV\PHP\QuickStock\src`

**Índice**

- [Manual de Desarrollador — QuickStock](#manual-de-desarrollador--quickstock)
  - [1. Introducción general](#1-introducción-general)
  - [2. Arquitectura del sistema](#2-arquitectura-del-sistema)
  - [3. Entorno de desarrollo](#3-entorno-de-desarrollo)
  - [4. Estructura del proyecto](#4-estructura-del-proyecto)
  - [5. Flujo completo: vistas, controlador y plantilla](#5-flujo-completo-vistas-controlador-y-plantilla)
  - [6. Peticiones Front → API (api/client → api/server/index.php)](#6-peticiones-front--api-apiclient--apiserverindexphp)
  - [7. Consultas a la base de datos desde `api/server` y formato de respuesta](#7-consultas-a-la-base-de-datos-desde-apiserver-y-formato-de-respuesta)
  - [8. Validaciones: `view/js/*.js` y validación backend](#8-validaciones-viewjsjs-y-validación-backend)
  - [9. Cómo crear un Modelo, una Vista y un Controlador (MVC local)](#9-cómo-crear-un-modelo-una-vista-y-un-controlador-mvc-local)
  - [10. Modificar/Importar la base de datos (pgAdmin4)](#10-modificarimportar-la-base-de-datos-pgadmin4)
  - [11. Módulo de Reportes y Generación de PDF](#11-módulo-de-reportes-y-generación-de-pdf)
  - [12. Gestión de Roles y Accesos (RBAC)](#12-gestión-de-roles-y-accesos-rbac)
  - [13. Dependencias y Gestión con Composer](#13-dependencias-y-gestión-con-composer)
  - [14. Buenas prácticas y checklist de despliegue](#14-buenas-prácticas-y-checklist-de-despliegue)
  - [15. Anexos: ejemplos de código y comandos útiles](#15-anexos-ejemplos-de-código-y-comandos-útiles)

---

## 1. Introducción general

Bienvenido al **Manual de Desarrollador de QuickStock**, la guía técnica completa para desarrolladores que trabajarán en este sistema de gestión de inventario y ventas para zapaterías.

### 1.1 Propósito de este Manual

Este manual está diseñado para:

- **Nuevos desarrolladores**: Entender rápidamente la arquitectura y convenciones del proyecto
- **Desarrolladores experimentados**: Consultar patrones específicos y mejores prácticas
- **Mantenimiento**: Documentar decisiones técnicas y flujos críticos

### 1.2 Audiencia

Desarrolladores con conocimientos en:

- PHP 8+ (orientado a objetos)
- JavaScript (ES6+)
- PostgreSQL
- Arquitectura MVC
- APIs REST/JSON

### 1.3 Arquitectura Dual: MVC + Client-Server

QuickStock implementa **dos arquitecturas complementarias**:

#### 🏗️ **Arquitectura MVC (Model-View-Controller)**

**Punto de entrada**: `index.php` (front controller)

**Flujo de navegación**:

```
Usuario solicita: http://localhost/QuickStock/src/?page=inventario-ver-productos
    ↓
index.php recibe la petición
    ↓
vista_controller.php procesa $_GET['page']
    ↓
Sanitiza y valida el nombre de la página
    ↓
Busca: view/html/inventario-ver-productos-view.php
    ↓
Si existe → Carga plantilla.php con la vista
Si no existe → Carga 404-view.php
```

**Características clave**:

- **Enrutamiento centralizado**: Todas las páginas pasan por `index.php`
- **Convención de nombres**: Todas las vistas terminan en `-view.php`
- **Plantilla unificada**: `plantilla.php` incluye header, menú lateral y footer
- **Seguridad**: Sanitización con `basename()` para prevenir path traversal

**Ejemplo de URL**:

```
http://localhost/QuickStock/src/?page=empleados-listado
                                      ↑
                                Nombre de la vista (sin -view.php)
```

#### 🌐 **Arquitectura Client-Server (API JSON)**

**Punto de entrada**: `api/server/index.php`

**Flujo de peticiones asíncronas**:

```
JavaScript en el navegador (api/client/*.js)
    ↓
fetch() o api() helper con JSON: {accion: "obtener_productos", ...params}
    ↓
POST → api/server/index.php
    ↓
json_decode() extrae la acción y parámetros
    ↓
switch($accion) → Incluye archivo específico (ej: inventario/producto.php)
    ↓
Función ejecuta consulta a BD
    ↓
Retorna JSON: {status: "success", data: [...]}
    ↓
JavaScript procesa respuesta y actualiza DOM
```

**Características clave**:

- **Peticiones asíncronas**: No recarga la página
- **Formato JSON**: Comunicación estructurada
- **Modular**: Cada módulo tiene su carpeta en `api/server/`
- **Reutilizable**: Misma API para múltiples vistas

**Ejemplo de petición**:

```javascript
import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

api({ accion: "obtener_productos", categoria: "zapatos" })
  .then((res) => console.log(res.data))
  .catch((err) => console.error(err));
```

#### 🔀 **¿Cuándo usar cada arquitectura?**

| Escenario                                      | Arquitectura                    | Razón                             |
| ---------------------------------------------- | ------------------------------- | --------------------------------- |
| Cargar una página completa                     | MVC (`?page=...`)               | Necesita header, menú, footer     |
| Enviar formulario de creación/edición          | MVC (POST a controlador)        | Validación backend y redirección  |
| Filtrar tabla sin recargar                     | Client-Server (API)             | Mejor UX, más rápido              |
| Cargar datos dinámicos (selects, autocomplete) | Client-Server (API)             | Datos bajo demanda                |
| Generar PDF                                    | MVC (verificación en plantilla) | Requiere control total del output |

### 1.4 Estructura de Documentación

📚 **Documentos relacionados**:

- [README.md](../../README.MD) - Descripción general del proyecto
- [Changelog.md](Changelog.md) - Historial de versiones y cambios
- [estructura_proyecto.md](estructura_proyecto.md) - Organización de carpetas y archivos
- [Contributing.md](Contributing.md) - Guía de contribución
- [LICENSE.MD](LICENSE.MD) - Licencia del proyecto

> **💡 Consejo**: Mantén este manual abierto mientras desarrollas. Úsalo como referencia rápida para patrones y convenciones.

> **⚠️ Importante**: Antes de hacer cambios significativos, lee las secciones relevantes para entender el impacto en el sistema.

---

## 2. Arquitectura del sistema

- Monolito con organización estilo MVC: `controller/`, `model/`, `view/`.
- Punto de entrada frontal: `index.php` (instancia `vista_controller`).
- API JSON centralizado en `api/server/index.php` (recibe JSON con `accion` y parámetros).
- Persistencia: PostgreSQL accedido por funciones `pg_*` desde clases en `model/` o funciones en `api/server/*`.

> **💡 Consejo**: Revisa [estructura_proyecto.md](estructura_proyecto.md) para un mapa visual completo de carpetas.

> **📌 Nota**: La arquitectura dual (MVC + API) permite flexibilidad: usa MVC para páginas completas y API para operaciones dinámicas.

---

## 3. Entorno de desarrollo

- Recomendado: PHP 8+, PostgreSQL 12+, Apache (XAMPP) en Windows.
- Variables sensibles en `.env` (usar `.env.example` como plantilla).
- Conexión a BD centralizada en `config/SERVER.php` (constante `PostgreSQL`) o adaptar para usar dotenv.

Pasos rápidos (PowerShell):

```powershell
# Crear base de datos y cargar dump (si psql está en PATH)
psql -U postgres -d postgres -c "CREATE DATABASE \"QuickStock\";"
psql -U postgres -d QuickStock -f "C:/xampp/htdocs/DEV/PHP/QuickStock/src/config/quickstock.sql"
```

---

## 4. Estructura del proyecto

- `index.php` — front controller que instancia `vista_controller`.
- `controller/` — controladores de vistas (p.ej. `empleados_listado_C.php`).
- `model/` — clases y funciones para acceder a la BD (extienden `mainModel`).
- `view/` — plantillas y vistas (`plantilla.php`, `html/*.php`, `js/*`).
- `api/client/` — scripts JS que realizan peticiones a `api/server/index.php`.
- `api/server/` — funciones PHP que atienden las acciones y devuelven arrays/JSON.
- `config/` — scripts de BD y constantes (ej. `SERVER.php`).

**Convención importante**: todas las vistas públicas terminan en `-view.php` (ej. `inventario-ver-productos-view.php`).

> **⚠️ Importante**: Nunca modifiques archivos en `vendor/` directamente. Usa Composer para gestionar dependencias.

> **💡 Tip**: Usa la búsqueda de VS Code (Ctrl+P) para encontrar archivos rápidamente: `empleados-listado-view.php`

**📂 Ver estructura completa**: [estructura_proyecto.md](estructura_proyecto.md)

---

## 5. Flujo completo: vistas, controlador y plantilla

Descripción paso a paso del flujo principal cuando se carga una página:

1. El usuario solicita una URL como `http://.../src/?page=inventario-ver-productos`.
2. `index.php` instancia `vista_controller`.
3. `vista_controller` toma `$_GET['page']` y decide la vista a cargar:

   - Normaliza el nombre (seguridad: sanitize el string).
   - Comprueba si existe un archivo en `view/html/<page>-view.php`.
   - Si existe, delega a `vista_model` para cargar la `plantilla.php` con la vista.
   - Si no existe, carga `view/html/404-view.php`.

4. `vista_model` prepara cualquier dato necesario (ejecuta llamadas a modelos si la vista requiere datos) y pasa esos datos a la `plantilla.php`.
5. `plantilla.php` contiene el layout (header, footer, menú lateral) y un lugar donde incluir la vista concreta:
   - Ej. `require_once __DIR__ . '/html/' . $viewFile;`
   - `plantilla.php` también incluye `elements/header.php`, `elements/menu-lateral.php`, y `elements/footer.php` según permisos.
6. La vista `*-view.php` ejecuta su propio PHP para pintar datos provistos por `vista_model` y enlaza scripts JS específicos (`view/js/...` o `api/client/...`).

Ejemplo simplificado de `vista_controller` (pseudo-PHP):

````php
// controller/vista_controller.php
class vista_controller {
	public function cargarVista(){
		$page = $_GET['page'] ?? 'inicio-view';
		$page = basename($page); // seguridad
		$file = __DIR__ . '/../view/html/' . $page . '-view.php';
		if(file_exists($file)){
			// pasa control a model para preparar datos
			$model = new vista_model();
			$data = $model->prepararDatosPara($page);
			include __DIR__ . '/../view/plantilla.php';
		} else {
			include __DIR__ . '/../view/html/404-view.php';
		}

- El frontend usa archivos JS dentro de `api/client/` o `view/js/` para construir un objeto JSON con una propiedad `accion` y otros parámetros.
- Envío: `fetch()` o `XMLHttpRequest` hacia `api/server/index.php` con `Content-Type: application/json`.
- `api/server/index.php` ejecuta `json_decode(file_get_contents('php://input'), true)` y hace un `switch($accion)` para llamar a la función apropiada.

Ejemplo de petición desde `api/client/inventario-ver-productos.js`:

```javascript
import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// 📦 OBJETO GLOBAL PARA GUARDAR EL ESTADO DE LOS FILTROS
let filtrosActivos = {
  nombre: "",
  codigo: "",
  categoria: "",
  proveedor: "",
  sucursal: "",
  estado: "", // Valores posibles: "", "true", "false"
};

// 🔄 FUNCIÓN REUTILIZABLE PARA CARGAR PRODUCTOS APLICANDO LOS FILTROS
function cargarProductos() {
  // La función 'api' enviará 'filtrosActivos' como parámetros GET/POST al index.php
  api({
    accion: "obtener_todos_los_productos",
    ...filtrosActivos, // Despliega todos los filtros como parámetros de la petición
  })
    .then((res) => {
      const tabla = document.getElementById("tabla_productos");
      tabla.innerHTML = ""; // Limpia la tabla antes de cargar nuevos datos
      const productos = res.data || [];

      if (productos.length === 0) {
        tabla.innerHTML =
          '<tr><td colspan="11" class="text-center">No se encontraron productos con estos filtros.</td></tr>';
        return;
      }

      // Mapeo y renderizado de las filas (sin cambios en la lógica de renderizado)
      productos.forEach((prod) => {
        const estadoTexto =
          prod.estado == 1 || prod.estado === "t"
            ? '<span class="badge text-bg-success">Activo</span>'
            : '<span class="badge text-bg-danger">Inactivo</span>';

        const fila = document.createElement("tr");
        fila.innerHTML = `
                <td>${prod.codigo ?? "-"}</td>
                <td>${prod.nombre ?? "-"}</td>
                <td>${prod.categoria_nombre ?? "-"}</td>
                <td>${prod.talla ?? "-"}</td>
                <td>${prod.precio_compra ?? "-"}</td>
                <td>${prod.precio_venta ?? "-"}</td>
                <td>${prod.stock ?? 0}</td>
                <td>${prod.sucursal_nombre ?? "Sin sucursal"}</td>
                <td>${estadoTexto}</td>
                <td class="text-center">
                    <div class="container-fluid p-0">
                        <div class="row g-1">
                            <div class="col-6">
                                <form action="inventario-editar-producto" method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_producto" value="${
                                      prod.id_producto
                                    }">
                                    <input type="submit" class="btn btn-warning btn-sm w-100" value="Editar">
                                </form>
                            </div>
                            <div class="col-6">
                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_producto" value="${
                                      prod.id_producto
                                    }">
                                    <input type="submit" class="btn btn-danger btn-sm w-100" value="Eliminar">
                                </form>
                            </div>
                            <div class="col-12">
                                <form action="inventario-detalle-producto" method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="ver_detalle">
                                    <input type="hidden" name="id_producto" value="${
                                      prod.id_producto
                                    }">
                                    <input type="submit" class="btn btn-primary btn-sm w-100" value="Ver detalle">
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            `;
        tabla.appendChild(fila);
      });
    })
    .catch((error) => {
      console.error("Error al cargar productos:", error);
      document.getElementById("tabla_productos").innerHTML =
        '<tr><td colspan="11" class="text-center text-danger">Error al cargar los datos.</td></tr>';
    });
}

// 🎛️ FUNCIÓN PARA INICIALIZAR EVENTOS DE FILTRO
function inicializarFiltros() {
  // Función auxiliar para adjuntar eventos a selects e inputs
  const addEventListener = (id, eventType, filterKey) => {
    const element = document.getElementById(id);
    if (element) {
      element.addEventListener(eventType, (e) => {
        filtrosActivos[filterKey] = e.target.value.trim();
        cargarProductos();
      });
    }
  };

  // Filtros de texto (Input event para una búsqueda rápida)
  addEventListener("filtro_nombre", "input", "nombre");
  addEventListener("filtro_codigo", "input", "codigo");

  // Filtros de Select (Change event)
  addEventListener("filtro_sucursal", "change", "sucursal");
  addEventListener("filtro_categoria", "change", "categoria");
  addEventListener("filtro_proveedor", "change", "proveedor");
  addEventListener("filtro_estado", "change", "estado");

  // 🗑️ BOTÓN REESTABLECER FILTROS (ID CORREGIDO: "btn-reestablecer")
  document.getElementById("btn-reestablecer")?.addEventListener("click", () => {
    // 1. Resetear el objeto de filtros
    filtrosActivos = {
      nombre: "",
      codigo: "",
      categoria: "",
      proveedor: "",
      sucursal: "",
      estado: "",
    };

    // 2. Resetear los valores de los elementos de la vista
    document.getElementById("filtro_nombre").value = "";
    document.getElementById("filtro_codigo").value = "";

    // Asignar el valor de la opción por defecto ("") a los selects
    document.getElementById("filtro_categoria").value = "";
    document.getElementById("filtro_proveedor").value = "";
    document.getElementById("filtro_sucursal").value = "";
    document.getElementById("filtro_estado").value = "";

    // 3. Recargar productos sin filtros
    cargarProductos();
  });
}

// ⚙️ FUNCIÓN PARA CARGAR OPCIONES DINÁMICAS EN LOS SELECTS
function cargarOpcionesSelects() {
  // Función auxiliar para cargar opciones
  const cargarOpciones = (selectId, accionApi, valueKey, textKey, resKey) => {
    const select = document.getElementById(selectId);
    if (!select) return;

    api({ accion: accionApi })
      .then((res) => {
        // Manejar diferentes estructuras de respuesta (res.filas, res.categorias, res.proveedores)
        const data = res[resKey] || res.filas || [];
        data.forEach((item) => {
          const op = document.createElement("option");
          op.value = item[valueKey];
          op.textContent = item[textKey];
          select.appendChild(op);
        });
      })
      .catch((error) => {
        console.error(`Error al cargar ${selectId}:`, error);
      });
  };

  cargarOpciones(
    "filtro_sucursal",
    "obtener_sucursales",
    "id_sucursal",
    "nombre",
    "filas"
  );
  cargarOpciones(
    "filtro_categoria",
    "obtener_categorias",
    "id_categoria",
    "nombre",
    "categorias"
  );
  cargarOpciones(
    "filtro_proveedor",
    "obtener_proveedores",
    "id_proveedor",
    "nombre",
    "proveedores"
  );
}

// 🚀 CUANDO CARGA LA PÁGINA
document.addEventListener("DOMContentLoaded", () => {
  cargarOpcionesSelects(); // Llenar los selects
  inicializarFiltros(); // Configurar los listeners
  cargarProductos(); // Cargar la lista inicial de productos
});
````

Ejemplo de `api/server/index.php` (simplificado):

```php
session_start();
$peticion = json_decode(file_get_contents('php://input'), true);
$accion = $peticion['accion'] ?? null;
include_once __DIR__ . '/index.functions.php';

switch($accion) {
	case 'obtener_todos_los_productos':
		include __DIR__ . '/inventario/producto.php';
		$out = obtenerTodosLosProductos(
			$peticion['nombre'] ?? null,
			$peticion['codigo'] ?? null,
			$peticion['categoria'] ?? null,
			$peticion['proveedor'] ?? null,
			$peticion['sucursal'] ?? null,
			$peticion['estado'] ?? null
		);
		break;
	// otras acciones...
	default:
		$out = ['error' => 'Accion no reconocida'];
}

echo json_encode($out);
```

Puntos clave:

- Siempre devolver estructuras JSON consistentes: `{ status, data }` o `{ error, message }`.
- Validar y sanitizar los parámetros entrantes antes de usarlos en consultas.

---

## 7. Consultas a la base de datos desde `api/server` y formato de respuesta

Buenas prácticas ya aplicadas en el repo:

- Uso de `conectar_base_datos()` (ver `api/server/index.functions.php`) para obtener conexión `pg_connect`.
- Uso de `pg_prepare` / `pg_execute` o `pg_query_params` para evitar inyección SQL.

Ejemplo de función en `api/server/inventario/producto.php` que consulta la BD y devuelve un array:

```php
function obtenerTodosLosProductos($nombre=null, $codigo=null, $categoria=null, $proveedor=null, $sucursal=null, $estado=null){
	$conn = conectar_base_datos();
	$clauses = []; $params = []; $i = 1;
	if($nombre){ $clauses[] = "p.nombre ILIKE $".$i; $params[] = "%$nombre%"; $i++; }
	// ... construir WHERE dinámico similar al ejemplo existente ...
	$sql = "SELECT p.id_producto, p.nombre FROM inventario.producto p WHERE " . implode(' AND ', $clauses);
	$stmt = 'stmt_' . uniqid();
	pg_prepare($conn, $stmt, $sql);
	$res = pg_execute($conn, $stmt, $params);
	if(!$res) return ['status'=>'error','message'=>pg_last_error($conn)];
	$rows = pg_fetch_all($res) ?: [];
	return ['status'=>'success','data'=>$rows];
}
```

Cómo estructurar la respuesta:

- `['status'=>'success','data'=>[...] ]` en caso OK.
- `['status'=>'error','message'=>'...', 'detalle'=>'...']` en caso de error (ocultar `detalle` en producción).

---

## 8. Validaciones: `view/js/*.js` y validación backend

Estrategia de validación en dos capas:

1. Validación Frontend (`view/js/archivo.js` o `api/client/archivo.js`):
   - Validaciones UX: campos obligatorios, formatos básicos (email, número), longitudes.
   - Mostrar errores con Bootstrap: añadir `.is-invalid` al campo y llenar `.invalid-feedback`.
   - Evitar enviar peticiones inválidas al servidor (mejora UX y reduce tráfico).

Ejemplo de frontend:

```javascript
function validarFormularioProducto(form) {
  const nombre = form.querySelector('[name="nombre"]').value.trim();
  const precio = parseFloat(form.querySelector('[name="precio"]').value);
  const errores = [];
  if (!nombre) errores.push("Nombre requerido");
  if (isNaN(precio) || precio <= 0) errores.push("Precio inválido");
  return errores;
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const errores = validarFormularioProducto(e.target);
  if (errores.length) {
    mostrarErrores(errores);
    return;
  }
  // enviar petición a api/server/index.php
});
```

2. Validación Backend (obligatoria):
   - Siempre volver a validar todo en `controller`/`api/server` y en `model` antes de insertar/actualizar.
   - Comprobar tipos, rangos, unicidad y permisos del usuario.

Ejemplo en PHP (server/model):

```php
if(empty($data['nombre'])) return ['status'=>'error','message'=>'Nombre requerido'];
if(!is_numeric($data['precio']) || $data['precio'] <= 0) return ['status'=>'error','message'=>'Precio inválido'];
// luego ejecutar prepared statement
```

---

## 9. Cómo crear un Modelo, una Vista y un Controlador (MVC local)

Plantilla mínima para crear cada componente:

- Modelo: `model/nuevo_modelo.php`
  - Debe extender `mainModel` si necesita conexión compartida.
  - Implementar métodos: `crear()`, `editar()`, `eliminar()`, `buscar()`.

Ejemplo:

```php
// model/producto_model.php
class ProductoModel extends mainModel {
	public static function listarProductos($filtros = []){
		$conn = parent::conectar_base_datos();
		$sql = 'SELECT * FROM inventario.producto ORDER BY id_producto';
		$res = pg_query($conn, $sql);
		return pg_fetch_all($res) ?: [];
	}
}
```

- Controlador: `controller/producto_C.php`
  - Recibe POST/GET de la vista, valida, usa el modelo y redirige o devuelve JSON.

Ejemplo:

```php
// controller/producto_C.php
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$data = $_POST; // o json_decode(file_get_contents('php://input'), true)
	// validar
	$id = ProductoModel::crear($data);
	header('Location: ?page=inventario-ver-productos');
}
```

- Vista: `view/html/inventario-añadir-producto-view.php`
  - Archivo que contiene el HTML del formulario y enlaza el JS necesario.

Ejemplo (esqueleto):

```php
<form id="formProducto">
	<input name="nombre" />
	<input name="precio" />
	<button type="submit">Guardar</button>
</form>
<script src="/DEV/PHP/QuickStock/src/view/js/inventario-añadir-producto.js"></script>
```

Integración con `plantilla.php`:

- `plantilla.php` debe exponer una variable `$data` o similar, y hacer `require` de la vista correspondiente.

---

## 10. Modificar/Importar la base de datos (pgAdmin4)

Importar `config/quickstock.sql` en pgAdmin4:

1. Abrir pgAdmin4 y conectar al servidor PostgreSQL.
2. Crear nueva base de datos `QuickStock` (clic derecho → Create → Database).
3. Seleccionar la base de datos, ir a la pestaña "Query Tool".
4. Cargar el archivo SQL (`Open File`) o usar el comando `
\i 'C:/ruta/a/quickstock.sql'` en la consola psql.

También se puede usar `psql` desde PowerShell:

```powershell
psql -U postgres -d QuickStock -f "C:/xampp/htdocs/DEV/PHP/QuickStock/src/config/quickstock.sql"
```

Si haces cambios en el esquema:

- Mantén migraciones en `config/migrations/` y actualiza `config/quickstock.sql` con el snapshot.

---

## 11. Módulo de Reportes y Generación de PDF

### 11.1 Arquitectura del Módulo de Reportes

El módulo de reportes permite generar informes dinámicos en formato HTML y exportarlos a PDF. La arquitectura sigue el patrón MVC con una capa adicional de generación de documentos.

**Componentes principales:**

1. **Vista**: `view/html/prueba-reporte-view.php`

   - Formulario de selección de tipo de reporte y rango de fechas
   - Botones para generar reporte HTML o exportar a PDF
   - Área de visualización de resultados

2. **Controlador**: `controller/reportes_C.php`

   - Método `generarReporte($params)`: Genera reportes en HTML
   - Método `generarPDF($tipo, $inicio, $fin, $id_sucursal)`: Exporta a PDF
   - Métodos específicos: `rotacion()`, `ventas()`, `inventario()`, `financiero()`

3. **Plantilla**: `view/plantilla.php`
   - Verificación temprana de solicitudes PDF antes de enviar HTML
   - Previene el error "Data has already been sent to output"

### 11.2 Flujo de Generación de Reportes

**Flujo HTML (visualización en pantalla):**

```
Usuario → Formulario (accion=generar) → POST → reportes_C::generarReporte()
  → Consulta BD → Genera HTML → Retorna a vista → Renderiza en pantalla
```

**Flujo PDF (descarga):**

```
Usuario → Formulario (accion=imprimir_pdf) → POST → plantilla.php (verifica)
  → reportes_C::generarReporte() → reportes_C::generarPDF()
  → ob_end_clean() → mPDF genera PDF → Output('D') → Descarga archivo
```

### 11.3 Implementación de Generación de PDF

**Código clave en `plantilla.php` (líneas 99-104):**

```php
// 🔥 VERIFICAR SI ES UNA SOLICITUD DE PDF ANTES DE ENVIAR HTML
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST['accion'] ?? '') === 'imprimir_pdf') {
    include_once("controller/reportes_C.php");
    reportes_C::generarReporte($_POST);
    exit();
}
```

**¿Por qué es crítico?**

- Si se incluye el menú lateral antes de verificar, se envía HTML al navegador
- mPDF no puede generar un PDF si ya hay output buffering activo
- La verificación temprana previene este conflicto

**Código clave en `reportes_C.php`:**

```php
public static function generarPDF($tipo, $inicio, $fin, $id_sucursal)
{
    // Limpiar cualquier salida previa
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Generar contenido HTML del reporte
    $html_content = self::rotacion($inicio, $fin, $id_sucursal); // o ventas, inventario, etc.

    // Estilos CSS para el PDF
    $stylesheet = '
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #333; color: #fff; padding: 8px; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
    ';

    // Construir HTML completo
    $html = '<html><head><style>' . $stylesheet . '</style></head><body>
        <h1>Reporte de Ventas</h1>
        ' . $html_content . '
    </body></html>';

    try {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Reporte_' . date('YmdHis') . '.pdf', 'D'); // 'D' = descarga
        exit;
    } catch (\Mpdf\MpdfException $e) {
        echo "Error al generar PDF: " . $e->getMessage();
        exit;
    }
}
```

### 11.4 Tipos de Reportes Implementados

1. **Reporte de Rotación** (`rotacion()`):

   - Productos más vendidos por cantidad y valor
   - Agrupado por producto y código de barra
   - Filtrado por rango de fechas y sucursal

2. **Reporte de Ventas** (`ventas()`):

   - Listado de ventas con fecha, cliente, vendedor y total
   - Formato de fecha: DD/MM/YYYY HH:MM AM/PM
   - Soporte para clientes genéricos (sin registro)

3. **Reporte de Inventario** (`inventario()`):

   - Stock actual por producto, categoría y sucursal
   - Incluye cantidad mínima para alertas
   - No requiere rango de fechas

4. **Reporte Financiero** (`financiero()`):
   - Transacciones agrupadas por método de pago y moneda
   - Conteo de transacciones y total declarado
   - Útil para cuadre de caja

### 11.5 Filtrado por Sucursal

El sistema implementa filtrado automático basado en la sesión del usuario:

```php
// Lógica de Sucursal: Prioridad a la sesión
$id_sucursal = $_SESSION['sesion_usuario']['id_sucursal'] ?? null;

if (empty($id_sucursal)) {
    $id_sucursal = $params['id_sucursal'] ?? null;
}
```

- **Gerentes**: Pueden ver reportes de todas las sucursales (id_sucursal = null)
- **Cajeros**: Solo ven reportes de su sucursal asignada
- Si no hay sesión, se usa el parámetro enviado en el formulario

### 11.6 Personalización de Reportes

Para añadir un nuevo tipo de reporte:

1. **Crear método en `reportes_C.php`:**

```php
public static function miNuevoReporte($inicio, $fin, $id_sucursal)
{
    if (!self::$conn) self::conectar();

    $sql = "SELECT ... FROM ... WHERE fecha BETWEEN $1 AND $2";
    // Añadir filtro de sucursal si aplica
    if (!empty($id_sucursal)) {
        $sql .= " AND id_sucursal = $3";
    }

    // Ejecutar consulta y retornar HTML con generarTabla()
    return self::generarTabla($headers, $data, $keys);
}
```

2. **Añadir caso en `generarReporte()`:**

```php
case 'mi_nuevo_reporte':
    $out = self::miNuevoReporte($fecha_inicio_sql, $fecha_fin_sql, $id_sucursal);
    break;
```

3. **Añadir caso en `generarPDF()`:**

```php
case 'mi_nuevo_reporte':
    $html_content = self::miNuevoReporte($inicio, $fin, $id_sucursal);
    $titulo = 'Mi Nuevo Reporte';
    break;
```

4. **Actualizar vista con nueva opción:**

```html
<option value="mi_nuevo_reporte">Mi Nuevo Reporte</option>
```

---

---

## 12. Gestión de Roles y Accesos (RBAC)

QuickStock implementa un sistema de control de acceso basado en roles (RBAC) que adapta la interfaz y las funcionalidades según el perfil del usuario autenticado.

### 12.1 Roles Definidos

El sistema cuenta con 6 roles predefinidos en la base de datos (`seguridad_acceso.rol`):

| Rol            | ID | Descripción / Acceso Principal                                                                 |
| -------------- | -- | ---------------------------------------------------------------------------------------------- |
| **Gerente**    | 1  | Acceso total. Dashboard completo con widgets financieros. Gestión global (todas las sucursales). |
| **Admin**      | 2  | Gestión administrativa. Puede no tener sucursal asignada. Acceso restringido a ciertas finanzas. |
| **Encargado**  | 3  | Gestión operativa de sucursal. Inventario, ventas y reportes básicos.                          |
| **Cajero**     | 4  | Enfocado en Punto de Venta (POS) y Cierre de Caja. Sin acceso a gestión de usuarios o global.  |
| **Vendedor**   | 5  | Acceso mínimo: Dashboard de empleado y configuración de cuenta.                                |
| **Depositario**| 6  | Acceso mínimo: Dashboard de empleado y configuración de cuenta (similar a Vendedor).           |

### 12.2 Menús Laterales Dinámicos

La navegación se adapta mediante la inclusión dinámica de archivos de menú en `plantilla.php` basándose en `$_SESSION['sesion_usuario']['rol']`.

- `assets/elements/menu-lateral.php` (Gerente)
- `assets/elements/menu-lateral-administrador.php`
- `assets/elements/menu-lateral-cajero.php`
- `assets/elements/menu-lateral-encargado.php`
- `assets/elements/menu-lateral-vendedor.php` (Vendedores y Depositarios)

### 12.3 Dashboards Diferenciados

Se han separado las vistas del dashboard para evitar exponer información sensible (financiera) a roles operativos.

1.  **Dashboard Gerente (`dashboard-gerente-view.php`)**:
    *   Muestra widgets de Ganancias, Ventas Totales, Transacciones.
    *   Gráficos complejos de rendimiento.
    *   Utilizado por: Gerente, Administrador.

2.  **Dashboard Empleado (`dashboard-empleado-view.php`)**:
    *   Versión simplificada.
    *   Muestra: Productos con stock bajo, Notificaciones, Tareas pendientes.
    *   Utilizado por: Encargado, Cajero, Vendedor, Depositario.

### 12.4 Asignación de Sucursales

- **Gerentes**: Tienen acceso global. Su `id_sucursal` en sesión suele ser nulo o ignorado para consultas globales.
- **Administradores**: Pueden ser asignados a una sucursal específica o tener `id_sucursal = NULL` (Sucursal "Ninguna") para roles regionales o de auditoría.
- **Roles Operativos (Cajero, etc.)**: **Deben** estar asociados a una sucursal física. La validación se realiza tanto en frontend como en backend (aunque el backend permite nulos por diseño, la lógica de negocio lo restringe).

> **⚠️ Nota de Seguridad**: Actualmente, la restricción de crear empleados sin sucursal (para no-admins) reside principalmente en la validación del frontend (`empleados-añadir.js`). Se recomienda reforzar esta validación en el controlador en futuras versiones.

---

## 13. Dependencias y Gestión con Composer

### 12.1 Dependencias Actuales

El proyecto utiliza Composer para gestionar las siguientes dependencias:

```json
{
  "require": {
    "twbs/bootstrap": "^5.3",
    "twbs/bootstrap-icons": "^1.13",
    "mpdf/mpdf": "^8.0"
  }
}
```

**Dependencias instaladas (incluyendo transitorias):**

- `mpdf/mpdf: v8.2.7` - Generación de PDF
- `psr/log: 3.0.2` - Interfaz de logging PSR-3
- `psr/http-message: 2.0` - Interfaz HTTP PSR-7
- `paragonie/random_compat: v9.99.100` - Polyfill para funciones random de PHP 7
- `myclabs/deep-copy: 1.13.4` - Clonación profunda de objetos
- `mpdf/psr-log-aware-trait: v3.0.0` - Trait para logging en mPDF
- `mpdf/psr-http-message-shim: v2.0.1` - Adaptador PSR-7 para mPDF
- `setasign/fpdi: v2.6.4` - Importación de PDFs existentes

### 12.2 Actualización de mPDF (v6.1 → v8.2)

**Razón de la actualización:**

- mPDF v6.1 usa sintaxis de curly braces (`$array{0}`) removida en PHP 8.0+
- PHP 8.2 es incompatible con mPDF v6.1
- mPDF v8.x es totalmente compatible con PHP 8.0+

**Cambios en el código:**

| Aspecto     | mPDF v6.1                  | mPDF v8.2                                         |
| ----------- | -------------------------- | ------------------------------------------------- |
| Namespace   | Clase global `\mPDF`       | Namespace `\Mpdf\Mpdf`                            |
| Constructor | `new \mPDF('utf-8', 'A4')` | `new Mpdf(['mode' => 'utf-8', 'format' => 'A4'])` |
| Excepciones | `\Exception` genérica      | `\Mpdf\MpdfException`                             |
| Import      | No requiere                | `use Mpdf\Mpdf;`                                  |

**Proceso de actualización:**

```powershell
# 1. Actualizar composer.json
# Cambiar "mpdf/mpdf": "^6.1" a "mpdf/mpdf": "^8.0"

# 2. Ejecutar actualización
composer update mpdf/mpdf --with-all-dependencies --ignore-platform-req=ext-gd

# 3. Verificar versión instalada
composer show mpdf/mpdf
```

### 12.3 Comandos Útiles de Composer

```powershell
# Instalar todas las dependencias
composer install

# Actualizar una dependencia específica
composer update nombre/paquete

# Ver dependencias instaladas
composer show

# Ver dependencias desactualizadas
composer outdated

# Verificar problemas de seguridad
composer audit

# Limpiar caché
composer clear-cache

# Validar composer.json
composer validate
```

### 12.4 Troubleshooting Común

**Error: "ext-gd is missing"**

```powershell
# Solución temporal: ignorar requisito
composer update --ignore-platform-req=ext-gd

# Solución permanente: habilitar en php.ini
extension=gd
```

**Error: "Your requirements could not be resolved"**

```powershell
# Forzar actualización de dependencias
composer update --with-all-dependencies
```

**Error: "Class 'Mpdf\Mpdf' not found"**

```php
// Verificar que autoload esté incluido
require_once __DIR__ . '/../../vendor/autoload.php';

// Añadir use statement
use Mpdf\Mpdf;
```

---

## 13. Buenas prácticas y checklist de despliegue

- Validar cambios en un entorno de staging antes de producción.
- Hacer backup de la base de datos antes de correr migraciones.
- Ejecutar tests automatizados y revisar logs.

Checklist mínimo antes de merge/despliegue:

- [ ] Tests locales pasan
- [ ] Migraciones listas y probadas
- [ ] Backup de la BD tomado
- [ ] Documentación (manual y cambios de BD) actualizada

---

## 12. Anexos: ejemplos de código y comandos útiles

- Ejemplo cURL para llamar al endpoint central:

```bash
curl -X POST "http://localhost/DEV/PHP/QuickStock/src/api/server/index.php" \
	-H "Content-Type: application/json" \
	-d '{"accion":"obtener_todos_los_productos","nombre":"camisa"}'
// Lógica de Sucursal: Prioridad a la sesión
$id_sucursal = $_SESSION['sesion_usuario']['id_sucursal'] ?? null;

if (empty($id_sucursal)) {
    $id_sucursal = $params['id_sucursal'] ?? null;
}
```

- **Gerentes**: Pueden ver reportes de todas las sucursales (id_sucursal = null)
- **Cajeros**: Solo ven reportes de su sucursal asignada
- Si no hay sesión, se usa el parámetro enviado en el formulario

### 11.6 Personalización de Reportes

Para añadir un nuevo tipo de reporte:

1. **Crear método en `reportes_C.php`:**

```php
public static function miNuevoReporte($inicio, $fin, $id_sucursal)
{
    if (!self::$conn) self::conectar();

    $sql = "SELECT ... FROM ... WHERE fecha BETWEEN $1 AND $2";
    // Añadir filtro de sucursal si aplica
    if (!empty($id_sucursal)) {
        $sql .= " AND id_sucursal = $3";
    }

    // Ejecutar consulta y retornar HTML con generarTabla()
    return self::generarTabla($headers, $data, $keys);
}
```

2. **Añadir caso en `generarReporte()`:**

```php
case 'mi_nuevo_reporte':
    $out = self::miNuevoReporte($fecha_inicio_sql, $fecha_fin_sql, $id_sucursal);
    break;
```

3. **Añadir caso en `generarPDF()`:**

```php
case 'mi_nuevo_reporte':
    $html_content = self::miNuevoReporte($inicio, $fin, $id_sucursal);
    $titulo = 'Mi Nuevo Reporte';
    break;
```

4. **Actualizar vista con nueva opción:**

```html
<option value="mi_nuevo_reporte">Mi Nuevo Reporte</option>
```

---

## 12. Dependencias y Gestión con Composer

### 12.1 Dependencias Actuales

El proyecto utiliza Composer para gestionar las siguientes dependencias:

```json
{
  "require": {
    "twbs/bootstrap": "^5.3",
    "twbs/bootstrap-icons": "^1.13",
    "mpdf/mpdf": "^8.0"
  }
}
```

**Dependencias instaladas (incluyendo transitorias):**

- `mpdf/mpdf: v8.2.7` - Generación de PDF
- `psr/log: 3.0.2` - Interfaz de logging PSR-3
- `psr/http-message: 2.0` - Interfaz HTTP PSR-7
- `paragonie/random_compat: v9.99.100` - Polyfill para funciones random de PHP 7
- `myclabs/deep-copy: 1.13.4` - Clonación profunda de objetos
- `mpdf/psr-log-aware-trait: v3.0.0` - Trait para logging en mPDF
- `mpdf/psr-http-message-shim: v2.0.1` - Adaptador PSR-7 para mPDF
- `setasign/fpdi: v2.6.4` - Importación de PDFs existentes

### 12.2 Actualización de mPDF (v6.1 → v8.2)

**Razón de la actualización:**

- mPDF v6.1 usa sintaxis de curly braces (`$array{0}`) removida en PHP 8.0+
- PHP 8.2 es incompatible con mPDF v6.1
- mPDF v8.x es totalmente compatible con PHP 8.0+

**Cambios en el código:**

| Aspecto     | mPDF v6.1                  | mPDF v8.2                                         |
| ----------- | -------------------------- | ------------------------------------------------- |
| Namespace   | Clase global `\mPDF`       | Namespace `\Mpdf\Mpdf`                            |
| Constructor | `new \mPDF('utf-8', 'A4')` | `new Mpdf(['mode' => 'utf-8', 'format' => 'A4'])` |
| Excepciones | `\Exception` genérica      | `\Mpdf\MpdfException`                             |
| Import      | No requiere                | `use Mpdf\Mpdf;`                                  |

**Proceso de actualización:**

```powershell
# 1. Actualizar composer.json
# Cambiar "mpdf/mpdf": "^6.1" a "mpdf/mpdf": "^8.0"

# 2. Ejecutar actualización
composer update mpdf/mpdf --with-all-dependencies --ignore-platform-req=ext-gd

# 3. Verificar versión instalada
composer show mpdf/mpdf
```

### 12.3 Comandos Útiles de Composer

```powershell
# Instalar todas las dependencias
composer install

# Actualizar una dependencia específica
composer update nombre/paquete

# Ver dependencias instaladas
composer show

# Ver dependencias desactualizadas
composer outdated

# Verificar problemas de seguridad
composer audit

# Limpiar caché
composer clear-cache

# Validar composer.json
composer validate
```

### 12.4 Troubleshooting Común

**Error: "ext-gd is missing"**

```powershell
# Solución temporal: ignorar requisito
composer update --ignore-platform-req=ext-gd

# Solución permanente: habilitar en php.ini
extension=gd
```

**Error: "Your requirements could not be resolved"**

```powershell
# Forzar actualización de dependencias
composer update --with-all-dependencies
```

**Error: "Class 'Mpdf\Mpdf' not found"**

```php
// Verificar que autoload esté incluido
require_once __DIR__ . '/../../vendor/autoload.php';

// Añadir use statement
use Mpdf\Mpdf;
```

---

## 13. Buenas prácticas y checklist de despliegue

- Validar cambios en un entorno de staging antes de producción.
- Hacer backup de la base de datos antes de correr migraciones.
- Ejecutar tests automatizados y revisar logs.

Checklist mínimo antes de merge/despliegue:

- [ ] Tests locales pasan
- [ ] Migraciones listas y probadas
- [ ] Backup de la BD tomado
- [ ] Documentación (manual y cambios de BD) actualizada

---

## 12. Anexos: ejemplos de código y comandos útiles

- Ejemplo cURL para llamar al endpoint central:

```bash
curl -X POST "http://localhost/DEV/PHP/QuickStock/src/api/server/index.php" \
	-H "Content-Type: application/json" \
	-d '{"accion":"obtener_todos_los_productos","nombre":"camisa"}'
```

- Ejemplo de respuesta consistente (JSON):

````json
---

## 15. Conclusión y Próximos Pasos

### 15.1 Resumen del Manual

Has completado la lectura del **Manual de Desarrollador de QuickStock**. A lo largo de este documento, hemos cubierto:

✅ **Arquitectura Dual**: MVC para navegación completa + Client-Server para operaciones asíncronas
✅ **Flujo de Datos**: Desde la URL hasta la base de datos y viceversa
✅ **Módulo de Reportes**: Generación HTML y exportación a PDF con mPDF
✅ **Gestión de Dependencias**: Composer y actualización de librerías
✅ **Patrones y Convenciones**: Nomenclatura, estructura de archivos, validaciones
✅ **Buenas Prácticas**: Seguridad, mantenibilidad, escalabilidad

### 15.2 Checklist del Desarrollador

Antes de comenzar a desarrollar, asegúrate de:

- [ ] Leer la sección de [Arquitectura Dual (1.3)](#13-arquitectura-dual-mvc--client-server)
- [ ] Entender el [Flujo MVC (Sección 5)](#5-flujo-completo-vistas-controlador-y-plantilla)
- [ ] Revisar [Peticiones API (Sección 6)](#6-peticiones-front--api-apiclient--apiserverindexphp)
- [ ] Consultar [Convenciones de Nombres](#4-estructura-del-proyecto)
- [ ] Configurar entorno local según [Sección 3](#3-entorno-de-desarrollo)
- [ ] Leer [Changelog.md](Changelog.md) para conocer cambios recientes

### 15.3 Recursos Adicionales

📖 **Documentación Oficial**:
- [PHP 8 Documentation](https://www.php.net/manual/es/index.php)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [mPDF Documentation](https://mpdf.github.io/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/getting-started/introduction/)

🔧 **Herramientas Recomendadas**:
- **IDE**: Visual Studio Code, PHPStorm
- **DB Manager**: pgAdmin 4, DBeaver
- **API Testing**: Postman, Thunder Client (VS Code)
- **Version Control**: Git, GitHub Desktop

### 15.4 Soporte y Contribución

¿Encontraste un error en la documentación? ¿Tienes sugerencias?

1. Abre un **Issue** en el repositorio
2. Consulta [Contributing.md](Contributing.md) para guías de contribución
3. Contacta al equipo de desarrollo

### 15.5 Evolución del Proyecto

**Próximas funcionalidades planificadas**:
- Sistema de notificaciones en tiempo real
- Dashboard con gráficos interactivos
- Integración con APIs de pago
- Módulo de auditoría avanzada
- App móvil (PWA)

> **💡 Consejo Final**: QuickStock es un proyecto en constante evolución. Mantén este manual actualizado cuando implementes nuevas funcionalidades.

> **⚠️ Recordatorio**: Siempre haz backup de la base de datos antes de ejecutar migraciones en producción.

---

## 16. Anexos y Referencias Rápidas

### 16.1 Comandos Útiles

**Git**:
```bash
git status                    # Ver estado del repositorio
git add .                     # Agregar todos los cambios
git commit -m "mensaje"       # Commit con mensaje
git push origin master        # Subir cambios
git pull origin master        # Descargar cambios
````

**Composer**:

```bash
composer install              # Instalar dependencias
composer update               # Actualizar dependencias
composer show                 # Ver paquetes instalados
composer audit                # Verificar vulnerabilidades
```

**PostgreSQL**:

```bash
psql -U postgres -d QuickStock -f config/quickstock.sql  # Importar BD
psql -U postgres -d QuickStock                           # Conectar a BD
\dt                                                      # Listar tablas
\d nombre_tabla                                          # Describir tabla
```

### 16.2 Estructura de Respuestas API

**Respuesta exitosa**:

```json
{
  "status": "success",
  "data": [
    { "id": 1, "nombre": "Producto 1" },
    { "id": 2, "nombre": "Producto 2" }
  ]
}
```

**Respuesta con error**:

```json
{
  "status": "error",
  "message": "Descripción del error para el usuario",
  "detalle": "Información técnica (solo en desarrollo)"
}
```

### 16.3 Convenciones de Nomenclatura

| Elemento      | Convención               | Ejemplo                        |
| ------------- | ------------------------ | ------------------------------ |
| Vistas        | `modulo-accion-view.php` | `empleados-listado-view.php`   |
| Controladores | `modulo_accion_C.php`    | `empleados_listado_C.php`      |
| Modelos       | `schema.tabla.php`       | `core.empleado.php`            |
| API Client    | `modulo-accion.js`       | `empleados-listado.js`         |
| API Server    | `modulo/tabla.php`       | `seguridad_acceso/usuario.php` |

### 16.4 Enlaces Rápidos a Código

**Archivos clave del proyecto**:

- [index.php](../../index.php) - Punto de entrada MVC
- [plantilla.php](../view/plantilla.php) - Plantilla base
- [api/server/index.php](../api/server/index.php) - Punto de entrada API
- [mainModel.php](../model/mainModel.php) - Modelo base
- [reportes_C.php](../controller/reportes_C.php) - Controlador de reportes
- [composer.json](../../composer.json) - Dependencias del proyecto
- [quickstock.sql](../config/quickstock.sql) - Script de base de datos

---

**Fin del Manual de Desarrollador - QuickStock v1.0**

Última actualización: 2025-12-06

```

```
