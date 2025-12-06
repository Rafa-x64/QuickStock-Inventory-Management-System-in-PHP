<style>

</style>
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
        <h6>Menu Empleado</h6>
        <nav>
            <ul class="">
                <li class="list-group-item pt-2">
                    <a href="dashboard-empleado" class="d-flex flex-row align-items-center text-decoration-none Quick-white-link">
                        <i class="bi bi-house fs-5"></i>
                        <p class="m-0 ms-2">Dashboard</p>
                    </a>
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
                <a href="dashboard-empleado" class="d-flex flex-row align-items-center Quick-white-link menu-details-link">
                    <i class="bi bi-house fs-5"></i>
                    <p class="texto-menu">Dashboard</p>
                </a>
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