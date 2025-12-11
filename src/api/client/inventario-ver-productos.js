import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// Obtener id_sucursal de la sesión (viene de la variable global inyectada por PHP)
// Si es null, el usuario ve productos de todas las sucursales (ej: Gerente)
const sucursalSesion = window.ID_SUCURSAL_SESION || "";

// 📦 OBJETO GLOBAL PARA GUARDAR EL ESTADO DE LOS FILTROS
let filtrosActivos = {
    nombre: "",
    codigo: "",
    categoria: "",
    proveedor: "",
    sucursal: sucursalSesion ? String(sucursalSesion) : "",
    estado: "" // Valores posibles: "", "true", "false"
};

// 🔄 FUNCIÓN REUTILIZABLE PARA CARGAR PRODUCTOS APLICANDO LOS FILTROS
function cargarProductos() {
    // La función 'api' enviará 'filtrosActivos' como parámetros GET/POST al index.php
    api({
        accion: "obtener_todos_los_productos",
        ...filtrosActivos // Despliega todos los filtros como parámetros de la petición
    }).then(res => {
        const tabla = document.getElementById("tabla_productos");
        tabla.innerHTML = ""; // Limpia la tabla antes de cargar nuevos datos
        const productos = res.data || [];

        if (productos.length === 0) {
            tabla.innerHTML = '<tr><td colspan="11" class="text-center">No se encontraron productos con estos filtros.</td></tr>';
            return;
        }

        // Mapeo y renderizado de las filas (sin cambios en la lógica de renderizado)
        productos.forEach(prod => {
            const estadoTexto = prod.estado == 1 || prod.estado === "t"
                ? '<span class="badge text-bg-success">Activo</span>'
                : '<span class="badge text-bg-danger">Inactivo</span>';

            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${prod.codigo ?? '-'}</td>
                <td>${prod.nombre ?? '-'}</td>
                <td>${prod.categoria_nombre ?? '-'}</td>
                <td>${prod.talla ?? '-'}</td>
                <td>${prod.precio_compra ?? '-'}</td>
                <td>${prod.precio_venta ?? '-'}</td>
                <td>${prod.stock ?? 0}</td>
                <td>${prod.sucursal_nombre ?? 'Sin sucursal'}</td>
                <td>${estadoTexto}</td>
                <td class="text-center">
                    <div class="container-fluid p-0">
                        <div class="row g-1">
                            <div class="col-6">
                                <form action="inventario-editar-producto" method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_producto" value="${prod.id_producto}">
                                    <input type="submit" class="btn btn-warning btn-sm w-100" value="Editar">
                                </form>
                            </div>
                            <div class="col-6">
                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_producto" value="${prod.id_producto}">
                                    <input type="submit" class="btn btn-danger btn-sm w-100" value="Eliminar">
                                </form>
                            </div>
                            <div class="col-12">
                                <form action="inventario-detalle-producto" method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="ver_detalle">
                                    <input type="hidden" name="id_producto" value="${prod.id_producto}">
                                    <input type="submit" class="btn btn-primary btn-sm w-100" value="Ver detalle">
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            `;
            tabla.appendChild(fila);
        });
    }).catch(error => {
        console.error("Error al cargar productos:", error);
        document.getElementById("tabla_productos").innerHTML = '<tr><td colspan="11" class="text-center text-danger">Error al cargar los datos.</td></tr>';
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
        // 1. Resetear el objeto de filtros (manteniendo la sucursal de sesión si existe)
        filtrosActivos = {
            nombre: "",
            codigo: "",
            categoria: "",
            proveedor: "",
            sucursal: sucursalSesion ? String(sucursalSesion) : "",
            estado: ""
        };

        // 2. Resetear los valores de los elementos de la vista
        document.getElementById("filtro_nombre").value = "";
        document.getElementById("filtro_codigo").value = "";

        // Asignar el valor de la opción por defecto a los selects
        document.getElementById("filtro_categoria").value = "";
        document.getElementById("filtro_proveedor").value = "";
        document.getElementById("filtro_sucursal").value = sucursalSesion ? String(sucursalSesion) : "";
        document.getElementById("filtro_estado").value = "";

        // 3. Recargar productos
        cargarProductos();
    });
}

// ⚙️ FUNCIÓN PARA CARGAR OPCIONES DINÁMICAS EN LOS SELECTS
function cargarOpcionesSelects() {
    // Función auxiliar para cargar opciones
    const cargarOpciones = (selectId, accionApi, valueKey, textKey, resKey) => {
        const select = document.getElementById(selectId);
        if (!select) return;

        api({ accion: accionApi }).then(res => {
            // Manejar diferentes estructuras de respuesta (res.filas, res.categorias, res.proveedores)
            const data = res[resKey] || res.filas || [];
            data.forEach(item => {
                const op = document.createElement("option");
                op.value = item[valueKey];
                op.textContent = item[textKey];
                select.appendChild(op);
            });
        }).catch(error => {
            console.error(`Error al cargar ${selectId}:`, error);
        });
    };

    cargarOpciones("filtro_sucursal", "obtener_sucursales", "id_sucursal", "nombre", "filas");
    cargarOpciones("filtro_categoria", "obtener_categorias", "id_categoria", "nombre", "categorias");
    cargarOpciones("filtro_proveedor", "obtener_proveedores", "id_proveedor", "nombre", "proveedor");
}

// 🚀 CUANDO CARGA LA PÁGINA
document.addEventListener("DOMContentLoaded", () => {
    cargarOpcionesSelects(); // Llenar los selects
    inicializarFiltros();    // Configurar los listeners
    
    // Preseleccionar la sucursal de sesión en el select (si existe)
    if (sucursalSesion) {
        const selectSucursal = document.getElementById("filtro_sucursal");
        if (selectSucursal) {
            selectSucursal.value = String(sucursalSesion);
        }
    }
    
    cargarProductos();       // Cargar la lista inicial de productos
});