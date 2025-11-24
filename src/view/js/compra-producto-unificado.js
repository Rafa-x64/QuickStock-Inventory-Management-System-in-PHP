// Este archivo NO es un módulo.
// Se asume que 'cargarDatosProductoBase' estará disponible globalmente después de la carga del módulo 'peticiones.js'.

// *********************************************************************************
// ************************* START: FUNCIONES DE SOPORTE **************************
// *********************************************************************************

/**
 * Helper: Genera las opciones HTML a partir de un array de datos.
 * @param {Array} data - El array de objetos.
 * @param {string} idField - La clave para el atributo value (e.g., 'id_color').
 * @param {string} displayField - La clave para el texto de la opción (e.g., 'nombre').
 * @param {string} defaultText - Texto de la opción por defecto.
 * @returns {string} El HTML de las opciones.
 */
function crearOpcionesHTML(data, idField, displayField, defaultText) {
    let options = `<option value="">${defaultText}</option>`;
    if (data && Array.isArray(data)) {
        data.forEach(item => {
            const value = item[idField];
            const text = item[displayField];
            if (value !== undefined && text !== undefined) {
                options += `<option value="${value}">${text}</option>`;
            }
        });
    }
    return options;
}

// Contenedor de datos de la API para Categoría, Color y Talla (se llena al inicio)
let datosBaseProducto = {};


// *********************************************************************************
// ************************** END: FUNCIONES DE SOPORTE ***************************
// *********************************************************************************


