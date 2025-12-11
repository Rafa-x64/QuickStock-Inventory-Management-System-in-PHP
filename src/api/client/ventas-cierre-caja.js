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
    
    // 2. Tabla de Métodos de Pago (Desglose)
    const tbodyPagos = document.querySelector("#tabla_pagos tbody");
    tbodyPagos.innerHTML = "";
    
    // Contadores para estadísticas por método de pago
    const estadisticasMetodos = {};
    let totalTransacciones = 0;
    
    if (data.desglose_pagos && data.desglose_pagos.length > 0) {
        data.desglose_pagos.forEach(pago => {
            const tr = document.createElement("tr");
            const simbolo = pago.simbolo_moneda || '';
            tr.innerHTML = `
                <td>${pago.metodo_pago}</td>
                <td class="text-center">${pago.moneda}</td>
                <td class="text-end">${formatCurrency(pago.monto, pago.moneda, simbolo)}</td>
                <td class="text-center">${pago.transacciones}</td>
            `;
            tbodyPagos.appendChild(tr);
            
            // Acumular estadísticas por método
            const metodo = pago.metodo_pago;
            if (!estadisticasMetodos[metodo]) {
                estadisticasMetodos[metodo] = { transacciones: 0 };
            }
            estadisticasMetodos[metodo].transacciones += parseInt(pago.transacciones);
            totalTransacciones += parseInt(pago.transacciones);
        });
    } else {
        tbodyPagos.innerHTML = `<tr><td colspan="4" class="text-center">No hay pagos registrados</td></tr>`;
    }

    // Totales en el footer de la tabla (por cada moneda)
    const tfootPagos = document.querySelector("#tabla_pagos tfoot");
    tfootPagos.innerHTML = "";
    
    if (data.totales_moneda && data.totales_moneda.length > 0) {
        // Agregar separador
        const trSeparador = document.createElement("tr");
        trSeparador.innerHTML = `<td colspan="4" class="bg-secondary text-white text-center fw-bold">💰 TOTALES POR MONEDA</td>`;
        tfootPagos.appendChild(trSeparador);
        
        data.totales_moneda.forEach(moneda => {
            const tr = document.createElement("tr");
            const simbolo = moneda.simbolo || '';
            tr.innerHTML = `
                <td colspan="2" class="text-end fw-bold">Total ${moneda.nombre}:</td>
                <td class="text-end fw-bold text-success">${formatCurrency(moneda.total_recaudado, moneda.codigo, simbolo)}</td>
                <td class="text-center">${moneda.total_transacciones || '-'}</td>
            `;
            tfootPagos.appendChild(tr);
        });
        
        // Agregar total de transacciones
        const trTotalTx = document.createElement("tr");
        trTotalTx.innerHTML = `
            <td colspan="3" class="text-end fw-bold">Total Transacciones:</td>
            <td class="text-center fw-bold text-primary">${totalTransacciones}</td>
        `;
        tfootPagos.appendChild(trTotalTx);
    }

    // 3. Balance Final (Lista con más detalle)
    const listaBalance = document.getElementById("lista_balance");
    listaBalance.innerHTML = "";
    
    if (data.totales_moneda && data.totales_moneda.length > 0) {
        // Título
        const liTitulo = document.createElement("li");
        liTitulo.className = "list-group-item bg-primary text-white fw-bold text-center";
        liTitulo.innerHTML = `💵 TOTAL RECAUDADO POR MONEDA`;
        listaBalance.appendChild(liTitulo);
        
        // Totales por moneda
        data.totales_moneda.forEach(moneda => {
            const li = document.createElement("li");
            li.className = "list-group-item d-flex justify-content-between align-items-center";
            const simbolo = moneda.simbolo || '';
            li.innerHTML = `
                <span><strong>${moneda.nombre}</strong> (${moneda.codigo})</span>
                <span class="badge bg-success fs-6">${formatCurrency(moneda.total_recaudado, moneda.codigo, simbolo)}</span>
            `;
            listaBalance.appendChild(li);
        });
        
        // Estadísticas por método de pago
        if (Object.keys(estadisticasMetodos).length > 0) {
            const liMetodosTitulo = document.createElement("li");
            liMetodosTitulo.className = "list-group-item bg-info text-white fw-bold text-center mt-3";
            liMetodosTitulo.innerHTML = `💳 TRANSACCIONES POR MÉTODO DE PAGO`;
            listaBalance.appendChild(liMetodosTitulo);
            
            for (const [metodo, stats] of Object.entries(estadisticasMetodos)) {
                const li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center";
                li.innerHTML = `
                    <span>${metodo}</span>
                    <span class="badge bg-primary">${stats.transacciones} transacciones</span>
                `;
                listaBalance.appendChild(li);
            }
        }
        
        // Total general de transacciones
        const liTotal = document.createElement("li");
        liTotal.className = "list-group-item d-flex justify-content-between align-items-center bg-dark text-white";
        liTotal.innerHTML = `
            <span class="fw-bold">TOTAL TRANSACCIONES DEL DÍA</span>
            <span class="badge bg-warning text-dark fs-6">${totalTransacciones}</span>
        `;
        listaBalance.appendChild(liTotal);
        
    } else {
        listaBalance.innerHTML = `<li class="list-group-item text-center">Sin movimientos de caja</li>`;
    }

    // 4. Gráficos
    renderizarGraficos(data);
}

function formatCurrency(amount, currencyCode, simbolo = null) {
    const num = parseFloat(amount) || 0;
    
    // Usar el símbolo proporcionado o uno por defecto según el código
    if (simbolo) {
        return `${simbolo} ${num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    
    // Fallback según código de moneda
    switch (currencyCode) {
        case 'USD':
            return `$ ${num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        case 'EUR':
            return `€ ${num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        case 'VES':
        case 'BS':
            return `Bs. ${num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        default:
            return `${currencyCode} ${num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
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
