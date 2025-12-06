import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

// Variable global para almacenar la instancia de Chart
let chartInstance = null;

document.addEventListener("DOMContentLoaded", () => {
    // Cargar todos los datos del dashboard
    cargarDashboard();
});

/**
 * Función principal que carga todos los datos del dashboard
 */
async function cargarDashboard() {
    try {
        // Ejecutar todas las peticiones en paralelo
        await Promise.all([
            cargarNombreSucursal(),
            cargarResumenFinanciero(),
            cargarVentasHoy(),
            cargarProductoMasVendidoHoy(),
            cargarProductoMasVendidoSemana(),
            cargarTop5Productos(),
            cargarProductosStockBajo(),
            // cargarCategorias(),  // Simplificar: No needed for simple dashboard maybe? or keep it? User said "estadisticas ventas". Categories is inventory.
            // cargarTotalProductosActivos(),
            // cargarProductosSinStock()
            // I will keep them matching the view simplification. I'll read view first then decide what to keep. 
            // actually I will keep all for now, and just remove HTML elements. JS is safe.
            cargarCategorias(),
            cargarTotalProductosActivos(),
            cargarProductosSinStock()
        ]);
        console.log("Dashboard Empleado cargado correctamente");
    } catch (error) {
        console.error("Error al cargar el dashboard de empleado:", error);
    }
}

/**
 * Carga el nombre de la sucursal
 */
async function cargarNombreSucursal() {
    try {
        const res = await api({ accion: "dashboard_nombre_sucursal" });
        const elemento = document.getElementById("nombre_sucursal");
        if (elemento && res.nombre) {
            elemento.textContent = res.nombre;
        }
    } catch (error) {
        console.error("Error al cargar nombre de sucursal:", error);
    }
}

/**
 * Carga el resumen financiero (ingresos por moneda)
 */
async function cargarResumenFinanciero() {
    try {
        const res = await api({ accion: "dashboard_resumen_financiero" });
        
        // Inicializar valores
        let usd = 0, bs = 0, eur = 0;
        
        if (res.resumen && Array.isArray(res.resumen)) {
            res.resumen.forEach(item => {
                const codigo = (item.codigo_moneda || "").toUpperCase();
                const monto = parseFloat(item.total_moneda) || 0;
                
                if (codigo === "USD" || codigo === "$") {
                    usd = monto;
                } else if (codigo === "BS" || codigo === "VES" || codigo === "VEF") {
                    bs = monto;
                } else if (codigo === "EUR" || codigo === "€") {
                    eur = monto;
                }
            });
        }
        
        // Actualizar elementos
        const elUsd = document.getElementById("total_usd");
        const elBs = document.getElementById("total_bs");
        const elEur = document.getElementById("total_eur");
        
        if (elUsd) elUsd.textContent = usd.toFixed(2) + " $";
        if (elBs) elBs.textContent = bs.toFixed(2) + " Bs.";
        if (elEur) elEur.textContent = eur.toFixed(2) + " Eur";
        
    } catch (error) {
        console.error("Error al cargar resumen financiero:", error);
    }
}

/**
 * Carga la cantidad de ventas del día
 */
async function cargarVentasHoy() {
    try {
        const res = await api({ accion: "dashboard_ventas_hoy" });
        const elemento = document.getElementById("ventas_hoy");
        if (elemento) {
            const total = res.total_ventas || 0;
            elemento.textContent = total + " Venta" + (total !== 1 ? "s" : "");
        }
    } catch (error) {
        console.error("Error al cargar ventas de hoy:", error);
    }
}

/**
 * Carga el producto más vendido del día
 */
async function cargarProductoMasVendidoHoy() {
    try {
        const res = await api({ accion: "dashboard_producto_mas_vendido_hoy" });
        
        const nombreEl = document.getElementById("producto_mas_vendido_hoy_nombre");
        const cantidadEl = document.getElementById("producto_mas_vendido_hoy_cantidad");
        
        if (nombreEl) nombreEl.textContent = res.producto || "Sin ventas hoy";
        if (cantidadEl) {
            const cantidad = res.cantidad || 0;
            cantidadEl.textContent = cantidad + " " + (cantidad !== 1 ? "Pares" : "Par");
        }
    } catch (error) {
        console.error("Error al cargar producto más vendido hoy:", error);
    }
}

/**
 * Carga el producto más vendido de la semana
 */
