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

    case "obtener_una_sucursal":
        include_once __DIR__ . "/core/sucursal.php";
        $out = obtenerUnaSucursalPorId($peticion["id_sucursal"]);
        break;

    case "obtener_nombre_sucursal":
        $out = obtenerNombreSucursal();
        break;

    case "obtener_todos_los_empleados":
        include_once __DIR__ . "/seguridad_acceso/usuario.php";
        $out = obtenerEmpleados(
            $peticion["sucursal"] ?? null,
            $peticion["rol"] ?? null,
            $peticion["estado"] ?? null
        );
        break;

    case "obtener_un_usuario":
        include_once __DIR__ . "/seguridad_acceso/usuario.php";
        $out = obtenerUnUsuario($peticion["email"]);
        break;

    case "obtener_categorias":
        include_once __DIR__ . "/core/categoria.php";
        $out = obtenerCategorias();
        break;

    case "obtener_proveedores":
        include_once __DIR__ . "/core/proveedor.php";
        $out = obtenerProveedores();
        break;

    case "obtener_colores":
        include_once __DIR__ . "/core/color.php";
        $out = obtenerColores();
        break;

    case "obtener_tallas":
        include_once __DIR__ . "/core/talla.php";
        $out = obtenerTallas();
        break;

    case "obtener_todos_los_productos":
        include_once __DIR__ . "/inventario/producto.php";
        $out = obtenerTodosLosProductos(
            $peticion["nombre"] ?? null,
            $peticion["codigo"] ?? null,
            $peticion["categoria"] ?? null,
            $peticion["proveedor"] ?? null,
            $peticion["sucursal"] ?? null,
            $peticion["estado"] ?? null,
            $peticion["color"] ?? null,
            $peticion["talla"] ?? null
        );
        break;

    case "obtener_un_producto":
        include_once __DIR__ . "/inventario/producto.php";
        $out = obtenerUnProducto($peticion["id_producto"]);
        break;

    case "obtener_detalle_producto":
        include_once __DIR__ . "/inventario/producto.php";
        $out = obtenerDetalleProducto($peticion["id_producto"]);
        break;

    case "obtener_categoria_por_id":
        include_once __DIR__ . "/core/categoria.php";
        $out = seleccionarCategoriaPorId($peticion["id_categoria"]);
        break;

    case "obtener_categorias_filtro":
        include_once __DIR__ . "/core/categoria.php";
        $out = obtenerCategoriasFiltro($peticion["string"]);
        break;

    case "obtener_detalle_sucursal":
        include_once __DIR__ . "/core/sucursal.php";
        $out = obtenerDetalleSucursal($peticion["id_sucursal"]);
        break;

    case "obtener_empleados_responsables": // <-- NUEVA ACCIÓN
        include_once __DIR__ . "/seguridad_acceso/usuario.php"; // <-- Incluir el nuevo archivo
        $out = obtenerEmpleadosResponsables(); // <-- Llamar a la nueva función
        break;

    case "obtener_monedas":
        include_once __DIR__ . "/finanzas/moneda.php";
        $out = obtenerMonedas();
        break;

    case "obtener_historial_compras":
        include_once __DIR__ . "/inventario/compra.php"; // <--- Nuevo archivo a crear
        $out = obtenerHistorialCompras();
        break;

    case "obtener_detalle_compra":
        include_once __DIR__ . "/inventario/compra.php"; // <--- Nuevo archivo a crear
        $out = obtenerDetalleCompra($peticion["id_compra"]);
        break;

    case "obtener_compra_por_id":
        include_once __DIR__ . "/inventario/compra.php"; // <--- Nuevo archivo a crear
        $out = obtenerCompraPorId($peticion["id_compra"]);
        break;

    case "obtener_metodos_pago":
        include_once __DIR__ . "/finanzas/metodo_pago.php";
        $out = obtenerMetodosPago($peticion["filtro"] ?? null);
        break;

    case "obtener_metodo_pago_detalle":
        include_once __DIR__ . "/finanzas/metodo_pago.php";
        $out = obtenerMetodoPagoPorId($peticion["id_metodo_pago"]);
        break;

    // --- PUNTOS DE VENTA (POS) ---

    case "obtener_cliente_por_cedula":
        include_once __DIR__ . "/core/cliente.php"; // Asegurarse que este archivo tenga la función
        // Si no existe la función específica, usar una genérica o crearla.
        // Asumiremos que existe o se creará en core/cliente.php
        $out = obtenerClientePorCedula($peticion["cedula"]);
        break;

    case "obtener_producto_por_codigo":
        include_once __DIR__ . "/inventario/producto.php";
        $out = obtenerProductoPorCodigoBarra($peticion["codigo"]);
        break;

    case "obtener_tasas_cambio":
        include_once __DIR__ . "/finanzas/tasa_cambio.php"; // Asumiendo existencia
        $out = obtenerTasasCambioActivas();
        break;

    case "procesar_venta":
        include_once __DIR__ . "/../../controller/ventas_punto_venta_C.php";
        $out = ventas_punto_venta_C::procesarVenta($peticion);
        break;

    case "obtener_ventas_filtradas":
        include_once __DIR__ . "/ventas/venta.php";
        $out = obtenerVentasFiltradas(
            $peticion["fecha_desde"] ?? null,
            $peticion["fecha_hasta"] ?? null,
            $peticion["id_usuario"] ?? null,
            $peticion["monto_min"] ?? null,
            $peticion["monto_max"] ?? null,
            $peticion["id_sucursal"] ?? null,
            $peticion["id_metodo_pago"] ?? null,
            $peticion["id_moneda"] ?? null
        );
        break;

    case "obtener_detalle_venta":
        include_once __DIR__ . "/ventas/venta.php";
        $out = obtenerDetalleVentaPorId($peticion["id_venta"]);
        break;

    default:
        $out = ["error" => "Accion no reconocida"];
}

echo json_encode($out);
