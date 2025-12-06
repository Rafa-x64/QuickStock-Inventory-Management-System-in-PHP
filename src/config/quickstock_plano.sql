--
-- PostgreSQL database dump
--

\restrict qWYe6oifbJharh47ryfesEy3XnwHpVWAdWpUlpwdS6997bQTIKWPudxmcD24xnF

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2025-12-06 17:57:00

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 6 (class 2615 OID 30942)
-- Name: core; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA core;


ALTER SCHEMA core OWNER TO postgres;

--
-- TOC entry 8 (class 2615 OID 30944)
-- Name: finanzas; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA finanzas;


ALTER SCHEMA finanzas OWNER TO postgres;

--
-- TOC entry 9 (class 2615 OID 30945)
-- Name: inventario; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA inventario;


ALTER SCHEMA inventario OWNER TO postgres;

--
-- TOC entry 7 (class 2615 OID 30943)
-- Name: seguridad_acceso; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA seguridad_acceso;


ALTER SCHEMA seguridad_acceso OWNER TO postgres;

--
-- TOC entry 10 (class 2615 OID 30946)
-- Name: ventas; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA ventas;


ALTER SCHEMA ventas OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 225 (class 1259 OID 30948)
-- Name: categoria; Type: TABLE; Schema: core; Owner: postgres
--

CREATE TABLE core.categoria (
    id_categoria integer NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    activo boolean DEFAULT true,
    id_categoria_padre integer
);


ALTER TABLE core.categoria OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 30947)
-- Name: categoria_id_categoria_seq; Type: SEQUENCE; Schema: core; Owner: postgres
--

CREATE SEQUENCE core.categoria_id_categoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE core.categoria_id_categoria_seq OWNER TO postgres;

--
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 224
-- Name: categoria_id_categoria_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.categoria_id_categoria_seq OWNED BY core.categoria.id_categoria;


--
-- TOC entry 227 (class 1259 OID 30962)
-- Name: cliente; Type: TABLE; Schema: core; Owner: postgres
--

CREATE TABLE core.cliente (
    id_cliente integer NOT NULL,
    nombre character varying(100) NOT NULL,
    apellido character varying(100),
    cedula character varying(20),
    telefono character varying(20),
    correo character varying(120),
    direccion text,
    activo boolean DEFAULT true
);


ALTER TABLE core.cliente OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 30961)
-- Name: cliente_id_cliente_seq; Type: SEQUENCE; Schema: core; Owner: postgres
--

CREATE SEQUENCE core.cliente_id_cliente_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE core.cliente_id_cliente_seq OWNER TO postgres;

--
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 226
-- Name: cliente_id_cliente_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.cliente_id_cliente_seq OWNED BY core.cliente.id_cliente;


--
-- TOC entry 229 (class 1259 OID 30978)
-- Name: color; Type: TABLE; Schema: core; Owner: postgres
--

CREATE TABLE core.color (
    id_color integer NOT NULL,
    nombre character varying(50) NOT NULL,
    activo boolean DEFAULT true
);


ALTER TABLE core.color OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 30977)
-- Name: color_id_color_seq; Type: SEQUENCE; Schema: core; Owner: postgres
--

CREATE SEQUENCE core.color_id_color_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE core.color_id_color_seq OWNER TO postgres;

--
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 228
-- Name: color_id_color_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.color_id_color_seq OWNED BY core.color.id_color;


--
-- TOC entry 231 (class 1259 OID 30990)
-- Name: proveedor; Type: TABLE; Schema: core; Owner: postgres
--

CREATE TABLE core.proveedor (
    id_proveedor integer NOT NULL,
    nombre character varying(150) NOT NULL,
    telefono character varying(20),
    correo character varying(120),
    direccion text,
    activo boolean DEFAULT true
);


ALTER TABLE core.proveedor OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 30989)
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE; Schema: core; Owner: postgres
--

CREATE SEQUENCE core.proveedor_id_proveedor_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE core.proveedor_id_proveedor_seq OWNER TO postgres;

--
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 230
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.proveedor_id_proveedor_seq OWNED BY core.proveedor.id_proveedor;


--
-- TOC entry 233 (class 1259 OID 31002)
-- Name: sucursal; Type: TABLE; Schema: core; Owner: postgres
--

CREATE TABLE core.sucursal (
    id_sucursal integer NOT NULL,
    nombre character varying(120) NOT NULL,
    direccion text,
    telefono character varying(20),
    rif character varying(255) NOT NULL,
    activo boolean DEFAULT true,
    fecha_registro date DEFAULT CURRENT_DATE
);


ALTER TABLE core.sucursal OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 31001)
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE; Schema: core; Owner: postgres
--

CREATE SEQUENCE core.sucursal_id_sucursal_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE core.sucursal_id_sucursal_seq OWNER TO postgres;

--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 232
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.sucursal_id_sucursal_seq OWNED BY core.sucursal.id_sucursal;


--
-- TOC entry 235 (class 1259 OID 31017)
-- Name: talla; Type: TABLE; Schema: core; Owner: postgres
--

CREATE TABLE core.talla (
    id_talla integer NOT NULL,
    rango_talla character varying(20) CONSTRAINT talla_nombre_not_null NOT NULL,
    activo boolean DEFAULT true
);


ALTER TABLE core.talla OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 31016)
-- Name: talla_id_talla_seq; Type: SEQUENCE; Schema: core; Owner: postgres
--

CREATE SEQUENCE core.talla_id_talla_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE core.talla_id_talla_seq OWNER TO postgres;

--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 234
-- Name: talla_id_talla_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.talla_id_talla_seq OWNED BY core.talla.id_talla;


--
-- TOC entry 241 (class 1259 OID 31091)
-- Name: metodo_pago; Type: TABLE; Schema: finanzas; Owner: postgres
--

CREATE TABLE finanzas.metodo_pago (
    id_metodo_pago integer NOT NULL,
    nombre character varying(50) NOT NULL,
    activo boolean DEFAULT true,
    descripcion text,
    referencia boolean DEFAULT false NOT NULL
);


ALTER TABLE finanzas.metodo_pago OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 31090)
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE; Schema: finanzas; Owner: postgres
--

CREATE SEQUENCE finanzas.metodo_pago_id_metodo_pago_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE finanzas.metodo_pago_id_metodo_pago_seq OWNER TO postgres;

--
-- TOC entry 5187 (class 0 OID 0)
-- Dependencies: 240
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE OWNED BY; Schema: finanzas; Owner: postgres
--

ALTER SEQUENCE finanzas.metodo_pago_id_metodo_pago_seq OWNED BY finanzas.metodo_pago.id_metodo_pago;


