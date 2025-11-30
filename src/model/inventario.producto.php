<?php

class producto extends mainModel
{
    public $id_producto;
    public $nombre;
    public $descripcion;
    public $id_categoria;
    public $id_color;
    public $id_talla;
    public $precio; // Precio Venta
    public $precio_compra; // Precio Compra
    public $id_proveedor;
    public $activo;
    public $codigo_barra;

    public function __construct($id_producto, $nombre, $descripcion, $id_categoria, $id_color, $id_talla, $precio, $id_proveedor, $activo, $codigo_barra, $precio_compra)
    {
        $this->id_producto = $id_producto;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->id_categoria = $id_categoria;
        $this->id_color = $id_color;
        $this->id_talla = $id_talla;
        $this->precio = $precio;
        $this->precio_compra = $precio_compra;
        $this->id_proveedor = $id_proveedor;
        $this->activo = $activo;
        $this->codigo_barra = $codigo_barra;
    }

    public function crear(): ?int
    {
        $conn = parent::conectar_base_datos();

        $sql = "INSERT INTO inventario.producto
            (nombre, descripcion, id_categoria, id_color, id_talla, precio_venta, id_proveedor, activo, codigo_barra, precio_compra) 
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)
            RETURNING id_producto";

        $result = pg_query_params($conn, $sql, [
            $this->nombre,          // $1
            $this->descripcion,     // $2
            $this->id_categoria,    // $3
            $this->id_color,        // $4
            $this->id_talla,        // $5
            $this->precio,          // $6 (Precio Venta)
            $this->id_proveedor ?: null, // $7 (Usar null si es 0/vacío)
            $this->activo,          // $8
            $this->codigo_barra,    // $9
            $this->precio_compra    // $10 (Precio Compra)
        ]);

        if (!$result) {
            throw new Exception("Error al insertar producto: " . pg_last_error($conn));
        }

        $row = pg_fetch_assoc($result);
        return $row ? intval($row['id_producto']) : null;
    }

    public function agregarInventario($id_producto, $id_sucursal, $cantidad, $minimo)
    {
        $conn = parent::conectar_base_datos();

        $sql = "INSERT INTO inventario.inventario (id_producto, id_sucursal, cantidad, minimo, activo)
            VALUES ($1,$2,$3,$4,true)";

        $result = pg_query_params($conn, $sql, [$id_producto, $id_sucursal, $cantidad, $minimo]);

        if (!$result) {
            throw new Exception("Error al insertar inventario: " . pg_last_error($conn));
        }
    }

    public static function buscarPorNombreOCodigo($nombre, $codigo): ?array
    {
        $conn = parent::conectar_base_datos();

        // Limpiamos los valores para una búsqueda segura y consistente (insensible a mayúsculas en el nombre)
        $nombreLimpio = trim(mb_strtolower($nombre ?? '', 'UTF-8'));
        $codigoLimpio = trim($codigo ?? '');

        // 🚨 CORRECCIÓN CLAVE: Usamos pg_query_params para evitar el error de sentencia preparada.
        // Esto previene el error: "la sentencia preparada "buscar_producto" ya existe".
        $sql = "
            SELECT 
                id_producto
            FROM 
                inventario.producto 
            WHERE 
                (LOWER(nombre) = $1 OR codigo_barra = $2) 
            LIMIT 1
        ";

        $result = pg_query_params($conn, $sql, [$nombreLimpio, $codigoLimpio]);

        if (!$result) {
            // Error en la ejecución de la consulta.
            error_log("Error en buscarPorNombreOCodigo: " . pg_last_error($conn));
            return null;
        }

        if (pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }

        return null;
    }

    public static function editar($data): array
    {
        $conn = parent::conectar_base_datos();

        $id_producto = $data["id_producto"];
        $id_sucursal = $data["id_sucursal"];
        $precio_compra = $data["precio_compra"];

        // La lógica de la línea 118 para $activo_nuevo está bien, 
        // pero debemos asegurarnos que el valor final para la BD sea 't' o 'f'.
        $activo_nuevo = isset($data['activo']) && (bool)$data['activo'];

        pg_query($conn, "BEGIN");

        try {
            // A. Actualizar Producto (Tabla inventario.producto)
            $sql_producto = "
            UPDATE inventario.producto
            SET
                codigo_barra = $1,
                nombre = $2,
                descripcion = $3,
                id_categoria = $4,
                id_color = $5,
                id_talla = $6,
                precio_venta = $7,     /* precio_venta */
                id_proveedor = $8,
                precio_compra = $9,  /* AHORA $9 */
                activo = $10     /* AHORA $10 */
            WHERE id_producto = $11  /* AHORA $11 */
        ";

            $params_producto = [
                $data["codigo_barra"], // $1
                $data["nombre"],    // $2
                $data["descripcion"],  // $3
                $data["id_categoria"], // $4
                $data["id_color"],   // $5
                $data["id_talla"],   // $6
                $data["precio"],    // $7 (Precio Venta)
                $data["id_proveedor"], // $8
                $precio_compra,     // $9 (Precio Compra)
                // 👇 CORRECCIÓN CLAVE: Convertir el booleano PHP a cadena 't' o 'f' para PostgreSQL.
                $activo_nuevo ? 't' : 'f', // $10 (Activo - TRUE/FALSE)
                $id_producto      // $11 (ID para WHERE)
            ];

            $res_producto = pg_query_params($conn, $sql_producto, $params_producto); // Línea 133

            if (!$res_producto) {
                throw new Exception("Error al actualizar producto: " . pg_last_error($conn));
            }

            // B. Actualizar o Insertar Inventario
            $sql_inventario = "
            UPDATE inventario.inventario
            SET
                cantidad = $1,
                minimo = $2
            WHERE id_producto = $3 AND id_sucursal = $4
        ";

            $params_inventario = [
                $data["cantidad"],
                $data["minimo"],
                $id_producto,
                $id_sucursal
            ];

            $res_inventario = pg_query_params($conn, $sql_inventario, $params_inventario);

            if (!$res_inventario) {
                throw new Exception("Error al actualizar inventario: " . pg_last_error($conn));
            }

            if (pg_affected_rows($res_inventario) === 0) {
                self::agregarInventario($id_producto, $id_sucursal, $data["cantidad"], $data["minimo"]);
            }

            pg_query($conn, "COMMIT");

            return ["success" => true];
        } catch (Exception $e) {
            pg_query($conn, "ROLLBACK");
            return ["error" => $e->getMessage()];
        }
    }

    public static function eliminar($id_producto)
    {
        $conn = parent::conectar_base_datos();
        pg_prepare($conn, "eliminar_producto", "UPDATE inventario.producto SET activo = false WHERE id_producto = $1");
        $resultado = pg_execute($conn, "eliminar_producto", [$id_producto]);
        if (!$resultado) {
            return false;
        }

        return true;
    }
}
