import { api } from "./index.js";

export const ventasAPI = {
    obtenerClientePorCedula: async (cedula) => {
        return await api({
            accion: "obtener_cliente_por_cedula",
            cedula: cedula
        });
    },

    obtenerProductoPorCodigo: async (codigo) => {
        return await api({
            accion: "obtener_producto_por_codigo",
            codigo: codigo
        });
    },

    obtenerTasasCambio: async () => {
        return await api({
            accion: "obtener_tasas_cambio"
        });
    },

    procesarVenta: async (datosVenta) => {
        return await api({
            accion: "procesar_venta",
            ...datosVenta
        });
    },

    obtenerCategorias: async () => {
        return await api({ accion: "obtener_categorias" });
    },

    obtenerColores: async () => {
        return await api({ accion: "obtener_colores" });
    },

    obtenerTallas: async () => {
        return await api({ accion: "obtener_tallas" });
    },

    obtenerMetodosPago: async () => {
        return await api({ accion: "obtener_metodos_pago" });
    }
};
