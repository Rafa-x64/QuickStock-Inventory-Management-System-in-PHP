<?php

session_start();

$paginas_publicas = [
    "inicio-view.php",
    "inicio-sesion-usuario-view.php",
    "registro-usuario-view.php"
];

$paginas_existentes = [
    "dashboard-gerente-view.php",
    "inventario-ver-productos-view.php",
    "inventario-añadir-producto-view.php",
    "inventario-gestionar-categorias-view.php",
    "inventario-ajustes-manuales-stock-view.php",
    "inventario-stock-bajo-view.php",
    "listado-compras-view.php",
    "añadir-compras-view.php",
    "punto-venta-view.php",
    "historial-facturas-view.php",
    "cierre-caja-view.php",
    "clientes-ver-listado-clientes-view.php",
    "clientes-gestionar-clientes-view.php",
    "proveedores-lista-view.php",
    "proveedores-detalles-view.php",
    "proveedores-gestionar-proveedores-view.php",
    "empleados-lista-empleados-view.php",
    "empleado-gestionar-empleado-view.php",
    "sucursales-añadir-view.php",
    "sucursales-listado-view.php",
    "sucursales-detalle-view.php",
    "clientes-listado-view.php",
    "clientes-detalle-view.php",
    "clientes-editar-view.php",
    "proveedores-listado-view.php",
    "proveedores-añadir-view.php",
    "proveedores-detalle-view.php",
    "proveedores-editar-view.php",
    "empleados-listado-view.php",
    "empleados-añadir-view.php",
    "empleados-detalle-view.php",
    "empleados-añadir-rol-view.php",
    "empleados-lista-roles-view.php",
    "empleados-detalle-rol-view.php",
    "monedas-tasas-activas-view.php",
    "monedas-añadir-tasas-view.php",
    "monedas-historial-view.php",
    "monedas-añadir-view.php",
    "monedas-listado-view.php",
    "ventas-añadir-metodo-pago-view.php",
    "ventas-cierre-caja-view.php",
    "ventas-detalle-metodo-pago-view.php",
    "ventas-historial-facturas-view.php",
    "ventas-lista-metodos-pago-view.php",
    "ventas-añadir-metodo-pago-view.php",
    "ventas-detalle-metodo-pago-view.php",
    "ventas-productos-populares-view.php",
    "ventas-punto-venta-view.php",
    "compras-historial-view.php",
    "compras-añadir-view.php",
    "compras-detalle-view.php",
    "empleados-editar-view.php",
    "empleados-eliminar-view.php",
    "inventario-editar-producto-view.php",
    "inventario-detalle-producto-view.php",
    "inventario-editar-categorias-view.php",
    "sucursales-editar-view.php",
    "compras-editar-view.php",
    "ventas-editar-metodo-pago-view.php",
    "ventas-detalle-factura-view.php",
    "prueba-reporte-view.php"
];

// Redirección si la vista NO es pública y no hay sesión
if (!in_array($vista, $paginas_publicas) && !isset($_SESSION["sesion_usuario"])) {
    echo "<script>window.location.href = 'inicio';</script>";
    exit();
}

include_once("assets/elements/links.php");

// Vista de inicio
if ($vista === "inicio-view.php") {
    include_once("assets/elements/header.php");
    include_once("assets/elements/menu-lateral.php");
    include_once("view/html/" . $vista);
    include_once("assets/elements/footer.php");
    include_once("assets/elements/scripts.php");
    exit();
}

// Vista de login o registro
if ($vista === "inicio-sesion-usuario-view.php" || $vista === "registro-usuario-view.php") {
    include_once("assets/elements/menu_volver.php");
    include_once("view/html/" . $vista);
    include_once("assets/elements/scripts.php");
    exit();
}

// 🔥 VERIFICAR SI ES UNA SOLICITUD DE PDF ANTES DE ENVIAR HTML
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST['accion'] ?? '') === 'imprimir_pdf') {
    include_once("controller/reportes_C.php");
    reportes_C::generarReporte($_POST);
    exit();
}

// 🔥 SOLO EJECUTAR ESTO SI EXISTE SESIÓN
if (isset($_SESSION["sesion_usuario"])) {

    $rol = $_SESSION["sesion_usuario"]["rol"]["nombre_rol"];

    if ($rol == "Gerente" && in_array($vista, $paginas_existentes)) {
        include_once("assets/elements/menu-lateral-gerente.php");
        include_once("view/html/" . $vista);
        include_once("assets/elements/scripts.php");
        exit();
    }

    if ($rol == "Cajero" && in_array($vista, $paginas_existentes)) {
        include_once("assets/elements/menu-lateral-cajero.php");
        include_once("view/html/" . $vista);
        include_once("assets/elements/scripts.php");
        exit();
    }

    if ($rol == "Administrador" && in_array($vista, $paginas_existentes)) {
        include_once("assets/elements/menu-lateral-administrador.php");
        include_once("view/html/" . $vista);
        include_once("assets/elements/scripts.php");
        exit();
    }
}

// si nada aplica, simplemente carga la vista normal
include_once("view/html/" . $vista);
include_once("assets/elements/scripts.php");
