<?php
$accion = $_POST["accion"] ?? null;
$id_cliente = $_POST["id_cliente"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle del Cliente</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="clientes-listado">Clientes</a></li>
                            <li class="breadcrumb-item" id="breadcrumb_nombre">Cliente</li>
                        </ol>
                    </nav>
                </div>
                <a href="clientes-listado" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <!-- Input hidden para el ID del cliente -->
            <input type="hidden" id="id_cliente" value="<?php echo $id_cliente ?>">

            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge"></i> Información del Cliente
                    </h5>
                    <form action="clientes-editar" method="POST" class="d-inline">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_cliente" value="<?php echo $id_cliente ?>">
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Editar Cliente
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">ID Cliente</label>
                                <div class="form-control bg-light" id="detalle_id">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Nombre</label>
                                <div class="form-control bg-light" id="detalle_nombre">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Apellido</label>
                                <div class="form-control bg-light" id="detalle_apellido">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Cédula</label>
                                <div class="form-control bg-light" id="detalle_cedula">Cargando...</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Teléfono</label>
                                <div class="form-control bg-light" id="detalle_telefono">Cargando...</div>
                            </div>
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

<script type="module" src="api/client/clientes-detalle.js"></script>