document.addEventListener("DOMContentLoaded", () => {

    const reglas = {
        nombre_proveedor: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ0-9\s\.\,\-\(\)]{2,150}$/,
            msg: "Mínimo 2 caracteres. Letras, números y signos básicos."
        },
        telefono_proveedor: {
            regex: /^(\+58\s?)?(0?4(12|14|16|24|26|17|27))(\s?-?\d{3})(\s?-?\d{2}){2}$/,
            msg: "Formato: +58 412-555-12-12 o 0412-555-12-12"
        },
        correo_proveedor: {
            regex: /^[^@\s]+@[^@\s]+\.[a-zA-Z]{2,}$/,
            msg: "Debe ser un correo válido (ej: proveedor@empresa.com)"
        },
        direccion_proveedor: {
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

    const camposOpcionales = ["telefono_proveedor", "correo_proveedor", "direccion_proveedor"];

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
