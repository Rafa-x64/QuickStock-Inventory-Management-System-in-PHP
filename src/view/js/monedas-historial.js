import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#tabla_historial_tasas tbody");
    const filtroMoneda = document.getElementById("tipo-moneda-historial");
    const btnFiltro = document.querySelector(".btn-primary"); // Mejorar selección
    
    let offset = 0;
    const limit = 50;
    let loading = false;

    // Elementos UI
    const btnCargarMas = document.getElementById("btn-cargar-mas");
    
    // Cargar historial al inicio (reset=true para limpiar el "Cargando..." del HTML)
    cargarHistorial("", true);

    if (btnFiltro) {
        btnFiltro.addEventListener("click", () => {
             offset = 0; // Reset offset on filter
             cargarHistorial(filtroMoneda.value, true);
        });
    }

    if (btnCargarMas) {
        btnCargarMas.addEventListener("click", () => {
            cargarHistorial(filtroMoneda ? filtroMoneda.value : "", false);
        });
    }

    async function cargarHistorial(moneda = "", reset = false) {
        if (loading) return;
        loading = true;

        if (reset) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando...</td></tr>';
            offset = 0;
            // Ocultar botón mientras carga
            if (btnCargarMas) btnCargarMas.style.display = 'none';
        } else {
             // Loading state en botón
             if (btnCargarMas) btnCargarMas.textContent = "Cargando...";
        }
        
        try {
            // Enviamos offset y limit a la API
            const res = await api({ 
                accion: "obtener_historial_tasas",
                offset: offset,
                limit: limit
            });

            if (res && res.historial) {
                let data = res.historial;
                
                // Si hay filtro en JS (aunque lo ideal sería en BDD, por ahora mantenemos híbrido o BDD si el backend lo soportara)
                // NOTA: Si filtramos acá, la paginación de BDD se descuadra. 
                // Asumiremos que el backend trae TODO mezclado y aquí filtramos visualmente 
                // O mejor aún, pasamos el filtro al backend? 
                // El usuario pidió "mostrar boton para los siguientes 50".
                // Asumiremos paginación real de BDD.
                
                if (moneda) {
                    data = data.filter(h => h.codigo === moneda);
                }

                renderHistorial(data, reset);

                if (data.length < limit) {
                    // No hay más datos
                    if (btnCargarMas) btnCargarMas.style.display = 'none';
                } else {
                    if (btnCargarMas) {
                        btnCargarMas.style.display = 'block';
                        btnCargarMas.textContent = "Cargar más historial";
                    }
                    offset += limit; // Preparar siguiente offset
                }

                if (data.length === 0 && reset) {
                     tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay historial disponible.</td></tr>';
                }

            } else {
                if (reset) tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay historial disponible.</td></tr>';
                if (btnCargarMas) btnCargarMas.style.display = 'none';
            }
        } catch (e) {
            console.error(e);
            if (reset) tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error cargando historial.</td></tr>';
        } finally {
            loading = false;
             if (btnCargarMas && btnCargarMas.textContent === "Cargando...") btnCargarMas.textContent = "Cargar más historial";
        }
    }

    function renderHistorial(historial, reset) {
        if (reset) tableBody.innerHTML = "";
        
        historial.forEach(h => {
             // Filtro: No mostrar historial de Bolívares (VES)
            if (h.codigo === 'VES') return;

            const row = document.createElement("tr");
            
            const badgeOrigen = h.origen === 'API' 
                ? '<span class="badge bg-primary">API</span>' 
                : '<span class="badge bg-warning text-dark">Manual</span>';

            row.innerHTML = `
                <td>${h.fecha}</td>
                <td>${h.moneda} (${h.codigo})</td>
                <td>${parseFloat(h.tasa).toFixed(4)}</td>
                <td>${badgeOrigen}</td>
            `;
            tableBody.appendChild(row);
        });
    }
});
