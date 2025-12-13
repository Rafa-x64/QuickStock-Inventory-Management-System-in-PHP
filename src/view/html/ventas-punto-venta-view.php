<?php
// Obtener datos de sesión para sucursal
// Según estructura de sesión: $_SESSION['sesion_usuario']['sucursal']['id_sucursal']
$id_sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;
$nombre_sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['nombre_sucursal'] ?? null;

// Si no hay id_sucursal en la sesión (ej: Gerente sin sucursal asignada), usar la sucursal 5 por defecto
if (empty($id_sucursal_sesion)) {
    $id_sucursal_sesion = 5;
}

// Si no hay nombre en sesión y es la ID 5, asignar nombre por defecto
if (!$nombre_sucursal_sesion && $id_sucursal_sesion == 5) {
    $nombre_sucursal_sesion = "Global Sport (Principal)";
} elseif (!$nombre_sucursal_sesion) {
    $nombre_sucursal_sesion = "Sucursal ID: " . $id_sucursal_sesion;
}

$fecha_actual = date('Y-m-d\TH:i');
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-white text-center bg-primary">
                    <h4 class="mb-0">Módulo de Punto de Venta (Wizard)</h4>
                </div>
                <div class="card-body">

                    <!-- Navegación Wizard -->
                    <div class="mb-4">
                        <ul class="nav nav-pills nav-justified" id="wizard-steps">
                            <li class="nav-item"><a class="nav-link active" data-step="0" href="#">0. Registrar Cliente</a></li>
                            <li class="nav-item"><a class="nav-link disabled" data-step="1" href="#">1. Venta General</a></li>
                            <li class="nav-item"><a class="nav-link disabled" data-step="2" href="#">2. Detalle de Productos</a></li>
                            <li class="nav-item"><a class="nav-link disabled" data-step="3" href="#">3. Pago y Confirmación</a></li>
                        </ul>

                        <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar" style="width:25%"></div>
                        </div>
                    </div>

                    <form id="ventaWizardForm" class="quick-form">

                        <!-- REGISTRAR CLIENTE -->
                        <div class="wizard-step" data-step="0">
                            <h5>Registrar Cliente</h5>
                            <hr>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">Cédula / RIF</label>
                                    <input type="text" id="cliente_cedula" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" id="cliente_nombre" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" id="cliente_apellido" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo</label>
                                    <input type="email" id="cliente_email" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" id="cliente_telefono" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" id="cliente_direccion" class="form-control" required>
                                </div>

                            </div>
                        </div>

                        <!-- DATOS GENERALES -->
                        <div class="wizard-step" data-step="1">
                            <h5>Datos Generales de la Venta</h5>
                            <hr>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Sucursal</label>
                                    <!-- Hidden ID for JS -->
                                    <input type="hidden" id="idSucursalHidden" value="<?php echo $id_sucursal_sesion; ?>">
                                    <input type="text" id="nombreSucursalDisplay" class="form-control" value="<?php echo $nombre_sucursal_sesion; ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Vendedor</label>
                                    <select id="idUsuario" class="form-select" required>
                                        <option value="">Cargando vendedores...</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Fecha y Hora</label>
                                    <input type="datetime-local" id="fechaHora" class="form-control" value="<?php echo $fecha_actual; ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Moneda de Venta</label>
                                    <select id="idMoneda" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1">USD</option>
                                        <option value="2">EUR</option>
                                        <option value="3">VES</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- PASO 2: DETALLE DE PRODUCTOS -->
                        <div class="wizard-step" data-step="2">
                            <h5>Detalle de Productos</h5>
                            <hr>

                            <!-- Sección de Búsqueda de Productos -->
                            <div class="card mb-3 border-primary">
                                <div class="card-body bg-light">
                                    <h6 class="card-title">Agregar Producto</h6>
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small">Código de Barras</label>
                                            <input type="text" id="prod_codigo_barra" class="form-control form-control-sm" placeholder="Escanear o escribir...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Categoría</label>
                                            <input type="text" id="prod_categoria" class="form-control form-control-sm" readonly placeholder="Categoría del producto">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Color</label>
                                            <select id="prod_color" class="form-select form-select-sm">
                                                <option value="">Todos</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small">Talla</label>
                                            <select id="prod_talla" class="form-select form-select-sm">
                                                <option value="">Todas</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary btn-sm w-100" id="btnBuscarAgregar">
                                                <i class="bi bi-plus-lg"></i> Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive quick-table">
                                <table class="table table-striped align-middle" id="detalleVentaTable">
                                    <thead>
                                        <tr>
                                            <th>Producto / SKU</th>
                                            <th>Detalles</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                                            <td class="fw-bold" id="totalVentaDisplay">$0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- PASO 3: PAGO -->
                        <div class="wizard-step" data-step="3">
                            <h5>Pago y Confirmación</h5>
                            <hr>

                            <div class="row g-3">

                                <!-- Formulario para AGREGAR un pago -->
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-light text-dark">
                                            <h6 class="mb-0"><i class="bi bi-wallet2"></i> Agregar Método de Pago</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <label class="form-label small">Método</label>
                                                    <select id="idMetodoPago" class="form-select form-select-sm">
                                                        <option value="">Cargando...</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Moneda</label>
                                                    <select id="idMonedaPago" class="form-select form-select-sm">
                                                        <option value="">Seleccione...</option>
                                                        <option value="1">USD</option>
                                                        <option value="2">EUR</option>
                                                        <option value="3">VES</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">Tasa</label>
                                                    <input type="text" id="tasaConversion" class="form-control form-control-sm" placeholder="1.00" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">Monto</label>
                                                    <input type="number" step="0.01" id="montoPagado" class="form-control form-control-sm" placeholder="0.00">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-info btn-sm w-100 text-white" id="btnAgregarPago">
                                                        <i class="bi bi-plus-circle"></i> Agregar
                                                    </button>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small">Referencia (Opcional)</label>
                                                    <input type="text" id="referenciaPago" class="form-control form-control-sm" placeholder="Nro de referencia, lote, etc.">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de Pagos Agregados -->
                                <div class="col-12 mt-3">
                                    <h6 class="text-secondary">Pagos Registrados</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped" id="tablaPagos">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Método</th>
                                                    <th>Ref.</th>
                                                    <th>Monto Original</th>
                                                    <th>Tasa</th>
                                                    <th>Equivalente (Venta)</th>
                                                    <th style="width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Se llena dinámicamente con JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <!-- Totales Globales -->
                            <div class="alert alert-primary mt-3">
                                <div class="d-flex justify-content-between fs-5">
                                    <span>Total a Pagar:</span>
                                    <strong id="resumenTotal">$0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between fs-5 text-success">
                                    <span>Total Pagado:</span>
                                    <strong id="resumenPagado">$0.00</strong>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fs-4 fw-bold" id="containerRestante">
                                    <span>Restante:</span>
                                    <span id="resumenRestante">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between fs-5 text-muted mt-2" id="containerCambio" style="display: none !important;">
                                    <span>Cambio:</span>
                                    <span id="resumenCambio">$0.00</span>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <label class="form-label">Comentario General</label>
                                <textarea id="pagoComentario" class="form-control" rows="2" placeholder="Opcional"></textarea>
                            </div>
                        </div>

                        <!-- NAVEGACIÓN -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" id="prevBtn">← Anterior</button>
                            <button type="button" class="btn btn-primary" id="nextBtn">Siguiente →</button>
                            <button type="submit" class="btn btn-success" id="submitBtn">Finalizar Venta</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="module" src="view/js/ventas-punto-venta.js"></script>