--
-- TOC entry 243 (class 1259 OID 31103)
-- Name: moneda; Type: TABLE; Schema: finanzas; Owner: postgres
--

CREATE TABLE finanzas.moneda (
    id_moneda integer NOT NULL,
    nombre character varying(50) NOT NULL,
    codigo character varying(10) NOT NULL,
    activo boolean DEFAULT true,
    simbolo character varying(5) DEFAULT '$'::character varying
);


ALTER TABLE finanzas.moneda OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 31102)
-- Name: moneda_id_moneda_seq; Type: SEQUENCE; Schema: finanzas; Owner: postgres
--

CREATE SEQUENCE finanzas.moneda_id_moneda_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE finanzas.moneda_id_moneda_seq OWNER TO postgres;

--
-- TOC entry 5188 (class 0 OID 0)
-- Dependencies: 242
-- Name: moneda_id_moneda_seq; Type: SEQUENCE OWNED BY; Schema: finanzas; Owner: postgres
--

ALTER SEQUENCE finanzas.moneda_id_moneda_seq OWNED BY finanzas.moneda.id_moneda;


--
-- TOC entry 245 (class 1259 OID 31118)
-- Name: tasa_cambio; Type: TABLE; Schema: finanzas; Owner: postgres
--

CREATE TABLE finanzas.tasa_cambio (
    id_tasa integer NOT NULL,
    id_moneda integer NOT NULL,
    fecha date DEFAULT CURRENT_DATE,
    tasa numeric(10,4) NOT NULL,
    activo boolean DEFAULT true,
    origen character varying(20) DEFAULT 'MANUAL'::character varying,
    CONSTRAINT tasa_cambio_tasa_check CHECK ((tasa > (0)::numeric))
);


ALTER TABLE finanzas.tasa_cambio OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 31117)
-- Name: tasa_cambio_id_tasa_seq; Type: SEQUENCE; Schema: finanzas; Owner: postgres
--

CREATE SEQUENCE finanzas.tasa_cambio_id_tasa_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE finanzas.tasa_cambio_id_tasa_seq OWNER TO postgres;

--
-- TOC entry 5189 (class 0 OID 0)
-- Dependencies: 244
-- Name: tasa_cambio_id_tasa_seq; Type: SEQUENCE OWNED BY; Schema: finanzas; Owner: postgres
--

ALTER SEQUENCE finanzas.tasa_cambio_id_tasa_seq OWNED BY finanzas.tasa_cambio.id_tasa;


--
-- TOC entry 257 (class 1259 OID 32223)
-- Name: compra; Type: TABLE; Schema: inventario; Owner: postgres
--

CREATE TABLE inventario.compra (
    id_compra integer NOT NULL,
    id_proveedor integer NOT NULL,
    id_sucursal integer NOT NULL,
    id_usuario integer NOT NULL,
    id_moneda integer,
    numero_factura character varying(50),
    fecha_compra date DEFAULT CURRENT_DATE NOT NULL,
    fecha_registro timestamp without time zone DEFAULT now(),
    subtotal numeric(12,2) DEFAULT 0.00 NOT NULL,
    monto_impuesto numeric(12,2) DEFAULT 0.00 NOT NULL,
    total numeric(12,2) DEFAULT 0.00 NOT NULL,
    observaciones text,
    estado character varying(20) DEFAULT 'Completada'::character varying,
    activo boolean DEFAULT true
);


ALTER TABLE inventario.compra OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 32222)
-- Name: compra_id_compra_seq; Type: SEQUENCE; Schema: inventario; Owner: postgres
--

CREATE SEQUENCE inventario.compra_id_compra_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE inventario.compra_id_compra_seq OWNER TO postgres;

--
-- TOC entry 5190 (class 0 OID 0)
-- Dependencies: 256
-- Name: compra_id_compra_seq; Type: SEQUENCE OWNED BY; Schema: inventario; Owner: postgres
--

ALTER SEQUENCE inventario.compra_id_compra_seq OWNED BY inventario.compra.id_compra;


--
-- TOC entry 259 (class 1259 OID 32262)
-- Name: detalle_compra; Type: TABLE; Schema: inventario; Owner: postgres
--

CREATE TABLE inventario.detalle_compra (
    id_detalle_compra integer NOT NULL,
    id_compra integer NOT NULL,
    id_producto integer NOT NULL,
    cantidad integer NOT NULL,
    precio_unitario numeric(12,2) NOT NULL,
    subtotal numeric(12,2) NOT NULL
);


ALTER TABLE inventario.detalle_compra OWNER TO postgres;

--
-- TOC entry 258 (class 1259 OID 32261)
-- Name: detalle_compra_id_detalle_compra_seq; Type: SEQUENCE; Schema: inventario; Owner: postgres
--

CREATE SEQUENCE inventario.detalle_compra_id_detalle_compra_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE inventario.detalle_compra_id_detalle_compra_seq OWNER TO postgres;

--
-- TOC entry 5191 (class 0 OID 0)
-- Dependencies: 258
-- Name: detalle_compra_id_detalle_compra_seq; Type: SEQUENCE OWNED BY; Schema: inventario; Owner: postgres
--

ALTER SEQUENCE inventario.detalle_compra_id_detalle_compra_seq OWNED BY inventario.detalle_compra.id_detalle_compra;


--
-- TOC entry 249 (class 1259 OID 31173)
-- Name: inventario; Type: TABLE; Schema: inventario; Owner: postgres
--

CREATE TABLE inventario.inventario (
    id_inventario integer NOT NULL,
    id_producto integer NOT NULL,
    id_sucursal integer NOT NULL,
    cantidad integer NOT NULL,
    minimo integer DEFAULT 0,
    activo boolean DEFAULT true,
    CONSTRAINT inventario_cantidad_check CHECK ((cantidad >= 0)),
    CONSTRAINT inventario_minimo_check CHECK ((minimo >= 0))
);


ALTER TABLE inventario.inventario OWNER TO postgres;

--
-- TOC entry 248 (class 1259 OID 31172)
-- Name: inventario_id_inventario_seq; Type: SEQUENCE; Schema: inventario; Owner: postgres
--

CREATE SEQUENCE inventario.inventario_id_inventario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE inventario.inventario_id_inventario_seq OWNER TO postgres;

--
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 248
-- Name: inventario_id_inventario_seq; Type: SEQUENCE OWNED BY; Schema: inventario; Owner: postgres
--

ALTER SEQUENCE inventario.inventario_id_inventario_seq OWNED BY inventario.inventario.id_inventario;


