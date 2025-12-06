import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {

    const email = document.getElementById("id_email").value;

    console.log(email);

    // Campos del formulario
    const nombreInput = document.getElementById("nombre_empleado");
    const apellidoInput = document.getElementById("apellido_empleado");
    const cedulaInput = document.getElementById("cedula_empleado");
    const telefonoInput = document.getElementById("telefono_empleado");
    const rolSelect = document.getElementById("id_rol");
    const emailInput = document.getElementById("email_empleado");
    const direccionInput = document.getElementById("direccion_empleado");
    const sucursalSelect = document.getElementById("id_sucursal");
    const estadoSelect = document.getElementById("estado_empleado");

    // 1️⃣ Obtener el usuario por email
    api({ accion: "obtener_un_usuario", email: email }).then(res => {

        console.log(res.empleado);


        if (!res.empleado) {
            console.error("No se encontró el usuario");
            return;
        }

        const emp = res.empleado;

        // 2️⃣ Rellenar campos simples
        nombreInput.value = emp.nombre ?? "";
        apellidoInput.value = emp.apellido ?? "";
        cedulaInput.value = emp.cedula ?? "";
        telefonoInput.value = emp.telefono ?? "";
        emailInput.value = emp.email ?? "";
        direccionInput.value = emp.direccion ?? "";

        // Estado
        estadoSelect.value = emp.activo ? "activo" : "inactivo";

        // 3️⃣ Llenar roles (select) y seleccionar el rol actual
        api({ accion: "obtener_roles" }).then(rRoles => {
            const roles = rRoles.rol || rRoles.filas || [];
            roles.forEach(rol => {
                const op = document.createElement("option");
                op.value = rol.id_rol;
                op.textContent = rol.nombre_rol;

                if (rol.id_rol == emp.id_rol) op.selected = true;

                rolSelect.appendChild(op);
            });
        });

        // 4️⃣ Llenar sucursales (select) y seleccionar la actual
        api({ accion: "obtener_sucursales" }).then(rSuc => {
            
            // Opción "Ninguna"
            const opcionNinguna = document.createElement("option");
            opcionNinguna.value = "";
            opcionNinguna.textContent = "Ninguna (Solo para Administradores)";
            sucursalSelect.appendChild(opcionNinguna);

            if (!emp.id_sucursal) opcionNinguna.selected = true;

            rSuc.filas.forEach(s => {
                const op = document.createElement("option");
                op.value = s.id_sucursal;
                op.textContent = s.nombre;

                if (s.id_sucursal == emp.id_sucursal) op.selected = true;

                sucursalSelect.appendChild(op);
            });

             // Validar estado inicial del required al cargar
             validarRolAdmin();
        });


        // Función y Evento para validar Rol Admin
        function validarRolAdmin() {
            if (rolSelect.selectedIndex === -1) return;
            const textoRol = rolSelect.options[rolSelect.selectedIndex].textContent.toLowerCase();
            
            if (textoRol.includes("administrador")) {
                sucursalSelect.removeAttribute("required");
            } else {
                sucursalSelect.setAttribute("required", "true");
            }
        }

        rolSelect.addEventListener("change", validarRolAdmin);

    }).catch(err => {
        console.error("Error cargando usuario:", err);
    });



});