document.addEventListener("DOMContentLoaded", async () => {
    const formCompra = document.getElementById("formCompraProducto");
    const productosContainer = document.getElementById("productosContainer");
    const btnAgregarProducto = document.getElementById("btnAgregarProducto");

    if (!formCompra) {
        console.error('Formulario de Compra no encontrado. Revise el ID.');
        return;
    }

    // *** MODIFICACIÓN CLAVE: Acceder a la función globalmente ***
    // Se asume que 'cargarDatosProductoBase' se ha hecho accesible globalmente
    // después de cargar el módulo 'peticiones.js'.
    if (typeof cargarDatosProductoBase === 'function') {
        console.log("Cargando datos base de productos (Categorías, Colores, Tallas)...");
        datosBaseProducto = await cargarDatosProductoBase();
        console.log("Datos de productos cargados:", datosBaseProducto);
    } else {
        console.warn("ADVERTENCIA: Función 'cargarDatosProductoBase' no definida globalmente. ¿Se cargó 'peticiones.js' como módulo y se hizo global la función?");
    }
    // *********************************************************

    let productoIndex = 0;

    // --- MÓDULO: Funciones de Utilidad y Validaciones ---

    const getValue = (campo) => campo.value.trim();

    const reglasValidacion = {
        // Reglas de la Compra Principal
        // ❌ MODIFICACIÓN 1: Eliminada la regla de validación de fecha.
        // compra_fecha_compra: { min: new Date('2020-01-01').getTime(), mensaje: "Fecha inválida." }, 
        compra_id_proveedor: { min: 1, mensaje: "Debe seleccionar proveedor.", isSelect: true },
        compra_id_sucursal: { min: 1, mensaje: "Debe seleccionar sucursal.", isSelect: true },
        compra_id_usuario: { min: 1, mensaje: "Debe seleccionar empleado.", isSelect: true },
        compra_numero_factura: { regex: /^[A-Za-z0-9\-]{1,}$/, mensaje: "Factura obligatoria." },
        compra_id_moneda: { min: 1, mensaje: "Debe seleccionar moneda.", isSelect: true },

        // Reglas del Módulo de Producto (Las de color/talla aplican al campo activo)
        prod_codigo_barra: { regex: /^[A-Za-z0-9\-]{1,}$/, mensaje: "Código obligatorio." },
        prod_nombre: { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]{3,}$/, mensaje: "Nombre (mínimo 3)." },
        prod_id_categoria: { min: 1, mensaje: "Debe seleccionar categoría", isSelect: true },

        // Reglas de Color y Talla (para SELECT y INPUT)
        prod_id_color: { min: 1, mensaje: "Debe seleccionar color", isSelect: true },
        prod_nombre_color: { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]{1,}$/, mensaje: "Escriba un color válido." },
        prod_id_talla: { min: 1, mensaje: "Debe seleccionar talla", isSelect: true },
        prod_rango_talla: { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s\-\/]{1,}$/, mensaje: "Escriba una talla válida." },

        prod_cantidad: { min: 1, mensaje: "Cantidad mínima 1" },
        prod_precio_compra: { min: 0.01, mensaje: "Precio compra mínimo 0.01" },
        prod_precio_venta: { min: 0.01, mensaje: "Precio venta mínimo 0.01" }
    };

    const validarCampo = (campo, regla) => {
        if (campo.disabled || campo.readOnly) { // 📝 Nota: Se añade campo.readOnly aquí por consistencia, aunque la regla ya se eliminó
            campo.classList.remove("is-invalid", "is-valid");
            return true;
        }

        const valor = getValue(campo);
        let valido = true;

        if (valor.length === 0 && campo.required) {
            valido = false;
        } else if (valor.length === 0 && !campo.required) {
            valido = true;
        } else {
            if (regla && regla.regex) {
                valido = valido && regla.regex.test(valor);
            }
            if (regla && regla.min != null) {
                const valorNumerico = regla.isSelect ? parseInt(valor) : parseFloat(valor);
                valido = valido && !isNaN(valorNumerico) && (valorNumerico >= regla.min);
            }
        }

        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);

        const feedback = campo.parentElement.querySelector(".invalid-tooltip");
        if (feedback && !valido && regla) {
            feedback.textContent = regla.mensaje;
        }

        return valido;
    };

    const adjuntarValidacion = (campo, idBase) => {
        const regla = reglasValidacion[idBase] || null;
        if (!regla) return;

        const evento = campo.tagName === 'SELECT' ? "change" : "input";
        campo.addEventListener(evento, () => validarCampo(campo, regla));
    };


    // --- MÓDULO: Lógica de Alternancia Producto Dinámico (Reubicada aquí) ---

    /**
     * Alterna la visualización y estado (disabled/required/name) entre un select y un input.
     * Mantiene esta lógica en 'validaciones.js' ya que depende fuertemente de 'validarCampo'
     * y 'reglasValidacion'.
     * @param {HTMLElement} module - El contenedor del módulo de producto.
     * @param {string} field - 'color' o 'talla'.
     * @param {string} mode - 'select' o 'new'.
     */
    function toggleProductoInputSelect(module, field, mode) {
        const isColor = field === 'color';
        const index = module.dataset.index;

        const selectContainer = module.querySelector(`.producto-select-container[data-field="${field}"]`);
        const inputContainer = module.querySelector(`.producto-input-container[data-field="${field}"]`);

        const selectElement = module.querySelector(`#prod_id_${field}_${index}`);
        const inputElement = module.querySelector(isColor ? `#prod_nombre_${field}_${index}` : `#prod_rango_${field}_${index}`);

        const selectIdBase = `prod_id_${field}`;
        const inputIdBase = isColor ? `prod_nombre_${field}` : `prod_rango_${field}`;
        const selectRegla = reglasValidacion[selectIdBase];
        const inputRegla = reglasValidacion[inputIdBase];

        if (mode === 'select') {
            // Lógica para SELECCIONAR EXISTENTE
            selectContainer.style.display = 'block';
            inputContainer.style.display = 'none';

            selectElement.disabled = false;
            selectElement.required = true;
            selectElement.setAttribute('name', `productos[${index}][id_${field}]`);
            selectElement.classList.add("prod-campo-requerido");

            inputElement.disabled = true;
            inputElement.required = false;
            inputElement.value = '';
            inputElement.setAttribute('name', `productos[${index}][${inputIdBase}_disabled]`);
            inputElement.classList.remove("prod-campo-requerido", "is-valid", "is-invalid");

            validarCampo(selectElement, selectRegla);

        } else if (mode === 'new') {
            // Lógica para AÑADIR NUEVO
            selectContainer.style.display = 'none';
            inputContainer.style.display = 'block';

            inputElement.disabled = false;
            inputElement.required = true;
            inputElement.setAttribute('name', `productos[${index}][${inputIdBase.substring(4)}]`);
            inputElement.classList.add("prod-campo-requerido");

            selectElement.disabled = true;
            selectElement.required = false;
            selectElement.value = '';
            selectElement.setAttribute('name', `productos[${index}][id_${field}_disabled]`);
            selectElement.classList.remove("prod-campo-requerido", "is-valid", "is-invalid");

            validarCampo(inputElement, inputRegla);
        }
    }


    // --- MÓDULO: Inyección y Lógica del Producto Dinámico ---

    /**
     * Genera el HTML del formulario de registro de producto, 
     * inyectando las opciones de Select cargadas.
     * (Usa la función 'crearOpcionesHTML' definida en este mismo archivo).
     */
    function generarModuloProductoHTML(index, baseData) {
        const categoriaOptions = crearOpcionesHTML(baseData.categorias, 'id_categoria', 'nombre', 'Seleccione categoría');
        const colorOptions = crearOpcionesHTML(baseData.colores, 'id_color', 'nombre', 'Seleccione color');
        const tallaOptions = crearOpcionesHTML(baseData.tallas, 'id_talla', 'rango_talla', 'Seleccione talla');

        return `
            <div class="row Quick-form-product p-4 mb-4 border border-info rounded-3" data-index="${index}">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h5 class="Quick-title">Producto #${index + 1}</h5>
                    <button type="button" class="btn btn-danger btn-sm btn-remover-producto" data-index="${index}">Eliminar</button>
                </div>
                <hr>
                
                <div class="col-md-3 d-flex flex-column py-2 position-relative">
                    <label for="prod_codigo_barra_${index}" class="form-label Quick-title">Código de Barras / SKU</label>
                    <input type="text" id="prod_codigo_barra_${index}" name="productos[${index}][codigo_barra]" class="Quick-form-input prod-campo-requerido" required>
                    <div class="invalid-tooltip">Código de barras obligatorio.</div>
                </div>

                <div class="col-md-5 d-flex flex-column py-2 position-relative">
                    <label for="prod_nombre_${index}" class="form-label Quick-title">Nombre del Producto</label>
                    <input type="text" id="prod_nombre_${index}" name="productos[${index}][nombre]" class="Quick-form-input prod-campo-requerido" required>
                    <div class="invalid-tooltip">El nombre es obligatorio.</div>
                </div>
                
                <div class="col-md-4 d-flex flex-column py-2 position-relative">
                    <label for="prod_id_categoria_${index}" class="form-label Quick-title">Categoría</label>
                    <select id="prod_id_categoria_${index}" name="productos[${index}][id_categoria]" class="Quick-form-input prod-campo-requerido" required>
                        ${categoriaOptions}
                    </select>
                    <div class="invalid-tooltip">Debe seleccionar categoría.</div>
                </div>

                <div class="col-md-4 d-flex flex-column py-2 position-relative">
                    <label for="prod_id_color_${index}" class="form-label Quick-title">Color</label>

                    <div class="producto-select-container" data-field="color" style="display: block;">
                        <select id="prod_id_color_${index}" name="productos[${index}][id_color]" class="Quick-form-input prod-campo-requerido" required>
                            ${colorOptions}
                        </select>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-1 btn-toggle-color" data-mode="new" data-index="${index}">
                            ¿Añadir Nuevo Color?
                        </button>
                    </div>

                    <div class="producto-input-container" data-field="color" style="display: none;">
                        <input type="text" id="prod_nombre_color_${index}" name="productos[${index}][nombre_color_disabled]" placeholder="Añadir nuevo color (Ej: Rojo, Azul)" class="Quick-form-input" disabled>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-1 btn-toggle-color" data-mode="select" data-index="${index}">
                            Seleccionar color existente
                        </button>
                    </div>

                    <div class="invalid-tooltip">Debe seleccionar o agregar un color válido.</div>
                </div>
                <div class="col-md-4 d-flex flex-column py-2 position-relative">
                    <label for="prod_id_talla_${index}" class="form-label Quick-title">Talla</label>

                    <div class="producto-select-container" data-field="talla" style="display: block;">
                        <select id="prod_id_talla_${index}" name="productos[${index}][id_talla]" class="Quick-form-input prod-campo-requerido" required>
                            ${tallaOptions}
                        </select>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-1 btn-toggle-talla" data-mode="new" data-index="${index}">
                            ¿Nueva Talla?
                        </button>
                    </div>

                    <div class="producto-input-container" data-field="talla" style="display: none;">
                        <input type="text" id="prod_rango_talla_${index}" name="productos[${index}][rango_talla_disabled]" placeholder="Añadir nueva talla (Ej: 39 - 41, S, XL)" class="Quick-form-input" disabled>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-1 btn-toggle-talla" data-mode="select" data-index="${index}">
                            Seleccionar existente
                        </button>
                    </div>
                    
                    <div class="invalid-tooltip">Debe seleccionar o agregar una talla válida.</div>
                </div>
                <div class="col-md-4 d-flex flex-column py-2 position-relative">
                    <label for="prod_cantidad_${index}" class="form-label Quick-title">Cantidad a Comprar</label>
                    <input type="number" id="prod_cantidad_${index}" name="productos[${index}][cantidad]" class="Quick-form-input prod-campo-requerido prod-recalcular" step="1" min="1" value="1" required>
                    <div class="invalid-tooltip">Cantidad obligatoria y min 1.</div>
                </div>

                <div class="col-md-6 d-flex flex-column py-2 position-relative">
                    <label for="prod_precio_compra_${index}" class="form-label Quick-title">Precio Compra Unitario (Bs.)</label>
                    <input type="number" id="prod_precio_compra_${index}" name="productos[${index}][precio_compra]" class="Quick-form-input prod-campo-requerido prod-recalcular" step="0.01" min="0.01" value="0.01" required>
                    <div class="invalid-tooltip">Precio de compra obligatorio y min 0.01.</div>
                </div>

                <div class="col-md-6 d-flex flex-column py-2 position-relative">
                    <label for="prod_precio_venta_${index}" class="form-label Quick-title">Precio Venta Sugerido (Bs.)</label>
                    <input type="number" id="prod_precio_venta_${index}" name="productos[${index}][precio_venta]" class="Quick-form-input prod-campo-requerido" step="0.01" min="0.01" value="0.01" required>
                    <div class="invalid-tooltip">Precio de venta obligatorio y min 0.01.</div>
                </div>
            </div>
        `;
    }

    /**
     * Inyecta y configura un nuevo módulo de producto.
     */
    function agregarModuloProducto() {
        const index = productoIndex++;
        productosContainer.insertAdjacentHTML('beforeend', generarModuloProductoHTML(index, datosBaseProducto));

        const newModule = productosContainer.querySelector(`[data-index="${index}"]`);

        // 1. Adjuntar listeners de VALIDACIÓN y RECÁLCULO
        newModule.querySelectorAll('.Quick-form-input').forEach(campo => {
            const idParts = campo.id.split('_');
            const idBase = idParts.length > 3 ? idParts.slice(0, 3).join('_') : idParts.slice(0, 2).join('_');

            if (reglasValidacion[idBase]) {
                adjuntarValidacion(campo, idBase);
            }

            if (campo.classList.contains('prod-recalcular')) {
                campo.addEventListener('input', calcularTotales);
            }
        });

        // 2. Adjuntar listeners de ALTERNANCIA para Color y Talla
        newModule.querySelectorAll('.btn-toggle-color').forEach(button => {
            button.addEventListener('click', (e) => {
                const mode = e.currentTarget.getAttribute('data-mode');
                toggleProductoInputSelect(newModule, 'color', mode);
            });
        });

        newModule.querySelectorAll('.btn-toggle-talla').forEach(button => {
            button.addEventListener('click', (e) => {
                const mode = e.currentTarget.getAttribute('data-mode');
                toggleProductoInputSelect(newModule, 'talla', mode);
            });
        });

        // 3. Adjuntar listener de Eliminar
        newModule.querySelector('.btn-remover-producto').addEventListener('click', (e) => {
            e.target.closest('.Quick-form-product').remove();
            calcularTotales();
        });

        // 4. Recalcular totales al añadir un módulo
        calcularTotales();
    }

    // --- MÓDULO: Lógica de Cálculos de Totales ---

    const ivaRate = 0.16;

    function calcularTotales() {
        let subtotal = 0.00;

        productosContainer.querySelectorAll('.Quick-form-product').forEach(module => {
            const cantidadInput = module.querySelector('[id^="prod_cantidad_"]');
            const precioInput = module.querySelector('[id^="prod_precio_compra_"]');

            const cantidad = parseFloat(cantidadInput ? cantidadInput.value : 0) || 0;
            const precio = parseFloat(precioInput ? precioInput.value : 0) || 0;

            if (cantidad > 0 && precio > 0) {
                subtotal += cantidad * precio;
            }
        });

        const iva = subtotal * ivaRate;
        const total = subtotal + iva;

        const subtotalEl = document.getElementById('compra_subtotal');
        if (subtotalEl) subtotalEl.value = subtotal.toFixed(2);
        const ivaEl = document.getElementById('compra_iva');
        if (ivaEl) ivaEl.value = iva.toFixed(2);
        const totalEl = document.getElementById('compra_total');
        if (totalEl) totalEl.value = total.toFixed(2);
    }

    // --- Lógica Principal de Inicialización y Eventos ---

    const fechaCompraEl = document.getElementById("compra_fecha_compra");
    if (fechaCompraEl) {
        // 🚀 MODIFICACIÓN 2: Se añade el atributo 'readonly' al input de fecha.
        fechaCompraEl.readOnly = true;

        // Inicializar con la fecha de hoy
        const now = new Date();
        const yyyy = now.getFullYear();
        // getMonth() devuelve 0-11. Sumamos 1 y rellenamos con cero a la izquierda si es necesario.
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        fechaCompraEl.value = `${yyyy}-${mm}-${dd}`;

        // 📝 Nota: Se eliminó la validación, por lo que no es necesario adjuntar el listener de validación aquí.
    }

    if (btnAgregarProducto) btnAgregarProducto.addEventListener('click', agregarModuloProducto);

    // Adjuntar validación a los campos principales de la compra (excluyendo la fecha, que ya no tiene regla)
    Object.keys(reglasValidacion).filter(id => id.startsWith('compra_')).forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            adjuntarValidacion(campo, id);
        }
    });

    // 4. Listener para el envío del formulario de Compra
    if (formCompra) formCompra.addEventListener("submit", e => {
        e.preventDefault();

        let todoValido = true;
        let primerInvalido = null;

        // Validar campos de Compra Principal
        Object.keys(reglasValidacion).filter(id => id.startsWith('compra_')).forEach(id => {
            const campo = document.getElementById(id);
            if (campo && !validarCampo(campo, reglasValidacion[id])) {
                todoValido = false;
                if (!primerInvalido) primerInvalido = campo;
            }
        });

        // Validar Módulos de Producto
        const productosModulos = productosContainer.querySelectorAll('.Quick-form-product');
        if (productosModulos.length === 0) {
            // Mantenemos este alert solo si la lista está vacía, para informar la validación mínima.
            // Si desea eliminar *todo* alert, debe eliminar esta línea:
            alert("Debe agregar al menos un producto a la compra.");
            todoValido = false;
        } else {
            productosModulos.forEach(module => {
                module.querySelectorAll('.Quick-form-input').forEach(campo => {
                    if (campo.disabled) return;

                    const idParts = campo.id.split('_');
                    const idBase = idParts.length > 3 ? idParts.slice(0, 3).join('_') : idParts.slice(0, 2).join('_');

                    const regla = reglasValidacion[idBase] || null;

                    if (regla && !validarCampo(campo, regla)) {
                        todoValido = false;
                        if (!primerInvalido) primerInvalido = campo;
                    }
                });
            });
        }

        if (!todoValido) {
            if (primerInvalido) {
                primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // ⭐ ¡SOLUCIÓN A TU REQUERIMIENTO! ⭐
        // Después de la validación exitosa, enviamos el formulario de manera estándar.
        formCompra.submit();
    });

    const resetButton = document.querySelector('button[type="reset"]');
    if (resetButton) resetButton.addEventListener('click', () => {
        formCompra.reset();

        if (fechaCompraEl) {
            // Inicializar fecha de nuevo al hacer reset
            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            fechaCompraEl.value = `${yyyy}-${mm}-${dd}`;
        }

        productosContainer.innerHTML = '';
        productoIndex = 0;
        calcularTotales();

        formCompra.querySelectorAll('.is-invalid, .is-valid').forEach(el => el.classList.remove('is-invalid', 'is-valid'));
    });
});