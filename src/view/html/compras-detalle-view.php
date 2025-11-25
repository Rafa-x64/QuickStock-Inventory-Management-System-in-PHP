<?php
$accion = $_POST["accion"] ?? null;
$id_compra  = $_POST["id_compra"]  ?? ($_POST["id_compra"] ?? null);
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h1 class="mb-1 Quick-title">Detalle de la Compra</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="compras-ver-listado-view.php">Compras/</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cargando...</li>
                        </ol>
                    </nav>
                </div>
                <a href="compras-historial" class="btn btn-outline-secondary mt-2 mt-md-0">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Información General de la Compra
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <input type="hidden" name="id_compra_detalle" id="id_compra_detalle" value="<?php echo htmlspecialchars($id_compra); ?>">

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Código de Compra</label>
                            <div class="form-control bg-light" id="info_id_compra">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Fecha de Compra</label>
                            <div class="form-control bg-light" id="info_fecha_compra">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Proveedor</label>
                            <div class="form-control bg-light" id="info_nombre_proveedor">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Sucursal</label>
                            <div class="form-control bg-light" id="info_nombre_sucursal">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Empleado Responsable</label>
                            <div class="form-control bg-light" id="info_empleado_responsable">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Moneda</label>
                            <div class="form-control bg-light" id="info_moneda">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Estado</label>
                            <div class="form-control bg-light" id="info_estado">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Número de Factura</label>
                            <div class="form-control bg-light" id="info_numero_factura">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3 d-none d-lg-block"></div>

                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator"></i> Totales de la Compra
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Subtotal</label>
                            <div class="form-control bg-light" id="total_subtotal">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Impuesto</label>
                            <div class="form-control bg-light" id="total_impuesto">cargando...</div>
                        </div>

                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label fw-bold text-muted small">Total</label>
                            <div class="form-control bg-light" id="total_total">cargando...</div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-card-text"></i> Observaciones
                    </h5>
                </div>

                <div class="card-body">
                    <textarea class="form-control" rows="3" id="info_observaciones" disabled>cargando...</textarea>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam"></i> Productos Comprados
                    </h5>
                    <span id="cantidadItems" class="badge bg-light text-dark">cargando...</span>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Color</th>
                                    <th>Talla</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>

                            <tbody id="tablaProductos">
                                <tr>
                                    <td colspan="8" class="text-center py-4">Cargando productos...</td>
                                </tr>
                            </tbody>

                            <tfoot class="table-active">
                                <tr>
                                    <td colspan="7" class="fw-bold text-end">Subtotal:</td>
                                    <td class="fw-bold" id="footSubtotal">cargando...</td>
                                </tr>

                                <tr>
                                    <td colspan="7" class="fw-bold text-end">Impuesto:</td>
                                    <td class="fw-bold" id="footImpuesto">cargando...</td>
                                </tr>

                                <tr>
                                    <td colspan="7" class="fw-bold text-end">Total:</td>
                                    <td class="fw-bold text-success" id="footTotal">cargando...</td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/compras-detalle.js"></script>