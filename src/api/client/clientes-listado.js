import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
  const inputNombre = document.getElementById("nombre-filtro");
  const inputApellido = document.getElementById("apellido-filtro");
  const inputCedula = document.getElementById("cedula-filtro");
  const selectEstado = document.getElementById("estado-filtro");
  const btnReestablecer = document.getElementById("reestablecer-filtros");
  const tablaClientes = document.getElementById("lista_clientes");

  function normalizeBoolean(value) {
    if (value === true || value === 1) return true;
    if (value === false || value === 0 || value === null || value === undefined) return false;
    if (typeof value === "string") {
      const v = value.trim().toLowerCase();
      return v === "t" || v === "true" || v === "1" || v === "yes" || v === "y";
    }
    return false;
  }

  function renderizarClientes(clientes) {
    const tbody = document.querySelector("#lista_clientes tbody");
    tbody.innerHTML = "";

    if (!clientes || clientes.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">No se encontraron clientes</td>
                </tr>
            `;
      return;
    }

    clientes.forEach((cli) => {
      const activoBool = normalizeBoolean(cli.activo);

      const fila = document.createElement("tr");
      fila.innerHTML = `
                <td>${cli.id_cliente ?? ""}</td>
                <td>${cli.nombre ?? ""}</td>
                <td>${cli.apellido ?? ""}</td>
                <td>${cli.cedula ?? ""}</td>
                <td>${cli.telefono ?? ""}</td>
                <td>${cli.correo ?? ""}</td>
                <td>
                    <span class="badge ${
                      activoBool ? "bg-success" : "bg-danger"
                    }">
                        ${activoBool ? "Activo" : "Inactivo"}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 flex-row justify-content-center align-items-center">
                        <form action="clientes-detalle" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <input type="hidden" name="id_cliente" value="${
                              cli.id_cliente ?? ""
                            }">
                            <button type="submit" class="btn btn-sm btn-info text-white btn-action">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form action="clientes-editar" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id_cliente" value="${
                              cli.id_cliente ?? ""
                            }">
                            <button type="submit" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                        <form action="" method="POST" class="d-inline eliminar-form">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_cliente" value="${
                              cli.id_cliente ?? ""
                            }">
                            <button type="submit" class="btn btn-sm btn-danger text-white btn-action">
                                <i class="bi bi-person-x"></i>
                            </button>
                        </form>
                    </div>
                </td>
            `;

      tbody.appendChild(fila);
    });

    document.querySelectorAll(".eliminar-form").forEach((form) => {
      form.addEventListener("submit", function (e) {
        if (!confirm("¿Está seguro de eliminar (desactivar) este cliente?")) {
          e.preventDefault();
          return;
        }
      });
    });
  }

  function aplicarFiltros() {
    const nombre = inputNombre.value.trim();
    const apellido = inputApellido.value.trim();
    const cedula = inputCedula.value.trim();
    const estado = selectEstado.value;

    api({
      accion: "obtener_todos_los_clientes",
      nombre,
      apellido,
      cedula,
      estado,
    })
      .then((res) => {
        const filas = res && res.cliente ? res.cliente : [];

        if (res && res.error) {
          console.error("Error desde PHP:", res.error);
          renderizarClientes([]);
          return;
        }

        renderizarClientes(filas);
      })
      .catch((err) => {
        console.error("Error al filtrar clientes:", err);
        renderizarClientes([]);
      });
  }

  aplicarFiltros();

  inputNombre.addEventListener("input", aplicarFiltros);
  inputApellido.addEventListener("input", aplicarFiltros);
  inputCedula.addEventListener("input", aplicarFiltros);
  selectEstado.addEventListener("change", aplicarFiltros);

  btnReestablecer.addEventListener("click", () => {
    inputNombre.value = "";
    inputApellido.value = "";
    inputCedula.value = "";
    selectEstado.selectedIndex = 0;
    aplicarFiltros();
  });
});
