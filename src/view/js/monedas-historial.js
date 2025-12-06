import { api } from "../../api/client/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#tabla_historial tbody");
    const filtroMoneda = document.getElementById("tipo-moneda-historial");
    const btnFiltro = document.querySelector(".btn-primary"); // Mejorar selección
    
    // Cargar historial al inicio
    cargarHistorial();

    if (btnFiltro) {
        btnFiltro.addEventListener("click", () => {
             cargarHistorial(filtroMoneda.value);
        });
    }

    async function cargarHistorial(moneda = "") {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando...</td></tr>';
        
        try {
            const res = await api({ accion: "obtener_historial_tasas" });
            if (res && res.historial) {
                let data = res.historial;
                if (moneda) {
                    data = data.filter(h => h.codigo === moneda);
                }
                renderHistorial(data);
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay historial disponible.</td></tr>';
            }
        } catch (e) {
            console.error(e);
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error cargando historial.</td></tr>';
        }
    }

    function renderHistorial(historial) {
        tableBody.innerHTML = "";
        historial.forEach(h => {
            const row = document.createElement("tr");
            
            const badgeOrigen = h.origen === 'API' 
                ? '<span class="badge bg-primary">API</span>' 
                : '<span class="badge bg-warning text-dark">Manual</span>';

            row.innerHTML = `
                <td>${h.fecha}</td>
                <td>${h.moneda} (${h.codigo})</td>
                <td>${parseFloat(h.tasa).toFixed(4)}</td>
                <td>${badgeOrigen}</td>
                <td>
                    <span class="text-muted">N/A</span> 
                </td>
                <td>
                    <button class="btn btn-sm btn-info text-white" disabled>Detalles</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }
});
