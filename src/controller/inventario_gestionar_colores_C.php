<?php
include_once "model/core.color.php";

class gestionar_colores_C extends mainModel
{
    public static function crearColor($formulario): array
    {
        try {
            // 1. SANITIZACIÓN DE DATOS
            $nombre = filter_var(trim($formulario["nombre_color_añadir"] ?? ''), FILTER_SANITIZE_STRING);

            // 2. INSTANCIAR EL MODELO
            // __construct(int $id_color, string $nombre, bool $activo)
            $color_obj = new color(
                0,                  // ID temporal
                $nombre,            // Nombre
                true                // Activo por defecto
            );

            // 3. LLAMAR AL MÉTODO DE CREACIÓN
            $id_nuevo = $color_obj->crear();

            if ($id_nuevo > 0) {
                return ["success" => true, "mensaje" => "Color registrado con ID: $id_nuevo."];
            } else {
                return ["error" => true, "mensaje" => "No se pudo crear el color o ya existe uno con ese nombre."];
            }
        } catch (InvalidArgumentException $e) {
            return ["error" => true, "mensaje" => "Error de datos: " . $e->getMessage()];
        } catch (\Exception $e) {
            error_log("Error al crear color: " . $e->getMessage());
            return ["error" => true, "mensaje" => "Error interno del servidor."];
        }
    }

    public static function editarColor($formulario): array
    {
        try {
            // 1. SANITIZACIÓN
            $id_color = filter_var($formulario["id_color_editar"] ?? 0, FILTER_VALIDATE_INT);
            if ($id_color === false || $id_color <= 0) {
                return ["error" => true, "mensaje" => "ID de color no válido."];
            }

            $nombre = filter_var(trim($formulario["nombre_color_editar"] ?? ''), FILTER_SANITIZE_STRING);

            $activo_str = strtolower($formulario["activo_editar"] ?? 'f');
            $activo = ($activo_str === 'activo' || $activo_str === 't' || $activo_str === 'true');

            $color_obj = new color(
                $id_color,
                $nombre,
                $activo
            );

            if ($color_obj->editar()) {
                return ["success" => true, "mensaje" => "Color actualizado correctamente."];
            } else {
                return ["error" => true, "mensaje" => "No se pudo actualizar el color o no hubo cambios."];
            }
        } catch (InvalidArgumentException $e) {
            return ["error" => true, "mensaje" => "Error de datos: " . $e->getMessage()];
        } catch (\Exception $e) {
            error_log("Error al editar color: " . $e->getMessage());
            return ["error" => true, "mensaje" => "Error interno del servidor."];
        }
    }

    public static function eliminarColor($formulario): array
    {
        try {
            $id_color = filter_var($formulario["id_color"] ?? $formulario["id_color_eliminar"] ?? 0, FILTER_VALIDATE_INT);

            if ($id_color === false || $id_color <= 0) {
                return ["error" => true, "mensaje" => "ID de color no válido para eliminar."];
            }

            if (color::eliminar($id_color)) {
                return ["success" => true, "mensaje" => "Color eliminado (desactivado) correctamente."];
            } else {
                return ["error" => true, "mensaje" => "No se pudo eliminar el color."];
            }
        } catch (\Exception $e) {
            error_log("Error al eliminar color: " . $e->getMessage());
            return ["error" => true, "mensaje" => "Error interno del servidor."];
        }
    }
}
