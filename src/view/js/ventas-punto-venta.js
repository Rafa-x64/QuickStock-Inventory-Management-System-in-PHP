import { ventasAPI } from "../../api/client/ventas-punto-venta.js";
import { api } from "../../api/client/index.js"; // Import generic api for catalogs

document.addEventListener("DOMContentLoaded", () => {
    // --- Referencias DOM ---
    const wizardSteps = document.querySelectorAll(".wizard-step");
    const navLinks = document.querySelectorAll("#wizard-steps .nav-link");
    const progressBar = document.querySelector(".progress-bar");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.getElementById("submitBtn");

    // Inputs Paso 0 (Cliente)
    const clienteCedula = document.getElementById("cliente_cedula");
    const clienteNombre = document.getElementById("cliente_nombre");
    const clienteApellido = document.getElementById("cliente_apellido");
    const clienteEmail = document.getElementById("cliente_email");
    const clienteTelefono = document.getElementById("cliente_telefono");
    const clienteDireccion = document.getElementById("cliente_direccion");

    // Inputs Paso 1 (General)
    const idSucursalHidden = document.getElementById("idSucursalHidden");
    const idUsuarioSelect = document.getElementById("idUsuario");
    const idMonedaSelect = document.getElementById("idMoneda");
    // const fechaHoraInput = document.getElementById("fechaHora"); // Readonly now

    // Inputs Paso 2 (Productos)
    const prodCodigoBarra = document.getElementById("prod_codigo_barra");
    const prodCategoria = document.getElementById("prod_categoria");
    const prodColor = document.getElementById("prod_color");
    const prodTalla = document.getElementById("prod_talla");
    const btnBuscarAgregar = document.getElementById("btnBuscarAgregar");
    const detalleVentaTableBody = document.querySelector("#detalleVentaTable tbody");
    const totalVentaDisplay = document.getElementById("totalVentaDisplay");

    // Inputs Paso 3 (Pago)
    const idMetodoPago = document.getElementById("idMetodoPago");
    const montoPagadoInput = document.getElementById("montoPagado");
    const idMonedaPago = document.getElementById("idMonedaPago");
    const tasaConversionInput = document.getElementById("tasaConversion");
    const resumenTotal = document.getElementById("resumenTotal");
    const resumenCambio = document.getElementById("resumenCambio");

    // --- Estado de la Aplicación ---
    let currentStep = 0;
    let clienteActual = null; // Objeto cliente validado
    let carrito = []; // Array de objetos { producto, cantidad, precio, descuento }
    let tasasCambio = {}; // { USD: 1, VES: 40.5, EUR: 0.9 } (Ejemplo)
    let idSucursal = idSucursalHidden ? parseInt(idSucursalHidden.value) : 5;

    // --- Inicialización ---
    init();

    async function init() {
        updateWizard();
        await cargarTasas();
        await cargarEmpleados();
        await cargarCatalogos();
        
        // Listeners
        setupWizardListeners();
        setupClienteListeners();
        setupProductoListeners();
        setupPagoListeners();
    }

    // --- Funciones de Carga de Datos ---

    async function cargarTasas() {
        const res = await ventasAPI.obtenerTasasCambio();
        if (res && res.tasas) {
            // Convertir array a objeto mapa por codigo moneda (o ID)
            // Asumimos que la API devuelve tasas relativas a una base (ej. USD)
            // Estructura esperada: [{ codigo: 'VES', tasa: 45.0 }, { codigo: 'EUR', tasa: 0.95 }]
            // Base USD = 1.
            tasasCambio['USD'] = 1; 
            res.tasas.forEach(t => {
                tasasCambio[t.codigo] = parseFloat(t.tasa);
            });
            console.log("Tasas cargadas:", tasasCambio);
        }
    }

    async function cargarEmpleados() {
        // Cargar empleados filtrados por la sucursal actual
        try {
            const res = await api({ 
                accion: "obtener_todos_los_empleados", 
                sucursal: idSucursal,
                estado: "activo"
            });
            
            idUsuarioSelect.innerHTML = '<option value="">Seleccione...</option>';
            
            if (res && (res.filas || res.data)) {
                const empleados = res.filas || res.data;
                empleados.forEach(emp => {
                    const opt = document.createElement("option");
                    opt.value = emp.id_usuario;
                    opt.textContent = `${emp.nombre} ${emp.apellido}`;
                    idUsuarioSelect.appendChild(opt);
                });
            }
        } catch (err) {
            console.error("Error cargando empleados:", err);
            idUsuarioSelect.innerHTML = '<option value="">Error al cargar</option>';
        }
    }

    async function cargarCatalogos() {
        // Cargar Categorias
        try {
            const resCat = await api({ accion: "obtener_categorias" });
            if (resCat && resCat.categorias) {
                resCat.categorias.forEach(cat => {
                    const opt = document.createElement("option");
                    opt.value = cat.id_categoria;
                    opt.textContent = cat.nombre;
                    prodCategoria.appendChild(opt);
                });
            }
        } catch(e) { console.error(e); }

        // Cargar Colores
        try {
            const resCol = await api({ accion: "obtener_colores" });
            if (resCol && resCol.colores) {
                resCol.colores.forEach(col => {
                    const opt = document.createElement("option");
                    opt.value = col.id_color;
                    opt.textContent = col.nombre;
                    prodColor.appendChild(opt);
                });
            }
        } catch(e) { console.error(e); }

        // Cargar Tallas
        try {
            const resTal = await api({ accion: "obtener_tallas" });
            if (resTal && resTal.tallas) {
                resTal.tallas.forEach(tal => {
                    const opt = document.createElement("option");
                    opt.value = tal.id_talla;
                    opt.textContent = tal.rango_talla;
                    prodTalla.appendChild(opt);
                });
            }
        } catch(e) { console.error(e); }
    }

    // --- Listeners ---

    function setupWizardListeners() {
        prevBtn.addEventListener("click", () => {
            if (currentStep > 0) {
                currentStep--;
                updateWizard();
            }
        });

        nextBtn.addEventListener("click", async () => {
            if (await validateStep(currentStep)) {
                if (currentStep < 3) {
                    currentStep++;
                    updateWizard();
                }
            }
        });

        submitBtn.addEventListener("click", async (e) => {
            e.preventDefault();
            if (await validateStep(currentStep)) {
                finalizarVenta();
            }
        });
    }

    function setupClienteListeners() {
        clienteCedula.addEventListener("blur", async () => {
            const cedula = clienteCedula.value.trim();
            if (cedula.length > 5) {
                const res = await ventasAPI.obtenerClientePorCedula(cedula);
                if (res && res.status && res.cliente) {
                    // Cliente existe
                    clienteActual = res.cliente;
                    clienteNombre.value = clienteActual.nombre || "";
                    clienteApellido.value = clienteActual.apellido || "";
                    clienteEmail.value = clienteActual.email || "";
                    clienteTelefono.value = clienteActual.telefono || "";
                    clienteDireccion.value = clienteActual.direccion || "";
                    
                    // Opcional: Bloquear campos para no editar cliente existente
                    clienteNombre.readOnly = true;
                    clienteApellido.readOnly = true;
                    clienteEmail.readOnly = true;
                    clienteTelefono.readOnly = true;
                    clienteDireccion.readOnly = true;
                } else {
                    // Cliente nuevo - limpiar y habilitar campos
                    clienteActual = null;
                    clienteNombre.value = "";
                    clienteApellido.value = "";
                    clienteEmail.value = "";
                    clienteTelefono.value = "";
                    clienteDireccion.value = "";
                    
                    clienteNombre.readOnly = false;
                    clienteApellido.readOnly = false;
                    clienteEmail.readOnly = false;
                    clienteTelefono.readOnly = false;
                    clienteDireccion.readOnly = false;
                }
            }
        });
    }

    function setupProductoListeners() {
        // Buscar por código de barras al presionar Enter
        prodCodigoBarra.addEventListener("keypress", async (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                await buscarYAgregarProducto();
            }
        });

        // Botón Agregar
        btnBuscarAgregar.addEventListener("click", async () => {
            await buscarYAgregarProducto();
        });

        // Cambio de moneda recalcula totales
        idMonedaSelect.addEventListener("change", renderCarrito);
    }

    function setupPagoListeners() {
        montoPagadoInput.addEventListener("input", calcularCambio);
        idMonedaPago.addEventListener("change", calcularCambio);
        tasaConversionInput.addEventListener("input", calcularCambio);
    }

    // --- Lógica del Wizard ---

    function updateWizard() {
        // Actualizar UI pasos
        wizardSteps.forEach(step => {
            const stepNum = parseInt(step.dataset.step);
            step.style.display = stepNum === currentStep ? "block" : "none";
        });

        // Actualizar Nav
        navLinks.forEach((link, idx) => {
            link.classList.toggle("active", idx === currentStep);
            link.classList.toggle("disabled", idx > currentStep); // Deshabilitar futuros
        });

        // Progress Bar
        const progress = ((currentStep + 1) / 4) * 100;
        progressBar.style.width = `${progress}%`;

        // Botones
        prevBtn.style.display = currentStep === 0 ? "none" : "inline-block";
        nextBtn.style.display = currentStep === 3 ? "none" : "inline-block";
        submitBtn.style.display = currentStep === 3 ? "inline-block" : "none";

        // Renderizar carrito si entramos al paso 2
        if (currentStep === 2) renderCarrito();
        // Calcular totales si entramos al paso 3
        if (currentStep === 3) {
            calcularCambio();
            // Pre-llenar tasa si se selecciona moneda pago distinta a base
        }
    }

    async function validateStep(step) {
        if (step === 0) {
            if (!clienteCedula.value || !clienteNombre.value) {
                alert("Por favor complete los datos del cliente.");
                return false;
            }
            // Si clienteActual es null, asumimos que se registrará uno nuevo (backend lo maneja o implementamos lógica aquí)
            // Por simplicidad del MVP, requerimos que el cliente exista o se rellenen todos los datos.
            // Creamos objeto cliente temporal si no existe
            if (!clienteActual) {
                clienteActual = {
                    cedula: clienteCedula.value,
                    nombre: clienteNombre.value,
                    apellido: clienteApellido.value,
                    email: clienteEmail.value,
                    telefono: clienteTelefono.value,
                    direccion: clienteDireccion.value,
                    id_cliente: null // Indicar que es nuevo
                };
            }
            return true;
        }
        if (step === 1) {
            if (!idUsuarioSelect.value || !idMonedaSelect.value) {
                alert("Seleccione Vendedor y Moneda.");
                return false;
            }
            return true;
        }
        if (step === 2) {
            if (carrito.length === 0) {
                alert("Debe agregar al menos un producto.");
                return false;
            }
            return true;
        }
        return true;
    }

    // --- Lógica de Productos ---

    async function buscarYAgregarProducto() {
        const codigo = prodCodigoBarra.value.trim();
        const categoria = prodCategoria.value;
        const color = prodColor.value;
        const talla = prodTalla.value;

        // Construir filtros
        let filtros = {};
        if (codigo) filtros.codigo = codigo;
        if (categoria) filtros.categoria = categoria;
        if (color) filtros.color = color;
        if (talla) filtros.talla = talla;

        // Si no hay filtros, no buscar
        if (Object.keys(filtros).length === 0) {
            alert("Ingrese un código o seleccione filtros.");
            return;
        }

        // Llamar API (necesitamos un endpoint que soporte filtros o usar el de codigo si solo es codigo)
        // Por ahora, usaremos obtenerProductoPorCodigo si hay codigo, o una busqueda general si no.
        // Simplificación: Usar obtenerProductoPorCodigo si hay codigo. Si no, alertar que falta implementación de busqueda avanzada o usar un endpoint de busqueda.
        
        // NOTA: El usuario pidió "cada uno de estos debe ser seleccionable".
        // Asumiremos que si hay código, manda el código. Si no, manda los atributos.
        
        let producto = null;

        if (codigo) {
            const res = await ventasAPI.obtenerProductoPorCodigo(codigo);
            if (res && res.producto) producto = res.producto;
        } else {
            // Búsqueda por atributos
            try {
                const res = await api({
                    accion: "obtener_todos_los_productos",
                    categoria: categoria,
                    color: color,
                    talla: talla,
                    estado: "true" // Solo activos
                });

                if (res && res.data && res.data.length > 0) {
                    if (res.data.length === 1) {
                        producto = res.data[0];
                    } else {
                        alert(`Se encontraron ${res.data.length} productos. Por favor sea más específico o use el código de barras.`);
                        return;
                    }
                }
            } catch (err) {
                console.error("Error buscando producto:", err);
            }
        }

        if (producto) {
            agregarAlCarrito(producto);
            prodCodigoBarra.value = ""; // Limpiar input
            prodCodigoBarra.focus();
        } else {
            alert("Producto no encontrado.");
        }
    }

    function agregarAlCarrito(producto) {
        // Verificar si ya existe
        const existente = carrito.find(p => p.id_producto === producto.id_producto);
        if (existente) {
            existente.cantidad++;
        } else {
            carrito.push({
                ...producto,
                cantidad: 1,
                descuento: 0
            });
        }
        renderCarrito();
    }

    function renderCarrito() {
        detalleVentaTableBody.innerHTML = "";
        let total = 0;
        const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text; // USD, EUR, VES
        const tasa = obtenerTasa(monedaVenta); // Tasa respecto a base (USD)

        carrito.forEach((item, index) => {
            // Precio Base (USD)
            const precioBase = parseFloat(item.precio_venta);
            // Precio Convertido
            const precioConvertido = precioBase * tasa;
            const subtotal = precioConvertido * item.cantidad * (1 - item.descuento / 100);
            total += subtotal;

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>
                    <strong>${item.nombre}</strong><br>
                    <small class="text-muted">${item.codigo_barra || 'S/C'}</small>
                </td>
                <td>
                    <small>Talla: ${item.talla || '-'}</small><br>
                    <small>Color: ${item.color || '-'}</small>
                </td>
                <td>
                    <input type="number" min="1" value="${item.cantidad}" class="form-control form-control-sm cant-input" data-index="${index}" style="width: 70px;">
                </td>
                <td>${precioConvertido.toFixed(2)} ${monedaVenta}</td>
                <td>${subtotal.toFixed(2)} ${monedaVenta}</td>
                <td>
                    <button class="btn btn-sm btn-danger remove-btn" data-index="${index}">&times;</button>
                </td>
            `;
            detalleVentaTableBody.appendChild(tr);
        });

        totalVentaDisplay.textContent = `${total.toFixed(2)} ${monedaVenta}`;
        
        // Listeners dinámicos
        document.querySelectorAll(".cant-input").forEach(input => {
            input.addEventListener("change", (e) => {
                const idx = e.target.dataset.index;
                const val = parseInt(e.target.value);
                if (val > 0) {
                    carrito[idx].cantidad = val;
                    renderCarrito();
                }
            });
        });

        document.querySelectorAll(".remove-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const idx = e.target.dataset.index;
                carrito.splice(idx, 1);
                renderCarrito();
            });
        });
    }

    // --- Lógica de Pagos ---

    function obtenerTasa(codigoMoneda) {
        // Retorna tasa de cambio respecto a USD (1)
        // Ejemplo: VES = 40, EUR = 0.95
        return tasasCambio[codigoMoneda] || 1;
    }

    function calcularCambio() {
        // Calcular Total Venta en Moneda Base (USD)
        const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
        const tasaVenta = obtenerTasa(monedaVenta);
        
        let totalVentaEnMonedaVenta = 0;
        carrito.forEach(item => {
            const precioBase = parseFloat(item.precio_venta);
            const precioConvertido = precioBase * tasaVenta;
            totalVentaEnMonedaVenta += precioConvertido * item.cantidad * (1 - item.descuento / 100);
        });

        // Convertir Total Venta a USD para comparar
        const totalVentaUSD = totalVentaEnMonedaVenta / tasaVenta;

        // Obtener Pago
        const montoPago = parseFloat(montoPagadoInput.value) || 0;
        const monedaPago = idMonedaPago.options[idMonedaPago.selectedIndex].text;
        
        // Determinar tasa de conversión para el pago
        // Si el usuario ingresó una tasa manual, usarla. Si no, usar la del sistema.
        // OJO: La tasa manual suele ser "Cuantos VES son 1 USD".
        let tasaPago = parseFloat(tasaConversionInput.value);
        if (!tasaPago) {
            tasaPago = obtenerTasa(monedaPago);
            // Pre-llenar input si está vacío y no es USD
            if (monedaPago !== 'USD') tasaConversionInput.value = tasaPago;
        }

        // Convertir Pago a USD
        // Si la tasa es "X Moneda por 1 USD", entonces USD = Monto / Tasa
        // Si es USD, tasa es 1.
        let pagoEnUSD = 0;
        if (monedaPago === 'USD') {
            pagoEnUSD = montoPago;
        } else {
            pagoEnUSD = montoPago / tasaPago;
        }

        const cambioUSD = pagoEnUSD - totalVentaUSD;
        
        // Mostrar Resumen (en moneda de venta o USD? Generalmente en moneda de venta o local)
        // Mostremos en Moneda Venta
        resumenTotal.textContent = `${totalVentaEnMonedaVenta.toFixed(2)} ${monedaVenta}`;
        
        const cambioEnMonedaVenta = cambioUSD * tasaVenta;
        resumenCambio.textContent = `${cambioEnMonedaVenta.toFixed(2)} ${monedaVenta}`;
        
        if (cambioUSD < 0) {
            resumenCambio.classList.add("text-danger");
            resumenCambio.classList.remove("text-success");
        } else {
            resumenCambio.classList.remove("text-danger");
            resumenCambio.classList.add("text-success");
        }
    }

    async function finalizarVenta() {
        if (!confirm("¿Confirmar venta?")) return;

        // Preparar Datos
        // Convertir todo a base (USD) o enviar tal cual y que backend procese.
        // Backend espera: total_venta (USD), detalles, pagos.
        
        const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
        const tasaVenta = obtenerTasa(monedaVenta);
        
        // Calcular total en USD
        let totalUSD = 0;
        const detalles = carrito.map(item => {
            const subtotalUSD = parseFloat(item.precio_venta) * item.cantidad;
            totalUSD += subtotalUSD;
            return {
                id_producto: item.id_producto,
                cantidad: item.cantidad,
                precio_unitario: parseFloat(item.precio_venta), // Base USD
                descuento: item.descuento || 0,
                subtotal: subtotalUSD
            };
        });

        const datos = {
            id_cliente: clienteActual.id_cliente, // Si es null, backend debe crear (no implementado en backend aun, fallará si es nuevo)
            // Si cliente es nuevo, deberíamos enviarlo completo.
            // Ajuste rápido: enviar objeto cliente si id es null
            cliente_nuevo: clienteActual.id_cliente ? null : clienteActual,
            
            id_usuario: idUsuarioSelect.value,
            id_sucursal: idSucursal,
            total_venta: totalUSD,
            detalles: detalles,
            pagos: [{
                id_metodo_pago: idMetodoPago.value,
                monto: parseFloat(montoPagadoInput.value),
                id_moneda: idMonedaPago.value,
                tasa: parseFloat(tasaConversionInput.value) || 1,
                referencia: document.getElementById("referenciaPago").value
            }]
        };

        const res = await ventasAPI.procesarVenta(datos);
        if (res && res.status) {
            alert("Venta registrada con éxito. ID: " + res.id_venta);
            location.reload();
        } else {
            alert("Error al registrar venta: " + (res.error || "Desconocido"));
        }
    }
});
