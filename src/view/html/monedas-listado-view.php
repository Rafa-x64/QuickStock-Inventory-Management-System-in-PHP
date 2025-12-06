<?php
// Vista listado monedas
?>
<div class="container-fluid" id="mainContent">
    <div class="row">
        <div class="col-12 p-4">
            <div class="row align-items-center mb-4">
                <div class="col-12 col-md-6 Quick-title">
                    <h1 class="m-0"><i class="bi bi-cash-coin"></i> Gestión de Monedas</h1>
                </div>
                <div class="col-12 col-md-6 text-end">
                    <a href="monedas-añadir" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Añadir Moneda
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 Quick-widget p-4 rounded shadow-sm bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabla_monedas">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Símbolo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module" src="view/js/monedas-listado.js"></script>