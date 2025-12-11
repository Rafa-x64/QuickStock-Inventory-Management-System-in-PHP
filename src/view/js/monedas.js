import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

/**
 * Lógica para las vistas de Monedas y Tasas (Admin/Gerente)
 */
document.addEventListener("DOMContentLoaded", () => {

    // Identificar en qué vista estamos
    const containerCards = document.getElementById("container-tasas-cards");
    const tablaHistorial = document.getElementById("tabla_historial_tasas");
    const formManual = document.getElementById("form-tasa-manual");
    const btnSync = document.getElementById("btn-sync-api");

    // ==============================================
    // VISTA: TASAS ACTIVAS (Dashboard)
    // ==============================================

    if (containerCards) {
        cargarResumenTasas();

        if (btnSync) {
            btnSync.addEventListener("click", () => {
                const originalText = btnSync.innerHTML;
                btnSync.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Sincronizando...`;
                btnSync.disabled = true;

                api({ accion: "sincronizar_api" })
                    .then(res => {
                        if (res.error) {
                            alert("Error al sincronizar: " + res.error);
                        } else {
                            alert(res.msg || "Sincronización completada.");
                            cargarResumenTasas(); // Recargar UI
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        alert("Error de conexión al sincronizar.");
                    })
                    .finally(() => {
                        btnSync.innerHTML = originalText;
                        btnSync.disabled = false;
                    });
            });
        }
    }

    // Variable para almacenar el ID del Bolívar (VES) para el proxy de USD
    let id_ves_guardado = null;

    if (formManual) {
        // Cargar select de monedas al inicio
        cargarSelectMonedas();

        formManual.addEventListener("submit", (e) => {
            e.preventDefault();
            let id_moneda = document.getElementById("select_moneda_manual").value;
            const tasa = document.getElementById("valor_manual").value;

            if (!id_moneda || !tasa) {
                alert("Seleccione moneda e ingrese tasa.");
                return;
            }

            // Lógica Proxy: Si el usuario seleccionó "Dólar (USD)", en realidad quiere definir
            // cuántos Bs cuesta 1 Dólar. En nuestro sistema Base USD, esto significa actualizar
            // la tasa del Bolívar (VES). 
            // Ejemplo: Usurio pone USD = 60. Sistema guarda VES = 60.
            // Calculo visual: 60 (VES) / 1 (USD) = 60 Bs. Correcto.
            
            // Verificamos si la opción seleccionada es USD (buscando texto o flag, pero mejor usamos el ID si lo capturamos)
            // Como permitimos USD en el select, su ID vendrá en id_moneda.
            // Pero necesitamos saber si ESE id corresponde a USD.
            // Lo resolvemos simple: Si el value del select coincide con el ID de USD que cargamos, hacemos el switch.
            
            // Mejor enfoque: En el select guardamos el codigo como data-attribute
            const selectedOption = document.getElementById("select_moneda_manual").options[document.getElementById("select_moneda_manual").selectedIndex];
            const codigoMoneda = selectedOption.getAttribute("data-codigo");

            if (codigoMoneda === 'USD') {
                if (id_ves_guardado) {
                    id_moneda = id_ves_guardado; // Switcheamos a VES
                    // Nota: No cambiamos la tasa, porque si puso 60, guarda 60 en VES.
                } else {
                    alert("Error: No se encontró la moneda base (VES) para realizar la conversión.");
                    return;
                }
            }

            if (confirm(`¿Confirmar cambio de tasa manual?`)) {
                api({
                    accion: "guardar_tasa_manual",
                    id_moneda: id_moneda,
                    tasa: tasa
                })
                .then(res => {
                    if (res.error) {
                        alert("Error: " + res.error);
                    } else {
                        alert("Tasa actualizada y notificada.");
                        formManual.reset();
                        if (containerCards) cargarResumenTasas();
                    }
                })
                .catch(err => console.error(err));
            }
        });
    }

    // ==============================================
    // VISTA: HISTORIAL
    // ==============================================
    if (tablaHistorial) {
        cargarHistorial();
    }


    // ==============================================
    // FUNCIONES AUXILIARES
    // ==============================================

    function cargarResumenTasas() {
        if (!containerCards) return;
        containerCards.innerHTML = `<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"></div></div>`;

        api({ accion: "obtener_resumen_tasas" })
            .then(res => {
                containerCards.innerHTML = "";
                if (res.error) {
                    containerCards.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
                    return;
                }

                const data = res.data || [];
                if (data.length === 0) {
                    containerCards.innerHTML = `<div class="alert alert-info">No hay monedas activas registradas.</div>`;
                    return;
                }

                data.forEach(item => {
                    // Ocultar VES (Moneda Nativa - no se muestra en tarjetas)
                    if (item.codigo === 'VES') return;

                    const card = document.createElement("div");
                    card.className = "col-12 col-sm-6 col-md-4 col-lg-3 mb-4";
                    
                    const isApi = (item.origen === 'API');
                    const badgeClass = isApi ? "bg-info text-dark" : "bg-warning text-dark";

                    // El backend ya devuelve la tasa en Bolívares directamente
                    // No se requiere cálculo adicional
                    const tasaVisual = parseFloat(item.tasa);

                    card.innerHTML = `
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body text-center">
                                <h5 class="card-title text-primary fw-bold">${item.nombre} (${item.codigo})</h5>
                                <h2 class="display-6 my-3 text-dark">Bs. ${tasaVisual.toFixed(4)}</h2>
                                <p class="card-text text-muted small">
                                    <span class="badge ${badgeClass}">${item.origen || 'Manual'}</span><br>
                                    Actualizado: ${formatDate(item.fecha)}
                                </p>
                            </div>
                        </div>
                    `;
                    containerCards.appendChild(card);
                });
            })
            .catch(err => {
                console.error(err);
                containerCards.innerHTML = `<div class="alert alert-danger">Error al cargar tasas.</div>`;
            });
    }

    // Solución al problema de fechas: Parsear manualmente YYYY-MM-DD para evitar conversión UTC->Local que resta un día
    function formatDate(dateString) {
        if (!dateString) return "Sin fecha";
        const parts = dateString.split('-'); // [YYYY, MM, DD]
        if (parts.length !== 3) return dateString; 
        
        // Crear fecha en hora local (Media noche local)
        // Mes en JS es 0-indexado
        const date = new Date(parts[0], parts[1] - 1, parts[2]); 
        
        // Formatear a string local amigable
        return date.toLocaleDateString(); 
    }

    function cargarSelectMonedas() {
        const select = document.getElementById("select_moneda_manual");
        if (!select) return;
        select.innerHTML = '<option value="">Cargando...</option>';

        api({ accion: "obtener_resumen_tasas" }) // Reusamos este endpoint que trae monedas activas
            .then(res => {
                select.innerHTML = '<option value="">Seleccione una moneda...</option>';
                if (res.data) {
                    res.data.forEach(m => {
                        // Capturar ID de VES para el proxy
                        if (m.codigo === 'VES') {
                            id_ves_guardado = m.id_moneda;
                            return; // Ocultar VES del dropdown
                        }

                        // Permitir USD (Eliminamos el return que lo escondía)
                        // if (m.codigo === 'USD') return; 

                        const opt = document.createElement("option");
                        opt.value = m.id_moneda; 
                        opt.setAttribute("data-codigo", m.codigo); // Importante para la detección
                        opt.textContent = `${m.nombre} (${m.codigo})`;
                        select.appendChild(opt);
                    });
                }
            });
    }

    function cargarHistorial() {
        const tbody = tablaHistorial.querySelector("tbody");
        tbody.innerHTML = `<tr><td colspan="5" class="text-center">Cargando...</td></tr>`;

        api({ accion: "obtener_historial_tasas" })
            .then(res => {
                tbody.innerHTML = "";
                if (res.error) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-danger">${res.error}</td></tr>`;
                    return;
                }
                const filas = res.data || [];
                if (filas.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center">No hay historial.</td></tr>`;
                    return;
                }

                filas.forEach(f => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${f.fecha}</td>
                        <td>${f.nombre} (${f.codigo})</td>
                        <td>${f.simbolo} ${parseFloat(f.tasa).toFixed(4)}</td>
                        <td><span class="badge ${f.origen === 'API' ? 'bg-info' : 'bg-warning'} text-dark">${f.origen}</span></td>
                        <td><small class="text-muted">${f.id_tasa}</small></td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="5" class="text-danger">Error de conexión</td></tr>`;
            });
    }

});
