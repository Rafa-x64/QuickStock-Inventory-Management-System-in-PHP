<style>

</style>
<div class="container-fluid">
    <div class="row inicio-sesion-usuario m-1 m-lg-3">
        <div class="col-6 inicio-sesion-usuario-left d-none d-md-block m-0">

        </div>
        <div class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center inicio-sesion-usuario-right my-3 my-md-4 my-lg-5 p-0">
            <div class="row w-100">
                <div class="col-12 d-flex flex-column justify-content-center align-items-center mt-3">
                    <h3 class="Quick-title text-uppercase p-0 m-0">Inicia Sesion</h3>
                </div>
                <div class="col-12 mt-3 pt-3 px-md-2 px-md-5">
                    <form action="" method="POST" class="d-flex flex-column justify-content-center align-items-center needs-validation" novalidate>
                        <div class="row w-100">
                            <div class="col-12 p-0 position-relative">
                                <label for="usuario_correo" class="form-label">Correo</label>
                                <input type="email" name="usuario_correo" id="usuario_correo" class="form-control inicio-sesion-usuario-custom-input" required>
                                <div class="invalid-tooltip">
                                    El correo es incorrecto.
                                </div>
                            </div>
                            <div class="col-12 p-0 mt-3 position-relative">
                                <label for="usuario_contraseña" class="form-label">Contraseña</label>
                                <input type="password" name="usuario_contraseña" id="usuario_contraseña" class="form-control inicio-sesion-usuario-custom-input" required>
                                <div class="invalid-tooltip">
                                    La contraseña es incorrecta.
                                </div>
                            </div>
                            <div class="col-12 mt-4 d-flex flex-row justify-content-end align-items-center">
                                <button type="reset" class="btn Quick-title inicio-sesion-usuario-limpiar-btn">Limpiar</button>
                                <button type="submit" class="btn Quick-title inicio-sesion-usuario-acceder-btn">Acceder</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once "controller/inicio_sesion_C.php";

    $resultado = inicioSesionC::validarAccesso($_POST);

    // Verificar si el login fue exitoso (formato: "exito:NombreRol")
    if (strpos($resultado, "exito:") === 0) {
        $nombre_rol = substr($resultado, 6); // Extraer el nombre del rol

        // Redirigir según el rol
        switch ($nombre_rol) {
            case "Gerente":
                echo "<script>window.location.href = 'dashboard-gerente';</script>";
                break;
            case "Administrador":
            case "Cajero":
            case "Encargado":
            case "Vendedor":
            case "Depositario":
            default:
                echo "<script>window.location.href = 'dashboard-empleado';</script>";
                break;
        }
    } else {
        // Manejar errores
        switch ($resultado) {
            case "error de correo":
                echo "<script>alert('Correo invalido. Por favor, intente nuevamente.');</script>";
                break;
            case "error de contraseña":
                echo "<script>alert('Contraseña invalida. Por favor, intente nuevamente.');</script>";
                break;
            case "error al iniciar sesion":
                echo "<script>alert('Error durante inicio de sesion. Por favor, intente nuevamente.');</script>";
                break;
            default:
                echo "<script>alert('Error desconocido. Por favor, intente nuevamente.');</script>";
                break;
        }
    }
};
?>
<script src="view/html/inicio-sesion.js"></script>