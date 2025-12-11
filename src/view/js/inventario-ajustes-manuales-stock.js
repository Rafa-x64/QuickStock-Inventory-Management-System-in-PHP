import { api } from "/DEV/PHP/QuickStock/src/api/client/index.js";
import { realizarAjusteStock } from "/DEV/PHP/QuickStock/src/api/client/inventario-ajustes-manuales-stock.js";

// Obtener sucursal de sesión (inyectada desde PHP)
// Si es null, el usuario (Gerente) ve todas las sucursales
const sucursalSesion = window.ID_SUCURSAL_SESION || null;

// Estado de filtros - inicializar con sucursal de sesión
let filtrosActivos = {
    nombre: "",
    codigo: "",
    categoria: "",
    proveedor: "",
    sucursal: sucursalSesion || "", // Vacío = todas las sucursales
    estado: "true" // Solo activos por defecto
};

// Estado del ajuste en curso
let ajustePendiente = null;

document.addEventListener("DOMContentLoaded", () => {
    cargarOpcionesSelects();
    inicializarFiltros();
    // Intentar obtener sucursal de sesión (esto se podría mejorar trayéndolo del backend, 
    // por ahora asumimos que si el select de sucursal tiene valor, es ese).
    // Esperamos un poco a que carguen los selects para cargar productos
    setTimeout(() => {
        cargarProductos();
    }, 500);

    // Listener para confirmar ajuste
    document.getElementById("btn-confirmar-ajuste").addEventListener("click", confirmarAjuste);
});

function inicializarFiltros() {
    const addListener = (id, key) => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', (e) => {
                filtrosActivos[key] = e.target.value.trim();
                cargarProductos();
            });
        }
    };

    addListener("filtro_nombre", "nombre");
    addListener("filtro_codigo", "codigo");
    addListener("filtro_categoria", "categoria");
    addListener("filtro_proveedor", "proveedor");
    addListener("filtro_sucursal", "sucursal");

    document.getElementById("btn-reestablecer").addEventListener("click", () => {
        filtrosActivos = {
            nombre: "",
            codigo: "",
            categoria: "",
            proveedor: "",
            sucursal: sucursalSesion || "", // Volver a la sucursal de sesión
            estado: "true"
        };
        document.getElementById("form-filtros").reset();
        // Reseleccionar la sucursal de sesión en el select
        const selectSucursal = document.getElementById("filtro_sucursal");
        if (selectSucursal && sucursalSesion) {
            selectSucursal.value = sucursalSesion;
        }
        cargarProductos();
    });
}

function cargarOpcionesSelects() {
    const cargar = (id, accion, key, valueField, textField) => {
        const select = document.getElementById(id);
        api({ accion }).then(res => {
            const data = res[key] || res.filas || [];
            data.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item[valueField];
                opt.textContent = item[textField];
                select.appendChild(opt);
            });
            
            // Si es sucursal y hay una sucursal de sesión, preseleccionarla
            if (id === 'filtro_sucursal' && sucursalSesion) {
                select.value = sucursalSesion;
            }
        });
    };

    cargar("filtro_categoria", "obtener_categorias", "categorias", "id_categoria", "nombre");
    cargar("filtro_proveedor", "obtener_proveedores", "proveedor", "id_proveedor", "nombre");
    
    // Cargar sucursales con opción "Todas" para Gerente
    const sucursalEl = document.getElementById("filtro_sucursal");
    if (sucursalEl && sucursalEl.tagName === 'SELECT') {
        // Agregar opción "Todas las sucursales" solo si no hay sucursal de sesión (Gerente)
        if (!sucursalSesion) {
            const optTodas = document.createElement("option");
            optTodas.value = "";
            optTodas.textContent = "Todas las sucursales";
            sucursalEl.appendChild(optTodas);
        }
        cargar("filtro_sucursal", "obtener_sucursales", "filas", "id_sucursal", "nombre");
    }
}

