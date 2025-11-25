document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formMetodoPago");
    const getValue = (campo) => campo.value.trim();

    const reglas = {
        nombre_metodo: {
            regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s\-\.]{3,50}$/,
            mensaje: "Nombre: solo letras, números y espacios (mínimo 3 caracteres)"
        },
        descripcion_metodo: {
            regex: /^[\s\S]{0,255}$/, // Opcional, max 255
            mensaje: "Descripción: máximo 255 caracteres"
        }
    };

    const validar = (campo, regla) => {
        const valor = getValue(campo);
        let valido = true;

        if (valor.length === 0 && campo.required) {
            valido = false;
        } else if (valor.length > 0 && regla && regla.regex) {
            valido = regla.regex.test(valor);
        }

        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);

        const feedback = campo.parentElement.querySelector(".invalid-tooltip");
        if (feedback && !valido && regla) {
            feedback.textContent = regla.mensaje || "Campo inválido";
        }

        return valido;
    };

    Object.keys(reglas).forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            campo.addEventListener("input", () => validar(campo, reglas[id]));
        }
    });

    form.addEventListener("submit", e => {
        let todoValido = true;
        
        Object.keys(reglas).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                if (!validar(campo, reglas[id])) {
                    todoValido = false;
                }
            }
        });

        if (!todoValido) {
            e.preventDefault();
            const primerInvalido = document.querySelector(".is-invalid");
            if (primerInvalido) {
                primerInvalido.focus();
            }
        }
    });
});