--
-- TOC entry 247 (class 1259 OID 31136)
-- Name: producto; Type: TABLE; Schema: inventario; Owner: postgres
--

CREATE TABLE inventario.producto (
    id_producto integer NOT NULL,
    nombre character varying(150) NOT NULL,
    descripcion text,
    id_categoria integer,
    id_color integer NOT NULL,
    id_talla integer NOT NULL,
    precio_venta numeric(10,2) CONSTRAINT producto_precio_not_null NOT NULL,
    id_proveedor integer,
    activo boolean DEFAULT true,
    codigo_barra character varying(255) NOT NULL,
    precio_compra numeric(12,2),
    CONSTRAINT producto_precio_check CHECK ((precio_venta > (0)::numeric)),
    CONSTRAINT producto_precio_compra_check CHECK ((precio_compra >= (1)::numeric))
);


ALTER TABLE inventario.producto OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 31135)
-- Name: producto_id_producto_seq; Type: SEQUENCE; Schema: inventario; Owner: postgres
--

CREATE SEQUENCE inventario.producto_id_producto_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE inventario.producto_id_producto_seq OWNER TO postgres;

--
-- TOC entry 5193 (class 0 OID 0)
-- Dependencies: 246
-- Name: producto_id_producto_seq; Type: SEQUENCE OWNED BY; Schema: inventario; Owner: postgres
--

ALTER SEQUENCE inventario.producto_id_producto_seq OWNED BY inventario.producto.id_producto;


--
-- TOC entry 237 (class 1259 OID 31029)
-- Name: rol; Type: TABLE; Schema: seguridad_acceso; Owner: postgres
--

CREATE TABLE seguridad_acceso.rol (
    id_rol integer NOT NULL,
    nombre_rol character varying(80) NOT NULL,
    descripcion text,
    activo boolean DEFAULT true
);


ALTER TABLE seguridad_acceso.rol OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 31028)
-- Name: rol_id_rol_seq; Type: SEQUENCE; Schema: seguridad_acceso; Owner: postgres
--

CREATE SEQUENCE seguridad_acceso.rol_id_rol_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE seguridad_acceso.rol_id_rol_seq OWNER TO postgres;

--
-- TOC entry 5194 (class 0 OID 0)
-- Dependencies: 236
-- Name: rol_id_rol_seq; Type: SEQUENCE OWNED BY; Schema: seguridad_acceso; Owner: postgres
--

ALTER SEQUENCE seguridad_acceso.rol_id_rol_seq OWNED BY seguridad_acceso.rol.id_rol;


--
-- TOC entry 239 (class 1259 OID 31043)
-- Name: usuario; Type: TABLE; Schema: seguridad_acceso; Owner: postgres
--

CREATE TABLE seguridad_acceso.usuario (
    id_usuario integer NOT NULL,
    nombre character varying(100) NOT NULL,
    apellido character varying(100),
    cedula character varying(20),
    email character varying(120) NOT NULL,
    telefono character varying(20),
    direccion text,
    "contraseña" text NOT NULL,
    id_rol integer NOT NULL,
    id_sucursal integer,
    fecha_registro date DEFAULT CURRENT_DATE,
    activo boolean DEFAULT true
);


ALTER TABLE seguridad_acceso.usuario OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 31042)
-- Name: usuario_id_usuario_seq; Type: SEQUENCE; Schema: seguridad_acceso; Owner: postgres
--

CREATE SEQUENCE seguridad_acceso.usuario_id_usuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE seguridad_acceso.usuario_id_usuario_seq OWNER TO postgres;

--
-- TOC entry 5195 (class 0 OID 0)
-- Dependencies: 238
-- Name: usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: seguridad_acceso; Owner: postgres
--

ALTER SEQUENCE seguridad_acceso.usuario_id_usuario_seq OWNED BY seguridad_acceso.usuario.id_usuario;


--
-- TOC entry 253 (class 1259 OID 31223)
-- Name: detalle_venta; Type: TABLE; Schema: ventas; Owner: postgres
--

CREATE TABLE ventas.detalle_venta (
    id_detalle integer NOT NULL,
    id_venta integer NOT NULL,
    id_producto integer NOT NULL,
    cantidad integer NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    subtotal numeric(12,2) NOT NULL,
    comision numeric(12,2) GENERATED ALWAYS AS ((subtotal * 0.10)) STORED,
    activo boolean DEFAULT true,
    CONSTRAINT detalle_venta_cantidad_check CHECK ((cantidad > 0)),
    CONSTRAINT detalle_venta_precio_unitario_check CHECK ((precio_unitario > (0)::numeric)),
    CONSTRAINT detalle_venta_subtotal_check CHECK ((subtotal >= (0)::numeric))
);


ALTER TABLE ventas.detalle_venta OWNER TO postgres;

--
-- TOC entry 252 (class 1259 OID 31222)
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE; Schema: ventas; Owner: postgres
--

CREATE SEQUENCE ventas.detalle_venta_id_detalle_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE ventas.detalle_venta_id_detalle_seq OWNER TO postgres;

--
-- TOC entry 5196 (class 0 OID 0)
-- Dependencies: 252
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: ventas; Owner: postgres
--

ALTER SEQUENCE ventas.detalle_venta_id_detalle_seq OWNED BY ventas.detalle_venta.id_detalle;


--
-- TOC entry 255 (class 1259 OID 31251)
-- Name: pago_venta; Type: TABLE; Schema: ventas; Owner: postgres
--

CREATE TABLE ventas.pago_venta (
    id_pago integer NOT NULL,
    id_venta integer NOT NULL,
    id_metodo_pago integer NOT NULL,
    monto numeric(12,2) NOT NULL,
    id_moneda integer NOT NULL,
    tasa numeric(10,4) NOT NULL,
    monto_convertido numeric(12,2) GENERATED ALWAYS AS ((monto * tasa)) STORED,
    activo boolean DEFAULT true,
    referencia character varying(20) DEFAULT NULL::character varying,
    CONSTRAINT pago_venta_monto_check CHECK ((monto > (0)::numeric)),
    CONSTRAINT pago_venta_tasa_check CHECK ((tasa > (0)::numeric))
);


ALTER TABLE ventas.pago_venta OWNER TO postgres;

--
-- TOC entry 254 (class 1259 OID 31250)
-- Name: pago_venta_id_pago_seq; Type: SEQUENCE; Schema: ventas; Owner: postgres
--

CREATE SEQUENCE ventas.pago_venta_id_pago_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE ventas.pago_venta_id_pago_seq OWNER TO postgres;

--
-- TOC entry 5197 (class 0 OID 0)
-- Dependencies: 254
-- Name: pago_venta_id_pago_seq; Type: SEQUENCE OWNED BY; Schema: ventas; Owner: postgres
--

