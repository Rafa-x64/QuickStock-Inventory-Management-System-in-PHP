import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idMetodo = document.getElementById("id_metodo_pago").value;
    const nombreInput = document.getElementById("nombre_metodo");
    const descripcionInput = document.getElementById("descripcion_metodo");
    const referenciaCheckbox = document.getElementById("referencia_metodo");
    const activoCheckbox = document.getElementById("activo_metodo");

    if (!idMetodo) {
        console.error("ID no proporcionado para edición");
        return;
    }

    api({ accion: "obtener_metodo_pago_detalle", id_metodo_pago: idMetodo })
        .then(res => {
            if (res.error) {
                alert("Error al cargar datos: " + res.error);
                return;
            }

            const metodo = res.data;
            if (metodo) {
                nombreInput.value = metodo.nombre;
                descripcionInput.value = metodo.descripcion || "";
                referenciaCheckbox.checked = (metodo.referencia === 't' || metodo.referencia === true);
                activoCheckbox.checked = (metodo.activo === 't' || metodo.activo === true);
            }
        })
        .catch(err => {
            console.error("Error API cargar edición:", err);
        });
});
