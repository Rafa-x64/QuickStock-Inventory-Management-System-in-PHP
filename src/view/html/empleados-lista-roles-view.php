<?php
$accion = $_POST["accion"] ?? null;
$id_rol = $_POST["id_rol"] ?? null;

// Procesar eliminación si se envía
if ($_SERVER["REQUEST_METHOD"] === "POST" && $accion === "eliminar" && $id_rol) {
    include_once "controller/roles_eliminar_C.php";
    $resultado = roles_eliminar_C::eliminarRol($id_rol);

    if (!empty($resultado["success"])) {
        echo '<script>alert("Rol eliminado correctamente");</script>';
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
                    <h1 class="m-0">Lista de Roles</h1>
                </div>
                <div class="col-12 col-md-6 p-3 d-flex justify-content-end">
                    <a href="empleados-añadir-rol" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Nuevo Rol
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row p-0 m-0 mb-4">
                <div class="col-12 Quick-widget p-4">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-md-5">
                            <label for="nombre-filtro" class="form-label Quick-title">Nombre del Rol</label>
                            <input type="text" id="nombre-filtro" class="form-control" placeholder="Buscar por nombre...">
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="estado-filtro" class="form-label Quick-title">Estado</label>
                            <select id="estado-filtro" class="form-select">
                                <option value="todos">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4 d-flex align-items-end">
                            <button type="button" id="reestablecer-filtros" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Reestablecer
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Tabla de Roles -->
            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table pt-4 mb-3">
                    <div class="table-responsive">
                        <table class="w-100" id="lista_roles">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Rol</th>
                                    <th>Descripción</th>
                                    <th>Usuarios Asignados</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Cargando roles...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="api/client/roles-listado.js"></script>