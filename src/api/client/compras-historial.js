import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const tbody_compras = document.getElementById("comprasRegistradas");

    // 1. Referencias a los elementos de filtro
    const filtro_codigo = document.getElementById("filtro-codigo");
    const filtro_factura = document.getElementById("filtro-factura");
    const filtro_fecha = document.getElementById("filtro-fecha");
    const filtro_total = document.getElementById("filtro-total");
    const filtro_proveedor = document.getElementById("filtro-proveedor");
    const filtro_empleado = document.getElementById("filtro-empleado");
    const filtro_sucursal = document.getElementById("filtro-sucursal");
    const filtro_estado = document.getElementById("filtro-estado");
    const reestablecer_filtros = document.getElementById("reestablecer-filtros");

    function crearFilaCompra(compra) {
        const fila = document.createElement("tr");

        let badgeClass;
        let estadoTexto;
        switch (compra.estado?.toLowerCase()) {
            case 'completada':
                badgeClass = 'bg-success';
                estadoTexto = 'Completada';
                break;
            case 'pendiente':
                badgeClass = 'bg-warning text-dark';
                estadoTexto = 'Pendiente';
                break;
            case 'en proceso':
                badgeClass = 'bg-info text-dark';
                estadoTexto = 'En Proceso';
                break;
            case 'cancelada':
                badgeClass = 'bg-danger';
                estadoTexto = 'Cancelada';
                break;
            case 'pagada':
                badgeClass = 'bg-primary';
                estadoTexto = 'Pagada';
                break;
            default:
                badgeClass = 'bg-secondary';
                estadoTexto = compra.estado || 'Desconocido';
        }

        const totalConMoneda = `$ ${parseFloat(compra.total || 0).toFixed(2)} ${compra.codigo_moneda || ''}`;

        fila.innerHTML = `
        <td>${compra.id_compra ?? ''}</td>
        <td>${compra.fecha_compra ?? ''}</td>
        <td>${compra.numero_factura ?? ''}</td>
        <td>${compra.nombre_proveedor ?? ''}</td>
        <td class="d-none d-lg-table-cell">${compra.nombre_empleado_responsable ?? ''}</td>
        <td class="d-none d-md-table-cell">${compra.nombre_sucursal ?? ''}</td>
        <td class="text-end">${totalConMoneda}</td>
        <td class="text-center">
            <span class="badge ${badgeClass}">${estadoTexto}</span>
        </td>
        <td class="text-center">
            <div class="container-fluid p-0">
                <div class="row g-1">
                    
                    <div class="col-6">
                        <form action="compras-editar" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id_compra" value="${compra.id_compra}">
                            <button type="submit" class="btn btn-warning btn-sm w-100">Editar</button>
                        </form>
                    </div>

                    <div class="col-6">
                        <form action="" method="POST" class="d-inline eliminar-compra-form">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_compra" value="${compra.id_compra}">
                            <button type="submit" class="btn btn-danger btn-sm w-100">Eliminar</button>
                        </form>
                    </div>
                    
                    <div class="col-12">
                        <form action="compras-detalle" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <input type="hidden" name="id_compra" value="${compra.id_compra}">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Ver Detalle</button>
                        </form>
                    </div>

                </div>
            </div>
        </td>
    `;

        return fila;
    }

    function renderizarCompras(compras) {
        tbody_compras.innerHTML = "";

        if (!compras || compras.length === 0) {
            // Mensaje específico cuando no hay resultados con filtros
            const mensajeFiltro = (
                filtro_codigo.value || filtro_factura.value || filtro_fecha.value ||
                filtro_total.value || filtro_proveedor.value || filtro_empleado.value ||
                filtro_sucursal.value || filtro_estado.value
            ) ? "No se encontraron compras con estas especificaciones." : "No se encontraron compras registradas.";

            tbody_compras.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center p-3">${mensajeFiltro}</td>
                </tr>
            `;
            return;
        }

        compras.forEach(compra => {
            const fila = crearFilaCompra(compra);
            tbody_compras.appendChild(fila);
        });

        document.querySelectorAll(".eliminar-compra-form").forEach(form => {
            form.addEventListener("submit", function (e) {
                const id_compra = this.querySelector('input[name="id_compra"]').value;
                if (!confirm(`¿Estás seguro de que quieres eliminar (desactivar) la compra ID ${id_compra}?`)) {
                    e.preventDefault();
                }
            });
        });
    }

    // Función de Debounce para limitar la frecuencia de llamadas a la API
    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    };

    /**
     * @function cargarCompras
     * Realiza la petición al backend, enviando los filtros actuales.
     */
    function cargarCompras() {
        const filtros = {
            accion: "obtener_historial_compras",
            codigo: filtro_codigo.value.trim(),
            factura: filtro_factura.value.trim(),
            fecha: filtro_fecha.value.trim(),
            total: filtro_total.value.trim(),
            proveedor: filtro_proveedor.value.trim(),
            empleado: filtro_empleado.value.trim(),
            sucursal: filtro_sucursal.value.trim(),
            estado: filtro_estado.value.trim()
        };

        tbody_compras.innerHTML = `
            <tr>
                <td colspan="9" class="text-center p-3">Cargando historial de compras...</td>
            </tr>
        `;

        api(filtros)
            .then(res => {
                // El backend devuelve 'compras', usamos eso para compatibilidad
                const compras = (res && (res.compras || res.data)) ? (res.compras || res.data) : [];

                if (res && res.error) {
                    console.error("Error desde PHP:", res.error || res.message || res.detalle);
                    renderizarCompras([]);
                    return;
                }

                renderizarCompras(compras);
            })
            .catch(err => {
                console.error("Error API al cargar compras:", err);
                renderizarCompras([]);
            });
    }

    // Función debounced para la aplicación asíncrona de filtros
    const aplicarFiltrosDebounced = debounce(cargarCompras, 300);

    // 2. Adjuntar Listeners para la aplicación asíncrona de filtros
    [filtro_codigo, filtro_factura, filtro_fecha, filtro_total, filtro_proveedor, filtro_empleado, filtro_sucursal].forEach(input => {
        input.addEventListener("keyup", aplicarFiltrosDebounced);
    });

    filtro_estado.addEventListener("change", cargarCompras);

    // 3. Listener para restablecer filtros
    reestablecer_filtros.addEventListener("click", () => {
        filtro_codigo.value = "";
        filtro_factura.value = "";
        filtro_fecha.value = "";
        filtro_total.value = "";
        filtro_proveedor.value = "";
        filtro_empleado.value = "";
        filtro_sucursal.value = "";
        filtro_estado.value = "";

        cargarCompras(); // Llama a la carga sin debounce para una respuesta inmediata
    });

    // Cargar las compras al inicio
    cargarCompras();
});