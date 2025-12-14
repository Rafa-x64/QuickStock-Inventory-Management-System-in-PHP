<?php
// Vista de historial
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <div class="row d-flex flex-row justify-content-between align-items-center mb-4">
                <div class="col-12 col-md-6 Quick-title">
                    <h1 class="m-0">Historial de Tasas</h1>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-end">
                    <a href="monedas-tasas-activas" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Tasas Activas
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12 Quick-widget p-4 rounded-2">
                    <div class="">
                        <table class="Quick-table" id="tabla_historial_tasas">
                            <thead class="">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Moneda</th>
                                    <th>Tasa</th>
                                    <th>Origen</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Cargando historial...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Botón Cargar Más -->
                    <div class="d-flex justify-content-center mt-3">
                        <button id="btn-cargar-mas" class="btn btn-outline-primary" style="display: none;">
                            <i class="bi bi-chevron-down"></i> Cargar más historial
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module" src="view/js/monedas-historial.js"></script>