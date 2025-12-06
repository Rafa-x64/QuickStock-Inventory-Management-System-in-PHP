import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    cargarDatosStockBajo();
    cargarDatosMayorStock();
    cargarDistribucionCategoria();
    cargarStockPorSucursal();
});

// 1. Stock Bajo (Tabla y Gráfico)
function cargarDatosStockBajo() {
    api({ accion: "dashboard_stock_bajo" }).then(res => {
        const productos = res.productos || [];
        renderizarTabla(productos);
        renderizarGraficoStockBajo(productos);
    }).catch(err => console.error("Error cargando stock bajo:", err));
}

function renderizarTabla(productos) {
    const tbody = document.getElementById("tabla_stock_bajo");
    tbody.innerHTML = "";

    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay alertas de stock bajo.</td></tr>';
        return;
    }

    productos.forEach(prod => {
        const stock = parseInt(prod.cantidad);
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${prod.codigo}</td>
            <td>${prod.producto}</td>
            <td class="fw-bold text-danger">${stock}</td>
            <td><span class="badge bg-warning text-dark">Bajo</span></td>
            <td><span class="badge bg-danger">Crítico</span></td>
            <td>
                <a href="inventario-ajustes-manuales-stock" class="btn btn-sm btn-primary">
                    <i class="bi bi-box-seam"></i> Ajustar
                </a>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderizarGraficoStockBajo(productos) {
    const ctx = document.getElementById('chartStockBajo');
    if (!ctx) return;

    const labels = productos.map(p => p.producto);
    const data = productos.map(p => p.cantidad);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Top Productos con Stock Crítico' }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}

// 2. Mayor Stock
function cargarDatosMayorStock() {
    api({ accion: "dashboard_mayor_stock" }).then(res => {
        const productos = res.productos || [];
        renderizarGraficoMayorStock(productos);
    }).catch(err => console.error("Error cargando mayor stock:", err));
}

function renderizarGraficoMayorStock(productos) {
    const ctx = document.getElementById('chartStockAlto');
    if (!ctx) return;

    const labels = productos.map(p => p.producto);
    const data = productos.map(p => p.cantidad);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Productos con Mayor Stock' }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}

// 3. Distribución por Categoría
function cargarDistribucionCategoria() {
    api({ accion: "dashboard_distribucion_categoria" }).then(res => {
        const categorias = res.categorias || [];
        renderizarGraficoCategorias(categorias);
    }).catch(err => console.error("Error cargando categorías:", err));
}

function renderizarGraficoCategorias(categorias) {
    const ctx = document.getElementById('chartCategorias');
    if (!ctx) return;

    const labels = categorias.map(c => c.categoria);
    const data = categorias.map(c => c.total_stock);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 205, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: 'rgba(255, 255, 255, 0.3)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Distribución de stock por categoría' }
            }
        }
    });
}

// 4. Stock por Sucursal
function cargarStockPorSucursal() {
    api({ accion: "dashboard_stock_sucursal" }).then(res => {
        const sucursales = res.sucursales || [];
        renderizarGraficoSucursales(sucursales);
    }).catch(err => console.error("Error cargando sucursales:", err));
}

function renderizarGraficoSucursales(sucursales) {
    const ctx = document.getElementById('chartSucursales');
    if (!ctx) return;

    const labels = sucursales.map(s => s.sucursal);
    const data = sucursales.map(s => s.total_stock);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Stock',
                data: data,
                backgroundColor: 'rgba(255, 206, 86, 0.7)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Stock total por sucursal' },
                legend: { display: false }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}