/**
 * Lógica de Validaciones y Funcionalidad para la Vista de Registrar Nueva Compra
 * Archivo: compra-producto-unificado.js
 * CORRECCIÓN: Se ha mejorado la lógica de visualización del invalid-tooltip 
 * en los campos de producto, asegurando que se muestre correctamente al fallar la validación.
 */

document.addEventListener('DOMContentLoaded', function () {
    const formCompraProducto = document.getElementById('formCompraProducto');
    const productosContainer = document.getElementById('productosContainer');
    const btnAgregarProducto = document.getElementById('btnAgregarProducto');
    let productoIndex = 0;

    // --- Elementos de Totales ---
    const compraSubtotal = document.getElementById('compra_subtotal');
    const compraIVA = document.getElementById('compra_iva');
    const compraTotal = document.getElementById('compra_total');
    const IVA_RATE = 0.16; // Tasa de IVA (16%)

    // --- Funciones de Utilidad ---

    /**
     * @function updateTotals
     * Calcula y actualiza el subtotal, IVA y total de la compra.
     */
    function updateTotals() {
        let subtotalAcumulado = 0;

        const totalInputs = productosContainer.querySelectorAll('input[id^="producto_costo_total_"]');

        totalInputs.forEach(input => {
            const total = parseFloat(input.value) || 0;
            subtotalAcumulado += total;
        });

        const ivaCalculado = subtotalAcumulado * IVA_RATE;
        const totalFinal = subtotalAcumulado + ivaCalculado;

        compraSubtotal.value = subtotalAcumulado.toFixed(2);
        compraIVA.value = ivaCalculado.toFixed(2);
        compraTotal.value = totalFinal.toFixed(2);
    }

    /**
     * @function validateProductoField
     * Valida un campo de producto específico y gestiona la visibilidad del tooltip.
     * @param {HTMLElement} element El elemento input/select a validar.
     * @param {string} message El mensaje de error a mostrar.
     * @returns {boolean} true si es válido, false en caso contrario.
     */
    function validateProductoField(element, message) {
        const value = element.value.trim();
        const tooltip = element.closest('.position-relative').querySelector('.invalid-tooltip');

        // 1. Limpiar estado previo
        element.classList.remove('is-valid', 'is-invalid');
        // 2. Ocultar el tooltip forzándolo (lo contrario a la corrección anterior)
        if (tooltip) {
            tooltip.style.display = 'none';
        }

        let isValid = true;
        let errorMessage = message;

        // Validación base (required)
        if (!value || (element.tagName === 'SELECT' && value === "")) {
            isValid = false;
        }

        // Validación específica para cantidad y costo (números positivos)
        if (isValid && (element.name.includes('cantidad') || element.name.includes('costo_unitario'))) {
            const num = parseFloat(value);
            if (isNaN(num) || num <= 0) {
                isValid = false;
                errorMessage = (num <= 0) ? 'Debe ser un valor positivo mayor a cero.' : 'Debe ser un número válido.';
            }
        }

        // 3. Aplicar estado final
        if (isValid) {
            element.classList.add('is-valid');
        } else {
            element.classList.add('is-invalid');
            if (tooltip) {
                tooltip.textContent = errorMessage;
                // 4. AQUI ESTA LA CLAVE: Forzar la visibilidad y posición del tooltip al fallar
                tooltip.style.display = 'block';
                tooltip.style.opacity = '1';
                // Para asegurar que aparezca justo debajo del campo (si no se corrige en CSS)
                tooltip.style.top = '100%';
                tooltip.style.transform = 'translateY(0)';
            }
        }

        return isValid;
    }

    /**
     * @function calculateProductTotal
     * Calcula y actualiza el costo total de un producto.
     */
    function calculateProductTotal(index) {
        const cantidadInput = document.getElementById(`producto_cantidad_${index}`);
        const costoInput = document.getElementById(`producto_costo_unitario_${index}`);
        const totalInput = document.getElementById(`producto_costo_total_${index}`);

        const cantidad = parseFloat(cantidadInput.value) || 0;
        const costo = parseFloat(costoInput.value) || 0;

        const total = cantidad * costo;
        totalInput.value = total.toFixed(2);

        updateTotals();
    }


    // --- Lógica del Detalle de Producto ---

    /**
     * @function createProductRow
     * Crea y añade un nuevo bloque para registrar un producto.
     */
    function createProductRow() {
        const currentIndex = productoIndex;
        productoIndex++;

        const newRow = document.createElement('div');
        newRow.className = 'row p-3 mb-3 Quick-form-producto border'; // Agregué border para distinguirlos
        newRow.id = `producto-row-${currentIndex}`;
        newRow.innerHTML = `
            <div class="col-12 d-flex justify-content-between border-bottom pb-2">
                <h6 class="Quick-title">Producto #${currentIndex + 1}</h6>
                <button type="button" class="btn btn-danger btn-sm btn-remover-producto" data-index="${currentIndex}">
                    <i class="bi bi-x-circle"></i> Eliminar
                </button>
            </div>
            <div class="col-md-5 py-2 position-relative">
                <label class="Quick-title" for="producto_id_producto_${currentIndex}">Producto</label>
                <select id="producto_id_producto_${currentIndex}" name="producto_id_producto_${currentIndex}" class="Quick-form-input producto-field" required>
                    <option value="">Seleccione un producto...</option>
                    </select>
                <div class="invalid-tooltip">Campo requerido.</div>
            </div>
            <div class="col-md-3 py-2 position-relative">
                <label class="Quick-title" for="producto_cantidad_${currentIndex}">Cantidad</label>
                <input type="number" step="0.01" min="0.01" id="producto_cantidad_${currentIndex}" name="producto_cantidad_${currentIndex}" class="Quick-form-input producto-field" required value="1.00">
                <div class="invalid-tooltip">Cantidad requerida y debe ser positiva.</div>
            </div>
            <div class="col-md-2 py-2 position-relative">
                <label class="Quick-title" for="producto_costo_unitario_${currentIndex}">Costo Unitario</label>
                <input type="number" step="0.01" min="0.01" id="producto_costo_unitario_${currentIndex}" name="producto_costo_unitario_${currentIndex}" class="Quick-form-input producto-field" required value="0.00">
                <div class="invalid-tooltip">Costo unitario requerido y debe ser positivo.</div>
            </div>
            <div class="col-md-2 py-2 position-relative">
                <label class="Quick-title" for="producto_costo_total_${currentIndex}">Costo Total</label>
                <input type="text" id="producto_costo_total_${currentIndex}" name="producto_costo_total_${currentIndex}" class="Quick-form-input" readonly value="0.00">
            </div>
        `;
        productosContainer.appendChild(newRow);

        // Adjuntar event listeners para cálculos y validaciones
        const cantidadInput = document.getElementById(`producto_cantidad_${currentIndex}`);
        const costoInput = document.getElementById(`producto_costo_unitario_${currentIndex}`);
        const selectProducto = document.getElementById(`producto_id_producto_${currentIndex}`);
        const removeButton = newRow.querySelector('.btn-remover-producto');

        // Eventos para el cálculo del total por producto y general (input y blur)
        [cantidadInput, costoInput, selectProducto].forEach(element => {
            element.addEventListener('input', function () {
                // Validación en tiempo real al escribir (solo para inputs numéricos)
                if (this.type === 'number' || this.tagName === 'SELECT') {
                    let message = 'Campo requerido.';
                    if (element.name.includes('cantidad')) {
                        message = 'Cantidad requerida y debe ser positiva.';
                    } else if (element.name.includes('costo_unitario')) {
                        message = 'Costo unitario requerido y debe ser positivo.';
                    } else if (element.name.includes('id_producto')) {
                        message = 'Debe seleccionar un producto.';
                    }
                    validateProductoField(this, message);
                    if (this.type === 'number') {
                        calculateProductTotal(currentIndex);
                    }
                }
            });
            element.addEventListener('change', function () {
                // Validación al cambiar el select
                let message = 'Debe seleccionar un producto.';
                validateProductoField(this, message);
            });
            element.addEventListener('blur', function () {
                // Validación al perder el foco
                let message = 'Campo requerido.';
                if (element.name.includes('cantidad')) {
                    message = 'Cantidad requerida y debe ser positiva.';
                } else if (element.name.includes('costo_unitario')) {
                    message = 'Costo unitario requerido y debe ser positivo.';
                } else if (element.name.includes('id_producto')) {
                    message = 'Debe seleccionar un producto.';
                }
                validateProductoField(this, message);
            });
        });

        // Evento para eliminar la fila
        removeButton.addEventListener('click', function () {
            newRow.remove();
            updateTotals(); // Recalcular totales después de eliminar
        });

        // Inicializar el cálculo
        calculateProductTotal(currentIndex);
    }

    // --- Manejadores de Eventos ---

    // 1. Agregar Producto
    btnAgregarProducto.addEventListener('click', function () {
        createProductRow();
    });

    // 2. Manejo del Submit del Formulario
    formCompraProducto.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        // 1. Validar el Formulario Principal (Bootstrap/HTML5)
        let isFormValid = formCompraProducto.checkValidity();

        // 2. Validar Campos de Producto Individuales (Validación manual)
        let isProductDetailsValid = true;
        const productFields = productosContainer.querySelectorAll('.producto-field');

        productFields.forEach(field => {
            let message = '';
            if (field.name.includes('id_producto')) {
                message = 'Debe seleccionar un producto.';
            } else if (field.name.includes('cantidad')) {
                message = 'Cantidad requerida y debe ser positiva.';
            } else if (field.name.includes('costo_unitario')) {
                message = 'Costo unitario requerido y debe ser positivo.';
            }

            if (!validateProductoField(field, message)) {
                isProductDetailsValid = false;
            }
        });

        // 3. Validar que haya al menos un producto
        const productRows = productosContainer.querySelectorAll('.Quick-form-producto');
        const hasProducts = productRows.length > 0;

        if (!hasProducts) {
            alert('Debe agregar al menos un producto a la compra.');
            isProductDetailsValid = false;
        }

        // --- PROCESAR EL ENVÍO ---
        if (isFormValid && isProductDetailsValid && hasProducts) {
            console.log('✅ Formulario válido y listo para enviar al servidor.');
            // Aquí iría la lógica AJAX
        } else {
            // Forzar la validación de Bootstrap para mostrar tooltips en campos principales
            if (!isFormValid) {
                formCompraProducto.classList.add('was-validated');
            }
            console.error('❌ El formulario tiene errores. Revise los campos principales y los productos.');
        }

    });


    // 3. Manejo del Reset del Formulario
    formCompraProducto.addEventListener('reset', function () {
        formCompraProducto.classList.remove('was-validated');
        productosContainer.innerHTML = '';
        productoIndex = 0;
        updateTotals();

        // Ocultar manualmente los tooltips si estaban visibles
        document.querySelectorAll('.invalid-tooltip').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
    });

    // --- Inicialización ---

    const fechaInput = document.getElementById('compra_fecha_compra');
    if (fechaInput) {
        fechaInput.valueAsDate = new Date();
    }
});