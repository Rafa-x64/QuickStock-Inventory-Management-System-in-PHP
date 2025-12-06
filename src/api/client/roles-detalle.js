import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idRol = document.getElementById("id_rol").value;

    if (!idRol) {
        console.error("No se recibió el ID del rol");
        return;
    }

    api({ accion: "obtener_rol_por_id", id_rol: idRol })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener rol:", res.error);
                alert("No se pudo cargar la información del rol");
                return;
            }

            const rol = res.rol;

            document.getElementById("detalle_id").textContent = rol.id_rol ?? "";
            document.getElementById("detalle_nombre").textContent = rol.nombre_rol ?? "";
            document.getElementById("detalle_descripcion").textContent = rol.descripcion ?? "Sin descripción";
            document.getElementById("detalle_usuarios").textContent = rol.usuarios_asignados ?? "0";

            const activoBool = rol.activo === true || rol.activo === "t" || rol.activo === "true" || rol.activo === 1 || rol.activo === "1";
            const badgeEstado = document.getElementById("detalle_estado");
            badgeEstado.textContent = activoBool ? "Activo" : "Inactivo";
            badgeEstado.className = `badge ${activoBool ? "bg-success" : "bg-danger"}`;

            document.getElementById("breadcrumb_nombre").textContent = rol.nombre_rol ?? "Rol";
        })
        .catch(err => {
            console.error("Error cargando rol:", err);
            alert("Error al cargar los datos del rol");
        });
});
