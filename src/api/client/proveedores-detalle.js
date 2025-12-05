import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idProveedor = document.getElementById("id_proveedor").value;

    if (!idProveedor) {
        console.error("No se recibió el ID del proveedor");
        return;
    }

    api({ accion: "obtener_proveedor_por_id", id_proveedor: idProveedor })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener proveedor:", res.error);
                alert("No se pudo cargar la información del proveedor");
                return;
            }

            const prov = res.proveedor;

            document.getElementById("detalle_id").textContent = prov.id_proveedor ?? "";
            document.getElementById("detalle_nombre").textContent = prov.nombre ?? "";
            document.getElementById("detalle_telefono").textContent = prov.telefono ?? "No especificado";
            document.getElementById("detalle_correo").textContent = prov.correo ?? "No especificado";
            document.getElementById("detalle_direccion").textContent = prov.direccion ?? "No especificada";

            const activoBool = prov.activo === true || prov.activo === "t" || prov.activo === "true" || prov.activo === 1 || prov.activo === "1";
            const badgeEstado = document.getElementById("detalle_estado");
            badgeEstado.textContent = activoBool ? "Activo" : "Inactivo";
            badgeEstado.className = `badge ${activoBool ? "bg-success" : "bg-danger"}`;

            document.getElementById("breadcrumb_nombre").textContent = prov.nombre ?? "Proveedor";
        })
        .catch(err => {
            console.error("Error cargando proveedor:", err);
            alert("Error al cargar los datos del proveedor");
        });
});
