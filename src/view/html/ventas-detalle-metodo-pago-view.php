<?php
// Recoger el ID del método de pago enviado por POST desde el listado
$id_metodo = $_POST['id_metodo_pago'] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Detalle del Método de Pago</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="ventas-lista-metodos-pago">Ventas /</a></li>
                            <li class="" id="breadcrumb-nombre">Cargando...</li>
                        </ol>
                    </nav>
                </div>
                <a href="ventas-lista-metodos-pago" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>

            <input type="hidden" id="id_metodo_pago" value="<?php echo htmlspecialchars($id_metodo); ?>">

            <div class="card Quick-card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="detalle-nombre">
                        <i class="bi bi-credit-card"></i> Información General
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">ID del Método</label>
                                <div class="form-control bg-light" id="detalle-id">Cargando...</div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Nombre</label>
                                <div class="form-control bg-light" id="detalle-nombre-completo">Cargando...</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Requiere Referencia</label>
                                <div class="form-control bg-light" id="detalle-referencia">
                                    <span class="badge bg-secondary">Cargando...</span>
                                </div>
                            </div>

                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Estado</label>
                                <div class="form-control bg-light" id="detalle-estado">
                                    <span class="badge bg-secondary">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="info-group mb-3">
                                <label class="form-label fw-bold text-muted small">Descripción</label>
                                <div class="form-control bg-light" id="detalle-descripcion" style="min-height: 80px;">Cargando...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card Quick-card">
                <div class="card-header bg-warning">
                    <h5 class="text-white">Transacciones Asociadas (Opcional)</h5>
                </div>
                <div class="card-body m-0">
                    <p>Aquí puedes mostrar un resumen o una tabla de las últimas transacciones que usaron este método de pago.</p>
                    <div class="col-12 Quick-table m-0 p-0">
                        <table class="w-100">
                            <thead>
                                <tr>
                                    <th># Transacción</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_transacciones">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aún no hay datos de transacciones.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/ventas-detalle-metodo-pago.js"></script>