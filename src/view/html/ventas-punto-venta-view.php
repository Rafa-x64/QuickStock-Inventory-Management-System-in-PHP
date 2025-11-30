<?php
// Obtener datos de sesión para sucursal
$id_sucursal_sesion = $_SESSION['sesion_usuario']['id_sucursal'] ?? 5;
$nombre_sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['nombre'] ?? null;

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
                                            <select id="prod_categoria" class="form-select form-select-sm">
                                                <option value="">Todas</option>
                                            </select>
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

                                <div class="col-md-6">
                                    <label class="form-label">Método de Pago</label>
                                    <select id="idMetodoPago" class="form-select" required>
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Monto Pagado</label>
                                    <input type="number" id="montoPagado" step="0.01" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Moneda de Pago</label>
                                    <select id="idMonedaPago" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1">USD</option>
                                        <option value="2">EUR</option>
                                        <option value="3">VES</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Referencia</label>
                                    <input type="text" id="referenciaPago" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tasa de Conversión</label>
                                    <input type="number" step="0.0001" id="tasaConversion" class="form-control" placeholder="Ej: 40.5">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Comentario</label>
                                    <textarea id="pagoComentario" class="form-control" rows="2" placeholder="Opcional"></textarea>
                                </div>

                            </div>

                            <div class="alert alert-info mt-3">
                                <p class="mb-1"><strong>Total Venta:</strong> <span id="resumenTotal">$0.00</span></p>
                                <p class="mb-0"><strong>Cambio:</strong> <span id="resumenCambio">$0.00</span></p>
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