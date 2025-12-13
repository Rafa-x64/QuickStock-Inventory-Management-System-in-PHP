document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formProducto");
    const getValue = (campo) => campo.value.trim();
    const reglas = {
        codigo_barra: { regex: /^[A-Za-z0-9\-]{3,}$/, mensaje: "Código solo letras, números y guiones (mínimo 3)" },
        nombre: { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]{3,}$/, mensaje: "Nombre solo letras, números y espacios (mínimo 3)" },
        
        nombre_categoria: { regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,}$/, mensaje: "Categoría nueva: mínimo 4 letras" },
        nombre_color: { regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,}$/, mensaje: "Color nuevo: mínimo 3 letras" },
        rango_talla: { regex: /^\s*(\d+(\s*-\s*\d+)*)?\s*$/, mensaje: 'Talla nueva: Formato válido: "número - número - ..."' },

        id_categoria: { min: 1, mensaje: "Debe seleccionar una categoría", isSelect: true },
        id_color: { min: 1, mensaje: "Debe seleccionar un color", isSelect: true },
        id_talla: { min: 1, mensaje: "Debe seleccionar una talla", isSelect: true },

        id_sucursal: { min: 1, mensaje: "Debe seleccionar una sucursal", isSelect: true },

        precio_compra: { min: 0.01, mensaje: "Precio de compra mínimo 0.01" },
        precio: { min: 0.01, mensaje: "Precio de venta mínimo 0.01" },
        cantidad: { min: 0, mensaje: "Stock inicial no puede ser negativo" },
        minimo: { min: 1, mensaje: "Stock mínimo ≥ 1" }
    };

    const validar = (campo, regla) => {
        if (campo.disabled) {
            campo.classList.remove("is-invalid", "is-valid");
            return true;
        }

        const valor = getValue(campo);
        let valido = true;

        const esDescripcion = campo.id === 'descripcion';

        if (valor.length === 0 && campo.id !== 'id_proveedor' && !esDescripcion) {
            valido = false;
        } else if (valor.length === 0 && esDescripcion) {
            valido = true;
        } else {

            if (regla && regla.regex) {
                valido = valido && regla.regex.test(valor);
            }

            if (regla && regla.min != null) {
                const valorNumerico = regla.isSelect ? parseInt(valor) : parseFloat(valor);
                if (!isNaN(valorNumerico)) {
                    valido = valido && (valorNumerico >= regla.min);
                }
            }
        }

        /* 
           VALIDACIÓN ESPECIAL: PRECIO VENTA >= PRECIO COMPRA 
           Solo si ambos tienen valor válido numérico
        */
        if (campo.id === 'precio' || campo.id === 'precio_compra') {
            const precioCompraVal = parseFloat(document.getElementById('precio_compra').value);
            const precioVentaVal  = parseFloat(document.getElementById('precio').value);

            if (!isNaN(precioCompraVal) && !isNaN(precioVentaVal)) {
                const precioVentaInput = document.getElementById('precio');
                
                // Si estamos editando compra o venta, verificamos la relación
                if (precioVentaVal < precioCompraVal) {
                    if (campo.id === 'precio') {
                        // Si estoy en precio venta y es menor, error aquí
                        valido = false;
                        regla = { mensaje: "El precio de venta no puede ser menor al de compra." }; // Override msg temporal
                    }
                    // NOTA: Si edito compra y es mayor que venta, técnicamente venta es el invalido, pero 
                    // para UX simple, mostramos error en el que se está tocando o marcamos venta como error.
                    // Para simplificar, marcaremos venta como error si la condición falla.
                }
            }
        }

        if (!regla && campo.id !== 'precio' && campo.id !== 'precio_compra') { // Excluir precios de "sin regla" logic si queremos custom msg
            valido = true;
        }


        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);

        let feedback = campo.parentElement.querySelector(".invalid-tooltip");

        if (feedback && !valido && regla) {
            feedback.textContent = regla.mensaje;
        } else if (!feedback && !valido && regla) {
            feedback = campo.parentElement.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-tooltip') && !valido) {
                feedback.textContent = regla.mensaje;
            }
        }


        return valido;
    };


    Object.keys(reglas).forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            const evento = reglas[id].isSelect ? "change" : "input";
            campo.addEventListener(evento, () => validar(campo, reglas[id]));
        }
    });
    
    // Listeners cruzados para precios
    const pCompra = document.getElementById('precio_compra');
    const pVenta  = document.getElementById('precio');
    if(pCompra && pVenta){
        pCompra.addEventListener('input', () => { 
             validar(pCompra, reglas['precio_compra']);
             if(pVenta.value !== '') validar(pVenta, reglas['precio']); // Re-validar venta al cambiar compra
        });
        pVenta.addEventListener('input', () => { 
             validar(pVenta, reglas['precio']);
        });
    }

    const descripcionCampo = document.getElementById('descripcion');
    if (descripcionCampo) {
        descripcionCampo.addEventListener("input", () => {
            // Simplemente validamos para limpiar/añadir is-valid, pasando 'null' como regla.
            // La lógica dentro de validar() debe manejar si el campo es 'descripcion'
            validar(descripcionCampo, null);
        });
    }


    form.addEventListener("submit", e => {
        let todoValido = true;

        const camposAValidar = Object.keys(reglas).map(id => document.getElementById(id))
            .concat(document.getElementById('descripcion')) // Incluimos descripcion manualmente
            .filter(campo => campo !== null);

        const validaciones = camposAValidar.map(campo => {
            const regla = reglas[campo.id] || null;
            return validar(campo, regla);
        });
        
        // Validación final explícita de precios al submit
        const precioCompraVal = parseFloat(pCompra.value);
        const precioVentaVal  = parseFloat(pVenta.value);
        if(!isNaN(precioCompraVal) && !isNaN(precioVentaVal) && precioVentaVal < precioCompraVal){
             validar(pVenta, { mensaje: "El precio de venta no puede ser menor al de compra." });
             todoValido = false;
        }

        todoValido = todoValido && validaciones.every(v => v);


        if (!todoValido) {
            e.preventDefault();
            const primerInvalido = document.querySelector(".is-invalid");
            if (primerInvalido) {
                primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
});