ALTER SEQUENCE ventas.pago_venta_id_pago_seq OWNED BY ventas.pago_venta.id_pago;


--
-- TOC entry 251 (class 1259 OID 31200)
-- Name: venta; Type: TABLE; Schema: ventas; Owner: postgres
--

CREATE TABLE ventas.venta (
    id_venta integer NOT NULL,
    id_cliente integer,
    id_usuario integer NOT NULL,
    fecha timestamp without time zone DEFAULT now(),
    total numeric(12,2) NOT NULL,
    activo boolean DEFAULT true,
    CONSTRAINT venta_total_check CHECK ((total >= (0)::numeric))
);


ALTER TABLE ventas.venta OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 31199)
-- Name: venta_id_venta_seq; Type: SEQUENCE; Schema: ventas; Owner: postgres
--

CREATE SEQUENCE ventas.venta_id_venta_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE ventas.venta_id_venta_seq OWNER TO postgres;

--
-- TOC entry 5198 (class 0 OID 0)
-- Dependencies: 250
-- Name: venta_id_venta_seq; Type: SEQUENCE OWNED BY; Schema: ventas; Owner: postgres
--

ALTER SEQUENCE ventas.venta_id_venta_seq OWNED BY ventas.venta.id_venta;


--
-- TOC entry 4845 (class 2604 OID 30951)
-- Name: categoria id_categoria; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria ALTER COLUMN id_categoria SET DEFAULT nextval('core.categoria_id_categoria_seq'::regclass);


--
-- TOC entry 4847 (class 2604 OID 30965)
-- Name: cliente id_cliente; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente ALTER COLUMN id_cliente SET DEFAULT nextval('core.cliente_id_cliente_seq'::regclass);


--
-- TOC entry 4849 (class 2604 OID 30981)
-- Name: color id_color; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color ALTER COLUMN id_color SET DEFAULT nextval('core.color_id_color_seq'::regclass);


--
-- TOC entry 4851 (class 2604 OID 30993)
-- Name: proveedor id_proveedor; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.proveedor ALTER COLUMN id_proveedor SET DEFAULT nextval('core.proveedor_id_proveedor_seq'::regclass);


--
-- TOC entry 4853 (class 2604 OID 31005)
-- Name: sucursal id_sucursal; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal ALTER COLUMN id_sucursal SET DEFAULT nextval('core.sucursal_id_sucursal_seq'::regclass);


--
-- TOC entry 4856 (class 2604 OID 31020)
-- Name: talla id_talla; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla ALTER COLUMN id_talla SET DEFAULT nextval('core.talla_id_talla_seq'::regclass);


--
-- TOC entry 4863 (class 2604 OID 31094)
-- Name: metodo_pago id_metodo_pago; Type: DEFAULT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago ALTER COLUMN id_metodo_pago SET DEFAULT nextval('finanzas.metodo_pago_id_metodo_pago_seq'::regclass);


--
-- TOC entry 4866 (class 2604 OID 31106)
-- Name: moneda id_moneda; Type: DEFAULT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda ALTER COLUMN id_moneda SET DEFAULT nextval('finanzas.moneda_id_moneda_seq'::regclass);


--
-- TOC entry 4869 (class 2604 OID 31121)
-- Name: tasa_cambio id_tasa; Type: DEFAULT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio ALTER COLUMN id_tasa SET DEFAULT nextval('finanzas.tasa_cambio_id_tasa_seq'::regclass);


--
-- TOC entry 4888 (class 2604 OID 32226)
-- Name: compra id_compra; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra ALTER COLUMN id_compra SET DEFAULT nextval('inventario.compra_id_compra_seq'::regclass);


--
-- TOC entry 4896 (class 2604 OID 32265)
-- Name: detalle_compra id_detalle_compra; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra ALTER COLUMN id_detalle_compra SET DEFAULT nextval('inventario.detalle_compra_id_detalle_compra_seq'::regclass);


--
-- TOC entry 4875 (class 2604 OID 31176)
-- Name: inventario id_inventario; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario ALTER COLUMN id_inventario SET DEFAULT nextval('inventario.inventario_id_inventario_seq'::regclass);


--
-- TOC entry 4873 (class 2604 OID 31139)
-- Name: producto id_producto; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto ALTER COLUMN id_producto SET DEFAULT nextval('inventario.producto_id_producto_seq'::regclass);


--
-- TOC entry 4858 (class 2604 OID 31032)
-- Name: rol id_rol; Type: DEFAULT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol ALTER COLUMN id_rol SET DEFAULT nextval('seguridad_acceso.rol_id_rol_seq'::regclass);


--
-- TOC entry 4860 (class 2604 OID 31046)
-- Name: usuario id_usuario; Type: DEFAULT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario ALTER COLUMN id_usuario SET DEFAULT nextval('seguridad_acceso.usuario_id_usuario_seq'::regclass);


--
-- TOC entry 4881 (class 2604 OID 31226)
-- Name: detalle_venta id_detalle; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta ALTER COLUMN id_detalle SET DEFAULT nextval('ventas.detalle_venta_id_detalle_seq'::regclass);


--
-- TOC entry 4884 (class 2604 OID 31254)
-- Name: pago_venta id_pago; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta ALTER COLUMN id_pago SET DEFAULT nextval('ventas.pago_venta_id_pago_seq'::regclass);


--
-- TOC entry 4878 (class 2604 OID 31203)
-- Name: venta id_venta; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta ALTER COLUMN id_venta SET DEFAULT nextval('ventas.venta_id_venta_seq'::regclass);


--
-- TOC entry 5141 (class 0 OID 30948)
-- Dependencies: 225
-- Data for Name: categoria; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.categoria (id_categoria, nombre, descripcion, activo, id_categoria_padre) FROM stdin;
6	zapatos deportivos unisex	zapato para cualquier tipo de genero	t	5
5	zapatos deportivos	zapatos para hacer deporte	t	\N
4	Zapatos De Nieve	\N	t	\N
7	zandalias dama	calzado para mujer	t	\N
\.


--
-- TOC entry 5143 (class 0 OID 30962)
-- Dependencies: 227
-- Data for Name: cliente; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.cliente (id_cliente, nombre, apellido, cedula, telefono, correo, direccion, activo) FROM stdin;
5	madga	perozo	v-19621445	0424-1111111	magda@gmail.com	aiscnianscinasci	t
6	ejemplo ejemplo	ejemplo ejemplo	v-11111111	0424-1111111	ejemplo@gmail.com	ejemplo ejemplo ejemplo ejemplo	t
1	Rafael Andres	Alvarez Tortoza	V-31.757.781	0412-555-10-41	alvarezrafaelat@gmail.com	cabudare centro las mercedes	t
\.


