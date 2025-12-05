<?php
$accion = $_POST["accion"] ?? null;
$id_rol = $_POST["id_rol"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle del Rol</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="empleados-lista-roles">Roles</a></li>
                            <li class="breadcrumb-item" id="breadcrumb_nombre">Rol</li>
                        </ol>
                    </nav>
                </div>
                <a href="empleados-lista-roles" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a la lista
                </a>
            </div>

            <!-- Input hidden para el ID del rol -->
            <input type="hidden" id="id_rol" value="<?php echo $id_rol ?>">

            <!-- Información del Rol -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock"></i> Información del Rol
                    </h5>
                    <form action="empleados-editar-rol" method="POST" class="d-inline">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_rol" value="<?php echo $id_rol ?>">
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Editar Rol
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">ID Rol</label>
                                <div class="form-control bg-light" id="detalle_id">Cargando...</div>
                            </div>
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Nombre del Rol</label>
                                <div class="form-control bg-light" id="detalle_nombre">Cargando...</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Usuarios Asignados</label>
                                <div class="form-control bg-light" id="detalle_usuarios">Cargando...</div>
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
                                <label class="form-label fw-bold text-muted small">Descripción</label>
                                <div class="form-control bg-light" id="detalle_descripcion">Cargando...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/roles-detalle.js"></script>