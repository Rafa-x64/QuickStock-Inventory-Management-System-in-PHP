import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";

document.addEventListener("DOMContentLoaded", () => {
    cargarTallas();
    
    const inputSearch = document.getElementById("talla_input");
    if(inputSearch){
        inputSearch.addEventListener("input", (e) => {
            cargarTallas(e.target.value);
        });
    }

    const btnCancelar = document.getElementById("btn_cancelar_edicion");
    if(btnCancelar){
        btnCancelar.addEventListener("click", () => {
             document.getElementById("formulario_edicion").classList.remove("d-block");
             document.getElementById("formulario_edicion").classList.add("d-none");
             document.getElementById("formulario_registro").classList.remove("d-none");
             document.getElementById("formulario_registro").classList.add("d-block");
             document.getElementById("form_editar_talla").reset();
        });
    }
});

function cargarTallas(filtro = "") {
    api({ accion: "obtener_tallas" })
        .then(res => {
            if (res.error) {
                console.error("Error:", res.error);
                return;
            }
            renderizarTabla(res.tallas || [], filtro);
        })
        .catch(err => console.error("Error API tallas:", err));
}

function renderizarTabla(tallas, filtro) {
    const tbody = document.getElementById("tabla_tallas");
    tbody.innerHTML = "";

    const tallasFiltradas = tallas.filter(t => 
        (t.rango_talla || "").toLowerCase().includes(filtro.toLowerCase())
    );

    if (tallasFiltradas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center">No se encontraron tallas</td></tr>`;
        return;
    }

    tallasFiltradas.forEach(t => {
        const tr = document.createElement("tr");
        const activo = normalizeBoolean(t.activo);
        
        tr.innerHTML = `
            <td class="text-center">${t.id_talla}</td>
            <td class="text-center">${t.rango_talla}</td>
            <td class="text-center">
                <span class="badge ${activo ? 'bg-success' : 'bg-danger'}">
                    ${activo ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm btn-editar" data-id="${t.id_talla}">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que desea eliminar esta talla?');">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_talla" value="${t.id_talla}">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.addEventListener("click", () => {
             const id = btn.getAttribute("data-id");
             cargarDatosParaEditar(id);
        });
    });
}

function cargarDatosParaEditar(id) {
    api({ accion: "obtener_talla_por_id", id_talla: id })
        .then(res => {
            if (res && res.talla) {
                mostrarFormularioEdicion(res.talla);
            } else {
                alert("No se pudo cargar la información de la talla.");
            }
        })
        .catch(err => console.error(err));
}

function mostrarFormularioEdicion(talla) {
    document.getElementById("formulario_registro").classList.remove("d-block");
    document.getElementById("formulario_registro").classList.add("d-none");
    
    const formEdicion = document.getElementById("formulario_edicion");
    formEdicion.classList.remove("d-none");
    formEdicion.classList.add("d-block");

    document.getElementById("id_talla_editar").value = talla.id_talla;
    document.getElementById("rango_talla_editar").value = talla.rango_talla;
    
    const activo = normalizeBoolean(talla.activo);
    document.getElementById("activo_editar").value = activo ? "activo" : "inactivo";
    
    formEdicion.scrollIntoView({ behavior: 'smooth' });
}

function normalizeBoolean(val) {
    return val === true || val === "t" || val === "true" || val === 1;
}
