document.addEventListener("DOMContentLoaded", () => {

    const btnReset = document.getElementById("btnReset");

    const form = document.getElementById("formProducto");

    // Reglas iguales que registrar, solo que aplican en editar también
    const reglas = {
        codigo_barra: { regex: /^[A-Za-z0-9\-]+$/, mensaje: "Código solo letras, números y guiones" },
        nombre: { regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]+$/, mensaje: "Nombre solo letras, números y espacios" },
        descripcion: { regex: /.*/, mensaje: "" },

        // Inputs opcionales según alternancia, pero igual se validan cuando estén activos
        nombre_categoria: { regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{4,}$/, mensaje: "Categoría mínimo 4 letras" },
        nombre_color: { regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,}$/, mensaje: "Color mínimo 3 letras" },
        rango_talla: { regex: /^(\d+)(\s?-\s?\d+)+$/, mensaje: 'Formato válido: "número - número - ..."' },

        precio_compra: { min: 1, mensaje: "Precio de compra mínimo 1.00" }, // <--- AGREGAR ESTA LÍNEA

        precio: { min: 1, mensaje: "Precio mínimo 1" },
        cantidad: { min: 0, mensaje: "Stock no puede ser negativo" },
        minimo: { min: 1, mensaje: "Stock mínimo ≥ 1" },

        id_sucursal: { min: 1, mensaje: "Debe seleccionar una sucursal" }
    };

    // --- VALIDACIÓN GENÉRICA ---
    const validar = (campo, regla) => {

        // si está disabled por alternancia, no valida nada
        if (campo.disabled) {
            campo.classList.remove("is-invalid", "is-valid");
            return true;
        }

        const valor = campo.value.trim();

        const valido =
            (regla.regex ? regla.regex.test(valor) : true) &&
            (regla.min != null ? parseFloat(valor) >= regla.min : true);

        // VALIDACIÓN DE PRECIOS CRUZADA
        if (campo.id === 'precio' || campo.id === 'precio_compra') {
            const pCompra = document.getElementById('precio_compra');
            const pVenta = document.getElementById('precio');
            if (pCompra && pVenta) {
                const valCompra = parseFloat(pCompra.value);
                const valVenta = parseFloat(pVenta.value);
                
                if (!isNaN(valCompra) && !isNaN(valVenta)) {
                    if (valVenta < valCompra) {
                        if (campo.id === 'precio') {
                             // Si estamos validando precio venta y es menor, es inválido
                             campo.classList.add("is-invalid");
                             campo.classList.remove("is-valid");
                             // Actualizar mensaje
                             let feedback = campo.parentElement.querySelector(".invalid-tooltip");
                             if(feedback) feedback.textContent = "El precio de venta no puede ser menor al de compra.";
                             return false;
                        }
                    } else {
                        // Si la relación es correcta, nos aseguramos de limpiar el error del OTRO campo si estaba marcado por esto
                        // PERO cuidado de no limpiar errores de formato/minimo propios.
                        // Simplificación: al corregir uno, revalidamos el otro si tenía error.
                        if(campo.id === 'precio_compra' && pVenta.classList.contains('is-invalid')){
                             // Disparar evento input en pVenta para revalidarlo
                             // pVenta.dispatchEvent(new Event('input')); 
                             // Mejor no causar recursión infinita, simplemente asumimos que el usuario lo arreglará o el submit lo atrapará.
                        }
                    }
                }
            }
        }

        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);

        // Restaurar mensaje original si es válido o si el error no fue de precio cruzado (manejado arriba)
        let feedback = campo.parentElement.querySelector(".invalid-tooltip");
        if(feedback && !valido && regla.mensaje) {
             // Solo restaurar si no es el error de precio cruzado (que ya seteamos)
             // Como es difícil saber cual fue, lo restauramos siempre que validemos "normal"
             // Arriba retornamos false si era precio cruzado, así que aquí es error normal.
             feedback.textContent = regla.mensaje;
        }


        return valido;
    };

    // --- VALIDACIÓN EN TIEMPO REAL ---
    Object.keys(reglas).forEach(id => {
        const campo = document.getElementById(id);
        if (!campo) return; // si no existe (por alternancia), lo ignora
        campo.addEventListener("input", () => validar(campo, reglas[id]));
    });

    // Listeners extra para precios
    const pCompra = document.getElementById('precio_compra');
    const pVenta  = document.getElementById('precio');
    if(pCompra && pVenta){
        pCompra.addEventListener('input', () => { 
             if(pVenta.value !== '') validar(pVenta, reglas['precio']); // Re-validar venta al cambiar compra
        });
    }


    // --- VALIDACIÓN FINAL EN SUBMIT ---
    form.addEventListener("submit", e => {
        const resultado = Object.keys(reglas).map(id => {
            const campo = document.getElementById(id);
            if (!campo) return true; // si no existe, ignorar
            return validar(campo, reglas[id]);
        });
        
        // Check final explícito precios
        if(pCompra && pVenta){
             const vc = parseFloat(pCompra.value);
             const vv = parseFloat(pVenta.value);
             if(!isNaN(vc) && !isNaN(vv) && vv < vc){
                 validar(pVenta, reglas['precio']); // Esto marcará error
                 resultado.push(false);
             }
        }

        const todoValido = resultado.every(v => v);
        
        if (!todoValido) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});

btnReset.addEventListener("click", () =>{
    window.location.reload();
});