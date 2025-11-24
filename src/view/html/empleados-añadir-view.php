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
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Registrar Nuevo Empleado</h1>
                </div>

                <div class="Quick-widget col-12 col-md-10 p-0 p-2">
                    <div class="col-12 Quick-form px-4 rounded-2">
                        <form action="" method="POST" class="form py-3 needs-validation" novalidate>

                            <div class="row d-flex flex-row justify-content-center align-items-center">

                                <!-- PRIMER NOMBRE -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="nombre_empleado" class="form-label Quick-title">Nombre</label>
                                    <input type="text" id="nombre_empleado" name="nombre_empleado" class="Quick-form-input" maxlength="100" placeholder="Ej: Juan" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- APELLIDO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="apellido_empleado" class="form-label Quick-title">Apellido</label>
                                    <input type="text" id="apellido_empleado" name="apellido_empleado" class="Quick-form-input" maxlength="100" placeholder="Ej: Pérez" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- CEDULA -->
                                <div class="col-md-4 d-flex flex-column py-3">
                                    <label for="cedula_empleado" class="form-label Quick-title">Cédula</label>
                                    <input type="text" id="cedula_empleado" name="cedula_empleado" class="Quick-form-input" maxlength="12" placeholder="Ej: V-12345678" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- TELEFONO -->
                                <div class="col-md-4 d-flex flex-column py-3">
                                    <label for="telefono_empleado" class="form-label Quick-title">Teléfono</label>
                                    <input type="tel" id="telefono_empleado" name="telefono_empleado" class="Quick-form-input" maxlength="50" placeholder="+58 412-5551234" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- ROL DEL USUARIO (FK id_rol) -->
                                <div class="col-md-4 d-flex flex-column py-3">
                                    <label for="id_rol" class="form-label Quick-title">Rol del Empleado</label>
                                    <select id="id_rol" name="id_rol" class="Quick-select" required>

                                    </select>
                                </div>

                                <!-- CORREO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="email_empleado" class="form-label Quick-title">Correo Electrónico</label>
                                    <input type="email" id="email_empleado" name="email_empleado" class="Quick-form-input" maxlength="255" placeholder="empleado@quickstock.com" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- CONTRASEÑA -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="contrasena_empleado" class="form-label Quick-title">Contraseña</label>
                                    <input type="password" id="contrasena_empleado" name="contrasena_empleado" class="Quick-form-input" maxlength="255" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- DIRECCION -->
                                <div class="col-md-12 d-flex flex-column py-3">
                                    <label for="direccion_empleado" class="form-label Quick-title">Dirección</label>
                                    <textarea id="direccion_empleado" name="direccion_empleado" class="Quick-form-input" rows="3" maxlength="255" placeholder="Dirección completa..." required></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- SUCURSAL (FK id_sucursal) -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="id_sucursal" class="form-label Quick-title">Sucursal Asignada</label>
                                    <select id="id_sucursal" name="id_sucursal" class="Quick-select" required>

                                    </select>
                                </div>

                                <!-- ESTADO DEL EMPLEADO (no está en la tabla pero es útil para control) -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="estado_empleado" class="form-label Quick-title">Estado</label>
                                    <input type="text" name="estado_empleado" id="estado_empleado" value="activo" class="Quick-form-input" readonly>
                                </div>

                                <!-- FECHA DE REGISTRO -->
                                <div class="col-md-6 d-flex flex-column py-3">
                                    <label for="fecha_registro" class="form-label Quick-title">Fecha de Registro</label>
                                    <input type="date" id="fecha_registro" name="fecha_registro" class="Quick-form-input" required>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-column flex-md-row justify-content-center align-items-center py-3">
                                    <div class="row w-100 d-flex justify-content-around">
                                        <div class="col-md-5 pt-2 pt-md-0 col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success w-100">Registrar</button>
                                        </div>
                                        <div class="col-md-5 pt-2 pt-md-0 col-md-3 d-flex justify-content-center">
                                            <button type="reset" class="btn btn-danger w-100">Limpiar</button>
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
//si se envia un formulario por el metodo post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //se incluye el controlador
    include_once "controller/empleados_añadir_C.php";
    //se evalua el resultado del retorno del controlador
    switch (empleados_añadir_C::agregarEmpleado($_POST)) {
        case "Este correo ya existe":
            echo "<script>alert('Este correo ya existe. Error al crear el empleado');</script>";
            break;
        case "Esta cedula ya existe":
            echo "<script>alert('Esta cedula ya existe. Error al crear el empleado');</script>";
            break;
        case "Error al registrar el empleado":
            echo "<script>alert('Error al registrar el empleado');</script>";
            break;
        case "sisa mano":
            echo "<script>alert('Empleado registrado exitosamente');</script>";
            echo "<script>window.location.href = 'empleados-listado';</script>";
    }
}
?>

<!--incluir script para hacer peticiones al server mediante funcion api-->
<!--debe ser module-->
<script type="module" src="api/client/empleados-añadir.js"></script>
<!--js para las validaciones-->
<script src="view/js/empleados-añadir.js"></script>