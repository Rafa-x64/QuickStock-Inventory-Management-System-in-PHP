document.addEventListener("DOMContentLoaded", () => {
    
    // ==========================================
    // VALIDACIONES DE PERFIL
    // ==========================================
    const reglasPerfil = {
        nombre: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,50}$/,
            msg: "Solo letras y espacios. Mínimo 2 caracteres."
        },
        apellido: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,50}$/,
            msg: "Solo letras y espacios. Mínimo 2 caracteres."
        },
        telefono: {
            regex: /^[0-9\-\+\s]{10,20}$/,
            msg: "Formato inválido. Ej: 0412-1234567"
        },
        email: {
            regex: /^[^@\s]+@[^@\s]+\.[^@\s]+$/,
            msg: "Correo electrónico inválido."
        },
        direccion: {
            regex: /^.{10,255}$/,
            msg: "Mínimo 10 caracteres."
        }
    };

    const formPerfil = document.getElementById("formPerfil");
    if (formPerfil) {
        setupValidation(formPerfil, reglasPerfil);
    }

    // ==========================================
    // VALIDACIONES DE CONTRASEÑA
    // ==========================================
    const reglasPass = {
        pass_actual: {
            regex: /^.+$/,
            msg: "Campo obligatorio."
        },
        pass_nueva: {
            regex: /^.{6,}$/, // Mínimo 6 caracteres, ajustar según política
            msg: "Mínimo 6 caracteres."
        },
        pass_confirm: {
            custom: (val) => {
                const passNueva = document.getElementById("pass_nueva").value;
                return val === passNueva;
            },
            msg: "Las contraseñas no coinciden."
        }
    };

    const formPass = document.getElementById("formPassword");
    if (formPass) {
        setupValidation(formPass, reglasPass);
    }

    // ==========================================
    // FUNCIÓN GENÉRICA DE VALIDACIÓN
    // ==========================================
    function setupValidation(form, reglas) {
        const inputs = form.querySelectorAll("input, textarea");

        inputs.forEach(input => {
            if (reglas[input.id]) {
                input.addEventListener("input", () => validarCampo(input, reglas[input.id]));
            }
        });

        form.addEventListener("submit", (e) => {
            let valido = true;
            inputs.forEach(input => {
                if (reglas[input.id]) {
                    if (!validarCampo(input, reglas[input.id])) {
                        valido = false;
                    }
                }
            });

            if (!valido) {
                e.preventDefault();
                const primerInvalido = form.querySelector(".is-invalid");
                if (primerInvalido) primerInvalido.focus();
            }
        });
    }

    function validarCampo(input, regla) {
        const valor = input.value.trim();
        let esValido = true;

        if (regla.regex && !regla.regex.test(valor)) {
            esValido = false;
        }

        if (regla.custom && !regla.custom(valor)) {
            esValido = false;
        }

        if (esValido) {
            input.classList.remove("is-invalid");
            input.classList.add("is-valid");
        } else {
            input.classList.remove("is-valid");
            input.classList.add("is-invalid");
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains("invalid-tooltip")) {
                feedback.textContent = regla.msg;
            }
        }

        return esValido;
    }

    // ==========================================
    // TOGGLE PASSWORD VISIBILITY
    // ==========================================
    document.querySelectorAll(".toggle-password").forEach(btn => {
        btn.addEventListener("click", () => {
            const input = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector("i");
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        });
    });

});
