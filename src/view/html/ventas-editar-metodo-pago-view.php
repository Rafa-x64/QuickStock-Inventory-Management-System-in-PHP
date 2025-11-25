<?php
// Recoger el ID del método de pago enviado por POST desde el listado
$id_metodo = $_POST['id_metodo_pago'] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Editar Método de Pago</h1>
                </div>

                <div class="Quick-widget col-12 col-md-8 p-0 p-2">
                    <div class="col-12 Quick-form px-4 rounded-2">
                        <form action="" method="POST" class="form py-3 needs-validate" id="formMetodoPago" novalidate>
                            <input type="hidden" id="id_metodo_pago" name="id_metodo_pago" value="<?php echo htmlspecialchars($id_metodo); ?>">

                            <div class="row d-flex flex-row justify-content-center align-items-center">

                                <!-- Nombre del Método -->
                                <div class="col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="nombre_metodo" class="form-label Quick-title">Nombre del Método de Pago</label>
                                    <input type="text" id="nombre_metodo" name="nombre_metodo" class="Quick-form-input" maxlength="50" placeholder="Ej: Zelle, Pago Móvil..." required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Requiere Referencia -->
                                <div class="col-md-6 d-flex flex-column py-3 position-relative">
                                    <label class="form-label Quick-title">¿Requiere Referencia?</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="referencia_metodo" name="referencia_metodo">
                                        <label class="form-check-label" for="referencia_metodo">Sí, requiere número de referencia</label>
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="col-12 d-flex flex-column py-3 position-relative">
                                    <label for="descripcion_metodo" class="form-label Quick-title">Descripción</label>
                                    <textarea id="descripcion_metodo" name="descripcion_metodo" class="Quick-form-input" rows="3" maxlength="255" placeholder="Breve descripción..."></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Estado Activo -->
                                <div class="col-12 d-flex flex-column py-3 position-relative">
                                    <label class="form-label Quick-title">Estado</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_metodo" name="activo_metodo">
                                        <label class="form-check-label" for="activo_metodo">Método de pago activo</label>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-row justify-content-center align-items-center py-3">
                                    <div class="row w-100 d-flex justify-content-around">
                                        <div class="col-5 col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-warning text-white w-100">Actualizar</button>
                                        </div>
                                        <div class="col-5 col-md-3 d-flex justify-content-center">
                                            <a href="ventas-lista-metodos-pago" class="btn btn-danger w-100">Cancelar</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_metodo'])) {
                include_once "controller/ventas_metodos_pago_C.php";

                $resultado = ventas_metodos_pago_C::editarMetodoPago($_POST);

                if (isset($resultado['error'])) {
                    echo '<script>alert("' . $resultado['error'] . '");</script>';
                } else {
                    echo '<script>alert("' . $resultado['success'] . '");</script>';
                    echo '<script>window.location.href = "ventas-lista-metodos-pago";</script>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script type="module" src="api/client/ventas-editar-metodo-pago.js"></script>
<script src="view/js/ventas-editar-metodo-pago.js"></script>