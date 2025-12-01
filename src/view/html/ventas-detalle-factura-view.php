<?php
$accion = $_POST["accion"] ?? null;
$id_venta = $_POST["id_venta"] ?? null;

if (empty($id_venta)) {
    echo '<script>alert("No se recibió un ID de venta válido."); window.location.href = "ventas-historial-facturas";</script>';
    exit();
}
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">

            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle de la Factura</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="ventas-historial-facturas">Facturas</a>/</li>
                            <li class="" id="breadcrumb-id">Cargando...</li>
                        </ol>
                    </nav>
                </div>
                <a href="ventas-historial-facturas" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>

            <!-- Información General de la Venta -->
            <div class="card Quick-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Información de la Venta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" id="id_venta_hidden" value="<?php echo $id_venta; ?>">

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">ID Venta</label>
                                <div class="form-control bg-light" id="v_id_venta">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Fecha</label>
                                <div class="form-control bg-light" id="v_fecha">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Cliente</label>
                                <div class="form-control bg-light" id="v_cliente">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Cédula</label>
                                <div class="form-control bg-light" id="v_cedula">Cargando...</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Vendedor</label>
                                <div class="form-control bg-light" id="v_vendedor">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Sucursal</label>
                                <div class="form-control bg-light" id="v_sucursal">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Total</label>
                                <div class="form-control bg-light" id="v_total">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Teléfono Cliente</label>
                                <div class="form-control bg-light" id="v_telefono">Cargando...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos Vendidos -->
            <div class="card Quick-card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="text-white"><i class="bi bi-box-seam"></i> Productos Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="Quick-table">
                        <table class="w-100">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Categoría</th>
                                    <th>Color / Talla</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_productos">
                                <tr>
                                    <td colspan="7" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagos Realizados -->
            <div class="card Quick-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> Pagos Realizados</h5>
                </div>
                <div class="card-body">
                    <div class="Quick-table">
                        <table class="w-100">
                            <thead>
                                <tr>
                                    <th>Método de Pago</th>
                                    <th>Moneda</th>
                                    <th>Monto</th>
                                    <th>Tasa</th>
                                    <th>Referencia</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_pagos">
                                <tr>
                                    <td colspan="5" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/ventas-detalle-factura.js"></script>