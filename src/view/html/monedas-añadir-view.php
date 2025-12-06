<?php
// Vista añadir moneda
?>
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 Quick-widget p-4 rounded-2">
                    <h2 class="Quick-title mb-4">Añadir Nueva Moneda</h2>

                    <form id="form-crear-moneda">
                        <div class="mb-3">
                            <label class="form-label Quick-title">Nombre (Ej: Dólar Americano)</label>
                            <input type="text" class="form-control Quick-form-input" id="nombre_moneda" name="nombre" required maxlength="50">
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label Quick-title">Código ISO (Ej: USD)</label>
                                <input type="text" class="form-control Quick-form-input" id="codigo_moneda" name="codigo" required maxlength="10" placeholder="XYZ">
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label Quick-title">Símbolo (Ej: $)</label>
                                <input type="text" class="form-control Quick-form-input" id="simbolo_moneda" name="simbolo" required maxlength="5" placeholder="$">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label Quick-title">Estado</label>
                            <select class="form-select Quick-select" id="activo_moneda" name="activo">
                                <option value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="monedas-listado" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar Moneda</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="module" src="view/js/monedas-gestionar.js"></script>