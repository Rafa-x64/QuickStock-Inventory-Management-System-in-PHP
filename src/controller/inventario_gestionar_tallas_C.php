<?php
include_once "model/core.talla.php";

class gestionar_tallas_C extends mainModel
{
    public static function crearTalla($formulario): array
    {
        try {
            // 1. SANITIZACIÓN DE DATOS
            $rango = filter_var(trim($formulario["rango_talla_añadir"] ?? ''), FILTER_SANITIZE_STRING);

            // 2. INSTANCIAR MODELO
            // __construct(int $id_talla, string $rango_talla, bool $activo)
            $talla_obj = new talla(
                0,
                $rango,
                true
            );

            // 3. CREAR
            $id_nuevo = $talla_obj->crear();

            if ($id_nuevo > 0) {
                return ["success" => true, "mensaje" => "Talla registrada con ID: $id_nuevo."];
            } else {
                return ["error" => true, "mensaje" => "No se pudo crear la talla o ya existe."];
            }
        } catch (InvalidArgumentException $e) {
            return ["error" => true, "mensaje" => "Error de datos: " . $e->getMessage()];
        } catch (\Exception $e) {
            error_log("Error al crear talla: " . $e->getMessage());
            return ["error" => true, "mensaje" => "Error interno del servidor."];
        }
    }

    public static function editarTalla($formulario): array
    {
        try {
            $id_talla = filter_var($formulario["id_talla_editar"] ?? 0, FILTER_VALIDATE_INT);
            if ($id_talla === false || $id_talla <= 0) {
                return ["error" => true, "mensaje" => "ID de talla no válido."];
            }

            $rango = filter_var(trim($formulario["rango_talla_editar"] ?? ''), FILTER_SANITIZE_STRING);

            $activo_str = strtolower($formulario["activo_editar"] ?? 'f');
            $activo = ($activo_str === 'activo' || $activo_str === 't' || $activo_str === 'true');

            $talla_obj = new talla(
                $id_talla,
                $rango,
                $activo
            );

            if ($talla_obj->editar()) {
                return ["success" => true, "mensaje" => "Talla actualizada correctamente."];
            } else {
                return ["error" => true, "mensaje" => "No se pudo actualizar la talla o no hubo cambios."];
            }
        } catch (InvalidArgumentException $e) {
            return ["error" => true, "mensaje" => "Error de datos: " . $e->getMessage()];
        } catch (\Exception $e) {
            error_log("Error al editar talla: " . $e->getMessage());
            return ["error" => true, "mensaje" => "Error interno del servidor."];
        }
    }

    public static function eliminarTalla($formulario): array
    {
        try {
            $id_talla = filter_var($formulario["id_talla"] ?? $formulario["id_talla_eliminar"] ?? 0, FILTER_VALIDATE_INT);

            if ($id_talla === false || $id_talla <= 0) {
                return ["error" => true, "mensaje" => "ID de talla no válido para eliminar."];
            }

            if (talla::eliminar($id_talla)) {
                return ["success" => true, "mensaje" => "Talla eliminada (desactivada) correctamente."];
            } else {
                return ["error" => true, "mensaje" => "No se pudo eliminar la talla."];
            }
        } catch (\Exception $e) {
            error_log("Error al eliminar talla: " . $e->getMessage());
            return ["error" => true, "mensaje" => "Error interno del servidor."];
        }
    }
}
