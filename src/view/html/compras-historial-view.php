<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row">
                <div class="col-12 p-5 Quick-title">
                    <h1>Historial de Compras</h1>
                </div>
            </div>

            

            <div class="row g-2 mb-4 p-3">
                <div class="col-12">
                    <h5 class="Quick-title mb-3">Opciones de Filtrado Avanzado</h5>

                    <div class="row g-2">

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="filtro-codigo" class="form-label visually-hidden">Código</label>
                            <input type="number"
                                class=" form-control"
                                id="filtro-codigo"
                                placeholder="Cód. Compra"
                                title="Código de Compra">
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="filtro-factura" class="form-label visually-hidden">N° Factura</label>
                            <input type="text"
                                class=" form-control"
                                id="filtro-factura"
                                placeholder="N° Factura"
                                title="Número de Factura">
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="filtro-fecha" class="form-label visually-hidden">Fecha</label>
                            <input type="text"
                                class=" form-control"
                                id="filtro-fecha"
                                placeholder="AAAA-MM-DD"
                                title="Fecha de Compra (formato AAAA-MM-DD)">
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="filtro-total" class="form-label visually-hidden">Total</label>
                            <input type="number"
                                class=" form-control"
                                id="filtro-total"
                                placeholder="Total"
                                step="0.01"
                                title="Monto Total">
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="filtro-proveedor" class="form-label visually-hidden">Proveedor</label>
                            <input type="text"
                                class=" form-control"
                                id="filtro-proveedor"
                                placeholder="Proveedor (Nombre)"
                                title="Filtrar por Nombre de Proveedor">
                        </div>
                    </div>

                    <div class="row g-2 mt-2">

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="filtro-empleado" class="form-label visually-hidden">Empleado</label>
                            <input type="text"
                                class=" form-control"
                                id="filtro-empleado"
                                placeholder="Empleado (Nombre)"
                                title="Filtrar por Empleado Responsable">
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="filtro-sucursal" class="form-label visually-hidden">Sucursal</label>
                            <input type="text"
                                class=" form-control"
                                id="filtro-sucursal"
                                placeholder="Sucursal (Nombre)"
                                title="Filtrar por Nombre de Sucursal">
                        </div>

                        <div class="col-10 col-md-10 col-lg-3">
                            <label for="filtro-estado" class="form-label visually-hidden">Estado</label>
                            <select class=" form-select" id="filtro-estado" title="Filtrar por Estado de Compra">
                                <option value="">-- Todos los Estados --</option>
                                <option value="Completada">Completada</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>

                        <div class="col-2 col-md-2 col-lg-1 d-flex align-items-end">
                            <button class="btn btn-secondary w-100" id="reestablecer-filtros" title="Restablecer Filtros">
                                <i class="bi bi-x-circle fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row p-0 m-0 d-flex flex-row justify-content-center align-items-center Quick-widget">
                <div class="col-12 Quick-table p-1 p-md-3 table-responsive">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <th class="ps-1">Código</th>
                                <th class="ps-1">Fecha</th>
                                <th class="ps-1">Factura N°</th>
                                <th class="ps-1">Proveedor</th>
                                <th class="ps-1 d-none d-lg-table-cell">Empleado</th>
                                <th class="ps-1 d-none d-md-table-cell">Sucursal</th>
                                <th class="ps-1">Total</th>
                                <th class="ps-1 text-center">Estado</th>
                                <th class="ps-1 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="comprasRegistradas">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module" src="api/client/compras-historial.js"></script>