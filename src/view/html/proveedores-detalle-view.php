<?php
$accion = $_POST["accion"] ?? null;
$id_proveedor = $_POST["id_proveedor"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle del Proveedor</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="proveedores-listado">Proveedores</a></li>
                            <li class="breadcrumb-item" id="breadcrumb_nombre">Proveedor</li>
                        </ol>
                    </nav>
                </div>
                <a href="proveedores-listado" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <!-- Input hidden para el ID del proveedor -->
            <input type="hidden" id="id_proveedor" value="<?php echo $id_proveedor ?>">

            <!-- Información del Proveedor -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> Información del Proveedor
                    </h5>
                    <form action="proveedores-editar" method="POST" class="d-inline">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_proveedor" value="<?php echo $id_proveedor ?>">
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Editar Proveedor
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">ID Proveedor</label>
                                <div class="form-control bg-light" id="detalle_id">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Nombre / Razón Social</label>
                                <div class="form-control bg-light" id="detalle_nombre">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Teléfono</label>
                                <div class="form-control bg-light" id="detalle_telefono">Cargando...</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Correo Electrónico</label>
                                <div class="form-control bg-light" id="detalle_correo">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Estado</label>
                                <div class="form-control bg-light">
                                    <span class="badge bg-secondary" id="detalle_estado">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Dirección</label>
                                <div class="form-control bg-light" id="detalle_direccion">Cargando...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/proveedores-detalle.js"></script>