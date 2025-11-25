---
trigger: always_on
---

deberas realizar el mvc de la siguiente manera

es la vista debes incluir el controlador en un fragmento de codigo php que depure si hay errores y los muestre por alertas o consola

1._vista: siempre debe contener esta estructura para mantener el funcionamiento ideal de la vista y los menus... al editar la vista debes respetar mis clases css Quick-* y mantener la responsividad en toda la vista. la vista y sus campos deben estar de manera logica y relacionados a las columnas que requieren rellenado en la base de datos

<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
</div>
</div>
</div>

controlador: instancia la clase para utilizar los metodos de esta, valida y sanitiza los campos recibidos de la matriz post enviada por la vista y devuelve errores si los hay

<?php 
include_once "model/core.sucursal.php";
class sucursales_añadir_C extends mainModel{

    public static function agregarSucursal($formulario)
    {
        $rif = strtoupper(trim($formulario['rif_sucursal']));
        $nombre = ucwords(trim($formulario['nombre_sucursal']));
        $direccion = trim($formulario['direccion_sucursal']);
        if (!empty($direccion)) {
            $direccion = ucwords($direccion);
        } else {
            $direccion = null;
        }
        $telefono = trim($formulario['telefono_sucursal']);
        $fecha_registro = trim($formulario['fecha_registro']);

        if (sucursal::existeSucursalPorNombre($nombre)) {
            return -1;
        }

        $nuevaSucursal = new sucursal(
            $rif,
            $nombre,
            $direccion,
            $telefono,
            $fecha_registro
        );

        $resultado = $nuevaSucursal->crear();

        return $resultado;
    }

}
?>

modelo: clase que interactua con la base de datos y hereda de mainModel para poder usar los metodos de conexion, encriptacion etcetera... realiza la consulta sql y puede devolver mensajes de error o true y false

ejemplo:

vista:
<div class="container-fluid" id="mainContent">
    <div class="row d-flex flex-column justify-content-center align-items-center">
        <div class="col-12 p-3 p-lg-5">
            <div class="row d-flex flex-row justify-content-center align-items-center">
                <div class="col-12 p-5 Quick-title">
                    <h1 class="m-0 p-0">Registrar Nueva Sucursal</h1>
                </div>

                <div class="Quick-widget col-12 col-md-8 p-0 p-2">
                    <div class="col-12 Quick-form px-4 rounded-2">
                        <form action="" method="POST" class="form py-3 needs-validate" id="formSucursal" novalidate>

                            <div class="row d-flex flex-row justify-content-center align-items-center">

                                <!-- Nombre de la Sucursal -->
                                <div class="col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="nombre_sucursal" class="form-label Quick-title">Nombre de la Sucursal</label>
                                    <input type="text" id="nombre_sucursal" name="nombre_sucursal" class="Quick-form-input" maxlength="100" placeholder="Ej: QuickStock Central" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- RIF  -->
                                <div class="col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="rif_sucursal" class="form-label Quick-title">Numero de RIF</label>
                                    <input type="text" id="rif_sucursal" name="rif_sucursal" class="Quick-form-input" maxlength="100" placeholder="Ej: J-12345678-9" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-12 d-flex flex-column py-3 position-relative">
                                    <label for="direccion_sucursal" class="form-label Quick-title">Dirección</label>
                                    <textarea id="direccion_sucursal" name="direccion_sucursal" class="Quick-form-input" rows="3" maxlength="255" placeholder="Ingrese la dirección completa..." required></textarea>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="telefono_sucursal" class="form-label Quick-title">Teléfono</label>
                                    <input type="tel" id="telefono_sucursal" name="telefono_sucursal" class="Quick-form-input" maxlength="50" placeholder="+58 412-5551234" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Fecha de registro -->
                                <div class="col-md-6 d-flex flex-column py-3 position-relative">
                                    <label for="fecha_registro" class="form-label Quick-title">Fecha de Registro</label>
                                    <input type="date" id="fecha_registro" name="fecha_registro" class="Quick-form-input" required>
                                    <div class="invalid-tooltip"></div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 d-flex flex-row justify-content-center align-items-center py-3">
                                    <div class="row w-100 d-flex justify-content-around">
                                        <div class="col-5 col-md-3 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success w-100">Registrar</button>
                                        </div>
                                        <div class="col-5 col-md-3 d-flex justify-content-center">
                                            <button type="reset" class="btn btn-danger w-100">Limpiar</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                include_once "controller/sucursales_añadir_C.php";

                $resultado = sucursales_añadir_C::agregarSucursal($_POST);

                if ($resultado === -1) {
                    echo '<script>alert("Esta sucursal ya existe. Intente con otro nombre.");</script>';
                    exit();
                }

                if (!$resultado) {
                    echo '<script>alert("Error al crear la sucursal. Por favor, intente nuevamente.");</script>';
                    exit();
                }

                echo '<script>alert("Sucursal creada correctamente");</script>';
                echo '<script>window.location.href = "sucursales-listado";</script>';
            }
            ?>
        </div>
    </div>
