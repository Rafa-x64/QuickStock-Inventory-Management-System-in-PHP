<?php
include_once "model/seguridad_acceso.usuario.php";
include_once "controller/configuracion_cuenta_C.php";

// Obtener ID del usuario de la sesión
$id_usuario = $_SESSION['sesion_usuario']['usuario']['id_usuario'] ?? null;

if (!$id_usuario) {
    echo "<script>window.location.href = 'inicio';</script>";
    exit();
}

// Obtener datos frescos de la BD
$datosUsuario = usuario::obtenerDatosUsuario($id_usuario);
$statsUsuario = usuario::obtenerEstadisticas($id_usuario);

if (!$datosUsuario) {
    echo "<div class='alert alert-danger'>Error al cargar datos del usuario.</div>";
    exit();
}

// Procesar formularios
$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "actualizar_perfil") {
        $resp = configuracion_cuenta_C::actualizarPerfil($_POST);
        if (isset($resp["success"])) {
            $mensaje = $resp["mensaje"];
            $tipoMensaje = "success";
            // Refrescar datos
            $datosUsuario = usuario::obtenerDatosUsuario($id_usuario);
        } else {
            $mensaje = $resp["error"];
            $tipoMensaje = "danger";
        }
    } elseif ($accion === "cambiar_pass") {
        $resp = configuracion_cuenta_C::cambiarContrasena($_POST);
        if (isset($resp["success"])) {
            $mensaje = $resp["mensaje"];
            $tipoMensaje = "success";
        } else {
            $mensaje = $resp["error"];
            $tipoMensaje = "danger";
        }
    }
}
?>

<style>
    .profile-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }

    .nav-pills .nav-link {
        color: #495057;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 500;
    }

    .stat-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="container-fluid" id="mainContent">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 p-4">

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Header Perfil -->
            <div class="profile-header d-flex align-items-center shadow-sm">
                <div class="me-4">
                    <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                </div>
                <div>
                    <h2 class="m-0"><?php echo $datosUsuario['nombre'] . ' ' . $datosUsuario['apellido']; ?></h2>
                    <p class="m-0 opacity-75"><?php echo $datosUsuario['nombre_rol']; ?> - <?php echo $datosUsuario['nombre_sucursal'] ?? 'Sin Sucursal'; ?></p>
                </div>
            </div>

            <div class="row">
                <!-- Menú Lateral -->
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body p-2">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active text-start py-3 px-4" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                                    <i class="bi bi-person-lines-fill me-2"></i> Datos Personales
                                </button>
                                <button class="nav-link text-start py-3 px-4" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab">
                                    <i class="bi bi-shield-lock me-2"></i> Seguridad
                                </button>
                                <button class="nav-link text-start py-3 px-4" id="v-pills-info-tab" data-bs-toggle="pill" data-bs-target="#v-pills-info" type="button" role="tab">
                                    <i class="bi bi-info-circle me-2"></i> Información de Cuenta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="col-md-9">
                    <div class="tab-content" id="v-pills-tabContent">

                        <!-- Pestaña Datos Personales -->
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3">
                                    <h5 class="m-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</h5>
                                </div>
                                <div class="card-body p-4">
                                    <form action="" method="POST" id="formPerfil" class="needs-validation" novalidate>
                                        <input type="hidden" name="accion" value="actualizar_perfil">

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nombre" class="form-label">Nombre</label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $datosUsuario['nombre']; ?>" required>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="apellido" class="form-label">Apellido</label>
                                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $datosUsuario['apellido']; ?>" required>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="cedula" class="form-label">Cédula (No editable)</label>
                                                <input type="text" class="form-control bg-light" value="<?php echo $datosUsuario['cedula']; ?>" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="telefono" class="form-label">Teléfono</label>
                                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $datosUsuario['telefono']; ?>" required>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="email" class="form-label">Correo Electrónico</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $datosUsuario['email']; ?>" required>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="direccion" class="form-label">Dirección</label>
                                                <textarea class="form-control" id="direccion" name="direccion" rows="3" required><?php echo $datosUsuario['direccion']; ?></textarea>
                                                <div class="invalid-tooltip"></div>
                                            </div>

                                            <div class="col-12 mt-4 text-end">
                                                <button type="submit" class="btn btn-primary px-4">
                                                    <i class="bi bi-save me-2"></i>Guardar Cambios
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Seguridad -->
                        <div class="tab-pane fade" id="v-pills-security" role="tabpanel">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3">
                                    <h5 class="m-0 text-primary"><i class="bi bi-key me-2"></i>Cambiar Contraseña</h5>
                                </div>
                                <div class="card-body p-4">
                                    <form action="" method="POST" id="formPassword" class="needs-validation" novalidate>
                                        <input type="hidden" name="accion" value="cambiar_pass">

                                        <div class="mb-3">
                                            <label for="pass_actual" class="form-label">Contraseña Actual</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="pass_actual" name="pass_actual" required>
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass_actual">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="pass_nueva" class="form-label">Nueva Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="pass_nueva" name="pass_nueva" required>
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass_nueva">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                            <div class="form-text">Mínimo 6 caracteres.</div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="pass_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="pass_confirm" name="pass_confirm" required>
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="pass_confirm">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <div class="invalid-tooltip"></div>
                                            </div>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-warning px-4">
                                                <i class="bi bi-lock-fill me-2"></i>Actualizar Contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Información -->
                        <div class="tab-pane fade" id="v-pills-info" role="tabpanel">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white py-3">
                                    <h5 class="m-0 text-primary"><i class="bi bi-card-list me-2"></i>Detalles de la Cuenta</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="info-label">ID Usuario</div>
                                                <div class="info-value text-dark"><?php echo $datosUsuario['id_usuario']; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="info-label">Fecha de Registro</div>
                                                <div class="info-value text-dark"><?php echo $datosUsuario['fecha_registro']; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="info-label">Rol Asignado</div>
                                                <div class="info-value text-primary">
                                                    <span class="badge bg-primary"><?php echo $datosUsuario['nombre_rol']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="info-label">Sucursal</div>
                                                <div class="info-value text-dark"><?php echo $datosUsuario['nombre_sucursal'] ?? 'N/A'; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="info-label">Estado</div>
                                                <div class="info-value">
                                                    <?php
                                                    $activo = $datosUsuario['activo'] === 't' || $datosUsuario['activo'] === true;
                                                    ?>
                                                    <span class="badge bg-<?php echo $activo ? 'success' : 'danger'; ?>">
                                                        <?php echo $activo ? 'Activo' : 'Inactivo'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3">
                                    <h5 class="m-0 text-primary"><i class="bi bi-graph-up me-2"></i>Estadísticas</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card stat-card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="text-muted mb-2">Ventas Realizadas</h6>
                                                    <h3 class="mb-0 text-primary fw-bold"><?php echo $statsUsuario['ventas_realizadas']; ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="view/js/configuracion-cuenta.js"></script>