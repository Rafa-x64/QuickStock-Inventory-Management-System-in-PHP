<?php
include_once "model/seguridad_acceso.usuario.php";

class empleados_editar_C extends mainModel
{
    public static function editarEmpleado($formulario)
    {
        // Campos requeridos
        $requeridos = [
            "nombre_empleado",
            "apellido_empleado",
            "cedula_empleado",
            "telefono_empleado",
            "id_rol",
            "email_empleado",
            // "direccion_empleado", // Opcional
            // "id_sucursal", // Opcional si es Admin
            "estado_empleado",
            "id_email"
        ];

        foreach ($requeridos as $campo) {
            if (!isset($formulario[$campo]) || trim($formulario[$campo]) === "") {
                return ["error" => "Campo faltante: $campo"];
            }
        }

        // 🔒 PROTECCIÓN BACKEND: Verificar que no se esté intentando editar al Gerente
        $emailViejo = trim($formulario["id_email"]);
        $conn = parent::conectar_base_datos();
        $queryCheckGerente = "SELECT id_rol FROM seguridad_acceso.usuario WHERE email = $1";
        $resCheck = pg_query_params($conn, $queryCheckGerente, [$emailViejo]);

        if ($resCheck && pg_num_rows($resCheck) > 0) {
            $userData = pg_fetch_assoc($resCheck);
            if ($userData['id_rol'] == 1) {
                return ["error" => "No se puede editar el usuario Gerente. Este usuario tiene privilegios especiales."];
            }
        }

        // Formatear Cédula
        $c = trim($formulario["cedula_empleado"]);
        $clean_c = preg_replace('/[^a-zA-Z0-9]/', '', $c);
        if (preg_match('/^([VEve])(\d+)$/', $clean_c, $matches)) {
            $cedula = strtoupper($matches[1]) . "-" . number_format((int)$matches[2], 0, '', '.');
        } else {
            $cedula = strtoupper($c);
        }

        // Formatear Teléfono
        $t = preg_replace('/\D/', '', $formulario["telefono_empleado"]);
        if (strpos($t, '0') === 0) {
            $t = '58' . substr($t, 1);
        } elseif (strpos($t, '58') !== 0) {
            $t = '58' . $t;
        }
        if (strlen($t) >= 12) {
            $telefono = "+" . substr($t, 0, 2) . " " . substr($t, 2, 3) . "-" . substr($t, 5, 3) . "-" . substr($t, 8, 2) . "-" . substr($t, 10);
        } else {
            $telefono = "+" . $t;
        }

        // Normalización básica (sin tocar validaciones anteriores)
        $data = [
            "nombre"    => ucwords(strtolower(trim($formulario["nombre_empleado"]))),
            "apellido"  => ucwords(strtolower(trim($formulario["apellido_empleado"]))),
            "cedula"    => $cedula,
            "telefono"  => $telefono,
            "id_rol"    => (int)$formulario["id_rol"],
            "emailNuevo" => strtolower(trim($formulario["email_empleado"])),
            "direccion" => !empty($formulario["direccion_empleado"]) ? ucwords(strtolower(trim($formulario["direccion_empleado"]))) : null,
            "id_sucursal" => !empty($formulario["id_sucursal"]) ? (int)$formulario["id_sucursal"] : null,
            "estado"    => trim($formulario["estado_empleado"]),
            "emailViejo" => trim($formulario["id_email"])
        ];

        // Delegar TODO al modelo
        return usuario::editar($data);
    }
}
