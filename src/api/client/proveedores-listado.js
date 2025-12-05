import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const inputNombre = document.getElementById("nombre-filtro");
    const inputCorreo = document.getElementById("correo-filtro");
    const selectEstado = document.getElementById("estado-filtro");
    const btnReestablecer = document.getElementById("reestablecer-filtros");
    const tablaProveedores = document.getElementById("lista_proveedores");

    function normalizeBoolean(value) {
        if (value === true || value === 1) return true;
        if (value === false || value === 0) return false;
        if (typeof value === "string") {
            const v = value.trim().toLowerCase();
            return (v === "t" || v === "true" || v === "1" || v === "yes" || v === "y");
        }
        return Boolean(value);
    }

    function renderizarProveedores(proveedores) {
        const tbody = document.querySelector("#lista_proveedores tbody");
        tbody.innerHTML = "";

        if (!proveedores || proveedores.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No se encontraron proveedores</td>
                </tr>
            `;
            return;
        }

        proveedores.forEach(prov => {
            const activoBool = normalizeBoolean(prov.activo);

            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${prov.id_proveedor ?? ""}</td>
                <td>${prov.nombre ?? ""}</td>
                <td>${prov.telefono ?? ""}</td>
                <td>${prov.correo ?? ""}</td>
                <td>
                    <span class="badge ${activoBool ? "bg-success" : "bg-danger"}">
                        ${activoBool ? "Activo" : "Inactivo"}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 flex-row justify-content-center align-items-center">
                        <form action="proveedores-detalle" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <input type="hidden" name="id_proveedor" value="${prov.id_proveedor ?? ""}">
                            <button type="submit" class="btn btn-sm btn-info text-white btn-action">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form action="proveedores-editar" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id_proveedor" value="${prov.id_proveedor ?? ""}">
                            <button type="submit" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                        <form action="" method="POST" class="d-inline eliminar-form">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_proveedor" value="${prov.id_proveedor ?? ""}">
                            <button type="submit" class="btn btn-sm btn-danger text-white btn-action">
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
                if (!confirm("¿Está seguro de eliminar (desactivar) este proveedor?")) {
                    e.preventDefault();
                    return;
                }
            });
        });
    }

    function aplicarFiltros() {
        const nombre = inputNombre.value.trim();
        const correo = inputCorreo.value.trim();
        const estado = selectEstado.value;

        api({
            accion: "obtener_todos_los_proveedores",
            nombre,
            correo,
            estado
        })
            .then(res => {
                const filas = (res && res.proveedor) ? res.proveedor : [];

                if (res && res.error) {
                    console.error("Error desde PHP:", res.error);
                    renderizarProveedores([]);
                    return;
                }

                renderizarProveedores(filas);
            })
            .catch(err => {
                console.error("Error al filtrar proveedores:", err);
                renderizarProveedores([]);
            });
    }

    aplicarFiltros();

    inputNombre.addEventListener("input", aplicarFiltros);
    inputCorreo.addEventListener("input", aplicarFiltros);
    selectEstado.addEventListener("change", aplicarFiltros);

    btnReestablecer.addEventListener("click", () => {
        inputNombre.value = "";
        inputCorreo.value = "";
        selectEstado.selectedIndex = 0;
        aplicarFiltros();
    });

});
