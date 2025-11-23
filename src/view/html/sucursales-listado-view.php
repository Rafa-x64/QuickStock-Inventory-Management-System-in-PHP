<?php
$accion = $_POST["accion"] ?? null;
$id_sucursal = $_POST["id_sucursal"] ?? null;
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row">
                <div class="col-12 col-md-6 p-5 Quick-title">
                    <h1>Lista de Sucursales</h1>
                </div>
                <div class="col-12 col-md-6 p-5 d-flex flex-row justify-content-end align-items-center">
                    <form action="">
                        <input type="search" placeholder="Buscar Sucursal..." class="Quick-input" id="sucursales-buscar">
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-search fs-6"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table p-1 p-md-3">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <th class="ps-1">Código</th>
                                <th class="ps-1">Nombre de la Sucursal</th>
                                <th class="ps-1">Dirección</th>
                                <th class="ps-1">Teléfono</th>
                                <th class="ps-1">Estado</th>
                                <th class="ps-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_sucursales">

                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["accion"] == "eliminar_sucursal") {
                include_once "controller/sucursales_eliminar_C.php";
                $respuesta = sucursales_eliminar_C::eliminarSucursal($_POST);
                if ($respuesta["estado"] == "exito") {
                    echo '<script>alert(' . $respuesta["mensaje"] . ');</script>';
                } else {
                    echo '<script>alert(' . $respuesta["mensaje"] . ');</script>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script type="module" src="api/client/sucursales-listado.js"></script>