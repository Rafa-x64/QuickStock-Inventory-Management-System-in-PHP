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
  const detalleVentaTableBody = document.querySelector(
    "#detalleVentaTable tbody"
  );
  const totalVentaDisplay = document.getElementById("totalVentaDisplay");

  // Inputs Paso 3 (Pago - MODIFICADO)
  const idMetodoPago = document.getElementById("idMetodoPago");
  const montoPagadoInput = document.getElementById("montoPagado");
  const idMonedaPago = document.getElementById("idMonedaPago");
  const tasaConversionInput = document.getElementById("tasaConversion");
  const referenciaPagoInput = document.getElementById("referenciaPago");
  const btnAgregarPago = document.getElementById("btnAgregarPago");
  const tablaPagosBody = document.querySelector("#tablaPagos tbody");

  // Totales y Resumen
  const resumenTotal = document.getElementById("resumenTotal");
  const resumenPagado = document.getElementById("resumenPagado");
  const resumenRestante = document.getElementById("resumenRestante");
  const containerRestante = document.getElementById("containerRestante");
  const resumenCambio = document.getElementById("resumenCambio");
  const containerCambio = document.getElementById("containerCambio");

  // --- Estado de la Aplicación ---
  let currentStep = 0;
  let clienteActual = null; // Objeto cliente validado
  let carrito = []; // Array de objetos { producto, cantidad, precio, descuento }
  let listaPagos = []; // Array de pagos registrados para la venta actual { id_metodo, nombre_metodo, monto, id_moneda, moneda_nombre, tasa, referencia, equivalenteVenta }
  let tasasCambio = {}; // { USD: 1, VES: 40.5, EUR: 0.9 }
  let idSucursal = idSucursalHidden ? parseInt(idSucursalHidden.value) : 5;

  // --- Reglas de Validación ---
  const reglas = {
    cliente_cedula: {
      // Permite: V-12345678, V-12.345.678, 12345678 (se formateará), e-12345678
      regex: /^([VEJGvejg]-?)?(\d{1,3}(\.?\d{3}){1,3})$/,
      msg: "Formato inválido. Ej: V-12.345.678"
    },
    cliente_nombre: {
      regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/,
      msg: "Solo letras, mín 2 caracteres.",
    },
    cliente_apellido: {
      regex: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/,
      msg: "Solo letras, mín 2 caracteres.",
    },
    cliente_email: {
      regex: /^$|^[^\s@]+@[^\s@]+\.[^\s@]+$/,
      msg: "Email inválido.",
    },
    cliente_telefono: {
      regex: /^$|^(\+58|0)(412|414|424|416|426|2\d{2})[-\s]?\d{7}$/,
      msg: "Teléfono inválido. Ej: 04121234567",
    },
    cliente_direccion: {
      regex: /^.{5,200}$/,
      msg: "Mínimo 5 caracteres.",
    },
    montoPagado: {
      custom: (val) => parseFloat(val) > 0,
      msg: "El monto debe ser positivo.",
    },
    tasaConversion: {
      custom: (val) => parseFloat(val) > 0,
      msg: "La tasa debe ser mayor a 0.",
    },
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

  // --- Formateador de Cédula ---
  function formatCedula(val) {
    // 1. Limpiar todo lo que no sea número o V/E/J/G
    let raw = val.replace(/[^0-9vejgVEJG]/g, "").toUpperCase();

    // 2. Extraer prefijo si existe
    let prefix = "V"; // Default
    const match = raw.match(/^([VEJG])(.*)/);
    if (match) {
      prefix = match[1];
      raw = match[2]; // Dejar solo números en raw
    }

    // 3. Limpiar números (por si quedaron letras sueltas)
    let numbers = raw.replace(/\D/g, "");

    // Casos borde: si no hay números o es muy corto, devolvemos lo que hay para que falle regex
    if (!numbers || numbers.length < 5) return val.toUpperCase();

    // 4. Formatear con puntos
    let formattedNum = numbers.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    return `${prefix}-${formattedNum}`;
  }

  // --- Funciones de Validación ---
  function validarCampo(input) {
    const id = input.id;
    const regla = reglas[id];

    // Normalización específica para cédula
    if (id === 'cliente_cedula') {
      const rawVal = input.value.trim();
      // Solo formatear si tiene al menos unos números y no está vacío
      if (rawVal && rawVal.length > 2) { 
        input.value = formatCedula(rawVal);
      }
    }

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
    Object.keys(reglas).forEach((id) => {
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
      tasasCambio["USD"] = 1;
      res.tasas.forEach((t) => {
        tasasCambio[t.codigo] = parseFloat(t.tasa);
      });
    }
  }

  async function cargarEmpleados() {
    try {
      const res = await api({
        accion: "obtener_todos_los_empleados",
        sucursal: idSucursal,
        estado: "activo",
      });

      idUsuarioSelect.innerHTML = '<option value="">Seleccione...</option>';

      if (res && (res.filas || res.data)) {
        const empleados = res.filas || res.data;
        empleados.forEach((emp) => {
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
      const resCol = await ventasAPI.obtenerColores();
      if (resCol && resCol.colores) {
        resCol.colores.forEach((col) => {
          const opt = document.createElement("option");
          opt.value = col.id_color;
          opt.textContent = col.nombre;
          prodColor.appendChild(opt);
        });
      }
    } catch (e) {
      console.error(e);
    }

    try {
      const resTal = await ventasAPI.obtenerTallas();
      if (resTal && resTal.tallas) {
        resTal.tallas.forEach((tal) => {
          const opt = document.createElement("option");
          opt.value = tal.id_talla;
          opt.textContent = tal.rango_talla;
          prodTalla.appendChild(opt);
        });
      }
    } catch (e) {
      console.error(e);
    }

    // Cargar Metodos de Pago
    try {
      const resMet = await ventasAPI.obtenerMetodosPago();
      idMetodoPago.innerHTML = '<option value="">Seleccione...</option>';

      if (resMet && (resMet.filas || resMet.data)) {
        const metodos = resMet.filas || resMet.data;
        metodos.forEach((met) => {
          const opt = document.createElement("option");
          opt.value = met.id_metodo_pago;
          opt.textContent = met.nombre;
          idMetodoPago.appendChild(opt);
        });
      }
    } catch (e) {
      console.error(e);
    }
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
      // 1. Ejecutar validación (esto también formatea el input.value si es necesario)
      if (!validarCampo(clienteCedula)) return;

      // 2. Obtener el valor YA formateado del input
      const cedula = clienteCedula.value.trim();

      const res = await ventasAPI.obtenerClientePorCedula(cedula);
      if (res && res.status && res.cliente) {
        // Cliente existe
        clienteActual = res.cliente;
        clienteNombre.value = clienteActual.nombre || "";
        clienteApellido.value = clienteActual.apellido || "";
        clienteEmail.value = clienteActual.correo || "";
        clienteTelefono.value = clienteActual.telefono || "";
        clienteDireccion.value = clienteActual.direccion || "";

        // Bloquear campos
        [
          clienteNombre,
          clienteApellido,
          clienteEmail,
          clienteTelefono,
          clienteDireccion,
        ].forEach((el) => {
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
        [
          clienteNombre,
          clienteApellido,
          clienteEmail,
          clienteTelefono,
          clienteDireccion,
        ].forEach((el) => {
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
    // Al cambiar la moneda de pago, actualizar tasa sugerida
    idMonedaPago.addEventListener("change", () => {
        const moneda = idMonedaPago.options[idMonedaPago.selectedIndex].text;
        const tasa = obtenerTasa(moneda);
        tasaConversionInput.value = tasa;
        // Validar para visual
        validarCampo(tasaConversionInput);
    });

    btnAgregarPago.addEventListener("click", agregarPago);
  }

  // --- Lógica del Wizard ---

  function updateWizard() {
    wizardSteps.forEach((step) => {
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
    
    // El botón finalizar está deshabilitado por defecto hasta que se complete el pago
    if (currentStep === 3) {
        submitBtn.disabled = true;
        // Calcular totales iniciales
        calcularTotalesPago();
    }
    
    if (currentStep === 2) renderCarrito();
  }

  async function validateStep(step) {
    if (step === 0) {
      // Validar todos los campos del cliente
      const campos = [
        clienteCedula,
        clienteNombre,
        clienteApellido,
        clienteDireccion,
      ];
      let allValid = true;
      campos.forEach((c) => {
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
          id_cliente: null,
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
        // Validación del pago completo
        const totales = calcularTotalesGenerales();
        if (totales.restante > 0.01) { // Tolerancia por redondeo
            alert("El pago está incompleto. Faltan: " + totales.restante.toFixed(2) + " " + totales.moneda);
            return false;
        }
        return true;
    }
    return true;
  }

  // --- Lógica de Productos ---

  async function buscarYAgregarProducto() {
    const codigo = prodCodigoBarra.value.trim();
    // const categoria = prodCategoria.value; // Ya no se usa para buscar
    const color = prodColor.value;
    const talla = prodTalla.value;

    if (!codigo) {
      alert("Ingrese un código de barras.");
      return;
    }

    try {
      const res = await ventasAPI.obtenerProductoPorCodigo(codigo, idSucursal);

      if (res && res.status && res.producto) {
        const p = res.producto;

        // Rellenar campos para verificación
        prodCategoria.value = p.categoria || "Sin Categoría";

        // Seleccionar Color
        if (p.id_color) {
          prodColor.value = p.id_color;
        }

        // Seleccionar Talla
        if (p.id_talla) {
          prodTalla.value = p.id_talla;
        }

        // Agregar al carrito automáticamente
        agregarAlCarrito(p);
        prodCodigoBarra.value = "";
        prodCodigoBarra.focus();
      } else {
        // Manejo de errores específicos
        if (res && res.mensaje) {
          alert(res.mensaje);
        } else {
          alert("Producto no encontrado.");
        }

        // Limpiar campos
        prodCategoria.value = "";
        prodColor.value = "";
        prodTalla.value = "";
      }
    } catch (err) {
      console.error("Error buscando producto:", err);
      alert("Error interno al buscar producto.");
    }
  }

  function agregarAlCarrito(producto) {
    const existente = carrito.find(
      (p) => p.id_producto === producto.id_producto
    );
    if (existente) {
      existente.cantidad++;
    } else {
      carrito.push({
        ...producto,
        cantidad: 1,
        descuento: 0,
      });
    }
    renderCarrito();
  }

  function renderCarrito() {
    detalleVentaTableBody.innerHTML = "";
    let total = 0;
    const monedaVenta =
      idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
    const tasa = obtenerTasa(monedaVenta);

    carrito.forEach((item, index) => {
      const precioBase = parseFloat(item.precio_venta);
      const precioConvertido = precioBase * tasa;
      const subtotal =
        precioConvertido * item.cantidad * (1 - item.descuento / 100);
      total += subtotal;

      const tr = document.createElement("tr");
      tr.innerHTML = `
                <td>
                    <strong>${item.nombre}</strong><br>
                    <small class="text-muted">${
                      item.codigo_barra || "S/C"
                    }</small>
                </td>
                <td>
                    <small>Talla: ${item.talla || "-"}</small><br>
                    <small>Color: ${item.color || "-"}</small>
                </td>
                <td>
                    <input type="number" min="1" value="${
                      item.cantidad
                    }" class="form-control form-control-sm cant-input" data-index="${index}" style="width: 70px;">
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

    document.querySelectorAll(".cant-input").forEach((input) => {
      input.addEventListener("change", (e) => {
        const idx = e.target.dataset.index;
        const val = parseInt(e.target.value);
        if (val > 0) {
          carrito[idx].cantidad = val;
          renderCarrito();
        }
      });
    });

    document.querySelectorAll(".remove-btn").forEach((btn) => {
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

  // Calcula totales generales para la venta en curso
  function calcularTotalesGenerales() {
      const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
      const tasaVenta = obtenerTasa(monedaVenta);
      
      // 1. Calcular TOTAL DE LA VENTA (en moneda seleccionada)
      // Basado en el carrito, iteramos para sumar precios base convertidos
      let totalVentaUSD = 0;
      carrito.forEach(item => {
          totalVentaUSD += parseFloat(item.precio_venta) * item.cantidad * (1 - item.descuento / 100);
      });
      
      const totalVenta = totalVentaUSD * tasaVenta;

      // 2. Calcular TOTAL PAGADO (en moneda seleccionada)
      // Iteramos listaPagos, convertimos cada pago a USD y luego a monedaVenta
      let totalPagadoVenta = 0;
      listaPagos.forEach(p => {
          let montoPagoUSD = 0;
          if (p.moneda_nombre === 'USD') {
              montoPagoUSD = p.monto;
          } else {
              montoPagoUSD = p.monto / p.tasa;
          }
          totalPagadoVenta += montoPagoUSD * tasaVenta;
      });

      const restante = totalVenta - totalPagadoVenta;
      const cambio = restante < 0 ? Math.abs(restante) : 0;
      const restanteReal = restante > 0 ? restante : 0; // No mostrar negativos como restante

      return {
          totalVenta,
          totalPagadoVenta,
          restante: restanteReal,
          cambio,
          moneda: monedaVenta
      };
  }

  function calcularTotalesPago() {
      const info = calcularTotalesGenerales();

      resumenTotal.textContent = `${info.totalVenta.toFixed(2)} ${info.moneda}`;
      resumenPagado.textContent = `${info.totalPagadoVenta.toFixed(2)} ${info.moneda}`;

      if (info.cambio > 0) {
          // Hay cambio (sobrante)
          containerRestante.style.setProperty("display", "none", "important");
          containerCambio.style.setProperty("display", "flex", "important");
          resumenCambio.textContent = `${info.cambio.toFixed(2)} ${info.moneda}`;
          submitBtn.disabled = false;
      } else if (info.restante > 0.01) {
          // Falta por pagar
          containerRestante.style.setProperty("display", "flex", "important");
          containerCambio.style.setProperty("display", "none", "important");
          resumenRestante.textContent = `${info.restante.toFixed(2)} ${info.moneda}`;
          submitBtn.disabled = true;
      } else {
          // Pago exacto
          containerRestante.style.setProperty("display", "flex", "important");
          containerCambio.style.setProperty("display", "none", "important");
          resumenRestante.textContent = `0.00 ${info.moneda}`;
          submitBtn.disabled = false;
      }
  }

  function agregarPago() {
      // Validaciones
      const idMetodo = idMetodoPago.value;
      const nombreMetodo = idMetodoPago.options[idMetodoPago.selectedIndex]?.text;
      const monto = parseFloat(montoPagadoInput.value);
      const idMoneda = idMonedaPago.value;
      const nombreMoneda = idMonedaPago.options[idMonedaPago.selectedIndex]?.text;
      const tasa = parseFloat(tasaConversionInput.value);
      const referencia = referenciaPagoInput.value.trim();

      if (!idMetodo) { alert("Seleccione un método de pago."); return; }
      if (!idMoneda) { alert("Seleccione la moneda del pago."); return; }
      if (!monto || monto <= 0) { alert("El monto debe ser válido."); return; }
      if (!tasa || tasa <= 0) { alert("La tasa de cambio debe ser válida."); return; }

      // Calcular valor equivalente en moneda de venta (solo para visualización en tabla)
      const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;
      const tasaVenta = obtenerTasa(monedaVenta);
      
      let montoUSD = (nombreMoneda === 'USD') ? monto : (monto / tasa);
      let montoEnVenta = montoUSD * tasaVenta;

      // Agregar a lista
      listaPagos.push({
          id_metodo_pago: idMetodo,
          nombre_metodo: nombreMetodo,
          monto: monto,
          id_moneda: idMoneda,
          moneda_nombre: nombreMoneda,
          tasa: tasa,
          referencia: referencia,
          equivalenteVenta: montoEnVenta
      });

      // Limpiar inputs
      montoPagadoInput.value = "";
      referenciaPagoInput.value = "";
      // Resetear clases de validación
      montoPagadoInput.classList.remove("is-valid", "is-invalid");
      referenciaPagoInput.classList.remove("is-valid", "is-invalid");

      // Actualizar UI
      renderPagos();
      calcularTotalesPago();
  }

  function renderPagos() {
      tablaPagosBody.innerHTML = "";
      const monedaVenta = idMonedaSelect.options[idMonedaSelect.selectedIndex].text;

      listaPagos.forEach((p, index) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${p.nombre_metodo}</td>
            <td>${p.referencia || '<small class="text-muted">N/A</small>'}</td>
            <td>${p.monto.toFixed(2)} ${p.moneda_nombre}</td>
            <td>${p.tasa}</td>
            <td>${p.equivalenteVenta.toFixed(2)} ${monedaVenta}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-danger btn-borrar-pago" data-index="${index}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
          `;
          tablaPagosBody.appendChild(tr);
      });

      // Listeners para borrar
      document.querySelectorAll(".btn-borrar-pago").forEach(btn => {
          btn.addEventListener("click", (e) => {
              const target = e.target.closest("button");
              const idx = parseInt(target.dataset.index);
              eliminarPago(idx);
          });
      });
  }

  function eliminarPago(index) {
      listaPagos.splice(index, 1);
      renderPagos();
      calcularTotalesPago();
  }

  async function finalizarVenta() {
    if (!confirm("¿Confirmar venta?")) return;

    // Calcular total en USD
    let totalUSD = 0;
    const detalles = carrito.map((item) => {
      const subtotalUSD = parseFloat(item.precio_venta) * item.cantidad;
      totalUSD += subtotalUSD;
      return {
        id_producto: item.id_producto,
        cantidad: item.cantidad,
        precio_unitario: parseFloat(item.precio_venta), // Base USD
        descuento: item.descuento || 0,
        subtotal: subtotalUSD,
      };
    });

    const datos = {
      id_cliente: clienteActual.id_cliente,
      cliente_nuevo: clienteActual.id_cliente ? null : clienteActual,
      id_usuario: idUsuarioSelect.value,
      id_sucursal: idSucursal,
      total_venta: totalUSD,
      detalles: detalles,
      // Enviar listaPagos completa
      pagos: listaPagos.map((p) => ({
        id_metodo_pago: p.id_metodo_pago,
        monto: p.monto,
        id_moneda: p.id_moneda,
        tasa: p.tasa,
        referencia: p.referencia,
      })),
      // Comentario (opcional si el backend lo soporta, por ahora no está en DB pero lo enviamos)
      // comentario: document.getElementById("pagoComentario").value 
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
