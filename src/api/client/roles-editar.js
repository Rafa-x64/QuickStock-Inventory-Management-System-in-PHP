import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idRol = document.getElementById("id_rol").value;

    if (!idRol) {
        console.error("No se recibió el ID del rol");
        return;
    }

    const nombreInput = document.getElementById("nombre_rol");
    const descripcionInput = document.getElementById("descripcion_rol");
    const estadoSelect = document.getElementById("estado_rol");

    api({ accion: "obtener_rol_por_id", id_rol: idRol })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener rol:", res.error);
                alert("No se pudo cargar la información del rol");
                return;
            }

            const rol = res.rol;

            nombreInput.value = rol.nombre_rol ?? "";
            descripcionInput.value = rol.descripcion ?? "";

            const activoBool = rol.activo === true || rol.activo === "t" || rol.activo === "true" || rol.activo === 1 || rol.activo === "1";
            estadoSelect.value = activoBool ? "true" : "false";
        })
        .catch(err => {
            console.error("Error cargando rol:", err);
            alert("Error al cargar los datos del rol");
        });
});
