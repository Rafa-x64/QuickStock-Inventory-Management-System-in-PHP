<?php
$accion = $_POST["accion"] ?? null;
$id_color = $_POST["id_color"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1 class="m-0">Gestión de Colores</h1>
                </div>

                <div class="col-12 col-md-6 p-3 d-flex flex-row justify-content-end align-items-center">
                    <input type="search" placeholder="Buscar Color..." class="Quick-input me-2" id="color_input">
                </div>
            </div>

            <div class="col-12 col-md-6 p-3 mt-2 mt-md-3 Quick-title">
                <h1 class="m-0">Ver Colores</h1>
            </div>
            <!-- Tabla responsive -->
            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table pt-5 mb-3 table-responsive">
                    <table class="align-middle w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_colores">
                            <!-- Se llenará dinámicamente desde la base de datos -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario de registro -->
            <div class="d-block" id="formulario_registro">
                <div class="col-12 col-md-6 p-3 mt-3 mt-md-3 Quick-title">
                    <h1 class="m-0">Añadir un nuevo color</h1>
                </div>
                <div class="row d-flex flex-column justify-content-center align-items-center">
                    <div class="col-12 col-md-8 p-0 mt-md-0 p-2 Quick-widget">
                        <div class="col-12 Quick-form px-5 rounded-2">
                            <form action="" method="POST" class="form needs-validation" id="form_añadir_color" novalidate>
                                <div class="row d-flex flex-row justify-content-center align-items-center">

                                    <div class="col-12 d-flex flex-column py-3 position-relative">
                                        <label for="nombre_color_añadir" class="form-label Quick-title">Nombre del Color</label>
                                        <input type="text" id="nombre_color_añadir" name="nombre_color_añadir" class="Quick-form-input" required maxlength="50" placeholder="Ej: Rojo Intenso">
                                        <div class="invalid-tooltip" id="tooltip_nombre_añadir"></div>
                                    </div>

                                    <div class="col-12 d-flex flex-column py-3">
                                        <div class="row p-0 m-0 d-flex flex-column flex-md-row justify-content-center align-items-center justify-content-md-around align-items-md-center">
                                            <div class="col-12 col-md-3 d-flex justify-content-center">
                                                <button type="submit" class="btn btn-success w-100" id="btn_guardar_añadir">Guardar</button>
                                            </div>
                                            <div class="col-12 mt-2 mt-md-0 col-md-3 d-flex justify-content-center">
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

            <!-- Formulario de edicion -->
            <div class="d-none" id="formulario_edicion">
                <div class="col-12 col-md-6 p-3 mt-3 mt-md-3 Quick-title">
                    <h1 class="m-0">Editar color</h1>
                </div>
                <div class="row d-flex flex-column justify-content-center align-items-center">
                    <div class="col-12 col-md-8 p-0 mt-md-0 p-2 Quick-widget">
                        <div class="col-12 Quick-form px-5 rounded-2">
                            <form action="" method="POST" class="form needs-validation" id="form_editar_color" novalidate>
                                <div class="row d-flex flex-row justify-content-start align-items-center">

                                    <input type="hidden" name="id_color_editar" id="id_color_editar" value="<?php echo $id_color; ?>">

                                    <div class="col-12 col-md-6 d-flex flex-column py-3 position-relative">
                                        <label for="nombre_color_editar" class="form-label Quick-title">Nombre del Color</label>
                                        <input type="text" id="nombre_color_editar" name="nombre_color_editar" class="Quick-form-input" required maxlength="50">
                                        <div class="invalid-tooltip"></div>
                                    </div>

                                    <div class="col-12 col-md-6 d-flex flex-column py-3">
                                        <label for="activo_editar" class="form-label Quick-title">Estado</label>
                                        <select id="activo_editar" name="activo_editar" class="Quick-select">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                    </div>

                                    <div class="col-12 d-flex flex-column py-3">
                                        <div class="row p-0 m-0 d-flex flex-column flex-md-row justify-content-center align-items-center justify-content-md-around align-items-md-center">
                                            <div class="col-12 col-md-3 d-flex justify-content-center">
                                                <button type="submit" class="btn btn-success w-100">Guardar</button>
                                            </div>
                                            <div class="col-12 mt-2 mt-md-0 col-md-3 d-flex justify-content-center">
                                                <button type="button" class="btn btn-danger w-100" id="btn_cancelar_edicion">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["accion"])) {
                include_once "controller/inventario_gestionar_colores_C.php";
                $resultado = gestionar_colores_C::crearColor($_POST);

                if (isset($resultado["success"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                    echo '<script>window.location.href = "inventario-gestionar-colores"</script>';
                } elseif (isset($resultado["error"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                }
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["accion"] ?? '') == "editar") {
                include_once "controller/inventario_gestionar_colores_C.php";
                $resultado = gestionar_colores_C::editarColor($_POST);
                if (isset($resultado["success"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                    echo '<script>window.location.href = "inventario-gestionar-colores"</script>';
                } elseif (isset($resultado["error"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                }
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["accion"] ?? '') == "eliminar") {
                include_once "controller/inventario_gestionar_colores_C.php";
                $resultado = gestionar_colores_C::eliminarColor($_POST);
                if (isset($resultado["success"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                    echo '<script>window.location.href = "inventario-gestionar-colores"</script>';
                } elseif (isset($resultado["error"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script type="module" src="api/client/inventario-gestionar-colores.js"></script>
<script src="view/js/inventario-gestionar-colores.js"></script>