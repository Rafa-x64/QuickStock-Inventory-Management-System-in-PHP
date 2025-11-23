// api/client/sucursales-detalle.js

import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// Función para formatear fechas
const formatFecha = (fecha) => {
    if (!fecha) return '-';
    // Asumiendo que la fecha viene en formato 'YYYY-MM-DD' de la base de datos
    try {
        const d = new Date(fecha + 'T00:00:00');
        return d.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
    } catch (e) {
        return fecha; // Retorna la fecha original si hay error de formato
    }
}

// Función auxiliar para obtener el valor o un placeholder
const getVal = (val) => val ?? '-';

/**
 * Función para renderizar la tabla de empleados.
 * @param {Array} empleados Lista de objetos de empleado.
 */
function renderizarTablaEmpleados(empleados) {
    const tbody = document.getElementById('tabla_empleados');
    if (!tbody) return;

    // Limpiar contenido anterior
    tbody.innerHTML = '';

    if (!empleados || empleados.length === 0) {
        // Mostrar mensaje si no hay empleados
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay empleados asociados a esta sucursal.</td></tr>';
        return;
    }

    const getEstadoBadge = (activo) => {
        const estadoActivo = activo === true || activo === 't';

        return estadoActivo
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>';
    };

    // Construir las filas
    let htmlContent = '';
    empleados.forEach(empleado => {
        htmlContent += `
            <tr>
                <td>${getVal(empleado.nombre)}</td>
                <td>${getVal(empleado.cargo)}</td>
                <td>${getEstadoBadge(empleado.activo) }</td>
                <td>${getVal(empleado.telefono)}</td>
            </tr>
        `;
    });

    tbody.innerHTML = htmlContent;
}


// Función principal para cargar y rellenar los datos de la sucursal
async function cargarDetalleSucursal() {
    const idSucursalElement = document.getElementById('id_sucursal_hidden');
    const mainContent = document.getElementById('mainContent');

    if (!idSucursalElement) {
        mainContent.innerHTML = '<div class="alert alert-danger">Error: Elemento ID de sucursal no encontrado.</div>';
        return;
    }
    const id_sucursal = idSucursalElement.value;

    if (!id_sucursal) {
        mainContent.innerHTML = '<div class="alert alert-danger">Error: No se ha proporcionado un ID de sucursal válido.</div>';
        return;
    }

    try {
        // 2. Realizar la petición AJAX al backend
        const res = await api({
            accion: "obtener_detalle_sucursal",
            id_sucursal: id_sucursal
        });

        if (res.status !== "success") {
            console.error("Error del servidor:", res.message, res.detalle);
            mainContent.innerHTML = '<div class="alert alert-danger">Error al cargar el detalle de la sucursal: ' + (res.message || 'Desconocido') + '</div>';
            return;
        }

        const sucursal = res.sucursal;
        const empleados = res.empleados; // ⭐ Capturar la lista de empleados

        // Actualizar el título de la breadcrumb
        document.getElementById('breadcrumb-nombre').textContent = getVal(sucursal.nombre) || 'Detalle';

        // --- 3. Rellenar Información General de la Sucursal ---

        // Asumo que 'codigo_sucursal' es el id_sucursal de la BD
        document.getElementById('s_codigo_sucursal').textContent = getVal(sucursal.id_sucursal);
        document.getElementById('s_nombre').textContent = getVal(sucursal.nombre);
        document.getElementById('s_direccion').textContent = getVal(sucursal.direccion);
        document.getElementById('s_telefono').textContent = getVal(sucursal.telefono);
        document.getElementById('s_rif').textContent = getVal(sucursal.rif);
        document.getElementById('s_fecha_registro').textContent = formatFecha(sucursal.fecha_registro);

        // Estado
        const estadoActivo = sucursal.activo === true || sucursal.activo === 't';
        const estadoBadge = estadoActivo
            ? '<span class="badge bg-success">Activa</span>'
            : '<span class="badge bg-danger">Inactiva</span>';
        document.getElementById('s_estado').innerHTML = estadoBadge;

        // --- 4. Rellenar la Tabla de Empleados ---
        renderizarTablaEmpleados(empleados); // ⭐ Llamar a la nueva función

    } catch (error) {
        console.error("Error al cargar detalle de la sucursal:", error);
        mainContent.innerHTML = '<div class="alert alert-danger">Ocurrió un error inesperado al procesar los datos.</div>';
    }
}

// Inicialización: Cargar el detalle cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", cargarDetalleSucursal);