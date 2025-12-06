<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center p-3 p-md-5">
        <div class="col-12 Quick-title pb-4 px-5">
            <h1>Estadísticas de Inventario</h1>
            <p class="text-secondary">Resumen visual del estado del stock general</p>
        </div>

        <div class="col-12 col-md-10">
            <div class="row Quick-widget mb-3">
                <!-- STOCK BAJO -->
                <div class="col-12 col-md-6 p-3 mb-4 rounded-3 d-flex flex-column">
                    <h4 class="Quick-title mb-3">Productos con Stock Bajo</h4>
                    <div class="Quick-chart w-100" style="height: 300px;">
                        <canvas id="chartStockBajo"></canvas>
                    </div>
                </div>
                <!-- STOCK ALTO -->
                <div class="col-12 col-md-6 p-3 mb-4 rounded-3 d-flex flex-column">
                    <h4 class="Quick-title mb-3">Productos con Mayor Stock</h4>
                    <div class="Quick-chart w-100" style="height: 300px;">
                        <canvas id="chartStockAlto"></canvas>
                    </div>
                </div>
            </div>

            <!-- TABLA STOCK BAJO -->
            <div class="row Quick-widget mb-4 p-3 rounded-3">
                <div class="col-12">
                    <h4 class="Quick-title mb-3 text-danger">⚠️ Alerta de Stock Bajo</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Stock Actual</th>
                                    <th>Mínimo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_stock_bajo">
                                <tr>
                                    <td colspan="6" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- DISTRIBUCIÓN POR CATEGORÍA -->
        <div class="col-12 col-md-10 Quick-widget p-3 mb-4 rounded-3 d-flex flex-column justify-content-md-center align-items-md-center">
            <h4 class="Quick-title mb-3">Distribución de Stock por Categoría</h4>
            <div class="Quick-chart w-100" style="height: 300px;">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>

        <!-- STOCK POR SUCURSAL -->
        <div class="col-12 col-md-10 Quick-widget p-3 mb-4 rounded-3">
            <h4 class="Quick-title">Stock por Sucursal</h4>
            <div class="Quick-chart w-100" style="height: 300px;">
                <canvas id="chartSucursales"></canvas>
            </div>
        </div>

    </div>
</div>

<script type="module" src="view/js/inventario-stock-bajo.js"></script>