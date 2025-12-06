<?php
$accion = $_POST["accion"] ?? null;
$id_cliente = $_POST["id_cliente"] ?? null;

// Procesar eliminación si se envía
if ($_SERVER["REQUEST_METHOD"] === "POST" && $accion === "eliminar" && $id_cliente) {
    include_once "controller/clientes_eliminar_C.php";
    $resultado = clientes_eliminar_C::eliminarCliente($id_cliente);

    if (!empty($resultado["success"])) {
        echo '<script>alert("Cliente eliminado correctamente");</script>';
    } else {
        $msg = $resultado["error"] ?? "Error desconocido";
        echo '<script>alert("Error al eliminar: ' . $msg . '");</script>';
    }
}
?>
<style>
    .was-validated .form-control:invalid~.invalid-tooltip,
    .form-control.is-invalid~.invalid-tooltip {
        display: block;
    }

    .col-md-6,
    .col-md-4,
    .col-md-3,
    .col-md-12 {
        position: relative;
    }
</style>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-3 p-lg-5">

            <!-- Encabezado -->
            <div class="row d-flex flex-row justify-content-between align-items-center mb-4">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1 class="m-0">Lista de Clientes</h1>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row p-0 m-0 mb-4">
                <div class="col-12 Quick-widget p-4">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-md-3">
                            <label for="nombre-filtro" class="form-label Quick-title">Nombre</label>
                            <input type="text" id="nombre-filtro" class="form-control" placeholder="Buscar por nombre...">
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="apellido-filtro" class="form-label Quick-title">Apellido</label>
                            <input type="text" id="apellido-filtro" class="form-control" placeholder="Buscar por apellido...">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="cedula-filtro" class="form-label Quick-title">Cédula</label>
                            <input type="text" id="cedula-filtro" class="form-control" placeholder="Ej: V-12.345.678">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="estado-filtro" class="form-label Quick-title">Estado</label>
                            <select id="estado-filtro" class="form-select">
                                <option value="todos">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <button type="button" id="reestablecer-filtros" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Reestablecer
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Tabla de Clientes -->
            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table pt-4 mb-3">
                    <div class="table-responsive">
                        <table class="w-100" id="lista_clientes">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Cédula</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">Cargando clientes...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/clientes-listado.js"></script>