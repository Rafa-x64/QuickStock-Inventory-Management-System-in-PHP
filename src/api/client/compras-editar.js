import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// *********************************************************************************
// ************************** INICIO: FUNCIONES DE SERVICIO ************************
// *********************************************************************************

/**
 * Función central para cargar y renderizar las opciones de un select.
 * Mantiene la funcionalidad de carga y añade la capacidad de preseleccionar un valor.
 * * @param {string} selectId - ID del elemento select.
 * @param {string} apiAction - Acción a llamar en la API (ej: "obtener_proveedores").
 * @param {string} dataKey - Clave del arreglo de datos en la respuesta JSON (ej: "proveedores").
 * @param {string} idField - Nombre del campo ID en el objeto de datos (ej: "id_proveedor").
 * @param {string} displayField - Nombre del campo a mostrar al usuario (ej: "nombre").
 * @param {string} defaultOptionText - Texto de la opción por defecto.
 * @param {string|number|null} [selectedValue=null] - Valor a preseleccionar en el select.
 * @param {Object} [params={}] - Parámetros adicionales para la llamada API.
 * @returns {Promise<Array|null>} El arreglo de datos o null si hay error.
 */
async function cargarSelect(selectId, apiAction, dataKey, idField, displayField, defaultOptionText, selectedValue = null, params = {}) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) {
        console.error(`Elemento Select no encontrado: #${selectId}`);
        return null;
    }

    // Asegura que la opción por defecto SIEMPRE tenga value="" para la validación de PHP
    selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;

    try {
        const res = await api({ accion: apiAction, ...params });
        const datos = res[dataKey] || [];

        if (datos.length > 0) {
            datos.forEach(item => {
                const displayText = item[displayField] || 'N/A';
                const isSelected = (String(item[idField]) === String(selectedValue)) ? 'selected' : '';
                selectElement.innerHTML += `<option value="${item[idField]}" ${isSelected}>${displayText}</option>`;
            });
        }

        return datos;
    } catch (error) {
        console.error(`Error al cargar ${dataKey}:`, error);
        return null;
    }
}

/**
 * Carga los datos base (Categorías, Colores, Tallas) para los productos.
 * @returns {Promise<Object>} Objeto con las estructuras de categorías, colores y tallas.
 */
async function cargarDatosProductoBase() {
    const resultados = {};

    try {
        const [resCategorias, resColores, resTallas] = await Promise.all([
            api({ accion: "obtener_categorias" }),
            api({ accion: "obtener_colores" }),
            api({ accion: "obtener_tallas" })
        ]);

        resultados.categorias = resCategorias.categorias || [];
        resultados.colores = resColores.colores || [];
        resultados.tallas = resTallas.tallas || [];
    } catch (error) {
        console.error("Error al cargar datos base del producto:", error);
        return { categorias: [], colores: [], tallas: [] };
    }

    return resultados;
}

/**
 * Obtiene todos los datos de la compra (principal y detalles) para edición.
 * @param {number} id_compra - ID de la compra a editar.
 * @returns {Promise<{compra: Object, detalles: Array}|null>} Datos de la compra o null.
 */
async function obtenerCompraParaEdicion(id_compra) {
    if (!id_compra) return null;

    try {
        const res = await api({
            accion: "obtener_compra_por_id", // Acción definida en el backend
            id_compra: id_compra
        });

        if (res.success && res.data) {
            return res.data;
        } else {
            console.error("Error API al obtener datos de compra:", res.error);
            return null;
        }
    } catch (error) {
        console.error("Error de red/petición al obtener datos de compra:", error);
        return null;
    }
}


// *********************************************************************************
// *************************** FIN: FUNCIONES DE SERVICIO **************************
// *********************************************************************************

// Exposición de funciones para el script de la vista (view/js/compras-editar.js)
export { cargarSelect, cargarDatosProductoBase, obtenerCompraParaEdicion };