--
-- TOC entry 5145 (class 0 OID 30978)
-- Dependencies: 229
-- Data for Name: color; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.color (id_color, nombre, activo) FROM stdin;
4	Rojo	t
5	Blanco	t
6	Marron	t
8	Azul	t
7	Verde	t
\.


--
-- TOC entry 5147 (class 0 OID 30990)
-- Dependencies: 231
-- Data for Name: proveedor; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.proveedor (id_proveedor, nombre, telefono, correo, direccion, activo) FROM stdin;
1	Proveedor Ejemplo S.A.	0412-555-55-55	contacto@proveedorejemplo.com	Av. Principal #123, Ciudad	t
2	Calzados Juandiegiño	0412-555-10-55	juagdiego@gmail.com	piritu portuguesa	t
\.


--
-- TOC entry 5149 (class 0 OID 31002)
-- Dependencies: 233
-- Data for Name: sucursal; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.sucursal (id_sucursal, nombre, direccion, telefono, rif, activo, fecha_registro) FROM stdin;
6	Fifty Four Cabudare	Cabudare Centro	04125551041	J-12345678-9	t	2025-11-22
8	Ginza	Baquisimeto Av Vargas	04125551041	J-12345678-3	t	2025-11-23
9	Ginza Outlet	Oasdkoaskdoaksoddkoasd	0424-589-87-63	J-12345678-1	t	2025-11-30
5	Global Sport	Yaracuy	0412-555-10-41	J-12345678-9	t	2025-11-22
\.


--
-- TOC entry 5151 (class 0 OID 31017)
-- Dependencies: 235
-- Data for Name: talla; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.talla (id_talla, rango_talla, activo) FROM stdin;
5	40-41-42	t
6	35-40	t
7	30-40	t
4	39-44	t
\.


--
-- TOC entry 5157 (class 0 OID 31091)
-- Dependencies: 241
-- Data for Name: metodo_pago; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.metodo_pago (id_metodo_pago, nombre, activo, descripcion, referencia) FROM stdin;
1	Zelle	t		t
3	Efectivo En Dolares	t	money	f
2	Transferencia	t	transferencia bancara por medio de plataforma bancaria	t
\.


--
-- TOC entry 5159 (class 0 OID 31103)
-- Dependencies: 243
-- Data for Name: moneda; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.moneda (id_moneda, nombre, codigo, activo, simbolo) FROM stdin;
1	Bolívar	VES	t	Bs.
2	Dólar	USD	t	$
3	Euro	EUR	t	€
\.


--
-- TOC entry 5161 (class 0 OID 31118)
-- Dependencies: 245
-- Data for Name: tasa_cambio; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.tasa_cambio (id_tasa, id_moneda, fecha, tasa, activo, origen) FROM stdin;
1	1	2025-12-06	1.0000	t	Inicial
2	2	2025-12-06	1.0000	t	Inicial
3	3	2025-12-06	0.9500	t	Manual
4	1	2025-12-06	257.9287	t	API
5	3	2025-12-06	0.8586	t	API
6	1	2025-12-06	257.9287	t	API
7	3	2025-12-06	0.8586	t	API
8	1	2025-12-06	257.9287	t	API
9	3	2025-12-06	0.8586	t	API
10	1	2025-12-06	257.9287	t	API
11	3	2025-12-06	0.8586	t	API
12	1	2025-12-06	257.9287	t	API
13	3	2025-12-06	0.8586	t	API
14	1	2025-12-06	257.9287	t	API
15	3	2025-12-06	0.8586	t	API
16	1	2025-12-06	257.9287	t	API
17	3	2025-12-06	0.8586	t	API
18	1	2025-12-06	257.9287	t	API
19	3	2025-12-06	0.8586	t	API
20	1	2025-12-06	257.9287	t	API
21	3	2025-12-06	0.8586	t	API
\.


--
-- TOC entry 5173 (class 0 OID 32223)
-- Dependencies: 257
-- Data for Name: compra; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.compra (id_compra, id_proveedor, id_sucursal, id_usuario, id_moneda, numero_factura, fecha_compra, fecha_registro, subtotal, monto_impuesto, total, observaciones, estado, activo) FROM stdin;
1	1	6	5	1	0254	2025-11-23	2025-11-23 23:08:53.116799	1500.00	240.00	1740.00		pendiente	t
2	1	5	5	1	0450	2025-11-23	2025-11-23 23:12:25.204223	720.00	115.20	835.20	ninguna	pendiente	t
4	1	6	5	2	0111	2025-11-24	2025-11-24 17:54:13.023619	2000.00	320.00	2320.00		pendiente	t
6	1	6	5	1	0192	2025-11-24	2025-11-24 21:35:41.085196	4000.00	640.00	4640.00	ninguna	pendiente	t
5	1	8	5	1	0456	2025-11-24	2025-11-24 21:28:56.378523	3000.00	480.00	3480.00	aisdiasdiajsd	pendiente	t
7	1	5	5	3	0509	2025-12-04	2025-12-04 10:24:47.87401	4500.00	720.00	5220.00		pendiente	t
8	1	8	5	1	0578	2025-12-04	2025-12-04 11:04:22.032844	90000.00	14400.00	104400.00		pendiente	t
9	1	5	5	1	1823	2025-12-06	2025-12-06 12:33:15.280993	500.00	80.00	580.00		pendiente	t
10	2	6	5	2	1093	2025-12-06	2025-12-06 16:40:39.990396	2250.00	360.00	2610.00		pendiente	t
\.


--
-- TOC entry 5175 (class 0 OID 32262)
-- Dependencies: 259
-- Data for Name: detalle_compra; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.detalle_compra (id_detalle_compra, id_compra, id_producto, cantidad, precio_unitario, subtotal) FROM stdin;
1	1	6	50	30.00	1500.00
2	2	7	24	30.00	720.00
4	4	8	100	20.00	2000.00
7	6	15	50	60.00	3000.00
8	6	16	200	5.00	1000.00
5	5	12	50	60.00	3000.00
9	7	17	90	50.00	4500.00
10	8	18	60	1500.00	90000.00
11	9	17	10	50.00	500.00
12	10	21	50	45.00	2250.00
\.


