<?php
$accion = $_POST["accion"] ?? null;
// Verificar permisos si es necesario
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <div class="row d-flex flex-row justify-content-between align-items-center mb-4">
                <div class="col-12 col-md-6 Quick-title">
                    <h1 class="m-0">Tasas de Cambio</h1>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-end">
                    <button class="btn btn-primary" id="btn-sync-api">
                        <i class="bi bi-arrow-repeat"></i> Sincronizar Tasas (API)
                    </button>
                    <a href="monedas-historial" class="btn btn-secondary ms-2">
                        <i class="bi bi-clock-history"></i> Ver Historial
                    </a>
                </div>
            </div>

            <!-- Cards Container -->
            <div class="row mb-4" id="container-tasas-cards">
                <!-- Se llena dinamicamente con JS -->
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>

            <!-- Formulario Manual -->
            <div class="row d-flex flex-column justify-content-center align-items-center">
                <div class="col-12 col-md-8 Quick-widget">
                    <div class="col-12 Quick-form px-5 rounded-2">
                        <h4 class="Quick-title mb-3">Establecer Tasa Manual</h4>

                        <form id="form-tasa-manual" class="form">
                            <div class="row">
                                <div class="col-12 col-md-6 py-2">
                                    <label class="form-label Quick-title">Moneda</label>
                                    <select class="form-select Quick-select" id="select_moneda_manual" required>
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 py-2">
                                    <label class="form-label Quick-title">Nueva Tasa (vs USD)</label>
                                    <input type="number" step="0.0001" class="form-control Quick-form-input" id="valor_manual" required placeholder="Ej: 50.1234">
                                </div>
                                <div class="col-12 py-3 text-end">
                                    <button type="submit" class="btn btn-warning fw-bold">
                                        <i class="bi bi-save"></i> Guardar Tasa Manual
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="view/js/monedas.js"></script>