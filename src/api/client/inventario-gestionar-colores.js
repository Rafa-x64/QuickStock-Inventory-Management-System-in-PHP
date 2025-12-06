import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    cargarColores();
    
    // Filtro de búsqueda en tiempo real
    const inputSearch = document.getElementById("color_input");
    if(inputSearch){
        inputSearch.addEventListener("input", (e) => {
            cargarColores(e.target.value);
        });
    }

    // Listener para cancelar edición (botón cancelar)
    const btnCancelar = document.getElementById("btn_cancelar_edicion");
    if(btnCancelar){
        btnCancelar.addEventListener("click", () => {
             document.getElementById("formulario_edicion").classList.remove("d-block");
             document.getElementById("formulario_edicion").classList.add("d-none");
             document.getElementById("formulario_registro").classList.remove("d-none");
             document.getElementById("formulario_registro").classList.add("d-block");
             
             // Limpiar formulario de edición
             document.getElementById("form_editar_color").reset();
        });
    }
});

function cargarColores(filtro = "") {
    // Si queremos filtrar desde el cliente, podemos obtener todos y filtrar aqui, 
    // o si el endpoint soportara filtro, lo pasamos. Por ahora obtenemos todos.
    api({ accion: "obtener_colores" })
        .then(res => {
            if (res.error) {
                console.error("Error:", res.error);
                return;
            }
            renderizarTabla(res.colores || [], filtro);
        })
        .catch(err => console.error("Error API colores:", err));
}

function renderizarTabla(colores, filtro) {
    const tbody = document.getElementById("tabla_colores");
    tbody.innerHTML = "";

    const coloresFiltrados = colores.filter(c => 
        (c.nombre || "").toLowerCase().includes(filtro.toLowerCase())
    );

    if (coloresFiltrados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center">No se encontraron colores</td></tr>`;
        return;
    }

    coloresFiltrados.forEach(c => {
        const tr = document.createElement("tr");
        const activo = normalizeBoolean(c.activo);
        
        tr.innerHTML = `
            <td class="text-center">${c.id_color}</td>
            <td class="text-center">${c.nombre}</td>
            <td class="text-center">
                <span class="badge ${activo ? 'bg-success' : 'bg-danger'}">
                    ${activo ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm btn-editar" data-id="${c.id_color}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que desea eliminar éste color?');">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_color" value="${c.id_color}">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Asignar eventos a botones de editar
    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.addEventListener("click", () => {
             const id = btn.getAttribute("data-id");
             cargarDatosParaEditar(id);
        });
    });
}

function cargarDatosParaEditar(id) {
    api({ accion: "obtener_color_por_id", id_color: id })
        .then(res => {
            if (res && res.color) {
                mostrarFormularioEdicion(res.color);
            } else {
                alert("No se pudo cargar la información del color.");
            }
        })
        .catch(err => console.error(err));
}

function mostrarFormularioEdicion(color) {
    document.getElementById("formulario_registro").classList.remove("d-block");
    document.getElementById("formulario_registro").classList.add("d-none");
    
    const formEdicion = document.getElementById("formulario_edicion");
    formEdicion.classList.remove("d-none");
    formEdicion.classList.add("d-block");

    document.getElementById("id_color_editar").value = color.id_color;
    document.getElementById("nombre_color_editar").value = color.nombre;
    
    const activo = normalizeBoolean(color.activo);
    document.getElementById("activo_editar").value = activo ? "activo" : "inactivo";
    
    // Scroll hacia el formulario
    formEdicion.scrollIntoView({ behavior: 'smooth' });
}

function normalizeBoolean(val) {
    return val === true || val === "t" || val === "true" || val === 1;
}