--
-- TOC entry 5165 (class 0 OID 31173)
-- Dependencies: 249
-- Data for Name: inventario; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.inventario (id_inventario, id_producto, id_sucursal, cantidad, minimo, activo) FROM stdin;
4	6	6	50	0	t
5	7	5	48	0	t
6	8	6	100	0	t
7	12	8	50	0	t
9	15	6	50	0	t
10	16	6	200	0	t
8	13	8	0	0	t
12	18	8	60	0	t
13	19	9	100	5	t
14	20	9	50	10	t
11	17	5	88	0	t
3	5	5	13	10	t
15	21	6	50	0	t
\.


--
-- TOC entry 5163 (class 0 OID 31136)
-- Dependencies: 247
-- Data for Name: producto; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.producto (id_producto, nombre, descripcion, id_categoria, id_color, id_talla, precio_venta, id_proveedor, activo, codigo_barra, precio_compra) FROM stdin;
6	Nike air force one	\N	5	5	4	60.00	1	t	zap-02	30.00
7	Botas timberland	\N	5	6	4	45.00	1	t	timber-10	30.00
8	Nike retro jordan i	\N	6	7	4	30.00	1	t	zap-04	20.00
12	Nike retro jordan iii	\N	5	4	4	70.00	1	t	zap-30	60.00
13	Chancletas dama	\N	6	4	4	10.00	1	t	chan-dam	5.00
15	Nike retro jordan iv	\N	\N	7	4	70.00	1	t	zap-31	60.00
16	Chancleta niño	\N	\N	5	4	7.94	1	t	chan-niño	5.00
17	Tacos futbol	\N	5	7	4	60.00	1	t	zap-99	50.00
18	Chancleta unisex	\N	\N	4	4	1600.00	1	t	zap-100	1500.00
19	Chancletas	ninguna	4	4	5	40.00	1	t	zap-999	50.00
5	Zapato Numero 1	\N	4	4	4	20.00	\N	t	zap-01	10.00
20	Zandalias Amarre		7	6	6	250.00	\N	t	zap-150	150.00
21	Botas de lluvia	\N	5	8	7	50.00	2	t	zap-a	45.00
\.


--
-- TOC entry 5153 (class 0 OID 31029)
-- Dependencies: 237
-- Data for Name: rol; Type: TABLE DATA; Schema: seguridad_acceso; Owner: postgres
--

COPY seguridad_acceso.rol (id_rol, nombre_rol, descripcion, activo) FROM stdin;
1	Gerente	Responsable de supervisar ventas, inventario y personal.	t
2	Administrador	Administrador del sistema con acceso completo.	t
3	Cajero	Encargado de registrar ventas y procesar pagos.	t
4	Depositario	Encargado de gestionar depósitos y movimientos de caja.	t
5	Vendedor	Encargado de atender clientes y realizar ventas.	t
6	Encargado	Responsable de supervisar tareas básicas.	t
\.


--
-- TOC entry 5155 (class 0 OID 31043)
-- Dependencies: 239
-- Data for Name: usuario; Type: TABLE DATA; Schema: seguridad_acceso; Owner: postgres
--

COPY seguridad_acceso.usuario (id_usuario, nombre, apellido, cedula, email, telefono, direccion, "contraseña", id_rol, id_sucursal, fecha_registro, activo) FROM stdin;
6	Andres Alejandro	Alvarez Tortoza	V-12.345.678	vendedor@gmail.com	0416-123-45-67	Asdiajsdijaisdjiasjdiasdjij	$2y$12$dc565gjIFFgWw6v4K30m8.Ywu2ElOJovLB8yRhigjkYdgH.Ktwl7a	5	6	2025-11-22	t
7	Alicia Alanis	Reyes Gracia	V-32.757.781	alicia@gmail.com	0424-123-45-67	Piritu Portuguesa	$2y$12$8ev9.L/fRa.eRFHAS62ltueZdwYnzNzzir8SIiC6UTUXLUPpQZkh2	5	5	2025-11-29	t
5	Rafael Andres	Alvarez Tortoza	V-31.757.781	admin@gmail.com	+58 412-555-10-41	\N	$2y$10$obLkFJM0d4hPq59/KG.Fl.frxwgbPpsQaH6h71d7fPXqm4K6dnVdi	1	\N	2025-11-22	t
9	Admin Admin	Admin Admin	V-11.111.111	administrador@gmail.com	+58 424-111-11-11	Admin Admin	$2y$10$zznhokHiIVeU6NdeBhnAEe5WV0OmjPFmD.kM7mTMlIuNW4jSGUIge	2	\N	2025-12-06	t
10	Depositario Depositario	Depositario Depositario	V-33.333.333	depositario@gmail.com	+58 424-333-33-33	Depositario Depositario	$2y$12$voDt1k/b13xk/C0h/qxfpuNZ/BX5ZGxVBzQMZVOwqGFzfG0KVSOE6	4	9	2025-12-06	t
11	Cajero Cajero	Cajero Cajero	E-44.444.444	cajero@gmail.com	+58 424-444-44-44	Cajero Cajero	$2y$12$lpdUDeZghtZw6FbCGvg0dOjF0re37MamwJdBK//vIhDl6Ixucoyri	3	9	2025-12-06	t
12	Encargado Encargado	Encargado Encargado	V-55.555.555	encargado@gmail.com	+58 414-555-55-55	Encargado Encargado	$2y$12$4/D44T5wVhgjEq.EWLrN9eZHxE7/vaprnCmY34Yaq9mkhC7sP40qS	6	9	2025-12-06	t
\.


--
-- TOC entry 5169 (class 0 OID 31223)
-- Dependencies: 253
-- Data for Name: detalle_venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.detalle_venta (id_detalle, id_venta, id_producto, cantidad, precio_unitario, subtotal, activo) FROM stdin;
1	1	5	1	20.00	20.00	t
2	2	5	1	20.00	20.00	t
6	6	5	2	20.00	40.00	t
8	8	5	1	20.00	20.00	t
9	9	5	1	20.00	20.00	t
10	10	17	1	60.00	60.00	t
13	13	17	1	60.00	60.00	t
16	16	17	10	60.00	600.00	t
17	17	5	1	20.00	20.00	t
18	18	5	1	20.00	20.00	t
19	19	5	1	20.00	20.00	t
20	20	5	2	20.00	40.00	t
\.


