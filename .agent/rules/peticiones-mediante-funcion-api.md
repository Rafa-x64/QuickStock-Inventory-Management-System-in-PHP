---
trigger: always_on
---

Las peticiones del lado del cliente se suelen realizar mediante la funcion predefinida api() en la carpeta api/client/index.js y se importa en el js de las peticiones

el archivo de peticiones debe tener la logica js para generacion de elementos, obtenener respuestas del server "server/index.php" al realizar peticiones y logica extra de js que no pueda estar en el archivo de validaciones en view/js

la estructura normal de una peticion mediante js es la siguiente

importacion de api:

import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

ejecutar el codigo cuando el dom este cargado completamente para evitar errores de id´s inexistentes:
document.addEventListener("DOMContentLoaded", () => {
    const select_sucursal = document.getElementById("sucursal-filtro");
    const select_cargo = document.getElementById("cargo-filtro");
    const select_estado = document.getElementById("estado-filtro");
    const reestablecer_filtros = document.getElementById("reestablecer-filtros");
    const tabla_empleados = document.getElementById("lista_empleados");

realizar la peticion a "index.php y recibir su respuesta (array u objeto js)" mediante el parametro accion:"obtener_sucursales":
    api({ accion: "obtener_sucursales" }) //a esto se le pueden pasar mas parametros de la misma manera que se pasa accion... el index.php lo interpreta correctamente
        .then(res => {
            if (res.error) {
                console.error("Error al obtener sucursales:", res.error);
                return;
            }
            (res.filas || res.data || []).forEach(sucursal => {
                const opt = document.createElement("option");
                opt.value = sucursal.id_sucursal;
                opt.textContent = sucursal.nombre;
                select_sucursal.appendChild(opt);
            });
        })
        .catch(err => console.error("Error API sucursales:", err));

    api({ accion: "obtener_roles" })
        .then(res => {
            if (res.error) {
                console.error("Error al obtener roles:", res.error);
                return;
            }
            (res.filas || res.data || []).forEach(rol => {
                const opt = document.createElement("option");
                opt.value = rol.id_rol;
                opt.textContent = rol.nombre_rol;
                select_cargo.appendChild(opt);
            });
        })
        .catch(err => console.error("Error API roles:", err));

funciones extra para asegurar el correcto funcionamiento:
    function normalizeBoolean(value) {
        // Normaliza distintos formatos posibles que vienen desde PG / PHP
        // Accepts: true, false, "t", "f", "true", "false", "1", "0", 1, 0
        if (value === true || value === 1) return true;
        if (value === false || value === 0) return false;
        if (typeof value === "string") {
            const v = value.trim().toLowerCase();
            return (v === "t" || v === "true" || v === "1" || v === "yes" || v === "y");
        }
        return Boolean(value);
    }

    function renderizarEmpleados(empleados) {
        const tbody = document.querySelector("#lista_empleados tbody");
        tbody.innerHTML = "";

        if (!empleados || empleados.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">No se encontraron empleados</td>
                </tr>
            `;
            return;
        }

        empleados.forEach(emp => {
            const activoBool = normalizeBoolean(emp.activo);

            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${emp.id_usuario ?? ""}</td>
                <td>${(emp.nombre ?? "") + " " + (emp.apellido ?? "")}</td>
                <td>${emp.nombre_rol ?? ""}</td>
                <td>${emp.sucursal_nombre ?? "Sin asignar"}</td>
                <td>${emp.telefono ?? ""}</td>
                <td>${emp.cedula ?? ""}</td>
                <td>
                    <span class="badge ${activoBool ? "bg-success" : "bg-danger"}">
                        ${activoBool ? "Activo" : "Inactivo"}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 flex-row justify-content-center align-items-center">
                        <form action="empleados-detalle" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="ver_detalle">
                            <input type="hidden" name="email" value="${emp.email ?? ""}">
                            <button type="submit" class="btn btn-sm btn-info text-white btn-action">
                                <i class="bi bi-eye"></i>
                            </button>
                        </form>
                        <form action="empleados-editar" method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="email" value="${emp.email ?? ""}">
                            <button type="submit" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </form>
                        <form action="" method="POST" class="d-inline eliminar-form">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="email" value="${emp.email ?? ""}">
                            <button type="submit" class="btn btn-sm btn-danger text-white btn-action">
                                <i class="bi bi-person-x"></i>
                            </button>
                        </form>
                    </div>
                </td>
            `;

            tbody.appendChild(fila);
        });

        // Opcional: interceptar los formularios de eliminar para confirmar y luego refrescar lista vía JS
        // Si quieres mantener envío normal (POST y recarga), puedes quitar este bloque.
        document.querySelectorAll(".eliminar-form").forEach(form => {
            form.addEventListener("submit", function (e) {
                // Si prefieres confirmación con diálogo nativo:
                if (!confirm("¿Eliminar (desactivar) este empleado?")) {
                    e.preventDefault();
                    return;
                }
                // Si quieres hacer la eliminación por API y refrescar sin recargar:
                // e.preventDefault();
                // const formData = new FormData(this);
                // fetch('ruta_a_tu_endpoint_de_eliminar', { method: 'POST', body: formData })
                //   .then(r => r.json()).then(resp => { aplicarFiltros(); alert('Empleado eliminado'); })
                //   .catch(err => console.error(err));
            });
        });
    }

    function aplicarFiltros() {
        const sucursal = select_sucursal.value;
        const rol = select_cargo.value;
        const estado = select_estado.value;

        api({
            accion: "obtener_todos_los_empleados",
            sucursal,
            rol,
            estado
        })
            .then(res => {
                // soporte tanto res.filas (antes) como res.data (si usaste la versión que devuelve data)
                const filas = (res && (res.filas || res.data)) ? (res.filas || res.data) : [];

                if (res && res.error) {
                    console.error("Error desde PHP:", res.error || res.message || res.detalle);
                    renderizarEmpleados([]);
                    return;
                }

                renderizarEmpleados(filas);
            })
            .catch(err => {
                console.error("Error al filtrar usuarios:", err);
                renderizarEmpleados([]);
            });
    }

    aplicarFiltros();

    select_sucursal.addEventListener("change", aplicarFiltros);
    select_cargo.addEventListener("change", aplicarFiltros);
    select_estado.addEventListener("change", aplicarFiltros);

    reestablecer_filtros.addEventListener("click", () => {
        select_sucursal.selectedIndex = 0;
        select_cargo.selectedIndex = 0;
        select_estado.selectedIndex = 0;
        aplicarFiltros();
    });

});

