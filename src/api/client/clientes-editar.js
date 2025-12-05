import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idCliente = document.getElementById("id_cliente").value;

    if (!idCliente) {
        console.error("No se recibió el ID del cliente");
        return;
    }

    const nombreInput = document.getElementById("nombre_cliente");
    const apellidoInput = document.getElementById("apellido_cliente");
    const cedulaInput = document.getElementById("cedula_cliente");
    const telefonoInput = document.getElementById("telefono_cliente");
    const correoInput = document.getElementById("correo_cliente");
    const direccionInput = document.getElementById("direccion_cliente");
    const estadoSelect = document.getElementById("estado_cliente");

    api({ accion: "obtener_cliente_por_id", id_cliente: idCliente })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener cliente:", res.error);
                alert("No se pudo cargar la información del cliente");
                return;
            }

            const cli = res.cliente;

            nombreInput.value = cli.nombre ?? "";
            apellidoInput.value = cli.apellido ?? "";
            cedulaInput.value = cli.cedula ?? "";
            telefonoInput.value = cli.telefono ?? "";
            correoInput.value = cli.correo ?? "";
            direccionInput.value = cli.direccion ?? "";

            const activoBool = cli.activo === true || cli.activo === "t" || cli.activo === "true" || cli.activo === 1 || cli.activo === "1";
            estadoSelect.value = activoBool ? "true" : "false";
        })
        .catch(err => {
            console.error("Error cargando cliente:", err);
            alert("Error al cargar los datos del cliente");
        });
});
