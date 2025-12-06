import { cargarSelect, cargarDatosProductoBase, obtenerCompraParaEdicion } from "../../api/client/compras-editar.js";

// Variable global para almacenar los datos base para clonar productos
let datosBaseProducto = null;
let productoIndex = 0; // Contador para asignar índices únicos a los nombres de los productos

// Elementos clave del DOM
const $form = document.getElementById('formCompraProducto');
const $productosContainer = document.getElementById('productosContainer');
const $btnAgregarProducto = document.getElementById('btnAgregarProducto');
const $compraSubtotal = document.getElementById('compra_subtotal');
const $compraIVA = document.getElementById('compra_iva');
const $compraTotal = document.getElementById('compra_total');


// *********************************************************************************
// ************************** INICIO: LÓGICA DE CARGA Y PRECARGA *******************
// *********************************************************************************

/**
 * Carga los datos principales y los precarga en el formulario.
 * @param {Object} datosCompra - Datos de la compra principal.
 */
async function cargarDatosPrincipales(datosCompra, selectedIDs) {
    // 1. Cargar selects de forma asíncrona y preseleccionar
    await Promise.all([
        cargarSelect("compra_id_proveedor", "obtener_proveedores", "proveedor", "id_proveedor", "nombre", "Seleccione un proveedor...", selectedIDs.id_proveedor),
        cargarSelect("compra_id_sucursal", "obtener_sucursales", "filas", "id_sucursal", "nombre", "Seleccione una sucursal...", selectedIDs.id_sucursal),
        cargarSelect("compra_id_moneda", "obtener_monedas", "monedas", "id_moneda", "codigo", "Seleccione una moneda...", selectedIDs.id_moneda),
        cargarSelect("compra_id_usuario", "obtener_empleados_responsables", "empleados", "id_usuario", "nombre_completo", "Seleccione un empleado...", selectedIDs.id_usuario),
    ]);

    // 2. Precargar campos simples y el estado
    document.getElementById('compra_fecha_compra').value = datosCompra.fecha_compra || '';
    document.getElementById('compra_numero_factura').value = datosCompra.numero_factura || '';
    document.getElementById('compra_observaciones').value = datosCompra.observaciones || '';
    document.getElementById('compra_estado').value = datosCompra.estado || 'pendiente';
}

/**
 * Rellena los detalles de los productos existentes en el formulario.
 * @param {Array} detalles - Arreglo de objetos de detalle_compra.
 */
function precargarDetalleProductos(detalles) {
    // Limpia el contenedor de productos antes de empezar
    $productosContainer.innerHTML = '';

    detalles.forEach(detalle => {
        // Incrementa el índice para asignar names únicos
        productoIndex++;

        const htmlProducto = generarEstructuraProducto(productoIndex, detalle);
        $productosContainer.insertAdjacentHTML('beforeend', htmlProducto);
    });

    // Actualiza los eventos y recalcula los totales de los productos precargados
    actualizarEventosDinamicos();
    calcularTotalesGlobales();
}


/**
 * Genera la estructura HTML para un producto, ya sea nuevo o existente.
 * * @param {number} index - Índice del producto (para el array name).
 * @param {Object|null} [detalle=null] - Datos del producto a precargar (para edición).
 * @returns {string} HTML de la fila del producto.
 */
