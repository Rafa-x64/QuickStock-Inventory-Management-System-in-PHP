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
  const inputElement = document.getElementById(
    field === "talla" ? "rango_talla" : `nombre_${field}`
  );

  if (!selectContainer || !inputContainer || !selectElement || !inputElement) {
    console.error(`Error: Elementos para ${field} no encontrados.`);
    return;
  }

  if (mode === "select") {
    // Mostrar SELECT, ocultar INPUT. Requerir SELECT.
    selectContainer.style.display = "block";
    inputContainer.style.display = "none";

    selectElement.disabled = false;
    selectElement.required = true;

    inputElement.disabled = true;
    inputElement.required = false; // El input ya no es requerido
    inputElement.value = ""; // Limpiar valor
    inputElement.classList.remove("is-valid", "is-invalid"); // Limpiar validación
  } else if (mode === "new") {
    // Mostrar INPUT, ocultar SELECT. Requerir INPUT.
    selectContainer.style.display = "none";
    inputContainer.style.display = "block";

    inputElement.disabled = false;
    inputElement.required = true;

    selectElement.disabled = true;
    selectElement.required = false; // El select ya no es requerido
    selectElement.value = ""; // Deseleccionar
    selectElement.classList.remove("is-valid", "is-invalid"); // Limpiar validación
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Referencias a elementos
  const categoriaSelect = document.getElementById("id_categoria");
  const categoriaInput = document.getElementById("nombre_categoria");
  const proveedorSelect = document.getElementById("id_proveedor");
  const colorSelect = document.getElementById("id_color");
  const tallaSelect = document.getElementById("id_talla");
  const sucursalSelect = document.getElementById("id_sucursal");

  // 1. Manejar Alternancia de Color y Talla (Asumiendo que los botones ya están en el HTML)
  document
    .querySelectorAll('[data-toggle="color"], [data-toggle="talla"]')
    .forEach((button) => {
      button.addEventListener("click", (e) => {
        const field = e.currentTarget.getAttribute("data-toggle");
        const mode = e.currentTarget.getAttribute("data-mode");
        toggleInputSelect(field, mode);
      });
    });

  // 2. Cargar Categorías
  api({ accion: "obtener_categorias" })
    .then((res) => {
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

      // Limpiar opciones previas (excepto la por defecto)
      categoriaSelect.innerHTML = `<option value="">Seleccione categoría</option>`;

      res.categorias.forEach((cat) => {
        // Solo mostrar categorías activas si se desea, o todas. Usualmente solo activas para nuevos productos.
        // Asumiendo que el backend devuelve todas, filtramos o mostramos.
        // El backend ya debería filtrar o devolvemos todas y dejamos al usuario decidir.
        // Aquí asumimos mostrar todas las que llegan.
        if (cat.activo === "t" || cat.activo === true || cat.activo === 1) {
          // Pequeña validación de activo
          categoriaSelect.innerHTML += `<option value="${cat.id_categoria}">${cat.nombre}</option>`;
        }
      });
    })
    .catch((err) => console.error("Error al obtener categorías:", err));

  // 3. Cargar Proveedores
  api({ accion: "obtener_proveedores" })
    .then((res) => {
      proveedorSelect.innerHTML = `<option value="">Seleccione proveedor (Opcional)</option>`;

      // La API devuelve "proveedor" (singular) según inspección de index.php y core.proveedor.php
      if (res.proveedor && res.proveedor.length > 0) {
        res.proveedor.forEach((prov) => {
          // Validar activo
          const isActive =
            prov.activo === "t" || prov.activo === true || prov.activo === 1;
          if (isActive) {
            // Mostrar solo nombre si no tiene RIF, o Nombre (RIF) si tiene.
            const textoMostrar = prov.rif
              ? `${prov.nombre} (${prov.rif})`
              : prov.nombre;
            proveedorSelect.innerHTML += `<option value="${prov.id_proveedor}">${textoMostrar}</option>`;
          }
        });
      }
    })
    .catch((err) => console.error("Error al obtener proveedores:", err));

  // 4. Cargar Colores
  api({ accion: "obtener_colores" })
    .then((res) => {
      colorSelect.innerHTML = `<option value="">Seleccione color</option>`;
      const hayColores = res.colores && res.colores.length > 0;

      if (hayColores) {
        res.colores.forEach((color) => {
          const isActive =
            color.activo === "t" || color.activo === true || color.activo === 1;
          if (isActive) {
            colorSelect.innerHTML += `<option value="${color.id_color}">${color.nombre}</option>`;
          }
        });
        // Si hay colores, iniciamos en modo 'select' para obligar a seleccionar uno existente
        toggleInputSelect("color", "select");
      } else {
        // Si no hay colores, iniciamos en modo 'new' (input)
        toggleInputSelect("color", "new");
      }
    })
    .catch((err) => console.error("Error al obtener colores:", err));

  // 5. Cargar Tallas
  api({ accion: "obtener_tallas" })
    .then((res) => {
      tallaSelect.innerHTML = `<option value="">Seleccione talla</option>`;
      const hayTallas = res.tallas && res.tallas.length > 0;

      if (hayTallas) {
        res.tallas.forEach((talla) => {
          const isActive =
            talla.activo === "t" || talla.activo === true || talla.activo === 1;
          if (isActive) {
            tallaSelect.innerHTML += `<option value="${talla.id_talla}">${talla.rango_talla}</option>`;
          }
        });
        // Si hay tallas, iniciamos en modo 'select' para obligar a seleccionar una existente
        toggleInputSelect("talla", "select");
      } else {
        // Si no hay tallas, iniciamos en modo 'new' (input)
        toggleInputSelect("talla", "new");
      }
    })
    .catch((err) => console.error("Error al obtener tallas:", err));

  // 6. Cargar Sucursales
  api({ accion: "obtener_sucursales" })
    .then((res) => {
      if (!res.filas || res.filas.length === 0) {
        sucursalSelect.innerHTML = `<option value="">Seleccione una sucursal</option>`;
        return;
      }

      sucursalSelect.innerHTML = ""; // Limpiar antes de llenar para evitar duplicados si se llamara de nuevo
      res.filas.forEach((sucursal) => {
        sucursalSelect.innerHTML += `<option value="${sucursal.id_sucursal}">${sucursal.nombre}</option>`;
      });

      // Opcional: Seleccionar la primera sucursal si hay
      if (res.filas.length > 0) {
        sucursalSelect.value = res.filas[0].id_sucursal;
      }
    })
    .catch((err) => console.error("Error al obtener sucursales:", err));
});
