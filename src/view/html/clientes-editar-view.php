<style>
    .was-validated .form-control:invalid~.invalid-tooltip,
    .form-control.is-invalid~.invalid-tooltip {
        display: block;
    }

    .col-md-6,
    .col-md-4,
    .col-md-12 {
        position: relative;
    }
</style>
<?php
$accion = $_POST["accion"] ?? null;
$id_cliente = $_POST["id_cliente"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Editar Cliente</h1>
                </div>

                <div class="Quick-widget col-12 col-md-10 p-0 p-2">
                    <div class="col-12 Quick-form px-4 rounded-2">
                        <form action="" method="POST" class="form py-3 needs-validation" novalidate>

                            <div class="row d-flex flex-row justify-content-center align-items-center">
                                <input type="hidden" name="__editar" value="1">
                                <input type="hidden" name="accion" id="id_accion" value="<?php echo $accion ?>">
                                <input type="hidden" name="id_cliente" id="id_cliente" value="<?php echo $id_cliente ?>">

                                <!-- NOMBRE -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="nombre_cliente" class="form-label Quick-title">Nombre *</label>
                                    <input type="text" id="nombre_cliente" name="nombre_cliente" class="Quick-form-input" maxlength="100" placeholder="Ej: Juan" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- APELLIDO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="apellido_cliente" class="form-label Quick-title">Apellido</label>
                                    <input type="text" id="apellido_cliente" name="apellido_cliente" class="Quick-form-input" maxlength="100" placeholder="Ej: Pérez">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- CEDULA -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="cedula_cliente" class="form-label Quick-title">Cédula</label>
                                    <input type="text" id="cedula_cliente" name="cedula_cliente" class="Quick-form-input" maxlength="20" placeholder="Ej: V-12.345.678">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- TELEFONO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="telefono_cliente" class="form-label Quick-title">Teléfono</label>
                                    <input type="tel" id="telefono_cliente" name="telefono_cliente" class="Quick-form-input" maxlength="20" placeholder="Ej: 0412-555-12-12">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- CORREO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="correo_cliente" class="form-label Quick-title">Correo Electrónico</label>
                                    <input type="email" id="correo_cliente" name="correo_cliente" class="Quick-form-input" maxlength="120" placeholder="cliente@ejemplo.com">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- ESTADO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="estado_cliente" class="form-label Quick-title">Estado</label>
                                    <select name="estado_cliente" id="estado_cliente" class="Quick-select form-select">
                                        <option value="true">Activo</option>
                                        <option value="false">Inactivo</option>
                                    </select>
                                </div>

                                <!-- DIRECCION -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="direccion_cliente" class="form-label Quick-title">Dirección</label>
                                    <textarea id="direccion_cliente" name="direccion_cliente" class="Quick-form-input" rows="3" maxlength="255" placeholder="Dirección completa..."></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-column flex-md-row justify-content-center align-items-center py-3">
                                    <div class="row w-100 d-flex justify-content-center gap-3">
                                        <div class="col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                                        </div>
                                        <div class="col-md-3 d-flex justify-content-center">
                                            <a href="clientes-listado" class="btn btn-secondary w-100">Cancelar</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["__editar"]) && $_POST["__editar"] === "1"
) {
    include_once "controller/clientes_editar_C.php";
    $resp = clientes_editar_C::editarCliente($_POST);

    if (!empty($resp["success"])) {
        echo "<script>alert('Cliente editado exitosamente');</script>";
        echo "<script>window.location.href = 'clientes-listado';</script>";
        exit();
    } else {
        $msg = $resp["error"] ?? "Error desconocido";
        echo "<script>alert('Error al editar el cliente: $msg');</script>";
    }
}
?>

<script type="module" src="api/client/clientes-editar.js"></script>
<script src="view/js/clientes-editar.js"></script>