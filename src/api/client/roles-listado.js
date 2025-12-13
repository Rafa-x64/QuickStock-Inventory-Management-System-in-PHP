import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const inputNombre = document.getElementById("nombre-filtro");
    const selectEstado = document.getElementById("estado-filtro");
    const btnReestablecer = document.getElementById("reestablecer-filtros");
    const tablaRoles = document.getElementById("lista_roles");

    function normalizeBoolean(value) {
        if (value === true || value === 1) return true;
        if (value === false || value === 0 || value === null || value === undefined) return false;
        if (typeof value === "string") {
            const v = value.trim().toLowerCase();
            return (v === "t" || v === "true" || v === "1" || v === "yes" || v === "y");
        }
        return false;
    }

    function renderizarRoles(roles) {
        const tbody = document.querySelector("#lista_roles tbody");
        tbody.innerHTML = "";

        if (!roles || roles.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">No se encontraron roles</td>
                </tr>
            `;
            return;
        }

        roles.forEach(rol => {
            const activoBool = normalizeBoolean(rol.activo);
            const usuariosAsignados = rol.usuarios_asignados ?? 0;

            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${rol.id_rol ?? ""}</td>
                <td><strong>${rol.nombre_rol ?? ""}</strong></td>
                <td>${rol.descripcion ?? "Sin descripción"}</td>
                <td>
                    <span class="badge bg-info">
                        <i class="bi bi-people me-1"></i>${usuariosAsignados} usuario(s)
                    </span>
                </td>
                <td>
                    <span class="badge ${activoBool ? "bg-success" : "bg-danger"}">
                        ${activoBool ? "Activo" : "Inactivo"}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 flex-row justify-content-center align-items-center">
                        <form action="empleados-detalle-rol" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <input type="hidden" name="id_rol" value="${rol.id_rol ?? ""}">
                            <button type="submit" class="btn btn-sm btn-info text-white btn-action">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form action="empleados-editar-rol" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id_rol" value="${rol.id_rol ?? ""}">
                            <button type="submit" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                        <form action="" method="POST" class="d-inline eliminar-form">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_rol" value="${rol.id_rol ?? ""}">
                            <button type="submit" class="btn btn-sm btn-danger text-white btn-action" ${usuariosAsignados > 0 ? 'disabled title="Tiene usuarios asignados"' : ''}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            `;

            tbody.appendChild(fila);
        });

        document.querySelectorAll(".eliminar-form").forEach(form => {
            form.addEventListener("submit", function (e) {
                if (!confirm("¿Está seguro de eliminar (desactivar) este rol?")) {
                    e.preventDefault();
                    return;
                }
            });
        });
    }

    function aplicarFiltros() {
        const nombre = inputNombre.value.trim();
        const estado = selectEstado.value;

        api({
            accion: "obtener_todos_los_roles",
            nombre,
            estado
        })
            .then(res => {
                const filas = (res && res.rol) ? res.rol : [];

                if (res && res.error) {
                    console.error("Error desde PHP:", res.error);
                    renderizarRoles([]);
                    return;
                }

                renderizarRoles(filas);
            })
            .catch(err => {
                console.error("Error al filtrar roles:", err);
                renderizarRoles([]);
            });
    }

    aplicarFiltros();

    inputNombre.addEventListener("input", aplicarFiltros);
    selectEstado.addEventListener("change", aplicarFiltros);

    btnReestablecer.addEventListener("click", () => {
        inputNombre.value = "";
        selectEstado.selectedIndex = 0;
        aplicarFiltros();
    });

});
