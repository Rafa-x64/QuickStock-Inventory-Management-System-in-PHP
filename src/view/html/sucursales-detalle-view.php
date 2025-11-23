<?php
$accion = $_POST["accion"] ?? null;
$id_sucursal = $_POST["id_sucursal"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">

            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle de la Sucursal</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="sucursales-listado">Sucursal/</a></li>
                            <li class="" id="breadcrumb-nombre">Cargando...</li>
                        </ol>
                    </nav>
                </div>
                <a href="sucursales-listado" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <!-- Información General -->
            <div class="card Quick-card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> Información General
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" id="id_sucursal_hidden" value="<?php echo $id_sucursal ?>">

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Código Sucursal</label>
                                <div class="form-control bg-light" id="s_codigo_sucursal">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Nombre</label>
                                <div class="form-control bg-light" id="s_nombre">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Dirección</label>
                                <div class="form-control bg-light" id="s_direccion">Cargando...</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Teléfono</label>
                                <div class="form-control bg-light" id="s_telefono">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">RIF</label>
                                <div class="form-control bg-light" id="s_rif">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Estado</label>
                                <div class="form-control bg-light" id="s_estado">
                                    <span class="badge bg-secondary">Cargando...</span>
                                </div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Fecha de Registro</label>
                                <div class="form-control bg-light" id="s_fecha_registro">Cargando...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card Quick-card">
                <div class="card-header bg-warning">
                    <h5 class="text-white">Empleados asociados</h5>
                </div>
                <div class="card-body m-0">
                    <div class="row m-0 p-0">
                        <div class="col-12 Quick-table m-0 p-0">
                            <table class="w-100">
                                <thead>
                                    <tr>
                                        <th>nombre</th>
                                        <th>cargo</th>
                                        <th>estado</th>
                                        <th>telefono</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_empleados">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/sucursales-detalle.js"></script>