---
trigger: always_on
---

vista: en la vista debes incluir el controlador en un fragmento de codigo php que depure si hay errores y los muestre por alertas o consola

siempre debe contener esta estructura para mantener el funcionamiento ideal de la vista y los menus... al editar la vista debes respetar mis clases css Quick-\* y mantener la responsividad en toda la vista. la vista y sus campos deben estar de manera logica y relacionados a las columnas que requieren rellenado en la base de datos (revisar config/quickstock_plano.sql)

<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
<!--aqui va el php if server request method-->
</div>
</div>
</div>

vista para editar: debes obtener, mediante un fragmento de codigo php el id de lo que se quiere editar enviado mediante el metodo post y la accion que se quiere realizar

vista para eliminar: la logica para eliminar siempre va en la pagina de listado o de visualizacion de una tabla... si el metodo de envio es post y la accion es eliminar incluye el controlador y realiza la logica de eliminarcion logica

vista para ver detalles: debes obtener, mediante un fragmento de codigo php el id de lo que se quiere editar enviado mediante el metodo post y la accion que se quiere realizar y realizar una peticion al server para rellenar los campos o establecelos como readonly

vista para listados: carga mediante una petcion al server un listado de los objetos que se consultan como ya lo establecimos en .agent/rules/peticiones_mediante_funcion_api.md

-debe interpretar y mostrar correctamente los errores o repuestas enviadas por el controlador
-debe incluir el controlador unicamente si se envio el formulario por POST
-usar metodos del controlador en condicionales
-seguir patrones kiss al implemetnar logica .php
-si el id y la accion que se envian a esta pagina estan vacios o son null entonces muestra un mensaje de error asi:
   <?php
    $accion = $_POST["accion"] ?? null;
    $id_categoria = $_POST["id_categoria"] ?? null;
   ?>
-las visatas de edicion y creacion deben estar validadas correctamente segun los campos de la base de datos y los ids de la vista
-mostrar validaciones asincronas tipo bootstrap con invalid tooltip
-mejorar la origanizacion de bootstrap sin cambiar la estetica
-mantener la vista limpia, ordenada y escalable
-deja comentarios para separar codigo
-si creas una nueva vista deberas incluirla al array de paginas existentes de view/plantilla.php
-mantener responsividad
-consultar la estrucutra de la base de datos para realizar formularios coherentes al contexto de la bd y las columnas de sus tablas
-pendiente con lo que se envia a travez del formulario hidden en js la accion y el id, recibelos correctamente y asegurate que se envien correctamente
-la vista que crees o modifiques debe ubicarse en view/html/nombremodulo-nombrevista-view.php obligatoriamente
-puedes fijarte de las que ya tengo hechas
-debe incluir el js de las validaciones y el js de las peticiones una vez hallas verificado que su logica esta correcta de lo contrario notificame y deja comentarios
-la vista debe pasar la matriz $_POST a la funcion que ejecuta el controlador
    ejemplo de vista:
    <?php
$accion = $_POST["accion"] ?? null;
$id_sucursal = $_POST["id_sucursal"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <div class="row d-flex flex-row justify-content-between align-items-center">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1 class="m-0">Editar Sucursal</h1>
                </div>
            </div>

            <div class="row d-flex flex-column justify-content-center align-items-center">
                <div class="col-12 col-md-8 p-0 p-2 Quick-widget">
                    <div class="col-12 Quick-form px-5 rounded-2">

                        <form action="" method="POST" class="form needs-validation" id="form_editar_sucursal" novalidate>
                            <div class="row d-flex flex-row justify-content-start align-items-center">

                                <!-- Acción oculta -->
                                <input type="hidden" name="accion" value="__editar">

                                <!-- ID de la sucursal (oculto, no editable) -->
                                <input type="hidden" name="id_sucursal" id="id_sucursal" value="<?php echo $id_sucursal; ?>">

                                <!-- Nombre -->
                                <div class="col-12 col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="nombre_sucursal_editar" class="form-label Quick-title">Nombre de la Sucursal</label>
                                    <input
                                        type="text"
                                        id="nombre_sucursal_editar"
                                        name="nombre_sucursal"
                                        class="Quick-form-input form-control"
                                        required
                                        maxlength="120"
                                        placeholder="Ej: QuickStock Central">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- RIF -->
                                <div class="col-12 col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="rif_sucursal_editar" class="form-label Quick-title">RIF</label>
                                    <input
                                        type="text"
                                        id="rif_sucursal_editar"
                                        name="rif_sucursal"
                                        class="Quick-form-input form-control"
                                        required
                                        maxlength="255"
                                        placeholder="Ej: J-12345678-9">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-12 d-flex flex-column py-3 position-relative">
                                    <label for="direccion_sucursal_editar" class="form-label Quick-title">Dirección</label>
                                    <textarea
                                        id="direccion_sucursal_editar"
                                        name="direccion_sucursal"
                                        class="Quick-form-input form-control"
                                        rows="3"
                                        maxlength="255"
                                        placeholder="Ingrese la dirección completa..."
                                        required></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-12 col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="telefono_sucursal_editar" class="form-label Quick-title">Teléfono</label>
                                    <input
                                        type="tel"
                                        id="telefono_sucursal_editar"
                                        name="telefono_sucursal"
                                        class="Quick-form-input form-control"
                                        maxlength="20"
                                        required
                                        placeholder="+58 412-5551234">
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Fecha de Registro -->
                                <div class="col-12 col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="fecha_registro_editar" class="form-label Quick-title">Fecha de Registro</label>
                                    <input
                                        type="date"
                                        id="fecha_registro_editar"
                                        name="fecha_registro"
                                        class="Quick-form-input form-control"
                                        required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Estado Activo -->
                                <div class="col-12 col-md-6 d-flex flex-column py-3">
                                    <label for="activo_sucursal_editar" class="form-label Quick-title">Estado</label>
                                    <select name="activo" id="activo_sucursal_editar" class="Quick-select form-select">
                                        <option value="true">Activo</option>
                                        <option value="false">Inactivo</option>
                                    </select>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-column py-3">
                                    <div class="row p-0 m-0 d-flex flex-column flex-md-row justify-content-center align-items-center justify-content-md-around">

                                        <div class="col-12 col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success w-100">Guardar</button>
                                        </div>

                                        <div class="col-12 mt-2 mt-md-0 col-md-3 d-flex justify-content-center">
                                            <button type="button" class="btn btn-danger w-100" id="reestablecerBtn">Reestablecer</button>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["accion"] ?? null) == "__editar") {

                include_once "controller/sucursales_editar_C.php";
                $resultado = sucursales_editar_C::editarSucursal($_POST);

                if ($resultado === true) {
                    echo '<script>alert("Sucursal actualizada correctamente")</script>';
                    echo '<script>window.location.href = "sucursales-listado"</script>';
                } else {
                    echo '<script>alert("Error al actualizar la sucursal")</script>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script src="view/js/sucursales-editar.js"></script>
<script type="module" src="api/client/sucursales-editar.js"></script>