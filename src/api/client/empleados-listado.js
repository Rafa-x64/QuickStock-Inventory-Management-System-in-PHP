import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const select_sucursal = document.getElementById("sucursal-filtro");
    const select_cargo = document.getElementById("cargo-filtro");
    const select_estado = document.getElementById("estado-filtro");
    const reestablecer_filtros = document.getElementById("reestablecer-filtros");
    const tabla_empleados = document.getElementById("lista_empleados");

    api({ accion: "obtener_sucursales" })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener sucursales:", res.error);
                return;
            }
            (res.filas || res.data || []).forEach(sucursal => {
                const opt = document.createElement("option");
                opt.value = sucursal.id_sucursal;
                opt.textContent = sucursal.nombre;
                select_sucursal.appendChild(opt);
            });
        })
        .catch(err => console.error("Error API sucursales:", err));

    api({ accion: "obtener_roles" })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener roles:", res.error);
                return;
            }
            (res.filas || res.data || []).forEach(rol => {
                const opt = document.createElement("option");
                opt.value = rol.id_rol;
                opt.textContent = rol.nombre_rol;
                select_cargo.appendChild(opt);
            });
        })
        .catch(err => console.error("Error API roles:", err));

    function normalizeBoolean(value) {
        // Normaliza distintos formatos posibles que vienen desde PG / PHP
        // Accepts: true, false, "t", "f", "true", "false", "1", "0", 1, 0
        if (value === true || value === 1) return true;
        if (value === false || value === 0 || value === null || value === undefined) return false;
        if (typeof value === "string") {
            const v = value.trim().toLowerCase();
            return (v === "t" || v === "true" || v === "1" || v === "yes" || v === "y");
        }
        // Default: false (más seguro que Boolean() que convierte cualquier string a true)
        return false;
    }

    function renderizarEmpleados(empleados) {
        const tbody = document.querySelector("#lista_empleados tbody");
        tbody.innerHTML = "";

        if (!empleados || empleados.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">No se encontraron empleados</td>
                </tr>
            `;
            return;
        }

        empleados.forEach(emp => {
            const activoBool = normalizeBoolean(emp.activo);

            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${emp.id_usuario ?? ""}</td>
                <td>${(emp.nombre ?? "") + " " + (emp.apellido ?? "")}</td>
                <td>${emp.nombre_rol ?? ""}</td>
                <td>${emp.sucursal_nombre ?? "Sin asignar"}</td>
                <td>${emp.telefono ?? ""}</td>
                <td>${emp.cedula ?? ""}</td>
                <td>
                    <span class="badge ${activoBool ? "bg-success" : "bg-danger"}">
                        ${activoBool ? "Activo" : "Inactivo"}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 flex-row justify-content-center align-items-center">
                        <form action="empleados-detalle" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <input type="hidden" name="email" value="${emp.email ?? ""}">
                            <button type="submit" class="btn btn-sm btn-info text-white btn-action">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form action="empleados-editar" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="email" value="${emp.email ?? ""}">
                            <button type="submit" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                        <form action="" method="POST" class="d-inline eliminar-form">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="email" value="${emp.email ?? ""}">
                            <button type="submit" class="btn btn-sm btn-danger text-white btn-action">
                                <i class="bi bi-person-x"></i>
                            </button>
                        </form>
                    </div>
                </td>
            `;

            tbody.appendChild(fila);
        });

        // Opcional: interceptar los formularios de eliminar para confirmar y luego refrescar lista vía JS
        // Si quieres mantener envío normal (POST y recarga), puedes quitar este bloque.
        document.querySelectorAll(".eliminar-form").forEach(form => {
            form.addEventListener("submit", function (e) {
                // Si prefieres confirmación con diálogo nativo:
                if (!confirm("¿Eliminar (desactivar) este empleado?")) {
                    e.preventDefault();
                    return;
                }
                // Si quieres hacer la eliminación por API y refrescar sin recargar:
                // e.preventDefault();
                // const formData = new FormData(this);
                // fetch('ruta_a_tu_endpoint_de_eliminar', { method: 'POST', body: formData })
                //   .then(r => r.json()).then(resp => { aplicarFiltros(); alert('Empleado eliminado'); })
                //   .catch(err => console.error(err));
            });
        });
    }

    function aplicarFiltros() {
        const sucursal = select_sucursal.value;
        const rol = select_cargo.value;
        const estado = select_estado.value;

        api({
            accion: "obtener_todos_los_empleados",
            sucursal,
            rol,
            estado
        })
            .then(res => {
                // soporte tanto res.filas (antes) como res.data (si usaste la versión que devuelve data)
                const filas = (res && (res.filas || res.data)) ? (res.filas || res.data) : [];

                if (res && res.error) {
                    console.error("Error desde PHP:", res.error || res.message || res.detalle);
                    renderizarEmpleados([]);
                    return;
                }

                renderizarEmpleados(filas);
            })
            .catch(err => {
                console.error("Error al filtrar usuarios:", err);
                renderizarEmpleados([]);
            });
    }

    aplicarFiltros();

    select_sucursal.addEventListener("change", aplicarFiltros);
    select_cargo.addEventListener("change", aplicarFiltros);
    select_estado.addEventListener("change", aplicarFiltros);

    reestablecer_filtros.addEventListener("click", () => {
        select_sucursal.selectedIndex = 0;
        select_cargo.selectedIndex = 0;
        select_estado.selectedIndex = 0;
        aplicarFiltros();
    });

});
