import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const idMetodo = document.getElementById("id_metodo_pago").value;

    if (!idMetodo) {
        document.getElementById("detalle-nombre").textContent = "ID no proporcionado";
        return;
    }

    api({ accion: "obtener_metodo_pago_detalle", id_metodo_pago: idMetodo })
        .then(res => {
            if (res.error) {
                document.getElementById("detalle-nombre").textContent = "Error: " + res.error;
                return;
            }

            const metodo = res.data;
            if (metodo) {
                document.getElementById("detalle-nombre-completo").textContent = metodo.nombre;
                document.getElementById("detalle-id").textContent = metodo.id_metodo_pago;
                document.getElementById("detalle-descripcion").textContent = metodo.descripcion || "Sin descripción";
                document.getElementById("breadcrumb-nombre").textContent = metodo.nombre;
                
                const referencia = metodo.referencia === 't' || metodo.referencia === true;
                document.getElementById("detalle-referencia").textContent = referencia ? "Sí" : "No";
                
                const estadoSpan = document.getElementById("detalle-estado");
                const activo = metodo.activo === 't' || metodo.activo === true;
                
                estadoSpan.textContent = activo ? "Activo" : "Inactivo";
                estadoSpan.className = activo ? "badge bg-success" : "badge bg-danger";
            } else {
                document.getElementById("detalle-nombre").textContent = "Método no encontrado";
            }
        })
        .catch(err => {
            console.error("Error API detalle método:", err);
            document.getElementById("detalle-nombre").textContent = "Error de conexión";
        });
});