--
-- TOC entry 5171 (class 0 OID 31251)
-- Dependencies: 255
-- Data for Name: pago_venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.pago_venta (id_pago, id_venta, id_metodo_pago, monto, id_moneda, tasa, activo, referencia) FROM stdin;
1	1	3	20.00	1	1.0000	t	\N
2	2	3	20.00	1	1.0000	t	\N
3	6	3	40.00	1	1.0000	t	\N
4	8	3	20.00	3	1.0000	t	\N
5	9	2	20.00	3	1.0000	t	0456
6	10	2	80.00	3	1.0000	t	0555
11	13	2	20.00	3	1.0000	t	0964
12	13	3	40.00	1	1.0000	t	
16	16	2	300.00	3	1.0000	t	1023
17	16	3	300.00	1	1.0000	t	\N
18	17	3	20.00	1	1.0000	t	\N
19	18	2	5097.40	3	1.0000	t	1294
20	19	3	20.00	2	1.0000	t	\N
21	20	3	20.00	2	1.0000	t	\N
22	20	3	20.00	2	1.0000	t	\N
\.


--
-- TOC entry 5167 (class 0 OID 31200)
-- Dependencies: 251
-- Data for Name: venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.venta (id_venta, id_cliente, id_usuario, fecha, total, activo) FROM stdin;
1	1	7	2025-11-30 15:38:15.419179	20.00	t
2	1	7	2025-11-30 22:04:53.407207	20.00	t
6	1	7	2025-12-04 10:21:29.070794	40.00	t
8	5	7	2025-12-04 11:10:17.980034	20.00	t
9	1	7	2025-12-04 20:06:38.923812	20.00	t
10	6	7	2025-12-05 14:50:36.777199	60.00	t
13	1	7	2025-12-06 12:07:06.107264	60.00	t
16	1	7	2025-12-06 12:50:13.255384	600.00	t
17	1	7	2025-12-06 14:51:35.977054	20.00	t
18	1	7	2025-12-06 14:54:22.070657	20.00	t
19	1	7	2025-12-06 15:16:17.486444	20.00	t
20	1	7	2025-12-06 16:37:58.393636	40.00	t
\.


--
-- TOC entry 5199 (class 0 OID 0)
-- Dependencies: 224
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.categoria_id_categoria_seq', 7, true);


--
-- TOC entry 5200 (class 0 OID 0)
-- Dependencies: 226
-- Name: cliente_id_cliente_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.cliente_id_cliente_seq', 6, true);


--
-- TOC entry 5201 (class 0 OID 0)
-- Dependencies: 228
-- Name: color_id_color_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.color_id_color_seq', 8, true);


--
-- TOC entry 5202 (class 0 OID 0)
-- Dependencies: 230
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.proveedor_id_proveedor_seq', 2, true);


--
-- TOC entry 5203 (class 0 OID 0)
-- Dependencies: 232
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.sucursal_id_sucursal_seq', 9, true);


--
-- TOC entry 5204 (class 0 OID 0)
-- Dependencies: 234
-- Name: talla_id_talla_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.talla_id_talla_seq', 7, true);


--
-- TOC entry 5205 (class 0 OID 0)
-- Dependencies: 240
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.metodo_pago_id_metodo_pago_seq', 3, true);


--
-- TOC entry 5206 (class 0 OID 0)
-- Dependencies: 242
-- Name: moneda_id_moneda_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.moneda_id_moneda_seq', 3, true);


--
-- TOC entry 5207 (class 0 OID 0)
-- Dependencies: 244
-- Name: tasa_cambio_id_tasa_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.tasa_cambio_id_tasa_seq', 21, true);


--
-- TOC entry 5208 (class 0 OID 0)
-- Dependencies: 256
-- Name: compra_id_compra_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.compra_id_compra_seq', 10, true);


--
-- TOC entry 5209 (class 0 OID 0)
-- Dependencies: 258
-- Name: detalle_compra_id_detalle_compra_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.detalle_compra_id_detalle_compra_seq', 12, true);


--
-- TOC entry 5210 (class 0 OID 0)
-- Dependencies: 248
-- Name: inventario_id_inventario_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.inventario_id_inventario_seq', 15, true);


--
-- TOC entry 5211 (class 0 OID 0)
-- Dependencies: 246
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.producto_id_producto_seq', 21, true);


--
-- TOC entry 5212 (class 0 OID 0)
-- Dependencies: 236
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.rol_id_rol_seq', 6, true);


--
-- TOC entry 5213 (class 0 OID 0)
-- Dependencies: 238
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.usuario_id_usuario_seq', 12, true);


--
-- TOC entry 5214 (class 0 OID 0)
-- Dependencies: 252
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.detalle_venta_id_detalle_seq', 20, true);


--
-- TOC entry 5215 (class 0 OID 0)
-- Dependencies: 254
-- Name: pago_venta_id_pago_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.pago_venta_id_pago_seq', 22, true);


--
-- TOC entry 5216 (class 0 OID 0)
-- Dependencies: 250
-- Name: venta_id_venta_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.venta_id_venta_seq', 20, true);


--
-- TOC entry 4909 (class 2606 OID 30960)
-- Name: categoria categoria_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria
    ADD CONSTRAINT categoria_nombre_key UNIQUE (nombre);


--
-- TOC entry 4911 (class 2606 OID 30958)
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id_categoria);


--
-- TOC entry 4913 (class 2606 OID 30974)
-- Name: cliente cliente_cedula_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_cedula_key UNIQUE (cedula);


--
-- TOC entry 4915 (class 2606 OID 30976)
-- Name: cliente cliente_correo_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_correo_key UNIQUE (correo);


--
-- TOC entry 4917 (class 2606 OID 30972)
-- Name: cliente cliente_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente);


--
-- TOC entry 4919 (class 2606 OID 30988)
-- Name: color color_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color
    ADD CONSTRAINT color_nombre_key UNIQUE (nombre);


--
-- TOC entry 4921 (class 2606 OID 30986)
-- Name: color color_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color
    ADD CONSTRAINT color_pkey PRIMARY KEY (id_color);


--
-- TOC entry 4923 (class 2606 OID 31000)
-- Name: proveedor proveedor_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.proveedor
    ADD CONSTRAINT proveedor_pkey PRIMARY KEY (id_proveedor);


--
-- TOC entry 4925 (class 2606 OID 31015)
-- Name: sucursal sucursal_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal
    ADD CONSTRAINT sucursal_nombre_key UNIQUE (nombre);


--
-- TOC entry 4927 (class 2606 OID 31013)
-- Name: sucursal sucursal_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal
    ADD CONSTRAINT sucursal_pkey PRIMARY KEY (id_sucursal);


--
-- TOC entry 4929 (class 2606 OID 31027)
-- Name: talla talla_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla
    ADD CONSTRAINT talla_nombre_key UNIQUE (rango_talla);


--
-- TOC entry 4931 (class 2606 OID 31025)
-- Name: talla talla_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla
    ADD CONSTRAINT talla_pkey PRIMARY KEY (id_talla);


