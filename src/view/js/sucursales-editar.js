/**
 * sucursales-validacion-editar.js
 * Lógica de validación del formulario de edición de sucursal.
 * Diseñado para ser cargado con un <script> tradicional (no-module).
 */

// --- CONSTANTES Y UTILIDADES ---

// IDs de los campos en el formulario de edición
const CAMPO_NOMBRE = "nombre_sucursal_editar";
const CAMPO_RIF = "rif_sucursal_editar";
const CAMPO_DIRECCION = "direccion_sucursal_editar";
const CAMPO_TELEFONO = "telefono_sucursal_editar";
const CAMPO_FECHA = "fecha_registro_editar";

// Obtener el formulario de edición
const form = document.getElementById("form_editar_sucursal");
const getValue = (campo) => campo.value.trim();

// 1. Definición de Reglas de Validación
const reglas = {
    [CAMPO_NOMBRE]: {
        regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s\-\.\,\(\)]{3,}$/,
        mensaje: "Nombre: solo letras, números, espacios y signos básicos (mínimo 3)"
    },
    [CAMPO_RIF]: {
        regex: /^[JVEG]-\d{8}-\d{1}$/i,
        mensaje: "RIF inválido. Formato correcto: J-12345678-9 (J, V, E, G)"
    },
    [CAMPO_TELEFONO]: {
        regex: /^(\+58\s?)?(0?4(12|14|16|24|26|17|27))(\s?-?\d{3})(\s?-?\d{2}){2}$/,
        mensaje: "Teléfono inválido. Debe empezar con 04 o +58 4. Ej: 0412-1234567"
    }
};

// --- LÓGICA DE VALIDACIÓN ---

/**
 * Función de Validación Principal.
 * Aplica las reglas y las clases de Bootstrap.
 * @param {HTMLElement} campo El elemento input a validar.
 * @param {Object} regla Las reglas de validación asociadas.
 * @returns {boolean} True si el campo es válido, false en caso contrario.
 */
function validar(campo, regla) {
    // La fecha de registro debe estar deshabilitada, por lo tanto, es válida.
    if (campo.disabled || !campo.required) {
        campo.classList.remove("is-invalid", "is-valid");
        return true;
    }

    const valor = getValue(campo);
    let valido = true;

    const esDireccion = campo.id === CAMPO_DIRECCION;

    // 3.1. Validación de Campo No Vacío (Excepto Dirección, si es opcional)
    if (valor.length === 0 && !esDireccion) {
        valido = false;
    }

    // Si el campo es Dirección y está vacío, es válido
    if (valor.length === 0 && esDireccion) {
        valido = true;
    } else {
        // 3.2. Validación por Expresión Regular
        if (regla && regla.regex) {
            valido = valido && regla.regex.test(valor);
        }
    }

    // 3.4. Aplicar clases de Bootstrap y mensaje de Tooltip
    campo.classList.toggle("is-invalid", !valido);
    campo.classList.toggle("is-valid", valido);

    const feedback = campo.parentElement.querySelector(".invalid-tooltip");

    if (feedback && !valido && regla) {
        feedback.textContent = regla.mensaje || "Campo Inválido";
    } else if (feedback) {
        feedback.textContent = "";
    }

    return valido;
}


/**
 * Inicializa todos los listeners de input y el listener de submit.
 */
function inicializarValidacionListeners() {
    // Lista de campos que necesitan validación de entrada
    const camposConListeners = [CAMPO_NOMBRE, CAMPO_RIF, CAMPO_TELEFONO, CAMPO_DIRECCION];

    // Asignar listeners a los campos para validación en tiempo real
    camposConListeners.forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            const regla = reglas[id] || null;
            campo.addEventListener("input", () => validar(campo, regla));
        }
    });

    // Asignar listener para el Envío del Formulario
    if (form) {
        form.addEventListener("submit", e => {
            const camposAValidar = [
                document.getElementById(CAMPO_NOMBRE),
                document.getElementById(CAMPO_RIF),
                document.getElementById(CAMPO_TELEFONO),
                document.getElementById(CAMPO_DIRECCION)
            ].filter(campo => campo !== null);

            // Mapear y ejecutar todas las validaciones
            const validaciones = camposAValidar.map(campo => {
                const regla = reglas[campo.id] || null;
                return validar(campo, regla);
            });

            const todoValido = validaciones.every(v => v);

            if (!todoValido) {
                e.preventDefault(); // Detener el envío si no es válido
                const primerInvalido = document.querySelector(".is-invalid");
                if (primerInvalido) {
                    primerInvalido.focus();
                    primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
}

// 🚀 PUNTO DE ENTRADA: Ejecutar al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    inicializarValidacionListeners();
    const reestablecerBtn = document.getElementById("reestablecerBtn");
    reestablecerBtn.addEventListener("click", ()=>{
        window.location.reload();
    });
});