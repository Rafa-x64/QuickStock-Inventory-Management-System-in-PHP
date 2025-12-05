<!--menu para disposiciones mdianas y pequeñas-->
<button
    class="btn btn-secondary menu-flotante d-lg-none"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#offcanvasMenu"
    aria-controls="offcanvasMenu">
    <i class="bi bi-list fs-5"></i>
</button>


<div class="offcanvas offcanvas-start menu-sm-lateral-gerente" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <div class="offcanvas-title" id="offcanvasMenuLabel"></div>
        <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="btn-close"></button>
    </div>

    <div class="offcanvas-body">
        <h6>Menu Administrador</h6>
        <nav>
            <ul class="">
                <li class="list-group-item pt-2">
                    <a href="dashboard-gerente" class="d-flex flex-row align-items-center text-decoration-none Quick-white-link">
                        <i class="bi bi-house fs-5"></i>
                        <p class="m-0 ms-2">Dashboard</p>
                    </a>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-box2 fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Inventario</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="inventario-ver-productos" class="ms-3">Ver Productos</a>
                                <a href="inventario-añadir-producto" class="ms-3">Añadir Productos</a>
                                <a href="inventario-gestionar-categorias" class="ms-3">Gestionar Categorias</a>
                                <a href="inventario-ajustes-manuales-stock" class="ms-3">Ajustes Manuales de Stock</a>
                                <a href="inventario-stock-bajo" class="ms-3">Alertas de Stock Bajo</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-shop fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Sucursales</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="sucursales-listado" class="ms-3">Listado de Sucursales</a>
                                <a href="sucursales-detalle" class="ms-3">Detalle de Sucursal</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-bag-plus fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Compras</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="compras-historial" class="ms-3">Historial de Compras</a>
                                <a href="compras-detalle" class="ms-3">Detalle de Compra</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-cash-coin fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Ventas</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="ventas-historial-facturas" class="ms-3">Historial de Facturas</a>
                                <a href="ventas-productos-populares" class="ms-3">Productos mas Vendidos</a>
                                <a href="ventas-añadir-metodo-pago" class="ms-3">Añadir Metodo de Pago</a>
                                <a href="ventas-lista-metodos-pago" class="ms-3">Listado de Metodos de pago</a>
                                <a href="ventas-detalle-metodo-pago" class="ms-3">Detalle Metodo de Pago</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-person-badge fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Clientes</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="clientes-ver-listado-clientes" class="ms-3">Ver listado de Clientes</a>
                                <a href="clientes-gestionar-clientes" class="ms-3">Gestionar Clientes</a>
                                <a href="clientes-listado" class="ms-3">Ver listado de Clientes</a>
                                <a href="clientes-detalle" class="ms-3">Detalle Cliente</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-truck fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Proveedores</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="proveedores-listado" class="ms-3">lista de Proveedores</a>
                                <a href="proveedores-añadir" class="ms-3">Añadir Proveedor</a>
                                <a href="proveedores-detalles" class="ms-3">Detalles Proveedor</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-person-lines-fill fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Empleados</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="empleados-listado" class="ms-3">Listado de Empleados</a>
                                <a href="empleados-añadir" class="ms-3">Añadir Empleado</a>
                                <a href="empleados-detalle" class="ms-3">Detalle Empleado</a>
                                <a href="empleados-añadir-rol" class="ms-3">Añadir Rol</a>
                                <a href="empleados-lista-roles" class="ms-3">Lista de Roles</a>
                                <a href="empleados-detalle-rol" class="ms-3">Detalle Rol</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-file-earmark-text fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Contabilidad y Reportes</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="#" class="ms-3">Reportes de Rotacion</a>
                                <a href="#" class="ms-3">Reporte de Ventas</a>
                                <a href="#" class="ms-3">Reporte de Inventario</a>
                                <a href="#" class="ms-3">Reporte de Financiero</a>
                                <a href="#" class="ms-3">Reporte de Auditoria</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <div class="col-12 p-0 m-0 menu-item">
                        <details class="menu-details">
                            <summary class="d-flex flex-row align-items-center Quick-white-link">
                                <i class="bi bi-currency-exchange fs-6"></i>
                                <p class="texto-menu m-0 ms-2">Monedas y Tasas</p>
                            </summary>
                            <div class="menu-submenu d-flex flex-column ps-2">
                                <a href="monedas-tasas-activas" class="ms-3">Ver Tasas Activas</a>
                                <a href="monedas-añadir-tasas" class="ms-3">Añadir Tasas</a>
                                <a href="monedas-historial" class="ms-3">Historial de Cambios en Tasas</a>
                                <a href="monedas-añadir" class="ms-3">Añadir Monedas</a>
                                <a href="monedas-listado" class="ms-3">Ver Lista de Monedas</a>
                            </div>
                        </details>
                    </div>
                </li>
                <li class="list-group-item pt-2">
                    <a href="configuracion-cuenta" class="d-flex flex-row align-items-center text-decoration-none Quick-white-link">
                        <i class="bi bi-person-gear fs-5"></i>
                        <p class="m-0 ms-2">Configurar Cuenta</p>
                    </a>
                </li>
                <li class="list-group-item pt-2">
                    <a href="inicio" class="d-flex flex-row align-items-center text-decoration-none Quick-white-link">
                        <i class="bi bi-box-arrow-left fs-5"></i>
                        <p class="m-0 ms-2">Salir</p>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="mt-4 mt-auto">
            <p class="Quick-title text-center p-0 m-0">QuickStock © 2025</p>
        </div>
    </div>