function cargarProductos() {
    const tbody = document.getElementById("tbody_productos");
    tbody.innerHTML = '<tr><td colspan="8">Cargando...</td></tr>';

    // Usar la sucursal del filtro activo (que se inicializa con la sesión)
    // Si está vacío, el backend trae productos de todas las sucursales
    let sucursalParaConsulta = filtrosActivos.sucursal;
    
    // Si no hay filtro pero sí hay sesión, usar la sesión
    if (!sucursalParaConsulta && sucursalSesion) {
        sucursalParaConsulta = sucursalSesion;
    }
    // Si no hay sucursal (Gerente sin sucursal), envía vacío = todas las sucursales

    api({
        accion: "obtener_todos_los_productos",
        ...filtrosActivos,
        sucursal: sucursalParaConsulta || "" // Vacío = todas las sucursales
    }).then(res => {
        const productos = res.data || [];
        tbody.innerHTML = "";

        if (productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8">No se encontraron productos.</td></tr>';
            return;
        }

        productos.forEach(prod => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${prod.codigo || "-"}</td>
                <td>${prod.nombre || "-"}</td>
                <td>${prod.categoria_nombre || "-"}</td>
                <td>${prod.talla || "-"}</td>
                <td>${prod.color || "-"}</td>
                <td class="fw-bold fs-5 text-primary" id="stock-${prod.id_producto}">${prod.stock || 0}</td>
                <td>${prod.sucursal_nombre || "N/A"}</td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button class="btn btn-danger btn-sm btn-ajuste" data-action="salida" data-id="${prod.id_producto}" data-nombre="${prod.nombre}" data-sucursal="${prod.id_sucursal}">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm text-center input-cantidad" id="input-${prod.id_producto}" value="1" min="1" style="width: 60px;">
                        <button class="btn btn-success btn-sm btn-ajuste" data-action="entrada" data-id="${prod.id_producto}" data-nombre="${prod.nombre}" data-sucursal="${prod.id_sucursal}">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Adjuntar eventos a botones
        document.querySelectorAll(".btn-ajuste").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const btn = e.currentTarget;
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;
                const sucursal = btn.dataset.sucursal || sucursalParaConsulta; // Usar la del producto o la del filtro
                const tipo = btn.dataset.action;
                const input = document.getElementById(`input-${id}`);
                const cantidad = parseInt(input.value) || 1;

                if (cantidad <= 0) {
                    alert("La cantidad debe ser mayor a 0");
                    return;
                }

                iniciarAjuste(id, nombre, sucursal, tipo, cantidad);
            });
        });

    }).catch(err => {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="8" class="text-danger">Error al cargar productos.</td></tr>';
    });
}

function iniciarAjuste(id, nombre, sucursal, tipo, cantidad) {
    // Verificar stock negativo antes de abrir modal (UX rápida)
    if (tipo === 'salida') {
        const stockActualEl = document.getElementById(`stock-${id}`);
        const stockActual = parseInt(stockActualEl.textContent) || 0;
        if (stockActual - cantidad < 0) {
            alert(`No se puede reducir el stock por debajo de 0. Stock actual: ${stockActual}`);
            return;
        }
    }

    ajustePendiente = { id, nombre, sucursal, tipo, cantidad };

    // Llenar modal
    document.getElementById("modal_producto").textContent = nombre;
    document.getElementById("modal_tipo").textContent = tipo === 'entrada' ? 'Entrada (+)' : 'Salida (-)';
    document.getElementById("modal_tipo").className = tipo === 'entrada' ? 'text-success fw-bold' : 'text-danger fw-bold';
    document.getElementById("modal_cantidad").textContent = cantidad;
    
    // Nombre de sucursal (buscar en select o usar ID)
    const selectSucursal = document.getElementById("filtro_sucursal");
    const nombreSucursal = selectSucursal.options[selectSucursal.selectedIndex]?.text || "ID: " + sucursal;
    document.getElementById("modal_sucursal").textContent = nombreSucursal;

    document.getElementById("modal_motivo").value = "";
    document.getElementById("modal_comentario").value = "";

    const modal = new bootstrap.Modal(document.getElementById("modalAjuste"));
    modal.show();
}

async function confirmarAjuste() {
    if (!ajustePendiente) return;

    const motivo = document.getElementById("modal_motivo").value;
    const comentario = document.getElementById("modal_comentario").value;

    if (!motivo) {
        alert("Debe seleccionar un motivo.");
        return;
    }

    const params = {
        id_producto: ajustePendiente.id,
        id_sucursal: ajustePendiente.sucursal,
        cantidad: ajustePendiente.cantidad,
        tipo_ajuste: ajustePendiente.tipo,
        motivo: motivo,
        comentario: comentario
    };

    try {
        const res = await realizarAjusteStock(params);
        if (res.status === "success") {
            // Actualizar UI directamente
            const stockEl = document.getElementById(`stock-${ajustePendiente.id}`);
            if (stockEl) {
                stockEl.textContent = res.nuevo_stock;
                // Animación visual
                stockEl.classList.add("bg-warning");
                setTimeout(() => stockEl.classList.remove("bg-warning"), 1000);
            }
            
            // Cerrar modal
            const modalEl = document.getElementById("modalAjuste");
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // Limpiar pendiente
            ajustePendiente = null;
        } else {
            alert("Error: " + (res.message || "No se pudo realizar el ajuste."));
        }
    } catch (error) {
        console.error(error);
        alert("Error de conexión al realizar el ajuste.");
    }
}
