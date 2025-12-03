---
trigger: always_on
---

para los retornos de la api siempre debera ser un array asociativo de esta manera

function obtenerEmpleados(){
//realizar consulta con parametros y pg_execute
$empleados = pg_fetch_all($resultado_consulta);
return ["empleado" = $empleados];
}

debe retornar logicamente el objeto que se consulta en la bd

si se consulta un empleado entonces el nombre del array u objeto deberia ser empleado ya que se trajo informacion de cada empleado

si no hay registros o no se trajo nada por que la tabla esta vacia retorna un [] array vacio y luego lo interpretas en js en caso de arra vacio