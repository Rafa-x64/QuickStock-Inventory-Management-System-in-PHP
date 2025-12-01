<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center p-3 p-md-5">
        <div class="col-12 Quick-title pb-4 px-5">
            <h1>Estadís ticas de Ventas</h1>
            <p class="text-secondary">Resumen visual de productos más vendidos y desempeño general</p>
        </div>

        <!-- PRODUCTOS MÁS VENDIDOS -->
        <div class="row Quick-widget mb-3">
            <div class="col-12 col-md-6 p-3 mb-4 rounded-3 d-flex flex-column">
                <h4 class="Quick-title mb-3">Productos Más Vendidos</h4>
                <div class="Quick-chart w-100" style="height: 300px;">
                    <canvas id="chartProductosVendidos"></canvas>
                </div>
            </div>

            <div class="col-12 col-md-6 p-3 mb-4 rounded-3 d-flex flex-column">
                <h4 class="Quick-title mb-3">Categorías Más Vendidas</h4>
                <div class="Quick-chart w-100" style="height: 300px;">
                    <canvas id="chartCategoriasVendidas"></canvas>
                </div>
            </div>
        </div>

        <!-- VENTAS POR SUCURSAL -->
        <div class="col-12 col-md-10 Quick-widget p-3 mb-4 rounded-3 d-flex flex-column justify-content-md-center align-items-md-center">
            <h4 class="Quick-title mb-3">Ventas Totales por Sucursal</h4>
            <div class="Quick-chart w-100" style="height: 300px;">
                <canvas id="chartSucursalesVentas"></canvas>
            </div>
        </div>

        <!-- TENDENCIA MENSUAL -->
        <div class="col-12 col-md-10 Quick-widget p-3 mb-4 rounded-3">
            <h4 class="Quick-title mb-3">Tendencia de Ventas Mensual</h4>
            <div class="Quick-chart w-100" style="height: 300px;">
                <canvas id="chartTendenciaMensual"></canvas>
            </div>
        </div>

        <!-- VALOR TOTAL DE VENTAS -->
        <div class="col-12 col-md-8 Quick-widget p-4 mb-5 text-center">
            <h4 class="Quick-title mb-3">Valor Total deVentas</h4>
            <h2 id="valorTotalVentas" class="fw-bold text-success">$0.00</h2>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="module" src="api/client/ventas-productos-populares.js"></script>