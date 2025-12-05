document.addEventListener("DOMContentLoaded", () => {

    const reglas = {
        nombre_rol: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ0-9\s\-]{2,80}$/,
            msg: "Mínimo 2 caracteres. Solo letras, números, espacios y guiones."
        },
        descripcion_rol: {
            regex: /^.{5,255}$/,
            msg: "Debe contener entre 5 y 255 caracteres."
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

    const camposOpcionales = ["descripcion_rol"];

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
