import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    cargarEstadisticas();
});

function cargarEstadisticas() {
    cargarProductosPopulares();
    cargarCategoriasPopulares();
    cargarVentasPorSucursal();
    cargarTendenciaMensual();
}

function cargarProductosPopulares() {
    api({ accion: "obtener_productos_populares", limite: 10 })
        .then(res => {
            if (res.error) {
                console.error("Error productos populares:", res.error);
                return;
            }
            
            const productos = res.productos || [];
            const labels = productos.map(p => p.producto_nombre || "Sin nombre");
            const data = productos.map(p => parseInt(p.total_vendido) || 0);
            
            const ctx = document.getElementById("chartProductosVendidos");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Unidades Vendidas",
                        data: data,
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(err => console.error("Error al cargar productos:", err));
}

function cargarCategoriasPopulares() {
    api({ accion: "obtener_categorias_populares", limite: 10 })
        .then(res => {
            if (res.error) {
                console.error("Error categorías populares:", res.error);
                return;
            }
            
            const categorias = res.categorias || [];
            const labels = categorias.map(c => c.categoria || "Sin categoría");
            const data = categorias.map(c => parseInt(c.total_vendido) || 0);
            
            const ctx = document.getElementById("chartCategoriasVendidas");
            new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Unidades Vendidas",
                        data: data,
                        backgroundColor: [
                            "rgba(255, 99, 132, 0.5)",
                            "rgba(54, 162, 235, 0.5)",
                            "rgba(255, 206, 86, 0.5)",
                            "rgba(75, 192, 192, 0.5)",
                            "rgba(153, 102, 255, 0.5)",
                            "rgba(255, 159, 64, 0.5)"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })
        .catch(err => console.error("Error al cargar categorías:", err));
}

function cargarVentasPorSucursal() {
    api({ accion: "obtener_ventas_por_sucursal" })
        .then(res => {
            if (res.error) {
                console.error("Error ventas por sucursal:", res.error);
                return;
            }
            
            const sucursales = res.sucursales || [];
            const labels = sucursales.map(s => s.sucursal || "Sin sucursal");
            const data = sucursales.map(s => parseFloat(s.ingresos_totales) || 0);
            
            const ctx = document.getElementById("chartSucursalesVentas");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Ingresos Totales ($)",
                        data: data,
                        backgroundColor: "rgba(75, 192, 192, 0.5)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            const totalVentas = data.reduce((sum, val) => sum + val, 0);
            document.getElementById("valorTotalVentas").textContent = `$${totalVentas.toFixed(2)}`;
        })
        .catch(err => console.error("Error al cargar ventas por sucursal:", err));
}

function cargarTendenciaMensual() {
    api({ accion: "obtener_tendencia_mensual", meses: 12 })
        .then(res => {
            if (res.error) {
                console.error("Error tendencia mensual:", res.error);
                return;
            }
            
            const tendencia = res.tendencia || [];
            const labels = tendencia.map(t => t.mes || "");
            const data = tendencia.map(t => parseFloat(t.ingresos_totales) || 0);
            
            const ctx = document.getElementById("chartTendenciaMensual");
            new Chart(ctx, {
                type: "line",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Ingresos Mensuales ($)",
                        data: data,
                        backgroundColor: "rgba(153, 102, 255, 0.2)",
                        borderColor: "rgba(153, 102, 255, 1)",
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(err => console.error("Error al cargar tendencia:", err));
}
