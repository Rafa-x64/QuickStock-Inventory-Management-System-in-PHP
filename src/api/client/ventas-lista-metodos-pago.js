import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    // 1. Obtener referencias a todos los elementos de filtro
    const filtroNombre = document.getElementById("filtro-nombre");
    const filtroReferencia = document.getElementById("filtro-referencia");
    const filtroEstado = document.getElementById("filtro-estado");
    const tablaBody = document.querySelector("#tabla-metodos-pago tbody");

    const renderizarTabla = (metodos) => {
        tablaBody.innerHTML = "";

        if (metodos.length === 0) {
            tablaBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No hay métodos de pago con estas características</td>
                </tr>
            `;
            return;
        }

        metodos.forEach(metodo => {
            // Nota: En PostgreSQL, los booleanos son 't' (true) o 'f' (false)
            const activo = metodo.activo === 't' || metodo.activo === true;
            const referencia = metodo.referencia === 't' || metodo.referencia === true;
            
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${metodo.id_metodo_pago}</td>
                <td>${metodo.nombre}</td>
                <td>${metodo.descripcion || '-'}</td>
                <td>${referencia ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'}</td>
                <td><span class="badge ${activo ? 'bg-success' : 'bg-danger'}">${activo ? 'Activo' : 'Inactivo'}</span></td>
                <td>
                    <div class="d-flex flex-row justify-content-center align-items-center gap-2">
                        <form action="ventas-detalle-metodo-pago" method="POST" class="d-inline">
                            <input type="hidden" name="id_metodo_pago" value="${metodo.id_metodo_pago}">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <button type="submit" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form action="ventas-editar-metodo-pago" method="POST" class="d-inline">
                            <input type="hidden" name="id_metodo_pago" value="${metodo.id_metodo_pago}">
                            <input type="hidden" name="accion" value="editar">
                            <button type="submit" class="btn btn-sm btn-warning text-white">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                        <form action="" method="POST" class="d-inline form-eliminar">
                            <input type="hidden" name="id_metodo_pago" value="${metodo.id_metodo_pago}">
                            <input type="hidden" name="accion" value="eliminar">
                            <button type="submit" class="btn btn-sm btn-danger text-white">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            `;
            tablaBody.appendChild(row);
        });

        document.querySelectorAll(".form-eliminar").forEach(form => {
            form.addEventListener("submit", (e) => {
                if (!confirm("¿Estás seguro de eliminar este método de pago?")) {
                    e.preventDefault();
                }
            });
        });
    };

    const cargarMetodos = () => {
        // 2. Leer valores de los filtros
        const filtroNombreVal = filtroNombre.value.trim();
        const filtroReferenciaVal = filtroReferencia.value;
        const filtroEstadoVal = filtroEstado.value;
        
        // 3. Enviar todos los filtros a la API
        api({ 
            accion: "obtener_metodos_pago", 
            filtro: filtroNombreVal,
            referencia: filtroReferenciaVal,
            estado: filtroEstadoVal
        })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener métodos de pago:", res.error);
                renderizarTabla([]);
                return;
            }
            const data = res.data || res.filas || [];
            renderizarTabla(data);
        })
        .catch(err => {
            console.error("Error API métodos de pago:", err);
            renderizarTabla([]);
        });
    };

    // 4. Agregar listeners a los nuevos filtros
    filtroNombre.addEventListener("input", cargarMetodos);
    filtroReferencia.addEventListener("change", cargarMetodos); // 'change' para select
    filtroEstado.addEventListener("change", cargarMetodos); // 'change' para select
    
    // Cargar inicial
    cargarMetodos();
});