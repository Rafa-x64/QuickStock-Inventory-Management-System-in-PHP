import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idVenta = document.getElementById("id_venta_hidden").value;

    if (!idVenta) {
        alert("No se especificó un ID de venta.");
        window.location.href = "ventas-historial-facturas";
        return;
    }

    api({
        accion: "obtener_detalle_venta",
        id_venta: idVenta
    }).then(res => {
        if (res.error) {
            alert("Error: " + res.error);
            window.location.href = "ventas-historial-facturas";
            return;
        }

        const venta = res.venta || {};
        const detalles = res.detalles || [];
        const pagos = res.pagos || [];

        document.getElementById("v_id_venta").textContent = venta.id_venta || '-';
        document.getElementById("breadcrumb-id").textContent = `Factura #${venta.id_venta || ''}`;
        document.getElementById("v_fecha").textContent = venta.fecha ? new Date(venta.fecha).toLocaleString('es-VE') : '-';
        document.getElementById("v_cliente").textContent = `${venta.cliente_nombre || ''} ${venta.cliente_apellido || ''}`.trim() || '-';
        document.getElementById("v_cedula").textContent = venta.cedula || '-';
        document.getElementById("v_vendedor").textContent = venta.vendedor || '-';
        document.getElementById("v_sucursal").textContent = venta.sucursal || '-';
        document.getElementById("v_total").textContent = `$${parseFloat(venta.total || 0).toFixed(2)}`;
        document.getElementById("v_telefono").textContent = venta.telefono || '-';

        const tablaProductos = document.getElementById("tabla_productos");
        tablaProductos.innerHTML = "";
        if (detalles.length === 0) {
            tablaProductos.innerHTML = '<tr><td colspan="7" class="text-center">No hay productos registrados.</td></tr>';
        } else {
            detalles.forEach(det => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${det.producto_nombre || '-'}</td>
                    <td>${det.codigo_barra || '-'}</td>
                    <td>${det.categoria || '-'}</td>
                    <td>${det.color || '-'} / ${det.talla || '-'}</td>
                    <td>${det.cantidad || 0}</td>
                    <td>$${parseFloat(det.precio_unitario || 0).toFixed(2)}</td>
                    <td>$${parseFloat(det.subtotal || 0).toFixed(2)}</td>
                `;
                tablaProductos.appendChild(fila);
            });
        }

        const tablaPagos = document.getElementById("tabla_pagos");
        tablaPagos.innerHTML = "";
        if (pagos.length === 0) {
            tablaPagos.innerHTML = '<tr><td colspan="5" class="text-center">No hay pagos registrados.</td></tr>';
        } else {
            pagos.forEach(pag => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${pag.metodo_pago || '-'}</td>
                    <td>${pag.moneda || '-'} (${pag.simbolo || ''})</td>
                    <td>${parseFloat(pag.monto || 0).toFixed(2)} ${pag.simbolo || ''}</td>
                    <td>${parseFloat(pag.tasa || 1).toFixed(4)}</td>
                    <td>${pag.referencia || '-'}</td>
                `;
                tablaPagos.appendChild(fila);
            });
        }

    }).catch(err => {
        console.error("Error al cargar detalle de venta:", err);
        alert("Error al cargar el detalle de la venta.");
        window.location.href = "ventas-historial-facturas";
    });
});
