<?php
$accion = $_POST["accion"] ?? null;
$email  = $_POST["email"]  ?? ($_POST["id_email"] ?? null);
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">

            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle del Empleado</h1>

                    <!-- Hidden para JS -->
                    <input type="hidden" id="id_email" value="<?php echo $email ?>">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="empleados-listado">Empleados</a></li>
                            <li class="breadcrumb-item" id="det_breadcrumb">Empleado</li>
                        </ol>
                    </nav>
                </div>
                <a href="empleados-listado" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Información General -->
            <div class="card mb-4 shadow-sm Quick-card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        <!-- Columna izquierda -->
                        <div class="col-md-6">

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Nombre</label>
                                <div class="form-control bg-light" id="det_nombre"></div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Apellido</label>
                                <div class="form-control bg-light" id="det_apellido"></div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Cédula</label>
                                <div class="form-control bg-light" id="det_cedula"></div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Rol</label>
                                <div class="form-control bg-light" id="det_rol"></div>
                            </div>

                        </div>

                        <!-- Columna derecha -->
                        <div class="col-md-6">

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Sucursal</label>
                                <div class="form-control bg-light" id="det_sucursal"></div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Email</label>
                                <div class="form-control bg-light" id="det_email"></div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Teléfono</label>
                                <div class="form-control bg-light" id="det_telefono"></div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Estado</label>
                                <div class="form-control bg-light" id="det_estado"></div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Dirección y Fecha -->
            <div class="card mb-4 shadow-sm Quick-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Información Adicional</h5>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Dirección</label>
                                <div class="form-control bg-light" id="det_direccion"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="fw-bold text-muted small">Fecha de registro</label>
                                <div class="form-control bg-light" id="det_fecha"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Actividad del Empleado -->
            <div class="card shadow-sm Quick-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Actividad del Empleado</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">

                        <div class="col-md-4 mb-3 Quick-widget">
                            <div class="p-3 border rounded shadow-sm">
                                <h6 class="">Ventas realizadas</h6>
                                <h4 class="fw-bold text-dark" id="det_ventas">84</h4>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 Quick-widget">
                            <div class="p-3 border rounded shadow-sm">
                                <h6 class="">Clientes atendidos</h6>
                                <h4 class="fw-bold text-primary" id="det_clientes">142</h4>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3 Quick-widget">
                            <div class="p-3 border rounded shadow-sm">
                                <h6 class="">Última actividad</h6>
                                <h4 class="fw-bold text-success" id="det_ultima_actividad">2025-11-10</h4>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/empleados-detalle.js"></script>