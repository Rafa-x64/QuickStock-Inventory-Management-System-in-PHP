<?php
$tipo_reporte = $_POST["tipo_reporte"] ?? "";
$fecha_inicio = $_POST["fecha_inicio"] ?? date('Y-m-01');
$fecha_fin = $_POST["fecha_fin"] ?? date('Y-m-d');
$accion = $_POST['accion'] ?? ''; // <-- Asegurarse de capturar la acción

echo __DIR__;
// =========================================================
// === INICIO DE LA NUEVA LÓGICA DE CONTROL (PDF HANDLER) ===
// =========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && $accion === 'imprimir_pdf') {
    // 1. Incluimos el controlador (la ruta 'controller/reportes_C.php' debe ser correcta)
    include_once "controller/reportes_C.php";

    // 2. Llama a la función. Dentro de ella, se genera el PDF y se hace 'exit'.
    reportes_C::generarReporte($_POST);

    // Previene que se renderice el HTML de la vista si por alguna razón el 'exit' en el controlador falla.
    exit;
}
// =========================================================
// === FIN DE LA NUEVA LÓGICA DE CONTROL ===
// =========================================================
?>

<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">

            <div class="row mb-4">
                <div class="col-12 text-center Quick-title">
                    <h1 class="m-0">Generación de Reportes</h1>
                    <p class="text-muted">Seleccione los parámetros para generar su reporte</p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 Quick-card p-4 rounded shadow-sm">
                    <form action="" method="POST" class="row g-3 needs-validation" id="reportForm" novalidate>

                        <!-- Input oculto para la acción -->
                        <input type="hidden" name="accion" id="accion" value="generar">

                        <!-- Tipo de Reporte -->
                        <div class="col-12 col-md-4">
                            <label for="tipo_reporte" class="form-label fw-bold">Tipo de Reporte</label>
                            <select name="tipo_reporte" id="tipo_reporte" class="form-select Quick-input" required>
                                <option value="" disabled <?php echo empty($tipo_reporte) ? 'selected' : ''; ?>>Seleccione...</option>
                                <option value="ventas" <?php echo $tipo_reporte == 'ventas' ? 'selected' : ''; ?>>Reporte de Ventas</option>
                                <option value="inventario" <?php echo $tipo_reporte == 'inventario' ? 'selected' : ''; ?>>Inventario General</option>
                                <option value="rotacion" <?php echo $tipo_reporte == 'rotacion' ? 'selected' : ''; ?>>Rotación de Productos</option>
                                <option value="financiero" <?php echo $tipo_reporte == 'financiero' ? 'selected' : ''; ?>>Reporte Financiero</option>
                            </select>
                        </div>

                        <!-- Fecha Inicio -->
                        <div class="col-12 col-md-4">
                            <label for="fecha_inicio" class="form-label fw-bold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control Quick-input" value="<?php echo $fecha_inicio; ?>" required>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-12 col-md-4">
                            <label for="fecha_fin" class="form-label fw-bold">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control Quick-input" value="<?php echo $fecha_fin; ?>" required>
                        </div>

                        <!-- Botones -->
                        <div class="col-12 text-center mt-4 d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" onclick="setAccion('generar')">
                                <i class="bi bi-file-earmark-text me-2"></i>Generar Reporte
                            </button>
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold" onclick="setAccion('imprimir_pdf')">
                                <i class="bi bi-file-pdf me-2"></i>Imprimir PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Área de Resultados -->
            <div class="row mt-5">
                <div class="col-12">
                    <?php
                    // Solo se ejecuta si es POST, hay un tipo de reporte, Y la acción es 'generar'
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($tipo_reporte) && $accion === 'generar') {
                        // Se incluye de nuevo, pero esto es seguro con include_once.
                        include_once "controller/reportes_C.php";
                        echo '<div class="fade-in">';
                        // Llama al controlador para obtener el HTML de la tabla
                        echo reportes_C::generarReporte($_POST);
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function setAccion(accion) {
        document.getElementById('accion').value = accion;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');

        fechaInicio.addEventListener('change', () => {
            fechaFin.min = fechaInicio.value;
        });

        fechaFin.addEventListener('change', () => {
            if (fechaInicio.value && fechaFin.value < fechaInicio.value) {
                alert("La fecha final no puede ser menor a la fecha de inicio");
                fechaFin.value = fechaInicio.value;
            }
        });
    });
</script>

<style>
    .Quick-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
    }

    .Quick-title h1 {
        color: #333;
        font-weight: 700;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>