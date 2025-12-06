import { api } from "./index.js";

/**
 * Realiza un ajuste de stock para un producto específico.
 * @param {Object} params - Parámetros del ajuste.
 * @param {number} params.id_producto - ID del producto.
 * @param {number} params.id_sucursal - ID de la sucursal.
 * @param {number} params.cantidad - Cantidad a ajustar.
 * @param {string} params.tipo_ajuste - 'entrada' o 'salida'.
 * @param {string} params.motivo - Motivo del ajuste.
 * @param {string} [params.comentario] - Comentario opcional.
 * @returns {Promise<Object>} Respuesta de la API.
 */
export async function realizarAjusteStock(params) {
    return await api({
        accion: "ajustar_stock",
        ...params
    });
}
