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
$id_proveedor = $_POST["id_proveedor"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Editar Proveedor</h1>
                </div>

                <div class="Quick-widget col-12 col-md-10 p-0 p-2">
                    <div class="col-12 Quick-form px-4 rounded-2">
                        <form action="" method="POST" class="form py-3 needs-validation" novalidate>

                            <div class="row d-flex flex-row justify-content-center align-items-center">
                                <input type="hidden" name="__editar" value="1">
                                <input type="hidden" name="accion" id="id_accion" value="<?php echo $accion ?>">
                                <input type="hidden" name="id_proveedor" id="id_proveedor" value="<?php echo $id_proveedor ?>">

                                <!-- NOMBRE -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="nombre_proveedor" class="form-label Quick-title">Nombre / Razón Social *</label>
                                    <input type="text" id="nombre_proveedor" name="nombre_proveedor" class="Quick-form-input" maxlength="150" placeholder="Ej: Sports Calzados C.A." required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- TELEFONO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="telefono_proveedor" class="form-label Quick-title">Teléfono</label>
                                    <input type="tel" id="telefono_proveedor" name="telefono_proveedor" class="Quick-form-input" maxlength="20" placeholder="Ej: 0412-555-12-12">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- CORREO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="correo_proveedor" class="form-label Quick-title">Correo Electrónico</label>
                                    <input type="email" id="correo_proveedor" name="correo_proveedor" class="Quick-form-input" maxlength="120" placeholder="proveedor@empresa.com">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- ESTADO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="estado_proveedor" class="form-label Quick-title">Estado</label>
                                    <select name="estado_proveedor" id="estado_proveedor" class="Quick-select form-select">
                                        <option value="true">Activo</option>
                                        <option value="false">Inactivo</option>
                                    </select>
                                </div>

                                <!-- DIRECCION -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="direccion_proveedor" class="form-label Quick-title">Dirección</label>
                                    <textarea id="direccion_proveedor" name="direccion_proveedor" class="Quick-form-input" rows="3" maxlength="255" placeholder="Dirección completa del proveedor..."></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-column flex-md-row justify-content-center align-items-center py-3">
                                    <div class="row w-100 d-flex justify-content-center gap-3">
                                        <div class="col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                                        </div>
                                        <div class="col-md-3 d-flex justify-content-center">
                                            <a href="proveedores-listado" class="btn btn-secondary w-100">Cancelar</a>
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
    include_once "controller/proveedores_editar_C.php";
    $resp = proveedores_editar_C::editarProveedor($_POST);

    if (!empty($resp["success"])) {
        echo "<script>alert('Proveedor editado exitosamente');</script>";
        echo "<script>window.location.href = 'proveedores-listado';</script>";
        exit();
    } else {
        $msg = $resp["error"] ?? "Error desconocido";
        echo "<script>alert('Error al editar el proveedor: $msg');</script>";
    }
}
?>

<script type="module" src="api/client/proveedores-editar.js"></script>
<script src="view/js/proveedores-form.js"></script>