import { api } from '/DEV/PHP/QuickStock/src/api/client/index.js';

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-crear-moneda");

    if (form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            
            // Recoger datos
            const formData = new FormData(form);
            const data = {
                accion: "crear_moneda",
                nombre: formData.get("nombre"),
                codigo: formData.get("codigo"),
                simbolo: formData.get("simbolo"),
                activo: formData.get("activo")
            };

            // Validacion simple
            if(!data.nombre || !data.codigo || !data.simbolo) {
                alert("Todos los campos son obligatorios.");
                return;
            }

            // Enviar
            api(data)
                .then(res => {
                    if (res.error) {
                        alert("Error: " + res.error);
                    } else {
                        alert("Moneda guardada exitosamente.");
                        window.location.href = "monedas-listado";
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Error al procesar la solicitud.");
                });
        });
    }
});
