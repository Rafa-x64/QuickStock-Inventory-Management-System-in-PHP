import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    // 1. Obtener el ID de Compra del formulario POST inicial (PHP)
    const id_compra_input = document.getElementById('id_compra_detalle');
    // Si no existe, intenta tomarlo de la URL o de otra fuente, pero para el ejemplo usamos el input.
    const id_compra = id_compra_input ? id_compra_input.value : null;

    // Elementos de la vista a actualizar
    const breadcrumb_active = document.querySelector('nav .breadcrumb .breadcrumb-item:last-child'); // Cambiado a last-child para robustez
    const tablaProductos = document.getElementById("tablaProductos");
    const cantidadItems = document.getElementById("cantidadItems");

    // Elementos de Información General, Observaciones y Totales.
    // Usamos IDs para mayor robustez en lugar de :nth-child
    const fields = {
        // Información General
        'id_compra': document.getElementById('info_id_compra'),
        'fecha_compra': document.getElementById('info_fecha_compra'),
        'nombre_proveedor': document.getElementById('info_nombre_proveedor'),
        'nombre_sucursal': document.getElementById('info_nombre_sucursal'),
        'nombre_empleado_responsable': document.getElementById('info_empleado_responsable'),
        'codigo_moneda': document.getElementById('info_moneda'),
        'estado_badge_container': document.getElementById('info_estado'),
        'numero_factura': document.getElementById('info_numero_factura'), // Nuevo campo en la vista

        // Observaciones (es un textarea, se usa value)
        'observaciones': document.getElementById('info_observaciones'),

        // Totales (Generales y Footers)
        'subtotal_general': document.getElementById('total_subtotal'),
        'monto_impuesto': document.getElementById('total_impuesto'),
        'total_general': document.getElementById('total_total'),

        'footSubtotal': document.getElementById('footSubtotal'),
        'footImpuesto': document.getElementById('footImpuesto'),
        'footTotal': document.getElementById('footTotal'),
    };

    // Función auxiliar para formato de moneda (VES ya no está hardcodeado en la imagen)
    function formatCurrency(amount, currencyCode = '') {
        const num = parseFloat(amount || 0);
        // Usamos Intl.NumberFormat para mejor formato si es posible, sino toFixed(2)
        try {
            return new Intl.NumberFormat('es-VE', {
                style: 'currency',
                currency: currencyCode || 'USD',
                minimumFractionDigits: 2
            }).format(num);
        } catch (e) {
            return `${num.toFixed(2)} ${currencyCode}`;
        }
    }

    // Función auxiliar para generar el badge de estado
    function getEstadoBadge(estado) {
        let badgeClass;
        let estadoTexto;
        switch (estado?.toLowerCase()) {
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
                estadoTexto = estado || 'Desconocido';
        }
        // Retorna el HTML del badge para asignarlo a innerHTML
        return `<span class="badge ${badgeClass} w-100">${estadoTexto}</span>`;
    }

    function renderizarDetalle(data) {
        if (!data || !data.compra) {
            console.error("Detalle de compra no encontrado o inválido.");
            // Actualizar la vista para reflejar el error
            Object.values(fields).forEach(el => {
                // Solo actualiza si el elemento existe
                if (el) {
                    if (el.tagName === 'TEXTAREA') {
                        el.value = 'Error al cargar';
                    } else {
                        el.textContent = 'Error al cargar';
                    }
                }
            });
            if (breadcrumb_active) breadcrumb_active.textContent = 'Error';
            if (tablaProductos) tablaProductos.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error: No se pudo cargar el detalle de la compra.</td></tr>`;
            if (cantidadItems) cantidadItems.textContent = '0 Ítems';
            return;
        }

        const compra = data.compra;
        const detalles = data.detalles || [];
        const moneda = compra.codigo_moneda || 'VES'; // Usar un código por defecto si no viene

        // 1. Actualizar Breadcrumb
        if (breadcrumb_active) breadcrumb_active.textContent = `Detalle Compra #${compra.id_compra}`;

        // 2. Actualizar Información General

        // ¡IMPORTANTE! Asignar valores a los elementos con .textContent (para divs) o .value (para textarea)
        if (fields.id_compra) fields.id_compra.textContent = compra.id_compra || 'N/A';
        if (fields.fecha_compra) fields.fecha_compra.textContent = compra.fecha_compra || 'N/A';
        if (fields.nombre_proveedor) fields.nombre_proveedor.textContent = compra.nombre_proveedor || 'N/A';
        if (fields.nombre_sucursal) fields.nombre_sucursal.textContent = compra.nombre_sucursal || 'N/A';
        if (fields.nombre_empleado_responsable) fields.nombre_empleado_responsable.textContent = compra.nombre_empleado_responsable || 'N/A';
        if (fields.codigo_moneda) fields.codigo_moneda.textContent = moneda;
        if (fields.numero_factura) fields.numero_factura.textContent = compra.numero_factura || 'N/A';

        // El estado es un badge, se usa innerHTML
        if (fields.estado_badge_container) fields.estado_badge_container.innerHTML = getEstadoBadge(compra.estado);

        // Las observaciones son un textarea, se usa value
        if (fields.observaciones) fields.observaciones.value = compra.observaciones || 'Sin observaciones.';

        // 3. Actualizar Totales (Cuerpo)
        if (fields.subtotal_general) fields.subtotal_general.textContent = formatCurrency(compra.subtotal, moneda);
        if (fields.monto_impuesto) fields.monto_impuesto.textContent = formatCurrency(compra.monto_impuesto, moneda);
        if (fields.total_general) fields.total_general.textContent = formatCurrency(compra.total, moneda);

        // 4. Renderizar Tabla de Productos
        if (tablaProductos) {
            tablaProductos.innerHTML = ''; // Limpiar la tabla
            if (detalles.length === 0) {
                tablaProductos.innerHTML = `<tr><td colspan="8" class="text-center py-4">Esta compra no tiene productos registrados.</td></tr>`;
            } else {
                detalles.forEach((detalle, index) => {
                    // Usamos .subtotal del detalle para evitar confusiones, aunque la consulta PHP lo llama 'subtotal'
                    const subtotalDetalle = detalle.subtotal;
                    const precioUnitario = formatCurrency(detalle.precio_unitario, moneda);
                    const subtotalLinea = formatCurrency(subtotalDetalle, moneda); // Asumo que es el subtotal de la línea

                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${detalle.nombre_producto || 'Producto Desconocido'}</td>
                        <td>${detalle.nombre_categoria || 'N/A'}</td>
                        <td>${detalle.nombre_color || 'N/A'}</td>
                        <td>${detalle.nombre_talla || 'N/A'}</td>
                        <td>${detalle.cantidad}</td>
                        <td class="text-end">${precioUnitario}</td>
                        <td class="text-end">${subtotalLinea}</td>
                    `;
                    tablaProductos.appendChild(fila);
                });
            }
        }

        // 5. Actualizar Cantidad de Ítems y Footers de la Tabla
        if (cantidadItems) cantidadItems.textContent = `${detalles.length} ${detalles.length === 1 ? 'Ítem' : 'Ítems'}`;
        if (fields.footSubtotal) fields.footSubtotal.textContent = formatCurrency(compra.subtotal, moneda);
        if (fields.footImpuesto) fields.footImpuesto.textContent = formatCurrency(compra.monto_impuesto, moneda);
        if (fields.footTotal) fields.footTotal.textContent = formatCurrency(compra.total, moneda);
    }

    /**
     * @function cargarDetalleCompra
     * Realiza la petición para obtener el detalle de una compra específica.
     */
    function cargarDetalleCompra() {
        if (!id_compra) {
            console.error("ID de compra no proporcionado.");
            renderizarDetalle(null); // Mostrar error en la vista
            return;
        }

        api({
            accion: "obtener_detalle_compra",
            id_compra: id_compra
        })
            .then(res => {
                if (res && res.error) {
                    console.error("Error desde PHP:", res.error || res.message || res.detalle);
                    renderizarDetalle(null);
                    return;
                }
                renderizarDetalle(res);
            })
            .catch(err => {
                console.error("Error API al cargar el detalle de la compra:", err);
                renderizarDetalle(null);
            });
    }

    // Iniciar la carga de datos
    cargarDetalleCompra();
});