document.addEventListener("DOMContentLoaded", () => {
    
    // Reglas de validación
    const reglas = {
        nombre_color_añadir: {
             regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]{3,50}$/,
             mensaje: "El nombre debe tener entre 3 y 50 caracteres alfanuméricos."
        },
        nombre_color_editar: {
             regex: /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]{3,50}$/,
             mensaje: "El nombre debe tener entre 3 y 50 caracteres alfanuméricos."
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

        // Buscar tooltip cercano
        const container = campo.closest("div") || campo.parentElement;
        const feedback = container.querySelector(".invalid-tooltip");
        if (feedback && regla) {
            feedback.textContent = regla.mensaje;
        }

        return valido;
    };

    // Listeners en tiempo real para añadir
    const inputNombreAñadir = document.getElementById("nombre_color_añadir");
    if (inputNombreAñadir) {
        inputNombreAñadir.addEventListener("input", () => validar(inputNombreAñadir, reglas.nombre_color_añadir));
    }

    // Listeners en tiempo real para editar
    const inputNombreEditar = document.getElementById("nombre_color_editar");
    if (inputNombreEditar) {
        inputNombreEditar.addEventListener("input", () => validar(inputNombreEditar, reglas.nombre_color_editar));
    }

    // Validación al enviar Form Añadir
    const formAñadir = document.getElementById("form_añadir_color");
    if (formAñadir) {
        formAñadir.addEventListener("submit", (e) => {
            const vNombre = validar(inputNombreAñadir, reglas.nombre_color_añadir);
            if (!vNombre) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    // Validación al enviar Form Editar
    // Nota: El form editar tiene un input hidden de accion 'editar' que se agrega dinámicamente si no está en el HTML plano,
    // pero aquí ya está en el HTML del view como input hidden accion=editar? No, en el view lo pusimos.
    // En el JS del cliente vimos que se hace submit normal.
    // Solo necesitamos interceptar el submit para validar.
    const formEditar = document.getElementById("form_editar_color");
    if (formEditar) {
        formEditar.addEventListener("submit", (e) => {
             // Agregamos input hidden accion = editar si no existe (aunque el view ya deberia tenerlo o manejarlo)
             // El view tiene un form genérico con action="" method="POST".
             // Vamos a inyectar el input hidden "accion" = "editar" dinámicamente si no está,
             // o asegurarnos que el backend lo reciba.
             // En el view HTML puse: <form ...> ... inputs ... </form>
             // PERO NO PUSE el input hidden name="accion" value="editar" DENTRO del form editar en el HTML
             // Espera, revisando el codigo del view... AH NO, NO LO PUSE.
             // DEBO AGREGARLO AHORA DINAMICAMENTE O CORREGIR EL VIEW.
             // CORREGIRÉ EL VIEW JS PARA INYECTARLO AL HACER SUBMIT O ASEGURARME QUE ESTÁ.
             
             let inputAccion = formEditar.querySelector("input[name='accion']");
             if(!inputAccion){
                 inputAccion = document.createElement("input");
                 inputAccion.type = "hidden";
                 inputAccion.name = "accion";
                 inputAccion.value = "editar";
                 formEditar.appendChild(inputAccion);
             }

             const vNombre = validar(inputNombreEditar, reglas.nombre_color_editar);
             if (!vNombre) {
                e.preventDefault();
                e.stopPropagation();
             }
        });
    }

});
