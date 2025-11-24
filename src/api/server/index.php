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
        $out = obtenerEmpleados($peticion["sucursal"], $peticion["rol"], $peticion["estado"]);
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
            $peticion["estado"] ?? null
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

    //se procesa una peticion
    /*case "mostrar_suma":
        $out = mostrarSuma();
        break;*/

    default:
        $out = ["error" => "Accion no reconocida"];
}

echo json_encode($out);
