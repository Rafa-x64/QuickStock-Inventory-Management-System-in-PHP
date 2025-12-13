import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const tbody_compras = document.getElementById("comprasRegistradas");

    // 1. Referencias a los elementos de filtro
    const filtro_moneda = document.getElementById("filtro-moneda");
    const filtro_factura = document.getElementById("filtro-factura");
    const filtro_fecha = document.getElementById("filtro-fecha");
    const filtro_total = document.getElementById("filtro-total");
    const filtro_proveedor = document.getElementById("filtro-proveedor");
    // const filtro_empleado = document.getElementById("filtro-empleado"); // Se mantiene si existe en HTML
    // const filtro_sucursal = document.getElementById("filtro-sucursal"); // Se mantiene si existe en HTML
    const filtro_estado = document.getElementById("filtro-estado");
    const reestablecer_filtros = document.getElementById("reestablecer-filtros");
    
    // NOTA: Si en el HTML siguen existiendo input empleado/sucursal, referenciarlos
    const filtro_empleado = document.getElementById("filtro-empleado") || { value: "" }; 
    const filtro_sucursal = document.getElementById("filtro-sucursal") || { value: "" };

    // --- CARGA INICIAL DE DATOS PARA FILTROS (Monedas y Proveedores) ---
    function cargarDatosFiltros() {
        // Monedas
        api({ accion: "obtener_monedas" }).then(res => {
            if (res.monedas) {
                res.monedas.forEach(m => {
                    const op = document.createElement("option");
                    op.value = m.id_moneda;
                    op.textContent = `${m.nombre} (${m.simbolo})`;
                    filtro_moneda.appendChild(op);
                });
            }
        });

        // Proveedores (asumiendo que devuelve lista simple)
        api({ accion: "obtener_proveedores" }).then(res => {
             // La respuesta suele ser { proveedor: [...] } o { proveedores: [...] } según implementación
             const list = res.proveedor || res.proveedores || [];
             list.forEach(p => {
                 if (p.activo === 't' || p.activo === true || p.activo === 1) {
                     const op = document.createElement("option");
                     op.value = p.id_proveedor;
                     op.textContent = p.nombre;
                     filtro_proveedor.appendChild(op);
                 }
             });
        });
    }
    cargarDatosFiltros();


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
        <td>${compra.codigo_moneda ?? ''}</td> <!-- Mostrando la moneda en la col Codigo, o el ID si se prefiere -->
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
                    
                    <div class="col-12">
                        <form action="compras-editar" method="GET" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id_compra" value="${compra.id_compra}">
                            <button type="submit" class="btn btn-warning btn-sm w-100">Editar</button>
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
            tbody_compras.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center p-3">No se encontraron compras.</td>
                </tr>
            `;
            return;
        }

        compras.forEach(compra => {
            const fila = crearFilaCompra(compra);
            tbody_compras.appendChild(fila);
        });

        // Re-adjuntar listeners de eliminar si fuera el caso, aqui no hay eliminar en HTML de filaCompra pero por si acaso
        document.querySelectorAll(".eliminar-compra-form").forEach(form => {
            form.addEventListener("submit", function (e) {
                const id_compra = this.querySelector('input[name="id_compra"]').value;
                if (!confirm(`¿Estás seguro de que quieres eliminar (desactivar) la compra ID ${id_compra}?`)) {
                    e.preventDefault();
                }
            });
        });
    }

    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    };

    function cargarCompras() {
        const filtros = {
            accion: "obtener_historial_compras",
            moneda: filtro_moneda.value,       // Nuevo param ID
            factura: filtro_factura.value.trim(),
            fecha: filtro_fecha.value,         // Fecha YYYY-MM-DD del input date
            total: filtro_total.value.trim(),
            proveedor: filtro_proveedor.value, // Nuevo param ID
            empleado: filtro_empleado.value ? filtro_empleado.value.trim() : "",
            sucursal: filtro_sucursal.value ? filtro_sucursal.value.trim() : "",
            estado: filtro_estado.value
        };

        tbody_compras.innerHTML = `
            <tr>
                <td colspan="9" class="text-center p-3">Cargando historial...</td>
            </tr>
        `;

        api(filtros)
            .then(res => {
                const compras = (res && (res.compras || res.data)) ? (res.compras || res.data) : [];
                if (res && res.error) {
                    console.error("Error PHP:", res.error);
                    renderizarCompras([]);
                    return;
                }
                renderizarCompras(compras);
            })
            .catch(err => {
                console.error("Error API:", err);
                renderizarCompras([]);
            });
    }

    const aplicarFiltrosDebounced = debounce(cargarCompras, 300);

    // Adjuntar listeners (Change para selects/dates, Keyup para texto)
    filtro_moneda.addEventListener("change", cargarCompras);
    filtro_proveedor.addEventListener("change", cargarCompras);
    filtro_fecha.addEventListener("change", cargarCompras); // Input Date dispara change
    filtro_estado.addEventListener("change", cargarCompras);

    filtro_factura.addEventListener("keyup", aplicarFiltrosDebounced);
    filtro_total.addEventListener("keyup", aplicarFiltrosDebounced);
    
    // Si existen los inputs texto opcionales
    if(filtro_empleado.addEventListener) filtro_empleado.addEventListener("keyup", aplicarFiltrosDebounced);
    if(filtro_sucursal.addEventListener) filtro_sucursal.addEventListener("keyup", aplicarFiltrosDebounced);


    reestablecer_filtros.addEventListener("click", () => {
        filtro_moneda.value = "";
        filtro_factura.value = "";
        filtro_fecha.value = "";
        filtro_total.value = "";
        filtro_proveedor.value = "";
        if(filtro_empleado.value !== undefined) filtro_empleado.value = "";
        if(filtro_sucursal.value !== undefined) filtro_sucursal.value = "";
        filtro_estado.value = "";
        cargarCompras();
    });

    // Carga inicial
    cargarCompras();
});