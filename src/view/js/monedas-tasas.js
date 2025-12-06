import { api } from "../../api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector("#tabla_tasas tbody");
    const btnSincronizar = document.getElementById("btn-sincronizar");
    const containerModals = document.getElementById("modals-container");

    // Cargar tasas al inicio
    cargarTasas();

    // Evento Sincronizar
    if (btnSincronizar) {
        btnSincronizar.addEventListener("click", async () => {
             btnSincronizar.disabled = true;
             btnSincronizar.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Sincronizando...';
             
             try {
                 const res = await api({ accion: "sincronizar_tasas_api" });
                 if (res.status === "success") {
                     alert("Sincronización exitosa: " + res.msg);
                     cargarTasas();
                 } else {
                     alert("Error: " + res.msg);
                 }
             } catch (e) {
                 console.error(e);
                 alert("Error de conexión al sincronizar.");
             } finally {
                 btnSincronizar.disabled = false;
                 btnSincronizar.innerHTML = '<i class="bi bi-cloud-arrow-down"></i> Sincronizar Ahora';
             }
        });
    }

    async function cargarTasas() {
        try {
            const res = await api({ accion: "obtener_tasas_cambio" });
            if (res.status && res.tasas) {
                renderTasas(res.tasas);
            } else {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay tasas activas.</td></tr>';
            }
        } catch (e) {
            console.error(e);
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error cargando tasas.</td></tr>';
        }
    }

    function renderTasas(tasas) {
        tableBody.innerHTML = "";
        tasas.forEach(t => {
            const row = document.createElement("tr");
            
            // Determinar badge de origen (si el backend lo devuelve, si no, asumir algo o nada)
            // (Asumimos que el backend 'obtener_tasas_cambio' fue actualizado para devolver 'origen' si modificamos la consulta SQL en tasa_cambio.php o usamos el nuevo modelo)
            // NOTA: El endpoint 'obtener_tasas_cambio' en index.php llamaba a 'obtenerTasasCambioActivas' del archivo antiguo o nuevo?
            // Revisamos index.php: include_once __DIR__ . "/finanzas/tasa_cambio.php"; 
            // Necesitamos asegurarnos que ese archivo devuelva el origen o cambiar index.php para usar TasaCambio::obtenerTasasActivas (si existe) o modificar el archivo viejo.
            // Para asegurar funcionalidad, modificaremos el archivo viejo o haremos que index.php use el Nuevo Modelo si tiene método equivalente. 
            // La función 'obtenerTasasCambioActivas' en 'finanzas/tasa_cambio.php' usaba SQL directo. Debería funcionar si la tabla tiene la columna, pero debemos agregarla al SELECT.
            
            // Asumiremos que arreglamos el backend para traer 'origen'.
            const origenBadge = t.origen === 'API' 
                ? '<span class="badge bg-primary">API</span>' 
                : '<span class="badge bg-warning text-dark">Manual</span>';

            row.innerHTML = `
                <td>${t.nombre}</td>
                <td><strong>${t.codigo}</strong></td>
                <td class="fs-5">${parseFloat(t.tasa).toFixed(4)}</td>
                <td>${t.fecha || 'Hoy'}</td>
                <td>${origenBadge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-warning btn-editar" 
                        data-id="${t.id_moneda}" 
                        data-nombre="${t.nombre}" 
                        data-codigo="${t.codigo}" 
                        data-tasa="${t.tasa}">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Listeners para editar
        document.querySelectorAll(".btn-editar").forEach(btn => {
            btn.addEventListener("click", () => abrirModalEditar(btn.dataset));
        });
    }

    function abrirModalEditar(data) {
        // Crear modal dinámicamente o usar uno existente
        // Para mantener estética QuickStock, usaremos un modal simple inyectado
        const modalHtml = `
            <div class="modal fade" id="modalEditarTasa" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Tasa: ${data.nombre}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form-editar-tasa">
                                <input type="hidden" name="id_moneda" value="${data.id}">
                                <div class="mb-3">
                                    <label class="form-label">Tasa de Cambio (vs USD)</label>
                                    <input type="number" step="0.0001" class="form-control" name="valor" value="${data.tasa}" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btn-guardar-manual">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        containerModals.innerHTML = modalHtml;
        const modalEl = document.getElementById("modalEditarTasa");
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        document.getElementById("btn-guardar-manual").onclick = async () => {
            const form = document.getElementById("form-editar-tasa");
            const valor = form.valor.value;
            const id_moneda = form.id_moneda.value;

            try {
                const res = await api({
                    accion: "registrar_tasa",
                    id_moneda: id_moneda,
                    valor: valor
                });

                if (res.status) {
                    alert("Tasa actualizada y notificada.");
                    modal.hide();
                    cargarTasas();
                } else {
                    alert("Error: " + res.mensaje);
                }
            } catch (e) {
                console.error(e);
                alert("Error de conexión");
            }
        };
    }

});
