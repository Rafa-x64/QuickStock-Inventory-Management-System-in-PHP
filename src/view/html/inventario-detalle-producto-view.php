<?php
$accion = $_POST["accion"] ?? null;
$id_producto = $_POST["id_producto"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">

            <input type="hidden" id="id_producto_hidden" value="<?php echo htmlspecialchars($id_producto); ?>">

            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle del Producto</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="inventario-ver-productos">Inventario</a></li>
                            <li class="breadcrumb-item" id="breadcrumb-nombre">Producto</li>
                        </ol>
                    </nav>
                </div>
                <a href="inventario-ver-productos" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Información General del Producto -->
            <div class="card mb-4 shadow-sm Quick-card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box"></i> Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Código de Barra</label>
                                <div class="form-control bg-light" id="p_codigo_barra">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Nombre</label>
                                <div class="form-control bg-light" id="p_nombre">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Categoría</label>
                                <div class="form-control bg-light" id="p_categoria">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Color</label>
                                <div class="form-control bg-light" id="p_color">Cargando...</div>
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Talla</label>
                                <div class="form-control bg-light" id="p_talla">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Precio Venta</label>
                                <div class="form-control bg-light text-success fw-bold" id="p_precio_venta">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Precio Compra</label>
                                <div class="form-control bg-light" id="p_precio_compra">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Proveedor</label>
                                <div class="form-control bg-light" id="p_proveedor">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Estado</label>
                                <div class="form-control bg-light" id="p_estado">Cargando...</div>
                            </div>
                        </div>

                    </div>

                    <div class="info-group mb-3">
                        <label class="fw-bold text-muted small">Descripción</label>
                        <div class="form-control bg-light" id="p_descripcion" style="height:auto;">Cargando...</div>
                    </div>

                </div>
            </div>

            <!-- Inventario por Sucursal -->
            <div class="card mb-4 shadow-sm Quick-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-shop"></i> Distribución por Sucursal</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="Quick-table" id="tabla_inventario">
                            <thead class="table-light">
                                <tr>
                                    <th>Sucursal</th>
                                    <th>Stock Disponible</th>
                                    <th>Stock Mínimo</th>
                                    <th>Última Actualización</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llena con JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Producto -->
            <div class="card shadow-sm Quick-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded sucursal shadow-sm Quick-widget">
                                <h6 class="">Total en Inventario</h6>
                                <h4 class="fw-bold text-primary" id="total_inventario">0</h4>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded sucursal shadow-sm Quick-widget">
                                <h6 class="">Stock Mínimo Global</h6>
                                <h4 class="fw-bold text-danger" id="stock_minimo_global">0</h4>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded sucursal shadow-sm Quick-widget">
                                <h6 class="">Sucursales con Bajo Stock</h6>
                                <h4 class="fw-bold text-warning" id="sucursales_bajo_stock">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/inventario-detalle-producto.js"></script>