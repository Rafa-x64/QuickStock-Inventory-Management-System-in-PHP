document.addEventListener("DOMContentLoaded", () => {
    
    const reglas = {
        rango_talla_añadir: {
             // Acepta letras, numeros, guiones, puntos, comas, espacios. min 1 max 20
             regex: /^[A-Za-z0-9\-\.\,\s]{1,20}$/,
             mensaje: "Mín 1, Máx 20 caracteres (Letras, números, -, . ,)"
        },
        rango_talla_editar: {
             regex: /^[A-Za-z0-9\-\.\,\s]{1,20}$/,
             mensaje: "Mín 1, Máx 20 caracteres (Letras, números, -, . ,)"
        }
    };

    const getValue = (campo) => campo.value.trim();

    const validar = (campo, regla) => {
        if (!campo) return true;
        const valor = getValue(campo);
        let valido = true;

        if (regla && regla.regex) {
            valido = regla.regex.test(valor);
        }

        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);

        const container = campo.closest("div") || campo.parentElement;
        const feedback = container.querySelector(".invalid-tooltip");
        if (feedback && regla) {
            feedback.textContent = regla.mensaje;
        }

        return valido;
    };

    const inputRangoAñadir = document.getElementById("rango_talla_añadir");
    if (inputRangoAñadir) {
        inputRangoAñadir.addEventListener("input", () => validar(inputRangoAñadir, reglas.rango_talla_añadir));
    }

    const inputRangoEditar = document.getElementById("rango_talla_editar");
    if (inputRangoEditar) {
        inputRangoEditar.addEventListener("input", () => validar(inputRangoEditar, reglas.rango_talla_editar));
    }

    const formAñadir = document.getElementById("form_añadir_talla");
    if (formAñadir) {
        formAñadir.addEventListener("submit", (e) => {
            const vRango = validar(inputRangoAñadir, reglas.rango_talla_añadir);
            if (!vRango) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    const formEditar = document.getElementById("form_editar_talla");
    if (formEditar) {
        formEditar.addEventListener("submit", (e) => {
             const vRango = validar(inputRangoEditar, reglas.rango_talla_editar);
             if (!vRango) {
                e.preventDefault();
                e.stopPropagation();
             }
        });
    }

});
