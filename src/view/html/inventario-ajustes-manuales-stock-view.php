<?php
// Obtener id_sucursal de la sesión para filtrar productos
// Si el usuario tiene sucursal asignada, filtra por ella; si no (ej: Gerente), muestra todas
$id_sucursal_sesion = $_SESSION['sesion_usuario']['sucursal']['id_sucursal'] ?? null;
?>
<script>
    // Variable global para el filtro inicial de sucursal
    window.ID_SUCURSAL_SESION = <?php echo $id_sucursal_sesion ? "\"$id_sucursal_sesion\"" : "null"; ?>;
</script>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-between align-items-center">
                <div class="col-12 col-md-6 p-3 Quick-title">
                    <h1 class="m-0">Ajustes Manuales de Stock</h1>
                </div>
            </div>

            <!-- Filtros -->
            <div class="row d-flex flex-column justify-content-center align-items-center">
                <div class="col-12 p-0 p-2 Quick-widget">
                    <div class="col-12 px-3 rounded-2">
                        <form id="form-filtros" class="form py-3">
                            <div class="row d-flex flex-row justify-content-center align-items-end">

                                <div class="col-12 col-md-2 d-flex flex-column py-2">
                                    <label for="filtro_nombre" class="form-label Quick-title">Nombre</label>
                                    <input type="text" id="filtro_nombre" class="form-control " placeholder="Buscar por nombre...">
                                </div>

                                <div class="col-12 col-md-2 d-flex flex-column py-2">
                                    <label for="filtro_codigo" class="form-label Quick-title">Código</label>
                                    <input type="text" id="filtro_codigo" class="form-control " placeholder="Buscar por código...">
                                </div>

                                <div class="col-12 col-md-2 d-flex flex-column py-2">
                                    <label for="filtro_categoria" class="form-label Quick-title">Categoría</label>
                                    <select id="filtro_categoria" class="form-control">
                                        <option value="">Todas</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 d-flex flex-column py-2">
                                    <label for="filtro_proveedor" class="form-label Quick-title">Proveedor</label>
                                    <select id="filtro_proveedor" class="form-control">
                                        <option value="">Todos</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 d-flex flex-column py-2">
                                    <label for="filtro_sucursal" class="form-label Quick-title">Sucursal</label>
                                    <select id="filtro_sucursal" class="form-control">
                                        <!-- Se llenará dinámicamente, default ID 5 si no hay sesión -->
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 d-flex flex-column py-2 justify-content-end">
                                    <button type="button" id="btn-reestablecer" class="btn btn-danger w-100">Reestablecer Filtros</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos con Ajustes -->
            <div class="row d-flex flex-row justify-content-center align-items-center mt-4 Quick-widget">
                <div class="col-12 Quick-table p-2 table-responsive">
                    <table class="align-middle w-100" id="tabla_productos">
                        <thead class="text-center">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Talla</th>
                                <th>Color</th>
                                <th>Stock Actual</th>
                                <th>Sucursal</th>
                                <th style="min-width: 250px;">Ajuste Rápido</th>
                            </tr>
                        </thead>
                        <tbody class="text-center" id="tbody_productos">
                            <!-- Se llena dinámicamente -->
                            <tr>
                                <td colspan="8">Cargando productos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal de Confirmación de Ajuste -->
<div class="modal fade" id="modalAjuste" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content Quick-modal">
            <div class="modal-header Quick-modal-header">
                <h5 class="modal-title">Confirmar Ajuste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de realizar el siguiente ajuste?</p>
                <ul>
                    <li><strong>Producto:</strong> <span id="modal_producto"></span></li>
                    <li><strong>Tipo:</strong> <span id="modal_tipo"></span></li>
                    <li><strong>Cantidad:</strong> <span id="modal_cantidad"></span></li>
                    <li><strong>Sucursal:</strong> <span id="modal_sucursal"></span></li>
                </ul>
                <div class="mb-3">
                    <label for="modal_motivo" class="form-label">Motivo del Ajuste</label>
                    <select id="modal_motivo" class="form-select form-control" required>
                        <option value="correccion">Corrección de inventario</option>
                        <option value="merma">Daño o pérdida</option>
                        <option value="ajuste_manual">Ajuste manual</option>
                        <option value="error_registro">Error de carga</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="modal_comentario" class="form-label">Comentario (Opcional)</label>
                    <textarea id="modal_comentario" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-ajuste">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script type="module" src="view/js/inventario-ajustes-manuales-stock.js"></script>