</div>

<!--menu para disposiciones grandes-->
<div class="col-2 col-md-1 h-100 p-0 m-0 d-none d-lg-block position-fixed menu-lateral-gerente" id="menuLateral">
    <div class="row h-100 w-100 p-0 m-0 d-flex flex-column justify-content-start align-items-start">

        <div class="col-12 p-0 p-2 m-0 d-flex flex-row justify-content-end align-items-center">
            <button type="button" class="btn Quick-white-btn" id="botonToggle">
                <i class="bi bi-three-dots-vertical fs-6"></i>
            </button>
        </div>

        <div class="col-12 p-0 d-column flex-row justify-content-center align-items-center">
            <a href="" class="d-flex flex-column text-white">
                <div class="row p-0 m-0 d-flex flex-column justify-content-center align-items-center">
                    <div class="col-12 p-3 d-flex justify-content-center">
                        <img src="<?php echo SERVERURL ?>/assets/images/example-menu-lateral-imagen-usuario.jpg" alt="" class="img-fluid imagen-usuario">
                    </div>
                    <div class="col-12 p-0">
                        <p class="fs-6 text-center fw-bold texto-menu" id="nombre_menu_grande"></p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 p-0 menu-items-scrollable">

            <div class="col-12 p-0 mt-2 menu-item">
                <a href="dashboard-gerente" class="d-flex flex-row align-items-center Quick-white-link menu-details-link">
                    <i class="bi bi-house fs-5"></i>
                    <p class="texto-menu">Dashboard</p>
                </a>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-box2 fs-5"></i>
                        <p class="texto-menu">Inventario</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="inventario-ver-productos" class="ms-3">Ver Productos</a>
                        <a href="inventario-añadir-producto" class="ms-3">Añadir Productos</a>
                        <a href="inventario-gestionar-categorias" class="ms-3">Gestionar Categorias</a>
                        <a href="inventario-ajustes-manuales-stock" class="ms-3">Ajustes Manuales de Stock</a>
                        <a href="inventario-stock-bajo" class="ms-3">Alertas de Stock Bajo</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-shop fs-5"></i>
                        <p class="texto-menu">Sucursales</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="sucursales-listado" class="ms-3">Listado de Sucursales</a>
                        <a href="sucursales-detalle" class="ms-3">Detalle de Sucursal</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-bag-plus fs-5"></i>
                        <p class="texto-menu">Compras</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="compras-historial" class="ms-3">Historial de Compras</a>
                        <a href="compras-detalle" class="ms-3">Detalle de Compra</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-cash-coin fs-5"></i>
                        <p class="texto-menu">Ventas</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="ventas-historial-facturas" class="ms-3">Historial de Facturas</a>
                        <a href="ventas-productos-populares" class="ms-3">Productos mas Vendidos</a>
                        <a href="ventas-añadir-metodo-pago" class="ms-3">Añadir Metodo de Pago</a>
                        <a href="ventas-lista-metodos-pago" class="ms-3">Listado de Metodos de pago</a>
                        <a href="ventas-detalle-metodo-pago" class="ms-3">Detalle Metodo de Pago</a>
                    </div>
                </details>
            </div>
            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-person-badge fs-5"></i>
                        <p class="texto-menu">Clientes</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="clientes-listado" class="ms-3">Ver listado de Clientes</a>
                        <a href="clientes-detalle" class="ms-3">Detalle Cliente</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-truck fs-5"></i>
                        <p class="texto-menu">Proveedores</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="proveedores-listado" class="ms-3">lista de Proveedores</a>
                        <a href="proveedores-añadir" class="ms-3">Añadir Proveedor</a>
                        <a href="proveedores-detalles" class="ms-3">Detalles Proveedor</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-person-lines-fill fs-5"></i>
                        <p class="texto-menu">Empleados</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="empleados-listado" class="ms-3">Listado de Empleados</a>
                        <a href="empleados-añadir" class="ms-3">Añadir Empleado</a>
                        <a href="empleados-detalle" class="ms-3">Detalle Empleado</a>
                        <a href="empleados-añadir-rol" class="ms-3">Añadir Rol</a>
                        <a href="empleados-lista-roles" class="ms-3">Lista de Roles</a>
                        <a href="empleados-detalle-rol" class="ms-3">Detalle Rol</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                        <p class="texto-menu">Contabilidad y Reportes</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="#" class="ms-3">Reportes de Rotacion</a>
                        <a href="#" class="ms-3">Reporte de Ventas</a>
                        <a href="#" class="ms-3">Reporte de Inventario</a>
                        <a href="#" class="ms-3">Reporte de Financiero</a>
                        <a href="#" class="ms-3">Reporte de Auditoria</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <details class="menu-details">
                    <summary class="d-flex flex-row align-items-center Quick-white-link">
                        <i class="bi bi-currency-exchange fs-5"></i>
                        <p class="texto-menu">Monedas y Tasas</p>
                    </summary>
                    <div class="menu-submenu d-flex flex-column ps-5">
                        <a href="monedas-tasas-activas" class="ms-3">Ver Tasas Activas</a>
                        <a href="monedas-añadir-tasas" class="ms-3">Añadir Tasas</a>
                        <a href="monedas-historial" class="ms-3">Historial de Cambios en Tasas</a>
                        <a href="monedas-añadir" class="ms-3">Añadir Monedas</a>
                        <a href="monedas-listado" class="ms-3">Ver Lista de Monedas</a>
                    </div>
                </details>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <a href="configuracion-cuenta" class="d-flex flex-row align-items-center Quick-white-link menu-details-link">
                    <i class="bi bi-person-gear fs-5"></i>
                    <p class="texto-menu">Configurar Cuenta</p>
                </a>
            </div>

            <div class="col-12 p-0 mt-2 menu-item">
                <a href="inicio" class="d-flex flex-row align-items-center Quick-white-link menu-details-link">
                    <i class="bi bi-box-arrow-left fs-5"></i>
                    <p class="texto-menu">Salir</p>
                </a>
            </div>

        </div>

        <div class="col-12 d-flex flex-column justify-content-end align-items-center p-0 mt-auto mb-1">
            <p class="Quick-title text-center m-0 p-0">QuickStock © 2025</p>
        </div>

    </div>
</div>

<script type="module" src="api/client/menu-lateral.js"></script>