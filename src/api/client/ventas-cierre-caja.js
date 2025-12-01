import { api } from "./index.js";

document.addEventListener("DOMContentLoaded", () => {
    const fechaInput = document.getElementById("fecha_cierre");
    const btnImprimir = document.getElementById("btn_imprimir");
    const btnBuscar = document.getElementById("btn_buscar");

    // Establecer fecha de hoy por defecto (Local Time)
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hoy = `${year}-${month}-${day}`;

    if (fechaInput) {
        fechaInput.value = hoy;
        fechaInput.max = hoy;
    }

    // Cargar datos iniciales
    cargarCierreCaja(hoy);

    // Event Listeners
    if (btnBuscar) {
        btnBuscar.addEventListener("click", () => {
            cargarCierreCaja(fechaInput.value);
        });
    }

    if (btnImprimir) {
        btnImprimir.addEventListener("click", () => {
            window.print();
        });
    }
});

function cargarCierreCaja(fecha) {
    if (!fecha) return;

    api({ accion: "obtener_cierre_caja", fecha: fecha })
        .then(res => {
            if (res.error) {
                console.error("Error:", res.error);
                alert("Error al cargar el cierre de caja: " + res.error);
                return;
            }
            renderizarCierre(res);
        })
        .catch(err => {
            console.error("Error de conexión:", err);
            alert("Error de conexión al cargar el cierre de caja.");
        });
}

function renderizarCierre(data) {
    // 1. Información del Turno
    document.getElementById("info_sucursal").textContent = data.sucursal || "Desconocida";
    document.getElementById("info_fecha_apertura").textContent = data.resumen.primera_venta || "Sin ventas";
    document.getElementById("info_fecha_cierre").textContent = data.resumen.ultima_venta || "Sin ventas";
    
    // Resumen Ventas
    document.getElementById("resumen_total_ventas").textContent = data.resumen.total_ventas || 0;
    document.getElementById("resumen_anuladas").textContent = data.resumen.ventas_anuladas || 0;
    
    // 2. Tabla de Métodos de Pago
    const tbodyPagos = document.querySelector("#tabla_pagos tbody");
    tbodyPagos.innerHTML = "";
    
    let totalBs = 0;
    let totalUsd = 0;
    let totalTransacciones = 0;

    // Agrupar por método de pago para mostrar filas consolidadas si es necesario
    // Pero el backend ya devuelve agrupado por metodo y moneda.
    // Vamos a mostrar una fila por combinación Método - Moneda o intentar agrupar visualmente.
    // Para simplificar y ser explícitos, listamos todo lo que llega.
    
    if (data.desglose_pagos && data.desglose_pagos.length > 0) {
        data.desglose_pagos.forEach(pago => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${pago.metodo_pago}</td>
                <td class="text-center">${pago.moneda}</td>
                <td class="text-end">${formatCurrency(pago.monto, pago.moneda)}</td>
                <td class="text-center">${pago.transacciones}</td>
            `;
            tbodyPagos.appendChild(tr);
            
            totalTransacciones += parseInt(pago.transacciones);
        });
    } else {
        tbodyPagos.innerHTML = `<tr><td colspan="4" class="text-center">No hay pagos registrados</td></tr>`;
    }

    // Totales en el footer de la tabla
    // Usamos data.totales_moneda para el resumen global
    const tfootPagos = document.querySelector("#tabla_pagos tfoot");
    tfootPagos.innerHTML = "";
    
    if (data.totales_moneda && data.totales_moneda.length > 0) {
        data.totales_moneda.forEach(moneda => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td colspan="2" class="text-end fw-bold">Total ${moneda.nombre} (${moneda.simbolo}):</td>
                <td class="text-end fw-bold">${formatCurrency(moneda.total_recaudado, moneda.codigo)}</td>
                <td></td>
            `;
            tfootPagos.appendChild(tr);
        });
    }

    // 3. Balance Final (Lista)
    const listaBalance = document.getElementById("lista_balance");
    listaBalance.innerHTML = "";
    
    // Total Recaudado (Ventas)
    if (data.totales_moneda && data.totales_moneda.length > 0) {
        data.totales_moneda.forEach(moneda => {
             const li = document.createElement("li");
             li.className = "list-group-item d-flex justify-content-between align-items-center";
             li.innerHTML = `
                Total Recaudado (${moneda.nombre}):
                <span class="fw-bold">${formatCurrency(moneda.total_recaudado, moneda.codigo)}</span>
             `;
             listaBalance.appendChild(li);
        });
    } else {
        listaBalance.innerHTML = `<li class="list-group-item text-center">Sin movimientos de caja</li>`;
    }

    // 4. Gráficos
    renderizarGraficos(data);
}

function formatCurrency(amount, currencyCode) {
    return new Intl.NumberFormat('es-VE', { 
        style: 'currency', 
        currency: currencyCode === 'BS' ? 'VES' : 'USD' 
    }).format(amount);
}

let chartMetodos = null;
let chartHoras = null;

function renderizarGraficos(data) {
    // Gráfico Métodos de Pago (Doughnut)
    // Agrupar montos por método de pago (sumando todo en una moneda base aproximada o solo contando transacciones para el gráfico de pastel si hay multiples monedas)
    // Para evitar confusión de tasas, usaremos Cantidad de Transacciones para el gráfico de pastel, o solo una moneda si es dominante.
    // Mejor: Gráfico de Transacciones por Método de Pago (independiente de la moneda)
    
    const metodosLabels = [];
    const metodosData = [];
    const metodosMap = {};

    data.desglose_pagos.forEach(p => {
        if (!metodosMap[p.metodo_pago]) metodosMap[p.metodo_pago] = 0;
        metodosMap[p.metodo_pago] += parseInt(p.transacciones);
    });

    for (const [key, value] of Object.entries(metodosMap)) {
        metodosLabels.push(key);
        metodosData.push(value);
    }

    const ctxMetodos = document.getElementById('chartCierreMetodos');
    if (chartMetodos) chartMetodos.destroy();

    chartMetodos = new Chart(ctxMetodos, {
        type: 'doughnut',
        data: {
            labels: metodosLabels,
            datasets: [{
                data: metodosData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Transacciones por Método de Pago' },
                legend: { position: 'bottom' }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico Ventas por Hora
    const horasLabels = data.ventas_hora.map(h => `${h.hora}:00`);
    const horasData = data.ventas_hora.map(h => h.cantidad_ventas);

    const ctxHoras = document.getElementById('chartCierreVentasHora');
    if (chartHoras) chartHoras.destroy();

    chartHoras = new Chart(ctxHoras, {
        type: 'line',
        data: {
            labels: horasLabels,
            datasets: [{
                label: 'Cantidad de Ventas',
                data: horasData,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Actividad por Hora' },
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