esta peticion index.php lo interpreta asi
<?php
//se inici ala sesion para obtener datos de usuario
session_start();
//se procesaa la peticion por medio de json_decode
$peticion = json_decode(file_get_contents("php://input"), true);
$accion = $peticion["accion"] ?? null;

//no tocar
include_once __DIR__ . "/index.functions.php";

//se procesan las peticiones
switch ($accion) {

    case "existe_gerente":
        include_once __DIR__ . "/seguridad_acceso/rol.php";
        $out = existeGerente();
        break;

    case "obtener_nombre_apellido":
        include_once __DIR__ . "/seguridad_acceso/usuario.php";
        $out = obtenerNombreApellido();
        break;

    case "obtener_roles":
        include_once __DIR__ . "/seguridad_acceso/rol.php";
        $out = obtenerRoles();
        break;

    case "obtener_sucursales":
        include_once __DIR__ . "/core/sucursal.php";
        $out = obtenerSucursales();
        break;
//otros case para interpretar las peticiones...
default:
        $out = ["error" => "Accion no reconocida"];
}
//retorno de out
echo json_encode($out);

y las funciones consultan la base de datos mediante la funcion conectar_base_de_datos(); y realizan la consulta devolviendo como respuesta un array (objeto)... este se debe llamar como lo que se esta obteniendo en la consulta:

function obtenerRoles()
{
    $conn = conectar_base_datos();
    pg_prepare($conn, "obtener_roles", "SELECT * FROM seguridad_acceso.rol");
    $res = pg_execute($conn, "obtener_roles", []);
    if (!$res) {
        return ["error" => "Error al realizar la consulta de roles"];
    }
    $roles = pg_fetch_all($res);
    if (!$roles) {
        return [];
    }
    return ["rol" => $roles ?: []];
}

