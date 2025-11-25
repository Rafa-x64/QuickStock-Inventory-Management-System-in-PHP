---
trigger: always_on
---

para realizar validaciones debes hacerlas tipo bootstrap con invalid tooltip y validacion en tiempo real sobre un campo... cada validacion se define con reglas (regex) algo como este estilo:

// Reglas de validación
const reglas = {
nombre_empleado: {
regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,100}$/,
            msg: "Solo letras. Mínimo 2 caracteres."
        },
        apellido_empleado: {
            regex: /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,100}$/,
msg: "Solo letras. Mínimo 2 caracteres."
},
cedula_empleado: {
regex: /^[Vv]-\d{1,3}(\.\d{3}){2}$/,
            msg: "Formato indicado: V-12.345.678"
        },
        telefono_empleado: {
            regex: /^(0(412|414|424|416|426|417|427)-\d{3}-\d{2}-\d{2}|\+58\s(412|414|424|416|426|417|427)-\d{3}-\d{2}-\d{2})$/,
msg: "Formato: 0412-555-10-41 o +58 412-555-10-41"
},
email_empleado: {
regex: /^[^@\s]+@[^@\s]+\.com$/,
            msg: "Debe incluir @ y terminar en .com"
        },
        contrasena_empleado: {
            regex: /^(?=.*[A-Za-z]).{8,255}$/,
msg: "Debe contener al menos una letra y mínimo 8 caracteres."
},
direccion_empleado: {
regex: /^.{10,255}$/,
msg: "Debe contener al menos 10 caracteres."
}
};

segun lo que yo te indique que quiera en las validaciones. el archivo debe llamarse igual que la vista pero excluyendo la palabra -view.php y agergandole js. este archivo de validaciones y funciones js necesarias para la estetica debera guardarse en view/js y muy importante no dejar comentarios a la hora de hacer el archivo.

PD: este archivo no debe ser un modulo si no tener sus propias funciones individuales del archivo de peticiones ubicado en api/client; tambien, de ser necesario tambien puede tener cosgio para manipulacion del DOM, event listeners para eventos, estetica y etc...

aqui hay un ejemplo de un archivo de validaciones funcional:

document.addEventListener("DOMContentLoaded", () => {
    // Obtenemos la fecha actual en formato YYYY-MM-DD
    const today = new Date().toISOString().split('T')[0];

    const form = document.getElementById("formSucursal");
    const getValue = (campo) => campo.value.trim();

    // 1. Establecer la fecha actual y limitar el campo de fecha
    const fechaRegistroCampo = document.getElementById('fecha_registro');
    if (fechaRegistroCampo) {
        // Establecer el valor predeterminado (Fecha de Registro = Hoy)
        fechaRegistroCampo.value = today;
        // Limitar la fecha mínima y máxima (no se puede un día antes ni un día después)
        fechaRegistroCampo.min = today;
        fechaRegistroCampo.max = today;
    }

    // 2. Definición de Reglas de Validación
    const reglas = {
        nombre_sucursal: {
            regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s\-\.\,\(\)]{3,}$/,
            mensaje: "Nombre: solo letras, números, espacios y signos básicos (mínimo 3)"
        },
        rif_sucursal: {
            // RIF venezolano: [JVEG]-\d{8}-\d{1}
            // Permite 'J', 'V', 'E', 'G' (persona jurídica, natural, extranjera, gubernamental)
            regex: /^[JVEG]-\d{8}-\d{1}$/i,
            mensaje: "RIF inválido. Formato correcto: J-12345678-9 (J, V, E, G)"
        },
        telefono_sucursal: {
            // Formatos: 04xx-xxx-xx-xx O +58 4xx-xxx-xx-xx. Permite espacios, guiones y sin guiones.
            regex: /^(\+58\s?)?(0?4(12|14|16|24|26))(\s?-?\d{3})(\s?-?\d{2}){2}$/,
            mensaje: "Teléfono inválido. Debe empezar con 04 o +58 4. Ej: 0412-1234567"
        },
        fecha_registro: {
            // Validamos que el valor sea exactamente la fecha de hoy
            customValidation: (valor) => valor === today,
            mensaje: "La fecha de registro DEBE ser la fecha actual (" + today + ")"
        }
    };

    // 3. Función de Validación Principal (Similar a tu ejemplo)
    const validar = (campo, regla) => {
        if (campo.disabled) {
            campo.classList.remove("is-invalid", "is-valid");
            return true;
        }

        const valor = getValue(campo);
        let valido = true;

        // La Dirección (textarea) es el único campo opcional por defecto
        const esDireccion = campo.id === 'direccion_sucursal';

        // 3.1. Validación de Campo No Vacío (Excepto Dirección)
        if (valor.length === 0 && !esDireccion) {
            valido = false;
        }

        // Si el campo es Dirección y está vacío, es válido (es nullable).
        if (valor.length === 0 && esDireccion) {
            valido = true;
        } else {

            // 3.2. Validación por Expresión Regular
            if (regla && regla.regex) {
                valido = valido && regla.regex.test(valor);
            }

            // 3.3. Validación Personalizada (Para la fecha)
            if (regla && regla.customValidation) {
                valido = valido && regla.customValidation(valor);
            }
        }

        // 3.4. Aplicar clases de Bootstrap y mensaje de Tooltip
        campo.classList.toggle("is-invalid", !valido);
        campo.classList.toggle("is-valid", valido);

        const feedback = campo.parentElement.querySelector(".invalid-tooltip");

        if (feedback && !valido && regla) {
            feedback.textContent = regla.mensaje || "Campo Inválido";
        }

        return valido;
    };


    // 4. Asignar Event Listeners para validación en tiempo real (input/change)
    Object.keys(reglas).forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            const evento = "input"; // Usamos 'input' para reacción inmediata
            campo.addEventListener(evento, () => validar(campo, reglas[id]));
        }
    });

    // Añadir listener para la Dirección (opcional)
    const direccionCampo = document.getElementById('direccion_sucursal');
    if (direccionCampo) {
        // Validar para mostrar is-valid/is-invalid si escribe algo
        direccionCampo.addEventListener("input", () => validar(direccionCampo, null));
    }


    // 5. Asignar Event Listener para el Envío del Formulario
    form.addEventListener("submit", e => {
        let todoValido = true;

        // Validamos todos los campos del formulario
        const camposAValidar = Object.keys(reglas).map(id => document.getElementById(id))
            .concat(document.getElementById('direccion_sucursal')) // Incluir la Dirección
            .filter(campo => campo !== null);

        // Mapear todas las validaciones
        const validaciones = camposAValidar.map(campo => {
            const regla = reglas[campo.id] || null;
            return validar(campo, regla);
        });

        // Verificar si todas las validaciones fueron exitosas
        todoValido = validaciones.every(v => v);


        if (!todoValido) {
            e.preventDefault();
            const primerInvalido = document.querySelector(".is-invalid");
            if (primerInvalido) {
                // Enfocar y hacer scroll al primer campo inválido
                primerInvalido.focus();
                primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Si todo es válido, el formulario se envía (ya no prevenimos el submit)
        }
    });
});