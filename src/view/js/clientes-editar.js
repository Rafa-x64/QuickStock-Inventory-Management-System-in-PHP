document.addEventListener("DOMContentLoaded", () => {

    const reglas = {
        nombre_cliente: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,100}$/,
            msg: "Solo letras. Mínimo 2 caracteres."
        },
        apellido_cliente: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,100}$/,
            msg: "Solo letras. Mínimo 2 caracteres."
        },
        cedula_cliente: {
            regex: /^[VvEe]-\d{1,3}(\.\d{3}){2}$/,
            msg: "Formato válido: V-12.345.678 o E-12.345.678"
        },
        telefono_cliente: {
            regex: /^(0(412|414|424|416|426|417|427)-\d{3}-\d{2}-\d{2}|\+58\s(412|414|424|416|426|417|427)-\d{3}-\d{2}-\d{2})$/,
            msg: "Formato: 0412-555-12-12 o +58 412-555-12-12"
        },
        correo_cliente: {
            regex: /^[^@\s]+@[^@\s]+\.[a-zA-Z]{2,}$/,
            msg: "Debe ser un correo válido (ej: usuario@dominio.com)"
        },
        direccion_cliente: {
            regex: /^.{10,255}$/,
            msg: "Debe contener mínimo 10 caracteres."
        }
    };

    const validarCampo = (input, regla, esOpcional = false) => {
        const tooltip = input.nextElementSibling;
        if (!input) return true;

        const valor = input.value.trim();

        if (valor === "" && esOpcional) {
            input.classList.remove("is-invalid");
            input.classList.remove("is-valid");
            if (tooltip) tooltip.textContent = "";
            return true;
        }

        if (valor === "" && !esOpcional) {
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            if (tooltip) tooltip.textContent = "Este campo no puede estar vacío.";
            return false;
        }

        if (!regla.regex.test(valor)) {
            input.classList.add("is-invalid");
            input.classList.remove("is-valid");
            if (tooltip) tooltip.textContent = regla.msg;
            return false;
        }

        input.classList.remove("is-invalid");
        input.classList.add("is-valid");
        if (tooltip) tooltip.textContent = "";
        return true;
    };

    const form = document.querySelector("form");

    const camposOpcionales = ["apellido_cliente", "cedula_cliente", "telefono_cliente", "correo_cliente", "direccion_cliente"];

    form.addEventListener("submit", e => {
        let valido = true;

        for (let id in reglas) {
            const input = document.getElementById(id);
            const esOpcional = camposOpcionales.includes(id);
            if (!validarCampo(input, reglas[id], esOpcional)) {
                valido = false;
            }
        }

        if (!valido) {
            e.preventDefault();
            form.classList.add("was-validated");
            const primerInvalido = document.querySelector(".is-invalid");
            if (primerInvalido) {
                primerInvalido.focus();
                primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    for (let id in reglas) {
        const input = document.getElementById(id);
        if (!input) continue;

        const regla = reglas[id];
        const esOpcional = camposOpcionales.includes(id);

        input.addEventListener("input", () => validarCampo(input, regla, esOpcional));
    }

});
