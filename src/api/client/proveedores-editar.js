import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idProveedor = document.getElementById("id_proveedor").value;

    if (!idProveedor) {
        console.error("No se recibió el ID del proveedor");
        return;
    }

    const nombreInput = document.getElementById("nombre_proveedor");
    const telefonoInput = document.getElementById("telefono_proveedor");
    const correoInput = document.getElementById("correo_proveedor");
    const direccionInput = document.getElementById("direccion_proveedor");
    const estadoSelect = document.getElementById("estado_proveedor");

    api({ accion: "obtener_proveedor_por_id", id_proveedor: idProveedor })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener proveedor:", res.error);
                alert("No se pudo cargar la información del proveedor");
                return;
            }

            const prov = res.proveedor;

            nombreInput.value = prov.nombre ?? "";
            telefonoInput.value = prov.telefono ?? "";
            correoInput.value = prov.correo ?? "";
            direccionInput.value = prov.direccion ?? "";

            const activoBool = prov.activo === true || prov.activo === "t" || prov.activo === "true" || prov.activo === 1 || prov.activo === "1";
            estadoSelect.value = activoBool ? "true" : "false";
        })
        .catch(err => {
            console.error("Error cargando proveedor:", err);
            alert("Error al cargar los datos del proveedor");
        });
});
