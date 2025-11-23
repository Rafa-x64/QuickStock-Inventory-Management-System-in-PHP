import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// *********************************************************************************
// ************************** INICIO: FUNCIONES DE SERVICIO ************************
// *********************************************************************************

/**
 * Función central para cargar y renderizar las opciones de un select.
 * NOTA: Esta función es interna al módulo peticiones.js.
 */
async function cargarSelect(selectId, apiAction, dataKey, idField, displayField, defaultOptionText, params = {}) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) {
        console.error(`Elemento Select no encontrado: #${selectId}`);
        return null;
    }

    selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;

    try {
        const res = await api({ accion: apiAction, ...params });
        const datos = res[dataKey] || [];

        if (datos.length > 0) {
            datos.forEach(item => {
                const displayText = item[displayField] || 'N/A';
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
    await cargarSelect(
        "compra_id_proveedor",
        "obtener_proveedores",
        "proveedores",
        "id_proveedor",
        "nombre",
        "Seleccione un proveedor..."
    );

    // Sucursales (compra_id_sucursal)
    const sucursales = await cargarSelect(
        "compra_id_sucursal",
        "obtener_sucursales",
        "filas", // NOTA: Asumo que la API de sucursales devuelve 'filas' como key
        "id_sucursal",
        "nombre",
        "Seleccione una sucursal..."
    );

    // Moneda (compra_id_moneda)
    await cargarSelect(
        "compra_id_moneda",
        "obtener_monedas",
        "monedas",
        "id_moneda",
        "simbolo",
        "Seleccione una moneda..."
    );

    // Empleado Responsable (compra_id_usuario)
    // *** MODIFICADO: Usar la nueva acción API y la clave de datos 'empleados' ***
    await cargarSelect(
        "compra_id_usuario",
        "obtener_empleados_responsables", // <-- CAMBIO DE ACCIÓN
        "empleados",                       // <-- CLAVE DE DATOS EN LA RESPUESTA
        "id_usuario",
        "nombre_completo",
        "Seleccione un empleado...",
        // { sucursal: null, rol: null, estado: 1 } <-- ELIMINADOS
    );

    // Seleccionar automáticamente la primera sucursal si existe
    if (sucursales && sucursales.length > 0) {
        const sucursalSelect = document.getElementById("compra_id_sucursal");
        if (sucursalSelect) sucursalSelect.value = sucursales[0].id_sucursal;
    }
}

/**
 * Carga los datos base de Categorías, Colores y Tallas desde la API.
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

// 2. EXPOSICIÓN GLOBAL: Hacemos la función clave accesible a 'validaciones.js'
// Al ser un módulo, se ejecuta una vez y define esta función en el scope global.
window.cargarDatosProductoBase = cargarDatosProductoBase;