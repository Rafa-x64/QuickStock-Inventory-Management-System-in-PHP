import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// Funciones especificas para el módulo de Monedas
export const MonedasAPI = {
    obtenerResumen: async () => {
        return await api({ accion: "obtener_resumen_tasas" });
    },

    sincronizar: async () => {
        return await api({ accion: "sincronizar_api" });
    },

    guardarManual: async (id_moneda, valor) => {
        return await api({ 
            accion: "guardar_tasa_manual", 
            id_moneda: id_moneda, 
            valor: valor 
        });
    },

    obtenerHistorial: async (limit = 50) => {
        return await api({ 
            accion: "obtener_historial", 
            limit: limit 
        });
    }
};
