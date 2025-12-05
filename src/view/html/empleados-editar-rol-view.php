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
$id_rol = $_POST["id_rol"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Editar Rol</h1>
                </div>

                <div class="Quick-widget col-12 col-md-8 p-0 p-2">
                    <div class="col-12 Quick-form px-4 rounded-2">
                        <form action="" method="POST" class="form py-3 needs-validation" novalidate>

                            <div class="row d-flex flex-row justify-content-center align-items-center">
                                <input type="hidden" name="__editar" value="1">
                                <input type="hidden" name="accion" id="id_accion" value="<?php echo $accion ?>">
                                <input type="hidden" name="id_rol" id="id_rol" value="<?php echo $id_rol ?>">

                                <!-- NOMBRE -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="nombre_rol" class="form-label Quick-title">Nombre del Rol *</label>
                                    <input type="text" id="nombre_rol" name="nombre_rol" class="Quick-form-input" maxlength="80" placeholder="Ej: Supervisor de Ventas" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- ESTADO -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="estado_rol" class="form-label Quick-title">Estado</label>
                                    <select name="estado_rol" id="estado_rol" class="Quick-select form-select">
                                        <option value="true">Activo</option>
                                        <option value="false">Inactivo</option>
                                    </select>
                                </div>

                                <!-- DESCRIPCION -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="descripcion_rol" class="form-label Quick-title">Descripción</label>
                                    <textarea id="descripcion_rol" name="descripcion_rol" class="Quick-form-input" rows="3" maxlength="255" placeholder="Breve descripción del rol..."></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-column flex-md-row justify-content-center align-items-center py-3">
                                    <div class="row w-100 d-flex justify-content-center gap-3">
                                        <div class="col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
                                        </div>
                                        <div class="col-md-3 d-flex justify-content-center">
                                            <a href="empleados-lista-roles" class="btn btn-secondary w-100">Cancelar</a>
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
    include_once "controller/roles_editar_C.php";
    $resp = roles_editar_C::editarRol($_POST);

    if (!empty($resp["success"])) {
        echo "<script>alert('Rol editado exitosamente');</script>";
        echo "<script>window.location.href = 'empleados-lista-roles';</script>";
        exit();
    } else {
        $msg = $resp["error"] ?? "Error desconocido";
        echo "<script>alert('Error al editar el rol: $msg');</script>";
    }
}
?>

<script type="module" src="api/client/roles-editar.js"></script>
<script src="view/js/roles-form.js"></script>