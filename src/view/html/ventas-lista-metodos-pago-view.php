<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Listado de Métodos de Pago</h1>
                </div>

                <div class="col-12 p-0 p-2">
                    <div class="row d-flex flex-row justify-content-between align-items-center mb-3">
                        <div class="col-md-3">
                            <input type="text" id="filtro-nombre" class="form-control" placeholder="Buscar por nombre...">
                        </div>
                        <div class="col-md-3">
                            <select name="filtro-referencia" id="filtro-referencia" class="form-select">
                                <option value="">Necesita referencia</option>
                                <option value="t">Referencia</option>
                                <option value="f">Sin Referencia</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="filtro-estado" id="filtro-estado" class="form-select">
                                <option value="">Estado</option>
                                <option value="t">Activo</option>
                                <option value="f">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="ventas-añadir-metodo-pago" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Nuevo Método
                            </a>
                        </div>
                    </div>

                    <div class="col-12 Quick-widget p-3">
                        <table class="Quick-table w-100" id="tabla-metodos-pago">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Referencia</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llena dinámicamente con JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
                include_once "controller/ventas_metodos_pago_C.php";
                $id = $_POST['id_metodo_pago'] ?? null;
                $resultado = ventas_metodos_pago_C::eliminarMetodoPago($id);

                if (isset($resultado['error'])) {
                    echo '<script>alert("' . $resultado['error'] . '");</script>';
                } else {
                    echo '<script>alert("' . $resultado['success'] . '");</script>';
                    echo '<script>window.location.href = "ventas-lista-metodos-pago";</script>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script type="module" src="api/client/ventas-lista-metodos-pago.js"></script>