function generarEstructuraProducto(index, detalle = null) {
    const isEditing = detalle !== null;

    const idDetalle = isEditing ? detalle.id_detalle_compra : '';

    // Asignación de valores por defecto o precargados
    const nombre = isEditing ? detalle.nombre : '';
    const codigoBarra = isEditing ? detalle.codigo_barra : '';
    const idCategoria = detalle ? detalle.id_categoria : null;
    const idColor = detalle ? detalle.id_color : null;
    const idTalla = detalle ? detalle.id_talla : null;
    const precioCompra = isEditing ? detalle.precio_compra : '';
    const precioVenta = isEditing ? detalle.precio_venta : '';
    const cantidad = isEditing ? detalle.cantidad : '';
    // El subtotal se calcula en JS, pero precargamos el valor inicial
    const subtotalCalculado = isEditing ? (parseFloat(detalle.cantidad) * parseFloat(detalle.precio_compra)).toFixed(2) : '0.00';

    const html = `
        <div class="Quick-form p-3 border-bottom producto-detalle-fila position-relative" data-index="${index}">
            <input type="hidden" name="productos[${index}][id_detalle_compra]" value="${idDetalle}">
            <input type="hidden" name="productos[${index}][id_producto_existente]" value="${isEditing ? detalle.id_producto : ''}">
            
            <div class="row">
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Código de Barras / SKU</label>
                    <input type="text" name="productos[${index}][codigo_barra]" class="Quick-form-input" value="${codigoBarra}" required>
                </div>
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Nombre del Producto</label>
                    <input type="text" name="productos[${index}][nombre]" class="Quick-form-input producto-nombre" value="${nombre}" required>
                </div>
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Categoría</label>
                    <select name="productos[${index}][id_categoria]" class="Quick-form-input producto-categoria" required>
                        ${generarOpcionesSelect(datosBaseProducto.categorias, 'id_categoria', 'nombre', idCategoria)}
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Color</label>
                    <select name="productos[${index}][id_color]" class="Quick-form-input" required>
                        ${generarOpcionesSelect(datosBaseProducto.colores, 'id_color', 'nombre', idColor)}
                    </select>
                    </div>
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Talla</label>
                    <select name="productos[${index}][id_talla]" class="Quick-form-input" required>
                        ${generarOpcionesSelect(datosBaseProducto.tallas, 'id_talla', 'rango_talla', idTalla)}
                    </select>
                    </div>
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Cantidad a Comprar</label>
                    <input type="number" min="1" name="productos[${index}][cantidad]" class="Quick-form-input producto-cantidad" value="${cantidad}" required>
                </div>
            </div>

            <div class="row align-items-end">
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Precio Compra Unitario</label>
                    <input type="number" step="0.01" min="0" name="productos[${index}][precio_compra]" class="Quick-form-input producto-precio" value="${precioCompra}" required>
                </div>
                <div class="col-lg-4 col-md-4 py-2 position-relative">
                    <label class="Quick-title">Precio Venta Sugerido</label>
                    <input type="number" step="0.01" min="0" name="productos[${index}][precio_venta]" class="Quick-form-input" value="${precioVenta}" required>
                </div>
                <div class="col-lg-4 col-md-4 py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <label class="Quick-title">Subtotal de Producto</label>
                        <input type="text" class="Quick-form-input producto-subtotal" readonly value="${subtotalCalculado}">
                    </div>
                    
                    <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto ms-3" title="Eliminar Producto" style="align-self: flex-end;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    return html;
}

/**
 * Helper: Genera las opciones HTML para un select dinámico.
 */
function generarOpcionesSelect(dataArray, idField, displayField, selectedValue) {
    let options = '<option value="">Seleccione...</option>';
    dataArray.forEach(item => {
        const isSelected = (String(item[idField]) === String(selectedValue)) ? 'selected' : '';
        options += `<option value="${item[idField]}" ${isSelected}>${item[displayField]}</option>`;
    });
    return options;
}

// *********************************************************************************
// **************************** FIN: LÓGICA DE CARGA Y PRECARGA ********************
// *********************************************************************************


// *********************************************************************************
// ******************************* INICIO: LÓGICA DINÁMICA *************************
// *********************************************************************************

/**
 * Función principal para añadir un nuevo producto al formulario.
 */
function agregarNuevoProducto() {
    productoIndex++;
    const htmlProducto = generarEstructuraProducto(productoIndex, null);
    $productosContainer.insertAdjacentHTML('beforeend', htmlProducto);
    actualizarEventosDinamicos();
}

/**
 * Elimina una fila de producto.
 */
function eliminarProducto(event) {
    // Asegura que el clic vino de un botón de eliminar
    const btn = event.target.closest('.btn-eliminar-producto');
    if (!btn) return;

    const filas = $productosContainer.querySelectorAll('.producto-detalle-fila');

    // CORRECCIÓN: Se revisa si hay MÁS de 1 fila. Si hay 1, no se elimina.
    if (filas.length > 1) {
        // 1. Encontrar y remover la fila del producto
        btn.closest('.producto-detalle-fila').remove();

        // 2. Reindexar y recalcular
        reindexarProductos();
        calcularTotalesGlobales();
    } else {
        alert("Debe haber al menos un artículo en la compra.");
    }
}

/**
 * Recalcula todos los totales (subtotal, IVA, total) basados en las filas de productos.
 */
function calcularTotalesGlobales() {
    let subtotalGlobal = 0;

    $productosContainer.querySelectorAll('.producto-detalle-fila').forEach(fila => {
        const cantidad = parseFloat(fila.querySelector('.producto-cantidad').value) || 0;
        const precio = parseFloat(fila.querySelector('.producto-precio').value) || 0;

        const subtotalFila = cantidad * precio;

        // Actualizar el subtotal de la fila (solo visual)
        fila.querySelector('.producto-subtotal').value = subtotalFila.toFixed(2);

        subtotalGlobal += subtotalFila;
    });

    const ivaRate = 0.16; // 16% de IVA (valor fijo en el ejemplo)
    const montoIVA = subtotalGlobal * ivaRate;
    const totalGlobal = subtotalGlobal + montoIVA;

    // Actualizar campos de totales
    $compraSubtotal.value = subtotalGlobal.toFixed(2);
    $compraIVA.value = montoIVA.toFixed(2);
    $compraTotal.value = totalGlobal.toFixed(2);
}

/**
 * Reasigna los índices del array en el atributo 'name' de todos los inputs de producto.
 * Esto asegura que PHP reciba un array contiguo (productos[0], productos[1], etc.).
 */
function reindexarProductos() {
    $productosContainer.querySelectorAll('.producto-detalle-fila').forEach((fila, newIndex) => {
        // Actualizar el índice en el data-attribute de la fila
        fila.setAttribute('data-index', newIndex);

        // Recorrer todos los inputs/selects dentro de la fila
        fila.querySelectorAll('input, select').forEach(input => {
            const oldName = input.getAttribute('name');
            if (oldName) {
                // Reemplazar el índice antiguo (el número entre corchetes) por el nuevo
                // Ejemplo: productos[2][campo] -> productos[0][campo]
                const newName = oldName.replace(/\[\d+\]/, `[${newIndex}]`);
                input.setAttribute('name', newName);
            }
        });
    });
    // Actualizar el contador global para el próximo producto nuevo
    productoIndex = $productosContainer.querySelectorAll('.producto-detalle-fila').length - 1;
}

/**
 * Asigna y reasigna los listeners a los elementos dinámicos.
 */
function actualizarEventosDinamicos() {
    // Escuchar cambios en Cantidad y Precio para recalcular subtotales y totales globales
    $productosContainer.querySelectorAll('.producto-cantidad, .producto-precio').forEach(input => {
        // Remueve el listener anterior para evitar duplicados en el clonado
        input.removeEventListener('input', calcularTotalesGlobales);
        input.addEventListener('input', calcularTotalesGlobales);
    });

    // Escuchar el evento de eliminar producto
    $productosContainer.querySelectorAll('.btn-eliminar-producto').forEach(btn => {
        btn.removeEventListener('click', eliminarProducto);
        btn.addEventListener('click', eliminarProducto);
    });
}

// *********************************************************************************
// ****************************** FIN: LÓGICA DINÁMICA *****************************
// *********************************************************************************


// *********************************************************************************
// ******************************** INICIO: INICIALIZACIÓN *************************
// *********************************************************************************

async function inicializarFormularioEdicion() {
    // 1. Obtener el ID de la compra (la variable se define en la vista PHP)
    const id_compra = document.getElementById("compra_id").value;

    if (!id_compra) {
        console.error("No se proporcionó ID de compra para editar.");
        alert("Error: ID de compra no encontrado. No se puede cargar el formulario de edición.");
        return;
    }

    // 2. Cargar datos base de productos (necesarios para selects de categoría, color, etc.)
    datosBaseProducto = await cargarDatosProductoBase();

    // 3. Obtener los datos de la compra a editar
    const datosCargados = await obtenerCompraParaEdicion(id_compra);

    if (datosCargados && datosCargados.compra) {
        // 4. Cargar y preseleccionar los selects principales
        const { compra, detalles } = datosCargados;
        await cargarDatosPrincipales(compra, compra); // Pasamos 'compra' como 'selectedIDs'

        // 5. Precargar la sección de productos
        if (detalles && detalles.length > 0) {
            precargarDetalleProductos(detalles);
        } else {
            // Si no hay detalles, añadir al menos una fila vacía para empezar
            agregarNuevoProducto();
        }
    } else {
        // Fallback: Mostrar mensaje y añadir al menos una fila vacía
        alert("No se pudieron cargar los datos de la compra. Iniciando formulario vacío.");
        agregarNuevoProducto();
    }

    // 6. Asignar listener para el botón de agregar producto
    $btnAgregarProducto.addEventListener('click', agregarNuevoProducto);

    // 7. Inicializar el listener para la eliminación de productos (delegado)
    $productosContainer.addEventListener('click', eliminarProducto);

    // 8. Inicializar la validación de Bootstrap (si no está ya en la vista)
    // (Generalmente es mejor dejar esto en la vista si es código Bootstrap estándar)
}

// Ejecución al cargar el DOM
document.addEventListener("DOMContentLoaded", inicializarFormularioEdicion);