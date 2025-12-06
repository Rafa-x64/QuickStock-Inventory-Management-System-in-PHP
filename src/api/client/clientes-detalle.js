import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idCliente = document.getElementById("id_cliente").value;

    if (!idCliente) {
        console.error("No se recibió el ID del cliente");
        return;
    }

    api({ accion: "obtener_cliente_por_id", id_cliente: idCliente })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener cliente:", res.error);
                alert("No se pudo cargar la información del cliente");
                return;
            }

            const cli = res.cliente;

            document.getElementById("detalle_id").textContent = cli.id_cliente ?? "";
            document.getElementById("detalle_nombre").textContent = cli.nombre ?? "";
            document.getElementById("detalle_apellido").textContent = cli.apellido ?? "No especificado";
            document.getElementById("detalle_cedula").textContent = cli.cedula ?? "No especificada";
            document.getElementById("detalle_telefono").textContent = cli.telefono ?? "No especificado";
            document.getElementById("detalle_correo").textContent = cli.correo ?? "No especificado";
            document.getElementById("detalle_direccion").textContent = cli.direccion ?? "No especificada";

            const activoBool = cli.activo === true || cli.activo === "t" || cli.activo === "true" || cli.activo === 1 || cli.activo === "1";
            const badgeEstado = document.getElementById("detalle_estado");
            badgeEstado.textContent = activoBool ? "Activo" : "Inactivo";
            badgeEstado.className = `badge ${activoBool ? "bg-success" : "bg-danger"}`;

            document.getElementById("breadcrumb_nombre").textContent = cli.nombre ?? "Cliente";
        })
        .catch(err => {
            console.error("Error cargando cliente:", err);
            alert("Error al cargar los datos del cliente");
        });
});
