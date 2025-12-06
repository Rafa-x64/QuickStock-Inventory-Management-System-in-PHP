import { api } from '/DEV/PHP/QuickStock/src/api/client/index.js';

document.addEventListener("DOMContentLoaded", () => {
    const tabla = document.getElementById("tabla_monedas");
    const tbody = tabla.querySelector("tbody");

    function renderizar(monedas) {
        tbody.innerHTML = "";
        if (!monedas || monedas.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">No hay monedas registradas.</td></tr>`;
            return;
        }

        monedas.forEach(m => {
            const tr = document.createElement("tr");
            
            // Estado badge
            const activo = (m.activo === true || m.activo === 't' || m.activo === 1);
            const badge = activo 
                ? `<span class="badge bg-success">Activo</span>`
                : `<span class="badge bg-secondary">Inactivo</span>`;

            tr.innerHTML = `
                <td>${m.id_moneda}</td>
                <td>${m.nombre}</td>
                <td><span class="fw-bold">${m.codigo}</span></td>
                <td>${m.simbolo}</td>
                <td>${badge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary btn-editar" data-id="${m.id_moneda}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <!-- Mas acciones si se requieren -->
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Listeners botones
        document.querySelectorAll(".btn-editar").forEach(btn => {
            btn.addEventListener("click", () => {
                alert("Para editar, por ahora use la base de datos o implemente la vista de edición completa.");
                // O redirigir a monedas-editar?accion=editar&id...
            });
        });
    }

    function cargarMonedas() {
        api({ accion: "obtener_todas_monedas" })
            .then(res => {
                if (res.error) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error: ${res.error}</td></tr>`;
                    return;
                }
                renderizar(res.monedas || res.filas || res.data || []);
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error de conexión</td></tr>`;
            });
    }

    cargarMonedas();
});
