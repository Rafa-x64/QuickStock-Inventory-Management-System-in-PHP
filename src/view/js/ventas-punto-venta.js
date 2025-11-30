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
    let tasasCambio = {}; // { USD: 1, VES: 40.5, EUR: 0.9 }
    let idSucursal = idSucursalHidden ? parseInt(idSucursalHidden.value) : 5;

    // --- Reglas de Validación ---
    const reglas = {
        cliente_cedula: {
            regex: /^[VvEeJjGg]-?\d{5,9}$/,
            msg: "Formato inválido. Ej: V-12345678"
        },
        cliente_nombre: {
            regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/,
            msg: "Solo letras, mín 2 caracteres."
        },
        cliente_apellido: {
            regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/,
            msg: "Solo letras, mín 2 caracteres."
        },
        cliente_email: {
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            msg: "Email inválido."
        },
        cliente_telefono: {
            regex: /^(\+58|0)(412|414|424|416|426|2\d{2})[-\s]?\d{7}$/,
            msg: "Teléfono inválido. Ej: 04121234567"
        },
        cliente_direccion: {
            regex: /^.{5,200}$/,
            msg: "Mínimo 5 caracteres."
        },
        montoPagado: {
            custom: (val) => parseFloat(val) > 0,
            msg: "El monto debe ser mayor a 0."
        },
        tasaConversion: {
            custom: (val) => parseFloat(val) > 0,
            msg: "La tasa debe ser mayor a 0."
        }
    };

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
        setupValidationListeners();
    }

    // --- Funciones de Validación ---
    function validarCampo(input) {
        const id = input.id;
        const regla = reglas[id];
        if (!regla) return true; // Si no hay regla, es válido (o no se valida aquí)

        const val = input.value.trim();
        let valido = true;

        if (regla.regex) {
            valido = regla.regex.test(val);
        } else if (regla.custom) {
            valido = regla.custom(val);
        }

        // Manejo visual Bootstrap
        if (valido) {
            input.classList.remove("is-invalid");
            input.classList.add("is-valid");
        } else {
            input.classList.remove("is-valid");
            input.classList.add("is-invalid");
            // Buscar o crear tooltip
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains("invalid-tooltip")) {
                // Si no existe el div, intentar buscarlo en el padre (por si hay input-group)
                // Ojo: En la estructura actual, el tooltip suele estar después del input.
                // Si no está, lo ignoramos o lo creamos dinámicamente (opcional).
            }
            if (feedback && feedback.classList.contains("invalid-tooltip")) {
                feedback.textContent = regla.msg;
            }
        }
        return valido;
    }

    function setupValidationListeners() {
        Object.keys(reglas).forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener("input", () => validarCampo(input));
                input.addEventListener("blur", () => validarCampo(input));
            }
        });
    }

    // --- Funciones de Carga de Datos ---

    async function cargarTasas() {
        const res = await ventasAPI.obtenerTasasCambio();
        if (res && res.tasas) {
            tasasCambio['USD'] = 1; 
            res.tasas.forEach(t => {
                tasasCambio[t.codigo] = parseFloat(t.tasa);
            });
        }
    }

    async function cargarEmpleados() {
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
        }
    }

    async function cargarCatalogos() {
        try {
            const resCat = await ventasAPI.obtenerCategorias();
            if (resCat && resCat.categorias) {
                resCat.categorias.forEach(cat => {
                    const opt = document.createElement("option");
                    opt.value = cat.id_categoria;
                    opt.textContent = cat.nombre;
                    prodCategoria.appendChild(opt);
                });
            }
        } catch(e) { console.error(e); }

        try {
            const resCol = await ventasAPI.obtenerColores();
            if (resCol && resCol.colores) {
                resCol.colores.forEach(col => {
                    const opt = document.createElement("option");
                    opt.value = col.id_color;
                    opt.textContent = col.nombre;
                    prodColor.appendChild(opt);
                });
            }
        } catch(e) { console.error(e); }

        try {
            const resTal = await ventasAPI.obtenerTallas();
            if (resTal && resTal.tallas) {
                resTal.tallas.forEach(tal => {
                    const opt = document.createElement("option");
                    opt.value = tal.id_talla;
                    opt.textContent = tal.rango_talla;
                    prodTalla.appendChild(opt);
                });
            }
        } catch(e) { console.error(e); }

        // Cargar Metodos de Pago
        try {
            const resMet = await ventasAPI.obtenerMetodosPago();
            const selectMetodo = document.getElementById("idMetodoPago");
            selectMetodo.innerHTML = '<option value="">Seleccione...</option>';
            
            if (resMet && (resMet.filas || resMet.data)) { // Ajustar según retorno de API
                const metodos = resMet.filas || resMet.data;
                metodos.forEach(met => {
                    const opt = document.createElement("option");
                    opt.value = met.id_metodo_pago;
                    opt.textContent = met.nombre;
                    selectMetodo.appendChild(opt);
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
            // Validar formato antes de buscar
            if (!validarCampo(clienteCedula)) return;

            const res = await ventasAPI.obtenerClientePorCedula(cedula);
            if (res && res.status && res.cliente) {
                // Cliente existe
                clienteActual = res.cliente;
                clienteNombre.value = clienteActual.nombre || "";
                clienteApellido.value = clienteActual.apellido || "";
                clienteEmail.value = clienteActual.email || "";
                clienteTelefono.value = clienteActual.telefono || "";
                clienteDireccion.value = clienteActual.direccion || "";
                
                // Bloquear campos
                [clienteNombre, clienteApellido, clienteEmail, clienteTelefono, clienteDireccion].forEach(el => {
                    el.readOnly = true;
                    el.classList.add("is-valid");
                    el.classList.remove("is-invalid");
                });
            } else {
                // Cliente nuevo
                clienteActual = null;
                // Limpiar (excepto cédula)
                clienteNombre.value = "";
                clienteApellido.value = "";
                clienteEmail.value = "";
                clienteTelefono.value = "";
                clienteDireccion.value = "";
                
                // Habilitar
                [clienteNombre, clienteApellido, clienteEmail, clienteTelefono, clienteDireccion].forEach(el => {
                    el.readOnly = false;
                    el.classList.remove("is-valid", "is-invalid");
                });
            }
        });
    }

    function setupProductoListeners() {
        prodCodigoBarra.addEventListener("keypress", async (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                await buscarYAgregarProducto();
            }
        });

        btnBuscarAgregar.addEventListener("click", async () => {
            await buscarYAgregarProducto();
        });

        idMonedaSelect.addEventListener("change", renderCarrito);
    }

    function setupPagoListeners() {
        montoPagadoInput.addEventListener("input", () => {
            validarCampo(montoPagadoInput);
            calcularCambio();
        });
        idMonedaPago.addEventListener("change", calcularCambio);
        tasaConversionInput.addEventListener("input", () => {
            validarCampo(tasaConversionInput);
            calcularCambio();
        });
    }

    // --- Lógica del Wizard ---

    function updateWizard() {
        wizardSteps.forEach(step => {
            const stepNum = parseInt(step.dataset.step);
            step.style.display = stepNum === currentStep ? "block" : "none";
        });

        navLinks.forEach((link, idx) => {
            link.classList.toggle("active", idx === currentStep);
            link.classList.toggle("disabled", idx > currentStep);
        });

        const progress = ((currentStep + 1) / 4) * 100;
        progressBar.style.width = `${progress}%`;

        prevBtn.style.display = currentStep === 0 ? "none" : "inline-block";
        nextBtn.style.display = currentStep === 3 ? "none" : "inline-block";
        submitBtn.style.display = currentStep === 3 ? "inline-block" : "none";

        if (currentStep === 2) renderCarrito();
        if (currentStep === 3) calcularCambio();
    }

    async function validateStep(step) {
        if (step === 0) {
            // Validar todos los campos del cliente
            const campos = [clienteCedula, clienteNombre, clienteApellido, clienteEmail, clienteTelefono, clienteDireccion];
            let allValid = true;
            campos.forEach(c => {
                if (!validarCampo(c)) allValid = false;
            });

            if (!allValid) {
                alert("Por favor corrija los errores en el formulario del cliente.");
                return false;
            }

            if (!clienteActual) {
                clienteActual = {
                    cedula: clienteCedula.value,
                    nombre: clienteNombre.value,
                    apellido: clienteApellido.value,
                    email: clienteEmail.value,
                    telefono: clienteTelefono.value,
                    direccion: clienteDireccion.value,
                    id_cliente: null
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
        if (step === 3) {
            // Validar pago
            if (!validarCampo(montoPagadoInput) || !validarCampo(tasaConversionInput)) {
                return false;
            }
            // Validar que cubra el monto (opcional en front, obligatorio en back)
            // Aquí solo validamos formato
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

        if (Object.keys(filtros).length === 0) {
            alert("Ingrese un código o seleccione filtros.");
            return;
        }

        let producto = null;

        if (codigo) {
            const res = await ventasAPI.obtenerProductoPorCodigo(codigo);
            if (res && res.producto) producto = res.producto;
        } else {
            try {
                const res = await api({
                    accion: "obtener_todos_los_productos",
                    categoria: categoria,
                    color: color,
                    talla: talla,
                    estado: "true"
                });

                if (res && res.data && res.data.length > 0) {
                    if (res.data.length === 1) {
                        producto = res.data[0];
                    } else {
                        alert(`Se encontraron ${res.data.length} productos. Por favor sea más específico.`);
                        return;
                    }
                }
            } catch (err) {
                console.error("Error buscando producto:", err);
            }
        }

        if (producto) {
            agregarAlCarrito(producto);
            prodCodigoBarra.value = "";
            prodCodigoBarra.focus();
        } else {
            alert("Producto no encontrado.");
        }
    }

    function agregarAlCarrito(producto) {
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
        const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
        const tasa = obtenerTasa(monedaVenta);

        carrito.forEach((item, index) => {
            const precioBase = parseFloat(item.precio_venta);
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
        return tasasCambio[codigoMoneda] || 1;
    }

    function calcularCambio() {
        const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
        const tasaVenta = obtenerTasa(monedaVenta);
        
        let totalVentaEnMonedaVenta = 0;
        carrito.forEach(item => {
            const precioBase = parseFloat(item.precio_venta);
            const precioConvertido = precioBase * tasaVenta;
            totalVentaEnMonedaVenta += precioConvertido * item.cantidad * (1 - item.descuento / 100);
        });

        const totalVentaUSD = totalVentaEnMonedaVenta / tasaVenta;
        const montoPago = parseFloat(montoPagadoInput.value) || 0;
        const monedaPago = idMonedaPago.options[idMonedaPago.selectedIndex].text;
        
        let tasaPago = parseFloat(tasaConversionInput.value);
        if (!tasaPago) {
            tasaPago = obtenerTasa(monedaPago);
            if (monedaPago !== 'USD') tasaConversionInput.value = tasaPago;
        }

        let pagoEnUSD = 0;
        if (monedaPago === 'USD') {
            pagoEnUSD = montoPago;
        } else {
            pagoEnUSD = montoPago / tasaPago;
        }

        const cambioUSD = pagoEnUSD - totalVentaUSD;
        const cambioEnMonedaVenta = cambioUSD * tasaVenta;
        
        resumenTotal.textContent = `${totalVentaEnMonedaVenta.toFixed(2)} ${monedaVenta}`;
        resumenCambio.textContent = `${cambioEnMonedaVenta.toFixed(2)} ${monedaVenta}`;
        
        if (cambioUSD < -0.01) { // Tolerancia pequeña
            resumenCambio.classList.add("text-danger");
            resumenCambio.classList.remove("text-success");
        } else {
            resumenCambio.classList.remove("text-danger");
            resumenCambio.classList.add("text-success");
        }
    }

    async function finalizarVenta() {
        if (!confirm("¿Confirmar venta?")) return;

        const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
        
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
            id_cliente: clienteActual.id_cliente,
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
