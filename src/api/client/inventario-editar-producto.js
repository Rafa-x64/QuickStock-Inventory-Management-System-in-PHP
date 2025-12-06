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
    const inputElement = document.getElementById(field === 'talla' ? 'rango_talla' : `nombre_${field}`);

    if (!selectContainer || !inputContainer || !selectElement || !inputElement) {
        console.error(`Error: Elementos para ${field} no encontrados.`);
        return;
    }

    if (mode === 'select') {
        selectContainer.style.display = 'block';
        inputContainer.style.display = 'none';
        // Habilitar select y deshabilitar/limpiar input
        selectElement.disabled = false;
        inputElement.disabled = true;
        inputElement.value = ''; // Limpiar el valor del input al cambiar a select
        inputElement.classList.remove("is-valid", "is-invalid"); // Limpiar validación
    } else if (mode === 'new') {
        selectContainer.style.display = 'none';
        inputContainer.style.display = 'block';
        // Habilitar input y deshabilitar select
        inputElement.disabled = false;
        selectElement.disabled = true;
        selectElement.value = ''; // Deseleccionar el valor del select al cambiar a input
        selectElement.classList.remove("is-valid", "is-invalid"); // Limpiar validación
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const idProductoInput = document.getElementById("id_producto");
    if (!idProductoInput || !idProductoInput.value) {
        console.error("No se encontró el ID del producto");
        return;
    }
    const idProducto = idProductoInput.value;

    // Referencias a campos y contenedores
    const codigoInput = document.getElementById("codigo_barra");
    const nombreInput = document.getElementById("nombre");
    const descripcionInput = document.getElementById("descripcion");
    const proveedorSelect = document.getElementById("id_proveedor");
    const precioCompraInput = document.getElementById("precio_compra"); // <--- NUEVO CAMPO AÑADIDO
    const precioInput = document.getElementById("precio");
    const sucursalSelect = document.getElementById("id_sucursal");
    const cantidadInput = document.getElementById("cantidad");
    const minimoInput = document.getElementById("minimo");

    // Elementos de CATEGORÍA
    const categoriaSelect = document.getElementById("id_categoria");
    // const nombreCategoriaInput = document.getElementById("nombre_categoria"); // <--- ELIMINADO para corregir TypeError

    // Elementos de COLOR
    const colorSelect = document.getElementById("id_color");
    const nombreColorInput = document.getElementById("nombre_color");
    // Elementos de TALLA
    const tallaSelect = document.getElementById("id_talla");
    const rangoTallaInput = document.getElementById("rango_talla");

    // Función para manejar los clics en los botones de alternancia
    document.querySelectorAll('[data-toggle="color"], [data-toggle="talla"]').forEach(button => {
        button.addEventListener('click', (e) => {
            const field = e.currentTarget.getAttribute('data-toggle');
            const mode = e.currentTarget.getAttribute('data-mode');
            toggleInputSelect(field, mode);
        });
    });

    // Deshabilitar/habilitar inicial:
    categoriaSelect.disabled = false;

    colorSelect.disabled = true;
    nombreColorInput.disabled = true;
    tallaSelect.disabled = true;
    rangoTallaInput.disabled = true;


    // Traer producto y cargar todos los selects
    api({ accion: "obtener_un_producto", id_producto: idProducto })
        .then(p => {
            if (!p) {
                console.error("Producto no encontrado");
                return;
            }

            // Rellenar campos simples
            codigoInput.value = p.codigo_barra ?? "";
            nombreInput.value = p.nombre ?? "";
            document.getElementById("activo").value = String(p.activo);
            descripcionInput.value = p.descripcion ?? "";
            precioInput.value = p.precio ?? 0;
            precioCompraInput.value = p.precio_compra ?? 1.00; // <--- ASIGNACIÓN DE PRECIO DE COMPRA

            // Los campos input de alternancia que se llenan si el producto los tiene
            nombreColorInput.value = p.nombre_color ?? "";
            rangoTallaInput.value = p.rango_talla ?? "";

            // --- Lógica de Relleno y Visibilidad Inicial de Selects ---

            // Categorías (SIN ALTERNANCIA)
            api({ accion: "obtener_categorias" }).then(rCat => {
                const categorias = rCat.categorias || [];
                categorias.forEach(cat => {
                    const op = document.createElement("option");
                    op.value = cat.id_categoria;
                    op.textContent = cat.nombre;
                });
            });

            // Colores (CON ALTERNANCIA)
            api({ accion: "obtener_colores" }).then(rCol => {
                const colores = rCol.colores || [];
                // Llenar el select
                colores.forEach(c => {
                    const op = document.createElement("option");
                    op.value = c.id_color;
                    op.textContent = c.nombre;
                    if (c.id_color == p.id_color) op.selected = true;
                    colorSelect.appendChild(op);
                });

                // Lógica de alternancia inicial: MODO NUEVO por defecto
                // A menos que el producto ya tenga un id_color, en cuyo caso forzamos 'select'.
                if (p.id_color && p.id_color != "") {
                    toggleInputSelect('color', 'select');
                } else {
                    toggleInputSelect('color', 'new');
                }
            });

            // Tallas (CON ALTERNANCIA)
            api({ accion: "obtener_tallas" }).then(rTall => {
                const tallas = rTall.tallas || [];
                // Llenar el select
                tallas.forEach(t => {
                    const op = document.createElement("option");
                    op.value = t.id_talla;
                    op.textContent = t.rango_talla;
                    if (t.id_talla == p.id_talla) op.selected = true;
                    tallaSelect.appendChild(op);
                });

                // Lógica de alternancia inicial: MODO SELECCIONAR por defecto si hay tallas
                // A menos que el producto no tenga id_talla o si no hay tallas disponibles.
                if (tallas.length > 0 && p.id_talla && p.id_talla != "") {
                    toggleInputSelect('talla', 'select');
                } else {
                    toggleInputSelect('talla', 'new');
                }
            });

            // Sucursales e inventario
            api({ accion: "obtener_sucursales" }).then(rSuc => {
                rSuc.filas.forEach(s => {
                    const op = document.createElement("option");
                    op.value = s.id_sucursal;
                    op.textContent = s.nombre;

                    // Buscar cantidad y mínimo de inventario para la sucursal seleccionada
                    if (p.inventario && p.inventario.length > 0) {
                        const inv = p.inventario.find(i => i.id_sucursal == s.id_sucursal);
                        if (inv) {
                            cantidadInput.value = inv.cantidad ?? 0;
                            minimoInput.value = inv.minimo ?? 0;

                            // Si esta es la sucursal que tiene inventario, seleccionarla
                            // (Se asume que el producto en edición solo tiene un registro de inventario aquí)
                            if (inv.id_sucursal == p.inventario[0].id_sucursal) op.selected = true;
                        }
                    }
                    sucursalSelect.appendChild(op);
                });
            });

        })
        .catch(err => console.error("Error cargando producto:", err));
});