--
-- TOC entry 4943 (class 2606 OID 31101)
-- Name: metodo_pago metodo_pago_nombre_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago
    ADD CONSTRAINT metodo_pago_nombre_key UNIQUE (nombre);


--
-- TOC entry 4945 (class 2606 OID 31099)
-- Name: metodo_pago metodo_pago_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago
    ADD CONSTRAINT metodo_pago_pkey PRIMARY KEY (id_metodo_pago);


--
-- TOC entry 4947 (class 2606 OID 31116)
-- Name: moneda moneda_codigo_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_codigo_key UNIQUE (codigo);


--
-- TOC entry 4949 (class 2606 OID 31114)
-- Name: moneda moneda_nombre_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_nombre_key UNIQUE (nombre);


--
-- TOC entry 4951 (class 2606 OID 31112)
-- Name: moneda moneda_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_pkey PRIMARY KEY (id_moneda);


--
-- TOC entry 4953 (class 2606 OID 31129)
-- Name: tasa_cambio tasa_cambio_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio
    ADD CONSTRAINT tasa_cambio_pkey PRIMARY KEY (id_tasa);


--
-- TOC entry 4969 (class 2606 OID 32245)
-- Name: compra compra_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_pkey PRIMARY KEY (id_compra);


--
-- TOC entry 4971 (class 2606 OID 32273)
-- Name: detalle_compra detalle_compra_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra
    ADD CONSTRAINT detalle_compra_pkey PRIMARY KEY (id_detalle_compra);


--
-- TOC entry 4957 (class 2606 OID 31188)
-- Name: inventario inventario_id_producto_id_sucursal_key; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_producto_id_sucursal_key UNIQUE (id_producto, id_sucursal);


--
-- TOC entry 4959 (class 2606 OID 31186)
-- Name: inventario inventario_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_pkey PRIMARY KEY (id_inventario);


--
-- TOC entry 4955 (class 2606 OID 31151)
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- TOC entry 4933 (class 2606 OID 31041)
-- Name: rol rol_nombre_rol_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol
    ADD CONSTRAINT rol_nombre_rol_key UNIQUE (nombre_rol);


--
-- TOC entry 4935 (class 2606 OID 31039)
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- TOC entry 4937 (class 2606 OID 31059)
-- Name: usuario usuario_cedula_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_cedula_key UNIQUE (cedula);


--
-- TOC entry 4939 (class 2606 OID 31061)
-- Name: usuario usuario_email_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_email_key UNIQUE (email);


--
-- TOC entry 4941 (class 2606 OID 31057)
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- TOC entry 4963 (class 2606 OID 31239)
-- Name: detalle_venta detalle_venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_pkey PRIMARY KEY (id_detalle);


--
-- TOC entry 4965 (class 2606 OID 31266)
-- Name: pago_venta pago_venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_pkey PRIMARY KEY (id_pago);


--
-- TOC entry 4967 (class 2606 OID 32290)
-- Name: pago_venta pago_venta_referencia_key; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_referencia_key UNIQUE (referencia);


--
-- TOC entry 4961 (class 2606 OID 31211)
-- Name: venta venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_pkey PRIMARY KEY (id_venta);


--
-- TOC entry 4974 (class 2606 OID 31130)
-- Name: tasa_cambio tasa_cambio_id_moneda_fkey; Type: FK CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio
    ADD CONSTRAINT tasa_cambio_id_moneda_fkey FOREIGN KEY (id_moneda) REFERENCES finanzas.moneda(id_moneda) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4988 (class 2606 OID 32246)
-- Name: compra compra_id_proveedor_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_id_proveedor_fkey FOREIGN KEY (id_proveedor) REFERENCES core.proveedor(id_proveedor) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4989 (class 2606 OID 32251)
-- Name: compra compra_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4990 (class 2606 OID 32256)
-- Name: compra compra_id_usuario_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES seguridad_acceso.usuario(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4991 (class 2606 OID 32274)
-- Name: detalle_compra detalle_compra_id_compra_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra
    ADD CONSTRAINT detalle_compra_id_compra_fkey FOREIGN KEY (id_compra) REFERENCES inventario.compra(id_compra) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4992 (class 2606 OID 32279)
-- Name: detalle_compra detalle_compra_id_producto_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra
    ADD CONSTRAINT detalle_compra_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4979 (class 2606 OID 31189)
-- Name: inventario inventario_id_producto_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4980 (class 2606 OID 31194)
-- Name: inventario inventario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4975 (class 2606 OID 31152)
-- Name: producto producto_id_categoria_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_categoria_fkey FOREIGN KEY (id_categoria) REFERENCES core.categoria(id_categoria) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4976 (class 2606 OID 31157)
-- Name: producto producto_id_color_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_color_fkey FOREIGN KEY (id_color) REFERENCES core.color(id_color) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4977 (class 2606 OID 31167)
-- Name: producto producto_id_proveedor_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_proveedor_fkey FOREIGN KEY (id_proveedor) REFERENCES core.proveedor(id_proveedor) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4978 (class 2606 OID 31162)
-- Name: producto producto_id_talla_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_talla_fkey FOREIGN KEY (id_talla) REFERENCES core.talla(id_talla) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4972 (class 2606 OID 31062)
-- Name: usuario usuario_id_rol_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES seguridad_acceso.rol(id_rol) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4973 (class 2606 OID 31067)
-- Name: usuario usuario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4983 (class 2606 OID 31245)
-- Name: detalle_venta detalle_venta_id_producto_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4984 (class 2606 OID 31240)
-- Name: detalle_venta detalle_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES ventas.venta(id_venta) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4985 (class 2606 OID 31272)
-- Name: pago_venta pago_venta_id_metodo_pago_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_metodo_pago_fkey FOREIGN KEY (id_metodo_pago) REFERENCES finanzas.metodo_pago(id_metodo_pago) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4986 (class 2606 OID 31277)
-- Name: pago_venta pago_venta_id_moneda_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_moneda_fkey FOREIGN KEY (id_moneda) REFERENCES finanzas.moneda(id_moneda) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4987 (class 2606 OID 31267)
-- Name: pago_venta pago_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES ventas.venta(id_venta) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4981 (class 2606 OID 31212)
-- Name: venta venta_id_cliente_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES core.cliente(id_cliente) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4982 (class 2606 OID 31217)
-- Name: venta venta_id_usuario_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES seguridad_acceso.usuario(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT;


-- Completed on 2025-12-06 17:57:00

--
-- PostgreSQL database dump complete
--

\unrestrict qWYe6oifbJharh47ryfesEy3XnwHpVWAdWpUlpwdS6997bQTIKWPudxmcD24xnF

