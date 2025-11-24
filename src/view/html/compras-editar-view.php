<?php
// Recibir el ID de la compra a editar
// Asumo que este archivo se incluye o es la vista principal, y el ID se recibe por GET o POST.
// Si se está editando, típicamente se recibe por GET, por ejemplo: editar.php?id=123
// Sin embargo, utilizaremos la variable $id_compra que mencionaste para la lógica de carga.

// La variable $id_compra debería ser establecida antes de este bloque si la vista no lo hace.
// Ejemplo: $id_compra = $_GET["id_compra"] ?? null; 
// Para el ejemplo, asumo que se está cargando el ID. Si no hay ID, se comporta como un formulario vacío (aunque esto no es ideal para una vista de edición).
$id_compra = $_REQUEST["id_compra"] ?? null;
?>

<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-3 p-md-5">
            <div class="row justify-content-center align-items-center">

                <div class="col-12 text-center p-2 p-md-5 Quick-title">
                    <h1 class="Quick-title">Editar Compra #<?php echo htmlspecialchars($id_compra ?? 'N/A'); ?></h1>
                </div>

                <div class="Quick-widget col-12 col-lg-12 p-2 p-md-4 mt-3">
                    <div class="col-12 Quick-form p-3 p-md-5 rounded-2">

                        <form action="" method="POST" id="formCompraProducto" class="py-2 needs-validate" novalidate>
                            <input type="hidden" name="id_compra" id="compra_id" value="<?php echo htmlspecialchars($id_compra ?? ''); ?>">

                            <div class="row">
                                <div class="col-12">
                                    <h4 class="Quick-title">1. Información Principal</h4>
                                    <hr>
                                </div>

                                <div class="col-md-3 py-2 position-relative">
                                    <label class="Quick-title" for="compra_fecha_compra">Fecha de Compra</label>
                                    <input type="date" id="compra_fecha_compra" name="fecha_compra" class="Quick-form-input" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-md-3 py-2 position-relative">
                                    <label class="Quick-title" for="compra_id_proveedor">Proveedor</label>
                                    <select id="compra_id_proveedor" name="id_proveedor" class="Quick-form-input" required>
                                        <option value="">Seleccione un proveedor...</option>
                                    </select>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-md-3 py-2 position-relative ">
                                    <label class="Quick-title" for="compra_id_sucursal">Sucursal</label>
                                    <select id="compra_id_sucursal" name="id_sucursal" class="Quick-form-input" required>
                                        <option value="">Seleccione una sucursal...</option>
                                    </select>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-md-3 py-2 position-relative">
                                    <label class="Quick-title" for="compra_id_usuario">Empleado Responsable</label>
                                    <select id="compra_id_usuario" name="id_usuario" class="Quick-form-input" required>
                                        <option value="">Seleccione un empleado...</option>
                                    </select>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-md-3 py-2 position-relative">
                                    <label class="Quick-title" for="compra_numero_factura">Número de Factura</label>
                                    <input type="text" id="compra_numero_factura" name="numero_factura" class="Quick-form-input" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-md-3 py-2 position-relative">
                                    <label class="Quick-title" for="compra_id_moneda">Moneda</label>
                                    <select id="compra_id_moneda" name="id_moneda" class="Quick-form-input" required>
                                        <option value="">Seleccione una moneda...</option>
                                    </select>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-md-3 py-2 position-relative">
                                    <label class="Quick-title" for="compra_estado">Estado</label>
                                    <select name="estado" id="compra_estado" class="Quick-form-input w-100">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="parcial">Pago Parcial</option>
                                        <option value="pagado">Pagado</option>
                                    </select>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <div class="col-12 py-2 position-relative">
                                    <label class="Quick-title" for="compra_observaciones">Observaciones</label>
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <textarea id="compra_observaciones" name="observaciones" class="Quick-form-input w-100" rows="3"></textarea>
                                    </div>
                                    <div class="invalid-tooltip"></div>
                                </div>

                            </div>
                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-between">
                                    <h4 class="Quick-title">2. Productos Adquiridos</h4>
                                    <button type="button" id="btnAgregarProducto" class="btn btn-warning btn-sm">
                                        <i class="bi bi-plus-circle"></i> Agregar Producto
                                    </button>
                                </div>
                                <hr>
                                <div id="productosContainer" class="col-12 Quick-form">
                                </div>
                            </div>


                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <label class="Quick-title">Subtotal</label>
                                    <input type="text" id="compra_subtotal" readonly class="Quick-form-input" value="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="Quick-title">IVA (16%)</label>
                                    <input type="text" id="compra_iva" readonly class="Quick-form-input" value="0.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="Quick-title">Total</label>
                                    <input type="text" id="compra_total" readonly class="Quick-form-input" value="0.00">
                                </div>
                            </div>


                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-around">
                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                    <button type="reset" class="btn btn-danger">Cancelar Edición</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
                <?php /*
                require_once "model/inventario.compra.php";
                // En el caso de edición, necesitarías un controlador diferente (ej: compras_editar_C.php)
                require_once "controller/compras_editar_C.php"; // <--- Nuevo Controlador para Editar

                $respuesta = ["success" => false, "message" => "Ocurrió un error inesperado."];

                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    // La lógica para editar/actualizar la compra irá en el nuevo controlador.
                    $controladorCompra = new compras_editar_C();
                    $resultadoTransaccion = $controladorCompra->actualizarCompra(); // <--- Nuevo método

                    if (isset($resultadoTransaccion['success']) && $resultadoTransaccion['success'] === true) {
                        $id_compra_act = $resultadoTransaccion['id_compra'];
                        $respuesta['success'] = true;
                        $respuesta['message'] = "✅ Compra #$id_compra_act actualizada exitosamente.";
                    } else {
                        $respuesta['message'] = "❌ Error al actualizar la compra: " . ($resultadoTransaccion['error'] ?? 'Error desconocido.');
                    }
                }

                echo '<script>alert("' . $respuesta['message'] . '")</script>';
                */ ?>
            </div>
        </div>
    </div>
</div>

<script type="module" src="api/client/compras-editar.js"></script>
<script type="module" src="view/js/compras-editar.js"></script>