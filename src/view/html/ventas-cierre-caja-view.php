<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <!-- Filtros y Acciones -->
            <div class="row mb-4 d-print-none">
                <div class="col-12 col-md-6 d-flex align-items-center gap-2">
                    <label for="fecha_cierre" class="fw-bold">Fecha de Cierre:</label>
                    <input type="date" id="fecha_cierre" class="form-control w-auto">
                    <button id="btn_buscar" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
                <div class="col-12 col-md-6 text-md-end mt-2 mt-md-0">
                    <button id="btn_imprimir" class="btn btn-secondary">
                        <i class="bi bi-printer"></i> Imprimir Reporte
                    </button>
                </div>
            </div>

            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Cierre de Caja - Reporte Diario</h4>
                </div>
                <div class="card-body">

                    <!-- 🧾 Información del Turno -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="Quick-title mb-2">Detalles Operativos</h5>
                            <p class="mb-1"><strong>Sucursal:</strong> <span id="info_sucursal">Cargando...</span></p>
                            <p class="mb-1"><strong>Fecha Apertura (1ra Venta):</strong> <span id="info_fecha_apertura">-</span></p>
                            <p class="mb-1"><strong>Fecha Cierre (Última Venta):</strong> <span id="info_fecha_cierre">-</span></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="Quick-title mb-2">Resumen de Ventas</h5>
                            <p class="mb-1"><strong>Total Transacciones:</strong> <span id="resumen_total_ventas">0</span></p>
                            <p class="mb-1"><strong>Ventas Anuladas:</strong> <span id="resumen_anuladas">0</span></p>
                        </div>
                    </div>

                    <hr>

                    <!-- 💳 Ingresos por método de pago -->
                    <h5 class="mt-4 Quick-title">Desglose de Ingresos</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle" id="tabla_pagos">
                            <thead class="table-light">
                                <tr>
                                    <th>Método de Pago</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-end">Monto Recaudado</th>
                                    <th class="text-center">Transacciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llena dinámicamente -->
                                <tr>
                                    <td colspan="4" class="text-center">Cargando datos...</td>
                                </tr>
                            </tbody>
                            <tfoot class="fw-bold bg-light">
                                <!-- Totales dinámicos -->
                            </tfoot>
                        </table>
                    </div>

                    <hr>

                    <!-- 💰 Balance Final -->
                    <h5 class="mt-4 Quick-title">Balance Final de Caja</h5>
                    <ul class="list-group list-group-flush" id="lista_balance">
                        <li class="list-group-item text-center">Cargando balance...</li>
                    </ul>

                    <div class="alert alert-info mt-3 d-print-none">
                        <i class="bi bi-info-circle"></i> Este reporte muestra el dinero recaudado basado en los pagos registrados en el sistema.
                    </div>

                    <hr class="d-print-none">

                    <!-- 📊 Resumen visual -->
                    <div class="row mt-4 avoid-break">
                        <div class="col-12 col-md-6 Quick-chart mb-3" style="height: 250px;">
                            <canvas id="chartCierreMetodos"></canvas>
                        </div>
                        <div class="col-12 col-md-6 Quick-chart mb-3" style="height: 250px;">
                            <canvas id="chartCierreVentasHora"></canvas>
                        </div>
                    </div>

                    <hr class="mt-5">

                    <!-- ✍️ Firma y Observaciones -->
                    <div class="row mt-4 avoid-break">
                        <div class="col-md-8">
                            <h6 class="Quick-title">Observaciones:</h6>
                            <textarea class="form-control" rows="3" placeholder="Escriba observaciones sobre diferencias de caja, devoluciones o incidencias..."></textarea>
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-end align-items-center mt-3 mt-md-0">
                            <p class="mb-1 fw-bold">Firma del Responsable</p>
                            <div class="w-75 border-bottom border-2 border-dark mt-5"></div>
                            <p class="text-muted mt-1">Firma y Sello</p>
                        </div>
                    </div>

                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script type="module" src="api/client/ventas-cierre-caja.js"></script>