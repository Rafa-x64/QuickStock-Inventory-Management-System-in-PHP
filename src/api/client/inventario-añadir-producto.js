import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

/**
 * Función central para alternar entre input (nuevo) y select (existente)
 * para campos como 'color' y 'talla'.
 * @param {string} field - El nombre del campo ('color' o 'talla').
 * @param {string} mode - El modo a activar ('select' o 'new').
 */
function toggleInputSelect(field, mode) {
    const selectContainer = document.getElementById(`${field}-select-container`);
    const inputContainer = document.getElementById(`${field}-input-container`);
    const selectElement = document.getElementById(`id_${field}`);
    // Asegurarse de seleccionar el input correcto (nombre_color o rango_talla)
    const inputElement = document.getElementById(field === 'talla' ? 'rango_talla' : `nombre_${field}`);

    if (!selectContainer || !inputContainer || !selectElement || !inputElement) {
        console.error(`Error: Elementos para ${field} no encontrados.`);
        return;
    }

    if (mode === 'select') {
        // Mostrar SELECT, ocultar INPUT. Requerir SELECT.
        selectContainer.style.display = 'block';
        inputContainer.style.display = 'none';

        selectElement.disabled = false;
        selectElement.required = true;

        inputElement.disabled = true;
        inputElement.required = false; // El input ya no es requerido
        inputElement.value = ''; // Limpiar valor
        inputElement.classList.remove("is-valid", "is-invalid"); // Limpiar validación
    } else if (mode === 'new') {
        // Mostrar INPUT, ocultar SELECT. Requerir INPUT.
        selectContainer.style.display = 'none';
        inputContainer.style.display = 'block';

        inputElement.disabled = false;
        inputElement.required = true;

        selectElement.disabled = true;
        selectElement.required = false; // El select ya no es requerido
        selectElement.value = ''; // Deseleccionar
        selectElement.classList.remove("is-valid", "is-invalid"); // Limpiar validación
    }
}

document.addEventListener("DOMContentLoaded", () => {
    // Referencias a elementos
    const categoriaSelect = document.getElementById("id_categoria");
    const categoriaInput = document.getElementById("nombre_categoria");
    const proveedorSelect = document.getElementById("id_proveedor");
    const colorSelect = document.getElementById("id_color");
    const colorInput = document.getElementById("nombre_color");
    const tallaSelect = document.getElementById("id_talla");
    const tallaInput = document.getElementById("rango_talla");
    const sucursalSelect = document.getElementById("id_sucursal");

    // 1. Manejar Alternancia de Color y Talla (Asumiendo que los botones ya están en el HTML)
    document.querySelectorAll('[data-toggle="color"], [data-toggle="talla"]').forEach(button => {
        button.addEventListener('click', (e) => {
            const field = e.currentTarget.getAttribute('data-toggle');
            const mode = e.currentTarget.getAttribute('data-mode');
            toggleInputSelect(field, mode);
        });
    });

    // 2. Cargar Categorías (Lógica de Alternancia basada en existencia)
    api({ accion: "obtener_categorias" }).then(res => {
        if (!res.categorias || res.categorias.length === 0) {
            // No hay categorías: Usar input y deshabilitar select
            categoriaSelect.classList.add("d-none");
            categoriaSelect.disabled = true;
            categoriaSelect.required = false;
            categoriaInput.classList.remove("d-none");
            categoriaInput.disabled = false;
            categoriaInput.required = true;
            return;
        }

        // Sí hay categorías: Usar select y deshabilitar input
        categoriaInput.classList.add("d-none");
        categoriaInput.disabled = true;
        categoriaInput.required = false;
        categoriaSelect.classList.remove("d-none");
        categoriaSelect.disabled = false;
        categoriaSelect.required = true;


        colorSelect.innerHTML = `<option value="">Seleccione color</option>`;
        const hayColores = res.colores && res.colores.length > 0;

        if (hayColores) {
            res.colores.forEach(color => {
                colorSelect.innerHTML += `<option value="${color.id_color}">${color.nombre}</option>`;
            });
            // Si hay colores, iniciamos en modo 'select' para obligar a seleccionar uno existente
            toggleInputSelect('color', 'select');
        } else {
            // Si no hay colores, iniciamos en modo 'new' (input)
            toggleInputSelect('color', 'new');
        }
    });

    // 5. Cargar Tallas (Usando toggleInputSelect para inicializar)
    api({ accion: "obtener_tallas" }).then(res => {
        tallaSelect.innerHTML = `<option value="">Seleccione talla</option>`;
        const hayTallas = res.tallas && res.tallas.length > 0;

        if (hayTallas) {
            res.tallas.forEach(talla => {
                tallaSelect.innerHTML += `<option value="${talla.id_talla}">${talla.rango_talla}</option>`;
            });
            // Si hay tallas, iniciamos en modo 'select' para obligar a seleccionar una existente
            toggleInputSelect('talla', 'select');
        } else {
            // Si no hay tallas, iniciamos en modo 'new' (input)
            toggleInputSelect('talla', 'new');
        }
    });

    // 6. Cargar Sucursales
    api({ accion: "obtener_sucursales" }).then(res => {
        if (!res.filas || res.filas.length === 0) {
            sucursalSelect.innerHTML = `<option value="">Seleccione una sucursal</option>`;
            return;
        }
        res.filas.forEach(sucursal => {
            sucursalSelect.innerHTML += `<option value="${sucursal.id_sucursal}">${sucursal.nombre}</option>`;
        });
        // Opcional: Seleccionar la primera sucursal si hay
        if (res.filas.length > 0) {
            sucursalSelect.value = res.filas[0].id_sucursal;
        }
    });
});