<?php
// Obtener id_sucursal de la sesión (estructura correcta: sucursal.id_sucursal)
// Si no hay sucursal asignada (ej: Gerente), será null y se mostrarán productos de todas las sucursales
$id_sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;
?>
<!-- Variable global para que JavaScript use la sucursal de la sesión -->
<script>
    window.ID_SUCURSAL_SESION = <?php echo json_encode($id_sucursal_sesion); ?>;
</script>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <!-- Título -->
            <div class="row mb-3">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1>Lista de Productos</h1>
                </div>
            </div>

            <!-- Barra de filtros -->
            <div class="row mb-4 g-3 align-items-end">
                <!-- flitro de sucursal  -->
                <div class="col-12 col-md-2">
                    <select class="form-select" id="filtro_sucursal">
                        <option value="">Selecciona Sucursal</option>
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                </div>
                <!-- filtro de nombre  -->
                <div class="col-12 col-md-2">
                    <input type="text" class="form-control" id="filtro_nombre" placeholder="Nombre del Producto">
                </div>
                <!-- filtro de estado  -->
                <div class="col-12 col-md-2">
                    <select class="form-select" id="filtro_estado">
                        <option value="">Selecciona Estado</option>
                        <option value="true">Activo</option>
                        <option value="false">Inactivo</option>
                    </select>
                </div>
                <!-- filtro de código -->
                <div class="col-12 col-md-2">
                    <input type="text" class="form-control" id="filtro_codigo" placeholder="Código de Barra">
                </div>
                <!-- filtro de categoría -->
                <div class="col-12 col-md-2">
                    <select class="form-select" id="filtro_categoria">
                        <option value="">Selecciona Categoría</option>
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                </div>
                <!-- filtro de proveedor -->
                <div class="col-12 col-md-2">
                    <select class="form-select" id="filtro_proveedor">
                        <option value="">Selecciona Proveedor</option>
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                </div>
                <div class="col-12 col-md-2 mt-2">
                    <button class="btn btn-secondary w-100" id="btn-reestablecer">Reestablecer Filtros</button>
                </div>
            </div>

            <!-- Tabla de productos e inventario -->
            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table p-1 p-md-3">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Talla</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Stock</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_productos">
                            <!-- Se llenará dinámicamente con JS desde inventario + producto -->
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["accion"] == "eliminar") {
                include "controller/inventario_eliminar_producto_C.php";
                if (!inventario_eliminar_producto_C::eliminarProducto($_POST["id_producto"])) {
                    echo "<script>alert('Error al eliminar el producto');</script>";
                    exit();
                }

                echo "<script>alert('Producto eliminado correctamente');</script>";
                echo "<script>window.location.href='inventario-ver-productos';</script>";
            }
            ?>
        </div>
    </div>
</div>



<script type="module" src="api/client/inventario-ver-productos.js"></script>