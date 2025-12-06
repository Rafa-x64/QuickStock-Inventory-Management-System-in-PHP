import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// *********************************************************************************
// ************************** INICIO: FUNCIONES DE SERVICIO ************************
// *********************************************************************************

/**
 * Función central para cargar y renderizar las opciones de un select.
 * @param {string} selectId - ID del elemento select.
 * @param {string} apiAction - Acción a llamar en la API (ej: "obtener_proveedores").
 * @param {string} dataKey - Clave del arreglo de datos en la respuesta JSON (ej: "proveedores").
 * @param {string} idField - Nombre del campo ID en el objeto de datos (ej: "id_proveedor").
 * @param {string} displayField - Nombre del campo a mostrar al usuario (ej: "nombre").
 * @param {string} defaultOptionText - Texto de la opción por defecto.
 * @param {Object} [params={}] - Parámetros adicionales para la llamada API.
 * @returns {Promise<Array|null>} El arreglo de datos o null si hay error.
 */
async function cargarSelect(selectId, apiAction, dataKey, idField, displayField, defaultOptionText, params = {}) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) {
        console.error(`Elemento Select no encontrado: #${selectId}`);
        return null;
    }

    // Asegura que la opción por defecto SIEMPRE tenga value="" para la validación de PHP
    selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;

    try {
        const res = await api({ accion: apiAction, ...params });
        // Extrae el arreglo de datos usando la clave proporcionada
        const datos = res[dataKey] || [];

        if (datos.length > 0) {
            datos.forEach(item => {
                const displayText = item[displayField] || 'N/A';
                // CORRECTO: El ID de la BD se usa como valor numérico (value) para que PHP lo capture.
                selectElement.innerHTML += `<option value="${item[idField]}">${displayText}</option>`;
            });
        }

        return datos;
    } catch (error) {
        console.error(`Error al cargar ${dataKey}:`, error);
        return null;
    }
}

async function cargarSelectsCompraPrincipal() {
    // 1. Proveedores (compra_id_proveedor)
    // Clave de retorno de la API: "proveedor", ID en la BD: "id_proveedor"
    await cargarSelect(
        "compra_id_proveedor",
        "obtener_proveedores",
        "proveedor", // <-- Confirmado por tu PHP
        "id_proveedor", // <-- Confirmado por tu DB
        "nombre",
        "Seleccione un proveedor..."
    );

    // 2. Sucursales (compra_id_sucursal)
    // Clave de retorno de la API: "filas" (asumido), ID en la BD: "id_sucursal"
    const sucursales = await cargarSelect(
        "compra_id_sucursal",
        "obtener_sucursales",
        "filas",
        "id_sucursal",
        "nombre",
        "Seleccione una sucursal..."
    );

    // 3. Moneda (compra_id_moneda)
    // Clave de retorno de la API: "monedas" (asumido), ID en la BD: "id_moneda"
    await cargarSelect(
        "compra_id_moneda",
        "obtener_todas_monedas",
        "monedas",
        "id_moneda",
        "codigo", // Asumo que el campo a mostrar es 'codigo'
        "Seleccione una moneda..."
    );

    // 4. Empleado Responsable (compra_id_usuario)
    // Clave de retorno de la API: "empleados" (asumido), ID en la BD: "id_usuario"
    await cargarSelect(
        "compra_id_usuario",
        "obtener_empleados_responsables",
        "empleados",
        "id_usuario",
        "nombre_completo",
        "Seleccione un empleado...",
    );

    // NOTA: El Estado ("compra_estado") no necesita carga de API, ya está en el HTML.

    // Seleccionar automáticamente la primera sucursal si existe y establecerla como valor
    if (sucursales && sucursales.length > 0) {
        const sucursalSelect = document.getElementById("compra_id_sucursal");
        if (sucursalSelect) sucursalSelect.value = sucursales[0].id_sucursal;
    }
}

/**
 * Carga los datos base de Categorías, Colores y Tallas desde la API.
 * Se utilizan en el clonado de productos.
 * @returns {Promise<Object>} Objeto con las estructuras de categorías, colores y tallas.
 */
async function cargarDatosProductoBase() {
    const resultados = {};

    const resCategorias = await api({ accion: "obtener_categorias" });
    resultados.categorias = resCategorias.categorias || [];

    const resColores = await api({ accion: "obtener_colores" });
    resultados.colores = resColores.colores || [];

    const resTallas = await api({ accion: "obtener_tallas" });
    resultados.tallas = resTallas.tallas || [];

    return resultados;
}

// *********************************************************************************
// *************************** FIN: FUNCIONES DE SERVICIO **************************
// *********************************************************************************


// 1. Inicialización de la carga de selects estáticos
document.addEventListener("DOMContentLoaded", cargarSelectsCompraPrincipal);

// 2. EXPOSICIÓN GLOBAL
window.cargarDatosProductoBase = cargarDatosProductoBase;
window.obtenerProductoPorCodigo = async (codigo) => {
    try {
        const res = await api({ 
            accion: "obtener_producto_por_codigo", 
            codigo: codigo
            // NO enviamos id_sucursal para que busque en el catálogo global (para compras)
        });
        return res;
    } catch (e) {
        console.error("Error buscando producto:", e);
        return null;
    }
};


// ---------------------------------------------------------------------------------
// AHORA DEBES ASEGURARTE DE AGREGAR AQUÍ LA LÓGICA PARA:
// 1. Clonar productos y asignar los NAMES con índices (productos[0][campo], productos[1][campo], etc.)
// 2. Manejar la lógica de 'Seleccionar color existente' vs 'Nuevo color'.
// 3. Calcular el subtotal, IVA y Total.
// 4. Capturar el evento 'submit' del formulario y enviarlo (ej: usando fetch o un submit estándar).
// ---------------------------------------------------------------------------------