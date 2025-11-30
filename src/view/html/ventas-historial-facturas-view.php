<?php
$accion = $_POST["accion"] ?? null;
$id_venta = $_POST["id_venta"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <div class="row">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1>Historial de Facturas</h1>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row Quick-widget p-3 mb-3">
                <div class="col-12">
                    <h5 class="Quick-title mb-3"><i class="bi bi-funnel"></i> Filtros</h5>
                </div>

                <div class="col-md-3">
                    <label class="">Fecha Desde</label>
                    <input type="date" id="filtro_fecha_desde" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="">Fecha Hasta</label>
                    <input type="date" id="filtro_fecha_hasta" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="">Vendedor</label>
                    <select id="filtro_vendedor" class="form-control">
                        <option value="">Todos</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="">Sucursal</label>
                    <select id="filtro_sucursal" class="form-control">
                        <option value="">Todas</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="">Monto Mínimo</label>
                    <input type="number" step="0.01" id="filtro_monto_min" class="form-control" placeholder="0.00">
                </div>

                <div class="col-md-3">
                    <label class="">Monto Máximo</label>
                    <input type="number" step="0.01" id="filtro_monto_max" class="form-control" placeholder="0.00">
                </div>

                <div class="col-md-3">
                    <label class="">Método de Pago</label>
                    <select id="filtro_metodo_pago" class="form-control">
                        <option value="">Todos</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="">Moneda</label>
                    <select id="filtro_moneda" class="form-control">
                        <option value="">Todas</option>
                    </select>
                </div>

                <div class="col-12 mt-3">
                    <button id="btn_reestablecer_filtros" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reestablecer Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de Ventas -->
            <div class="row Quick-widget p-0">
                <div class="col-12 Quick-table p-1 p-md-3">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <th class="ps-1">ID Venta</th>
                                <th class="ps-1">Fecha</th>
                                <th class="ps-1">Cliente</th>
                                <th class="ps-1">Vendedor</th>
                                <th class="ps-1">Sucursal</th>
                                <th class="ps-1">Total</th>
                                <th class="ps-1">Método Pago</th>
                                <th class="ps-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_ventas">
                            <tr>
                                <td colspan="8" class="text-center">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/ventas-historial-facturas.js"></script>