</div>

<script src="view/js/sucursales-añadir.js"></script>

controlador (debe incluir el modelo para poder seguir el flujo):
<?php 
include_once "model/core.sucursal.php";
class sucursales_añadir_C extends mainModel{

    public static function agregarSucursal($formulario)
    {
        $rif = strtoupper(trim($formulario['rif_sucursal']));
        $nombre = ucwords(trim($formulario['nombre_sucursal']));
        $direccion = trim($formulario['direccion_sucursal']);
        if (!empty($direccion)) {
            $direccion = ucwords($direccion);
        } else {
            $direccion = null;
        }
        $telefono = trim($formulario['telefono_sucursal']);
        $fecha_registro = trim($formulario['fecha_registro']);

        if (sucursal::existeSucursalPorNombre($nombre)) {
            return -1;
        }

        $nuevaSucursal = new sucursal(
            $rif,
            $nombre,
            $direccion,
            $telefono,
            $fecha_registro
        );

        $resultado = $nuevaSucursal->crear();

        return $resultado;
    }

}
?>
<?php
class sucursal extends mainModel
{
    public $rif;
    public $nombre;
    public $direccion;
    public $telefono;
    public $fecha_registro; // Asegúrate de que este atributo existe

    public function __construct($rif, $nombre, $direccion = null, $telefono, $fecha_registro)
    {
        $this->rif = $rif;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->fecha_registro = $fecha_registro ?? date('Y-m-d');
    }


    public function crear()
    {
        $conn = parent::conectar_base_datos();

        pg_prepare(
            $conn,
            "agregar_sucursal",
            "INSERT INTO core.sucursal (nombre, direccion, telefono, rif, activo, fecha_registro) 
            VALUES ($1, $2, $3, $4, 't', $5)"
        );

        $resultado = pg_execute($conn, "agregar_sucursal", [
            $this->nombre,
            $this->direccion,
            $this->telefono,
            $this->rif,
            $this->fecha_registro
        ]);

        if (!$resultado) {
            return false;
        }
        return true;
    }

    public static function editar($id_sucursal, $nombre, $rif, $direccion, $telefono, $activo)
    {
        $conn = parent::conectar_base_datos();

        // Convertir el valor de 'activo' de PHP a un booleano de PostgreSQL ('t' o 'f')
        $activo_db = (strtolower($activo) === 'true' || $activo === true || $activo === 1) ? 't' : 'f';

        pg_prepare(
            $conn,
            "actualizar_sucursal_estatica",
            "UPDATE core.sucursal SET 
                nombre = $1, 
                direccion = $2, 
                telefono = $3, 
                rif = $4, 
                activo = $5 
            WHERE id_sucursal = $6"
        );

        $resultado = pg_execute($conn, "actualizar_sucursal_estatica", [
            $nombre,
            $direccion,
            $telefono,
            $rif,
            $activo_db,
            $id_sucursal // Clave para la edición
        ]);

        return (bool)$resultado;
    }

    public static function eliminar($id_sucursal): bool
    {
        $conn = parent::conectar_base_datos();

        if ($id_sucursal <= 0) {
            return false;
        }

        $sql = "UPDATE core.sucursal 
            SET activo = 'f' 
            WHERE id_sucursal = $1";

        $params = [$id_sucursal];

        $statement_name = "desactivar_sucursal_" . time();
        $stmt = pg_prepare($conn, $statement_name, $sql);
        $result = pg_execute($conn, $statement_name, $params);

        return $result !== false && pg_affected_rows($result) > 0;
    }

    public static function existeSucursalPorNombre($nombre)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_sucursal_nombre_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SELECT COUNT(nombre) FROM core.sucursal WHERE nombre = $1"
        );

        $resultado = pg_execute($conn, $queryName, [$nombre]);

        if (!$resultado) {
            return true;
        }

        $fila = pg_fetch_assoc($resultado);

        return intval($fila['count']) > 0;
    }

    public static function existeSucursalPorNombreYIdDiferente($nombre, $id_sucursal)
    {
        $conn = parent::conectar_base_datos();
        $queryName = "check_sucursal_nombre_update_" . uniqid();

        pg_prepare(
            $conn,
            $queryName,
            "SEL