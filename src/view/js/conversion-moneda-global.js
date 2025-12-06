import { api } from "../../api/client/api.js"; // Ajustar ruta según ubicación real de api wrapper

export const MonedaGlobal = {
    tasas: {},
    monedaBase: 'USD',
    monedaActual: 'USD', // Por defecto

    async inicializar() {
        try {
            const res = await api({ accion: 'obtener_tasas_cambio' });
            if (res.status && res.tasas) {
                // Convertir array a objeto mapa: { "VES": 50.1, "EUR": 0.95 }
                res.tasas.forEach(t => {
                    this.tasas[t.codigo] = parseFloat(t.tasa);
                });
                this.tasas['USD'] = 1; // Base
            }
        } catch (e) {
            console.error("Error cargando tasas:", e);
        }
    },

    convertir(montoUSD, monedaDestino) {
        if (!monedaDestino || monedaDestino === 'USD') return montoUSD;
        const tasa = this.tasas[monedaDestino];
        if (!tasa) return montoUSD; // Fallback
        return montoUSD * tasa;
    },

    formatear(monto, moneda) {
        return new Intl.NumberFormat('es-VE', { 
            style: 'currency', 
            currency: moneda 
        }).format(monto);
    },

    /**
     * Busca elementos con clase .precio-dinamico y data-monto-usd
     * y actualiza su texto según la moneda seleccionada.
     */
    renderizarPrecios(monedaSeleccionada) {
        this.monedaActual = monedaSeleccionada || 'USD';
        document.querySelectorAll('.precio-dinamico').forEach(el => {
            const montoUSD = parseFloat(el.dataset.montoUsd);
            if (!isNaN(montoUSD)) {
                const valor = this.convertir(montoUSD, this.monedaActual);
                el.textContent = this.formatear(valor, this.monedaActual);
            }
        });
    }
};

// Inicializar al cargar (opcional, o llamar manualmente)
// document.addEventListener('DOMContentLoaded', () => MonedaGlobal.inicializar());