async function cargarProductoMasVendidoSemana() {
    try {
        const res = await api({ accion: "dashboard_producto_mas_vendido_semana" });
        
        const nombreEl = document.getElementById("producto_mas_vendido_semana_nombre");
        const cantidadEl = document.getElementById("producto_mas_vendido_semana_cantidad");
        
        if (nombreEl) nombreEl.textContent = res.producto || "Sin ventas esta semana";
        if (cantidadEl) {
            const cantidad = res.cantidad || 0;
            cantidadEl.textContent = cantidad + " " + (cantidad !== 1 ? "Pares" : "Par");
        }
    } catch (error) {
        console.error("Error al cargar producto más vendido semana:", error);
    }
}

/**
 * Carga el Top 5 de productos más vendidos y actualiza la gráfica
 */
async function cargarTop5Productos() {
    try {
        const res = await api({ accion: "dashboard_top5_productos" });
        
        const labels = [];
        const data = [];
        
        if (res.top5 && Array.isArray(res.top5)) {
            res.top5.forEach(item => {
                labels.push(item.producto || "Sin nombre");
                data.push(parseInt(item.cantidad_vendida) || 0);
            });
        }
        
        // Si no hay datos, mostrar mensaje
        if (labels.length === 0) {
            labels.push("Sin datos");
            data.push(0);
        }
        
        // Actualizar la gráfica
        actualizarGrafica(labels, data);
        
    } catch (error) {
        console.error("Error al cargar top 5 productos:", error);
    }
}

/**
 * Actualiza o crea la gráfica de Chart.js
 */
function actualizarGrafica(labels, data) {
    const ctx = document.getElementById("myChart");
    if (!ctx) return;
    
    // Destruir instancia anterior si existe
    if (chartInstance) {
        chartInstance.destroy();
    }
    
    // Crear nueva gráfica
    chartInstance = new Chart(ctx.getContext("2d"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Unidades Vendidas (Top 5)",
                data: data,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: 'rgba(0, 0, 0, 0.5)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad Vendida'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Carga productos con stock bajo
 */
async function cargarProductosStockBajo() {
    try {
        const res = await api({ accion: "dashboard_stock_bajo" });
        const tbody = document.getElementById("tbody_stock_bajo");
        
        if (!tbody) return;
        
        if (!res.productos || res.productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-success">Sin alertas de stock</td></tr>';
            return;
        }
        
        let html = "";
        res.productos.forEach(item => {
            html += `<tr>
                <td>${item.codigo || "-"}</td>
                <td>${item.producto || "-"}</td>
                <td>${item.cantidad || 0}</td>
            </tr>`;
        });
        
        tbody.innerHTML = html;
        
    } catch (error) {
        console.error("Error al cargar productos con stock bajo:", error);
    }
}

/**
 * Carga las categorías registradas
 */
async function cargarCategorias() {
    try {
        const res = await api({ accion: "dashboard_categorias" });
        const tbody = document.getElementById("tbody_categorias");
        
        if (!tbody) return;
        
        if (!res.categorias || res.categorias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center">Sin categorías registradas</td></tr>';
            return;
        }
        
        let html = "";
        res.categorias.forEach(item => {
            html += `<tr>
                <td>${item.id_categoria || "-"}</td>
                <td>${item.nombre || "-"}</td>
                <td>${item.categoria_padre || "-"}</td>
            </tr>`;
        });
        
        tbody.innerHTML = html;
        
    } catch (error) {
        console.error("Error al cargar categorías:", error);
    }
}

/**
 * Carga el total de productos activos
 */
async function cargarTotalProductosActivos() {
    try {
        const res = await api({ accion: "dashboard_total_productos" });
        const elemento = document.getElementById("total_productos_activos");
        if (elemento) {
            const total = res.total || 0;
            elemento.textContent = total + " Producto" + (total !== 1 ? "s" : "");
        }
    } catch (error) {
        console.error("Error al cargar total productos aktivos:", error);
    }
}

/**
 * Carga la cantidad de productos sin stock
 */
async function cargarProductosSinStock() {
    try {
        const res = await api({ accion: "dashboard_sin_stock" });
        const elemento = document.getElementById("productos_sin_stock");
        if (elemento) {
            const total = res.total || 0;
            elemento.textContent = total + " Producto" + (total !== 1 ? "s" : "");
        }
    } catch (error) {
        console.error("Error al cargar productos sin stock:", error);
    }
}
