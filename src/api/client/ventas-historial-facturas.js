import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// Obtener id_sucursal de la sesión (viene de la variable global inyectada por PHP)
// Si es null, el usuario ve todas las sucursales (ej: Gerente)
const sucursalSesion = window.ID_SUCURSAL_SESION || "";

let filtrosActivos = {
    fecha_desde: "",
    fecha_hasta: "",
    id_usuario: "",
    monto_min: "",
    monto_max: "",
    id_sucursal: sucursalSesion ? String(sucursalSesion) : "",
    id_metodo_pago: "",
    id_moneda: ""
};

function cargarVentas() {
    api({
        accion: "obtener_ventas_filtradas",
        ...filtrosActivos
    }).then(res => {
        const tabla = document.getElementById("tabla_ventas");
        tabla.innerHTML = "";
        const ventas = res.ventas || [];

        if (ventas.length === 0) {
            tabla.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron ventas con estos filtros.</td></tr>';
            return;
        }

        ventas.forEach(venta => {
            const fila = document.createElement("tr");
            const clienteNombre = (venta.cliente_nombre || '') + ' ' + (venta.cliente_apellido || '');
            fila.innerHTML = `
                <td>${venta.id_venta ?? '-'}</td>
                <td>${venta.fecha ? new Date(venta.fecha).toLocaleDateString('es-VE') : '-'}</td>
                <td>${clienteNombre.trim() || '-'}</td>
                <td>${venta.vendedor ?? '-'}</td>
                <td>${venta.sucursal ?? '-'}</td>
                <td>$${parseFloat(venta.total || 0).toFixed(2)}</td>
                <td>${venta.metodo_pago ?? '-'}</td>
                <td class="text-center">
                    <form action="ventas-detalle-factura" method="POST" class="d-inline">
                        <input type="hidden" name="accion" value="ver_detalle">
                        <input type="hidden" name="id_venta" value="${venta.id_venta}">
                        <button type="submit" class="btn btn-primary btn-sm">Ver Detalle</button>
                    </form>
                </td>
            `;
            tabla.appendChild(fila);
        });
    }).catch(error => {
        console.error("Error al cargar ventas:", error);
        document.getElementById("tabla_ventas").innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar los datos.</td></tr>';
    });
}

function cargarOpcionesSelects() {
    api({ accion: "obtener_todos_los_empleados" }).then(res => {
        const selectVendedor = document.getElementById("filtro_vendedor");
        const empleados = res.filas || res.data || [];
        empleados.forEach(emp => {
            const opt = document.createElement("option");
            opt.value = emp.id_usuario;
            opt.textContent = (emp.nombre || '') + ' ' + (emp.apellido || '');
            selectVendedor.appendChild(opt);
        });
    }).catch(err => console.error("Error al cargar empleados:", err));

    api({ accion: "obtener_sucursales" }).then(res => {
        const selectSucursal = document.getElementById("filtro_sucursal");
        const sucursales = res.filas || [];
        sucursales.forEach(suc => {
            const opt = document.createElement("option");
            opt.value = suc.id_sucursal;
            opt.textContent = suc.nombre;
            selectSucursal.appendChild(opt);
        });
    }).catch(err => console.error("Error al cargar sucursales:", err));

    api({ accion: "obtener_metodos_pago" }).then(res => {
        const selectMetodo = document.getElementById("filtro_metodo_pago");
        const metodos = res.data || [];
        metodos.forEach(met => {
            const opt = document.createElement("option");
            opt.value = met.id_metodo_pago;
            opt.textContent = met.nombre;
            selectMetodo.appendChild(opt);
        });
    }).catch(err => console.error("Error al cargar métodos pago:", err));

    api({ accion: "obtener_monedas" }).then(res => {
        const selectMoneda = document.getElementById("filtro_moneda");
        const monedas = res.data || [];
        monedas.forEach(mon => {
            const opt = document.createElement("option");
            opt.value = mon.id_moneda;
            opt.textContent = mon.nombre;
            selectMoneda.appendChild(opt);
        });
    }).catch(err => console.error("Error al cargar monedas:", err));
}

function inicializarFiltros() {
    const addEventListener = (id, eventType, filterKey) => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener(eventType, (e) => {
                filtrosActivos[filterKey] = e.target.value.trim();
                cargarVentas();
            });
        }
    };

    addEventListener("filtro_fecha_desde", "change", "fecha_desde");
    addEventListener("filtro_fecha_hasta", "change", "fecha_hasta");
    addEventListener("filtro_vendedor", "change", "id_usuario");
    addEventListener("filtro_monto_min", "input", "monto_min");
    addEventListener("filtro_monto_max", "input", "monto_max");
    addEventListener("filtro_sucursal", "change", "id_sucursal");
    addEventListener("filtro_metodo_pago", "change", "id_metodo_pago");
    addEventListener("filtro_moneda", "change", "id_moneda");

    document.getElementById("btn_reestablecer_filtros")?.addEventListener("click", () => {
        // Al restablecer, mantener la sucursal de sesión si existe
        filtrosActivos = {
            fecha_desde: "",
            fecha_hasta: "",
            id_usuario: "",
            monto_min: "",
            monto_max: "",
            id_sucursal: sucursalSesion ? String(sucursalSesion) : "",
            id_metodo_pago: "",
            id_moneda: ""
        };

        document.getElementById("filtro_fecha_desde").value = "";
        document.getElementById("filtro_fecha_hasta").value = "";
        document.getElementById("filtro_vendedor").value = "";
        document.getElementById("filtro_monto_min").value = "";
        document.getElementById("filtro_monto_max").value = "";
        document.getElementById("filtro_sucursal").value = sucursalSesion ? String(sucursalSesion) : "";
        document.getElementById("filtro_metodo_pago").value = "";
        document.getElementById("filtro_moneda").value = "";

        cargarVentas();
    });
}

document.addEventListener("DOMContentLoaded", () => {
    cargarOpcionesSelects();
    inicializarFiltros();
    
    // Preseleccionar la sucursal de sesión en el select (si existe)
    if (sucursalSesion) {
        const selectSucursal = document.getElementById("filtro_sucursal");
        if (selectSucursal) {
            selectSucursal.value = String(sucursalSesion);
        }
    }
    
    cargarVentas();
});
