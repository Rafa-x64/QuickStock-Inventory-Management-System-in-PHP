<?php
$accion = $_POST["accion"] ?? null;
$id_talla = $_POST["id_talla"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1 class="m-0">Gestión de Tallas</h1>
                </div>

                <div class="col-12 col-md-6 p-3 d-flex flex-row justify-content-end align-items-center">
                    <input type="search" placeholder="Buscar Talla..." class="Quick-input me-2" id="talla_input">
                </div>
            </div>

            <div class="col-12 col-md-6 p-3 mt-2 mt-md-3 Quick-title">
                <h1 class="m-0">Ver Tallas</h1>
            </div>
            <!-- Tabla responsive -->
            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table pt-5 mb-3 table-responsive">
                    <table class="align-middle w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ID</th>
                                <th>Rango / Talla</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_tallas">
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Formulario de registro -->
            <div class="d-block" id="formulario_registro">
                <div class="col-12 col-md-6 p-3 mt-3 mt-md-3 Quick-title">
                    <h1 class="m-0">Añadir una nueva talla</h1>
                </div>
                <div class="row d-flex flex-column justify-content-center align-items-center">
                    <div class="col-12 col-md-8 p-0 mt-md-0 p-2 Quick-widget">
                        <div class="col-12 Quick-form px-5 rounded-2">
                            <form action="" method="POST" class="form needs-validation" id="form_añadir_talla" novalidate>
                                <div class="row d-flex flex-row justify-content-center align-items-center">

                                    <div class="col-12 d-flex flex-column py-3 position-relative">
                                        <label for="rango_talla_añadir" class="form-label Quick-title">Rango de Talla</label>
                                        <input type="text" id="rango_talla_añadir" name="rango_talla_añadir" class="Quick-form-input" required maxlength="20" placeholder="Ej: 42, M, XL, 36-38">
                                        <div class="invalid-tooltip" id="tooltip_rango_añadir"></div>
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
                    <h1 class="m-0">Editar talla</h1>
                </div>
                <div class="row d-flex flex-column justify-content-center align-items-center">
                    <div class="col-12 col-md-8 p-0 mt-md-0 p-2 Quick-widget">
                        <div class="col-12 Quick-form px-5 rounded-2">
                            <form action="" method="POST" class="form needs-validation" id="form_editar_talla" novalidate>
                                <div class="row d-flex flex-row justify-content-start align-items-center">

                                    <input type="hidden" name="id_talla_editar" id="id_talla_editar" value="<?php echo $id_talla; ?>">
                                    <!-- Input hidden accion se inyectará por JS o podemos ponerlo aquí por seguridad -->
                                    <input type="hidden" name="accion" value="editar">

                                    <div class="col-12 col-md-6 d-flex flex-column py-3 position-relative">
                                        <label for="rango_talla_editar" class="form-label Quick-title">Rango de Talla</label>
                                        <input type="text" id="rango_talla_editar" name="rango_talla_editar" class="Quick-form-input" required maxlength="20">
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
                include_once "controller/inventario_gestionar_tallas_C.php";
                $resultado = gestionar_tallas_C::crearTalla($_POST);
                if (isset($resultado["success"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                    echo '<script>window.location.href = "inventario-gestionar-tallas"</script>';
                } elseif (isset($resultado["error"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                }
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["accion"] ?? '') == "editar") {
                include_once "controller/inventario_gestionar_tallas_C.php";
                $resultado = gestionar_tallas_C::editarTalla($_POST);
                if (isset($resultado["success"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                    echo '<script>window.location.href = "inventario-gestionar-tallas"</script>';
                } elseif (isset($resultado["error"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                }
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["accion"] ?? '') == "eliminar") {
                include_once "controller/inventario_gestionar_tallas_C.php";
                $resultado = gestionar_tallas_C::eliminarTalla($_POST);
                if (isset($resultado["success"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                    echo '<script>window.location.href = "inventario-gestionar-tallas"</script>';
                } elseif (isset($resultado["error"])) {
                    echo '<script>alert("' . $resultado["mensaje"] . '")</script>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script type="module" src="api/client/inventario-gestionar-tallas.js"></script>
<script src="view/js/inventario-gestionar-tallas.js"></script>