--
-- PostgreSQL database dump
--

\restrict Kvi8Ch4dFbTlXTXCclP0dlVGycdiMMNlMU28oega8A7B8Aib3wzk8j7RjoZtbtX

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2025-11-23 11:39:33

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
-- TOC entry 5155 (class 0 OID 0)
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
-- TOC entry 5156 (class 0 OID 0)
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
-- TOC entry 5157 (class 0 OID 0)
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
-- TOC entry 5158 (class 0 OID 0)
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
-- TOC entry 5159 (class 0 OID 0)
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
-- TOC entry 5160 (class 0 OID 0)
-- Dependencies: 234
-- Name: talla_id_talla_seq; Type: SEQUENCE OWNED BY; Schema: core; Owner: postgres
--

ALTER SEQUENCE core.talla_id_talla_seq OWNED BY core.talla.id_talla;


--
-- TOC entry 243 (class 1259 OID 31091)
-- Name: metodo_pago; Type: TABLE; Schema: finanzas; Owner: postgres
--

CREATE TABLE finanzas.metodo_pago (
    id_metodo_pago integer NOT NULL,
    nombre character varying(50) NOT NULL,
    activo boolean DEFAULT true
);


ALTER TABLE finanzas.metodo_pago OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 31090)
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
-- TOC entry 5161 (class 0 OID 0)
-- Dependencies: 242
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE OWNED BY; Schema: finanzas; Owner: postgres
--

ALTER SEQUENCE finanzas.metodo_pago_id_metodo_pago_seq OWNED BY finanzas.metodo_pago.id_metodo_pago;


--
-- TOC entry 245 (class 1259 OID 31103)
-- Name: moneda; Type: TABLE; Schema: finanzas; Owner: postgres
--

CREATE TABLE finanzas.moneda (
    id_moneda integer NOT NULL,
    nombre character varying(50) NOT NULL,
    codigo character varying(10) NOT NULL,
    activo boolean DEFAULT true
);


ALTER TABLE finanzas.moneda OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 31102)
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
-- TOC entry 5162 (class 0 OID 0)
-- Dependencies: 244
-- Name: moneda_id_moneda_seq; Type: SEQUENCE OWNED BY; Schema: finanzas; Owner: postgres
--

ALTER SEQUENCE finanzas.moneda_id_moneda_seq OWNED BY finanzas.moneda.id_moneda;


--
-- TOC entry 247 (class 1259 OID 31118)
-- Name: tasa_cambio; Type: TABLE; Schema: finanzas; Owner: postgres
--

CREATE TABLE finanzas.tasa_cambio (
    id_tasa integer NOT NULL,
    id_moneda integer NOT NULL,
    fecha date DEFAULT CURRENT_DATE,
    tasa numeric(10,4) NOT NULL,
    activo boolean DEFAULT true,
    CONSTRAINT tasa_cambio_tasa_check CHECK ((tasa > (0)::numeric))
);


ALTER TABLE finanzas.tasa_cambio OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 31117)
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
-- TOC entry 5163 (class 0 OID 0)
-- Dependencies: 246
-- Name: tasa_cambio_id_tasa_seq; Type: SEQUENCE OWNED BY; Schema: finanzas; Owner: postgres
--

ALTER SEQUENCE finanzas.tasa_cambio_id_tasa_seq OWNED BY finanzas.tasa_cambio.id_tasa;


--
-- TOC entry 251 (class 1259 OID 31173)
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
-- TOC entry 250 (class 1259 OID 31172)
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
-- TOC entry 5164 (class 0 OID 0)
-- Dependencies: 250
-- Name: inventario_id_inventario_seq; Type: SEQUENCE OWNED BY; Schema: inventario; Owner: postgres
--

ALTER SEQUENCE inventario.inventario_id_inventario_seq OWNED BY inventario.inventario.id_inventario;


--
-- TOC entry 249 (class 1259 OID 31136)
-- Name: producto; Type: TABLE; Schema: inventario; Owner: postgres
--

CREATE TABLE inventario.producto (
    id_producto integer NOT NULL,
    nombre character varying(150) NOT NULL,
    descripcion text,
    id_categoria integer NOT NULL,
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
-- TOC entry 248 (class 1259 OID 31135)
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
-- TOC entry 5165 (class 0 OID 0)
-- Dependencies: 248
-- Name: producto_id_producto_seq; Type: SEQUENCE OWNED BY; Schema: inventario; Owner: postgres
--

ALTER SEQUENCE inventario.producto_id_producto_seq OWNED BY inventario.producto.id_producto;


--
-- TOC entry 241 (class 1259 OID 31073)
-- Name: auditoria; Type: TABLE; Schema: seguridad_acceso; Owner: postgres
--

CREATE TABLE seguridad_acceso.auditoria (
    id_auditoria integer NOT NULL,
    id_usuario integer,
    tabla character varying(120) NOT NULL,
    accion character varying(50) NOT NULL,
    fecha timestamp without time zone DEFAULT now(),
    descripcion text
);


ALTER TABLE seguridad_acceso.auditoria OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 31072)
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE; Schema: seguridad_acceso; Owner: postgres
--

CREATE SEQUENCE seguridad_acceso.auditoria_id_auditoria_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE seguridad_acceso.auditoria_id_auditoria_seq OWNER TO postgres;

--
-- TOC entry 5166 (class 0 OID 0)
-- Dependencies: 240
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE OWNED BY; Schema: seguridad_acceso; Owner: postgres
--

ALTER SEQUENCE seguridad_acceso.auditoria_id_auditoria_seq OWNED BY seguridad_acceso.auditoria.id_auditoria;


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
-- TOC entry 5167 (class 0 OID 0)
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
-- TOC entry 5168 (class 0 OID 0)
-- Dependencies: 238
-- Name: usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: seguridad_acceso; Owner: postgres
--

ALTER SEQUENCE seguridad_acceso.usuario_id_usuario_seq OWNED BY seguridad_acceso.usuario.id_usuario;


--
-- TOC entry 255 (class 1259 OID 31223)
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
-- TOC entry 254 (class 1259 OID 31222)
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
-- TOC entry 5169 (class 0 OID 0)
-- Dependencies: 254
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE OWNED BY; Schema: ventas; Owner: postgres
--

ALTER SEQUENCE ventas.detalle_venta_id_detalle_seq OWNED BY ventas.detalle_venta.id_detalle;


--
-- TOC entry 257 (class 1259 OID 31251)
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
    CONSTRAINT pago_venta_monto_check CHECK ((monto > (0)::numeric)),
    CONSTRAINT pago_venta_tasa_check CHECK ((tasa > (0)::numeric))
);


ALTER TABLE ventas.pago_venta OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 31250)
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
-- TOC entry 5170 (class 0 OID 0)
-- Dependencies: 256
-- Name: pago_venta_id_pago_seq; Type: SEQUENCE OWNED BY; Schema: ventas; Owner: postgres
--

ALTER SEQUENCE ventas.pago_venta_id_pago_seq OWNED BY ventas.pago_venta.id_pago;


--
-- TOC entry 253 (class 1259 OID 31200)
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
-- TOC entry 252 (class 1259 OID 31199)
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
-- TOC entry 5171 (class 0 OID 0)
-- Dependencies: 252
-- Name: venta_id_venta_seq; Type: SEQUENCE OWNED BY; Schema: ventas; Owner: postgres
--

ALTER SEQUENCE ventas.venta_id_venta_seq OWNED BY ventas.venta.id_venta;


--
-- TOC entry 4840 (class 2604 OID 30951)
-- Name: categoria id_categoria; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria ALTER COLUMN id_categoria SET DEFAULT nextval('core.categoria_id_categoria_seq'::regclass);


--
-- TOC entry 4842 (class 2604 OID 30965)
-- Name: cliente id_cliente; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente ALTER COLUMN id_cliente SET DEFAULT nextval('core.cliente_id_cliente_seq'::regclass);


--
-- TOC entry 4844 (class 2604 OID 30981)
-- Name: color id_color; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color ALTER COLUMN id_color SET DEFAULT nextval('core.color_id_color_seq'::regclass);


--
-- TOC entry 4846 (class 2604 OID 30993)
-- Name: proveedor id_proveedor; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.proveedor ALTER COLUMN id_proveedor SET DEFAULT nextval('core.proveedor_id_proveedor_seq'::regclass);


--
-- TOC entry 4848 (class 2604 OID 31005)
-- Name: sucursal id_sucursal; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal ALTER COLUMN id_sucursal SET DEFAULT nextval('core.sucursal_id_sucursal_seq'::regclass);


--
-- TOC entry 4851 (class 2604 OID 31020)
-- Name: talla id_talla; Type: DEFAULT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla ALTER COLUMN id_talla SET DEFAULT nextval('core.talla_id_talla_seq'::regclass);


--
-- TOC entry 4860 (class 2604 OID 31094)
-- Name: metodo_pago id_metodo_pago; Type: DEFAULT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago ALTER COLUMN id_metodo_pago SET DEFAULT nextval('finanzas.metodo_pago_id_metodo_pago_seq'::regclass);


--
-- TOC entry 4862 (class 2604 OID 31106)
-- Name: moneda id_moneda; Type: DEFAULT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda ALTER COLUMN id_moneda SET DEFAULT nextval('finanzas.moneda_id_moneda_seq'::regclass);


--
-- TOC entry 4864 (class 2604 OID 31121)
-- Name: tasa_cambio id_tasa; Type: DEFAULT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio ALTER COLUMN id_tasa SET DEFAULT nextval('finanzas.tasa_cambio_id_tasa_seq'::regclass);


--
-- TOC entry 4869 (class 2604 OID 31176)
-- Name: inventario id_inventario; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario ALTER COLUMN id_inventario SET DEFAULT nextval('inventario.inventario_id_inventario_seq'::regclass);


--
-- TOC entry 4867 (class 2604 OID 31139)
-- Name: producto id_producto; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto ALTER COLUMN id_producto SET DEFAULT nextval('inventario.producto_id_producto_seq'::regclass);


--
-- TOC entry 4858 (class 2604 OID 31076)
-- Name: auditoria id_auditoria; Type: DEFAULT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.auditoria ALTER COLUMN id_auditoria SET DEFAULT nextval('seguridad_acceso.auditoria_id_auditoria_seq'::regclass);


--
-- TOC entry 4853 (class 2604 OID 31032)
-- Name: rol id_rol; Type: DEFAULT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol ALTER COLUMN id_rol SET DEFAULT nextval('seguridad_acceso.rol_id_rol_seq'::regclass);


--
-- TOC entry 4855 (class 2604 OID 31046)
-- Name: usuario id_usuario; Type: DEFAULT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario ALTER COLUMN id_usuario SET DEFAULT nextval('seguridad_acceso.usuario_id_usuario_seq'::regclass);


--
-- TOC entry 4875 (class 2604 OID 31226)
-- Name: detalle_venta id_detalle; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta ALTER COLUMN id_detalle SET DEFAULT nextval('ventas.detalle_venta_id_detalle_seq'::regclass);


--
-- TOC entry 4878 (class 2604 OID 31254)
-- Name: pago_venta id_pago; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta ALTER COLUMN id_pago SET DEFAULT nextval('ventas.pago_venta_id_pago_seq'::regclass);


--
-- TOC entry 4872 (class 2604 OID 31203)
-- Name: venta id_venta; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta ALTER COLUMN id_venta SET DEFAULT nextval('ventas.venta_id_venta_seq'::regclass);


--
-- TOC entry 5117 (class 0 OID 30948)
-- Dependencies: 225
-- Data for Name: categoria; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.categoria (id_categoria, nombre, descripcion, activo, id_categoria_padre) FROM stdin;
5	zapatos deportivos	zapatos para hacer deporte	t	\N
6	zapatos deportivos unisex	zapato para cualquier tipo de genero	t	5
4	Zapatos De Nieve	\N	f	\N
\.


--
-- TOC entry 5119 (class 0 OID 30962)
-- Dependencies: 227
-- Data for Name: cliente; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.cliente (id_cliente, nombre, apellido, cedula, telefono, correo, direccion, activo) FROM stdin;
\.


--
-- TOC entry 5121 (class 0 OID 30978)
-- Dependencies: 229
-- Data for Name: color; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.color (id_color, nombre, activo) FROM stdin;
4	Rojo	t
\.


--
-- TOC entry 5123 (class 0 OID 30990)
-- Dependencies: 231
-- Data for Name: proveedor; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.proveedor (id_proveedor, nombre, telefono, correo, direccion, activo) FROM stdin;
\.


--
-- TOC entry 5125 (class 0 OID 31002)
-- Dependencies: 233
-- Data for Name: sucursal; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.sucursal (id_sucursal, nombre, direccion, telefono, rif, activo, fecha_registro) FROM stdin;
6	Fifty Four Cabudare	Cabudare Centro	04125551041	J-12345678-9	t	2025-11-22
8	Ginza	Baquisimeto Av Vargas	04125551041	J-12345678-3	t	2025-11-23
5	Global Sport	Yaracuy	0412-555-10-41	J-12345678-9	t	2025-11-22
\.


--
-- TOC entry 5127 (class 0 OID 31017)
-- Dependencies: 235
-- Data for Name: talla; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.talla (id_talla, rango_talla, activo) FROM stdin;
4	39-40-41-42-43-44	t
\.


--
-- TOC entry 5135 (class 0 OID 31091)
-- Dependencies: 243
-- Data for Name: metodo_pago; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.metodo_pago (id_metodo_pago, nombre, activo) FROM stdin;
\.


--
-- TOC entry 5137 (class 0 OID 31103)
-- Dependencies: 245
-- Data for Name: moneda; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.moneda (id_moneda, nombre, codigo, activo) FROM stdin;
\.


--
-- TOC entry 5139 (class 0 OID 31118)
-- Dependencies: 247
-- Data for Name: tasa_cambio; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.tasa_cambio (id_tasa, id_moneda, fecha, tasa, activo) FROM stdin;
\.


--
-- TOC entry 5143 (class 0 OID 31173)
-- Dependencies: 251
-- Data for Name: inventario; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.inventario (id_inventario, id_producto, id_sucursal, cantidad, minimo, activo) FROM stdin;
3	5	5	24	10	t
\.


--
-- TOC entry 5141 (class 0 OID 31136)
-- Dependencies: 249
-- Data for Name: producto; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.producto (id_producto, nombre, descripcion, id_categoria, id_color, id_talla, precio_venta, id_proveedor, activo, codigo_barra, precio_compra) FROM stdin;
5	Zapato Numero 1	\N	4	4	4	20.00	\N	t	zap-01	10.00
\.


--
-- TOC entry 5133 (class 0 OID 31073)
-- Dependencies: 241
-- Data for Name: auditoria; Type: TABLE DATA; Schema: seguridad_acceso; Owner: postgres
--

COPY seguridad_acceso.auditoria (id_auditoria, id_usuario, tabla, accion, fecha, descripcion) FROM stdin;
\.


--
-- TOC entry 5129 (class 0 OID 31029)
-- Dependencies: 237
-- Data for Name: rol; Type: TABLE DATA; Schema: seguridad_acceso; Owner: postgres
--

COPY seguridad_acceso.rol (id_rol, nombre_rol, descripcion, activo) FROM stdin;
1	Gerente	Responsable de supervisar ventas, inventario y personal.	t
2	Administrador	Administrador del sistema con acceso completo.	t
3	Cajero	Encargado de registrar ventas y procesar pagos.	t
4	Depositario	Encargado de gestionar depósitos y movimientos de caja.	t
5	Vendedor	Encargado de atender clientes y realizar ventas.	t
\.


--
-- TOC entry 5131 (class 0 OID 31043)
-- Dependencies: 239
-- Data for Name: usuario; Type: TABLE DATA; Schema: seguridad_acceso; Owner: postgres
--

COPY seguridad_acceso.usuario (id_usuario, nombre, apellido, cedula, email, telefono, direccion, "contraseña", id_rol, id_sucursal, fecha_registro, activo) FROM stdin;
5	Rafael Andres	Alvarez Tortoza	V-31.757.781	admin@gmail.com	0412-555-10-41	\N	$2y$12$9JZWsbx4WcTne7Hb.WDjMODP2Vkdj1/s4Kc1RrN5jMrt8WUjxfVZ2	1	\N	2025-11-22	t
6	Andres Alejandro	Alvarez Tortoza	V-12.345.678	vendedor@gmail.com	0416-123-45-67	Asdiajsdijaisdjiasjdiasdjij	$2y$12$dc565gjIFFgWw6v4K30m8.Ywu2ElOJovLB8yRhigjkYdgH.Ktwl7a	5	6	2025-11-22	t
\.


--
-- TOC entry 5147 (class 0 OID 31223)
-- Dependencies: 255
-- Data for Name: detalle_venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.detalle_venta (id_detalle, id_venta, id_producto, cantidad, precio_unitario, subtotal, activo) FROM stdin;
\.


--
-- TOC entry 5149 (class 0 OID 31251)
-- Dependencies: 257
-- Data for Name: pago_venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.pago_venta (id_pago, id_venta, id_metodo_pago, monto, id_moneda, tasa, activo) FROM stdin;
\.


--
-- TOC entry 5145 (class 0 OID 31200)
-- Dependencies: 253
-- Data for Name: venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.venta (id_venta, id_cliente, id_usuario, fecha, total, activo) FROM stdin;
\.


--
-- TOC entry 5172 (class 0 OID 0)
-- Dependencies: 224
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.categoria_id_categoria_seq', 6, true);


--
-- TOC entry 5173 (class 0 OID 0)
-- Dependencies: 226
-- Name: cliente_id_cliente_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.cliente_id_cliente_seq', 1, false);


--
-- TOC entry 5174 (class 0 OID 0)
-- Dependencies: 228
-- Name: color_id_color_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.color_id_color_seq', 4, true);


--
-- TOC entry 5175 (class 0 OID 0)
-- Dependencies: 230
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.proveedor_id_proveedor_seq', 1, false);


--
-- TOC entry 5176 (class 0 OID 0)
-- Dependencies: 232
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.sucursal_id_sucursal_seq', 8, true);


--
-- TOC entry 5177 (class 0 OID 0)
-- Dependencies: 234
-- Name: talla_id_talla_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.talla_id_talla_seq', 4, true);


--
-- TOC entry 5178 (class 0 OID 0)
-- Dependencies: 242
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.metodo_pago_id_metodo_pago_seq', 1, false);


--
-- TOC entry 5179 (class 0 OID 0)
-- Dependencies: 244
-- Name: moneda_id_moneda_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.moneda_id_moneda_seq', 1, false);


--
-- TOC entry 5180 (class 0 OID 0)
-- Dependencies: 246
-- Name: tasa_cambio_id_tasa_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.tasa_cambio_id_tasa_seq', 1, false);


--
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 250
-- Name: inventario_id_inventario_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.inventario_id_inventario_seq', 3, true);


--
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 248
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.producto_id_producto_seq', 5, true);


--
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 240
-- Name: auditoria_id_auditoria_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.auditoria_id_auditoria_seq', 1, false);


--
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 236
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.rol_id_rol_seq', 5, true);


--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 238
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.usuario_id_usuario_seq', 6, true);


--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 254
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.detalle_venta_id_detalle_seq', 1, false);


--
-- TOC entry 5187 (class 0 OID 0)
-- Dependencies: 256
-- Name: pago_venta_id_pago_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.pago_venta_id_pago_seq', 1, false);


--
-- TOC entry 5188 (class 0 OID 0)
-- Dependencies: 252
-- Name: venta_id_venta_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.venta_id_venta_seq', 1, false);


--
-- TOC entry 4893 (class 2606 OID 30960)
-- Name: categoria categoria_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria
    ADD CONSTRAINT categoria_nombre_key UNIQUE (nombre);


--
-- TOC entry 4895 (class 2606 OID 30958)
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id_categoria);


--
-- TOC entry 4897 (class 2606 OID 30974)
-- Name: cliente cliente_cedula_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_cedula_key UNIQUE (cedula);


--
-- TOC entry 4899 (class 2606 OID 30976)
-- Name: cliente cliente_correo_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_correo_key UNIQUE (correo);


--
-- TOC entry 4901 (class 2606 OID 30972)
-- Name: cliente cliente_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente);


--
-- TOC entry 4903 (class 2606 OID 30988)
-- Name: color color_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color
    ADD CONSTRAINT color_nombre_key UNIQUE (nombre);


--
-- TOC entry 4905 (class 2606 OID 30986)
-- Name: color color_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color
    ADD CONSTRAINT color_pkey PRIMARY KEY (id_color);


--
-- TOC entry 4907 (class 2606 OID 31000)
-- Name: proveedor proveedor_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.proveedor
    ADD CONSTRAINT proveedor_pkey PRIMARY KEY (id_proveedor);


--
-- TOC entry 4909 (class 2606 OID 31015)
-- Name: sucursal sucursal_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal
    ADD CONSTRAINT sucursal_nombre_key UNIQUE (nombre);


--
-- TOC entry 4911 (class 2606 OID 31013)
-- Name: sucursal sucursal_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal
    ADD CONSTRAINT sucursal_pkey PRIMARY KEY (id_sucursal);


--
-- TOC entry 4913 (class 2606 OID 31027)
-- Name: talla talla_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla
    ADD CONSTRAINT talla_nombre_key UNIQUE (rango_talla);


--
-- TOC entry 4915 (class 2606 OID 31025)
-- Name: talla talla_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla
    ADD CONSTRAINT talla_pkey PRIMARY KEY (id_talla);


--
-- TOC entry 4929 (class 2606 OID 31101)
-- Name: metodo_pago metodo_pago_nombre_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago
    ADD CONSTRAINT metodo_pago_nombre_key UNIQUE (nombre);


--
-- TOC entry 4931 (class 2606 OID 31099)
-- Name: metodo_pago metodo_pago_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago
    ADD CONSTRAINT metodo_pago_pkey PRIMARY KEY (id_metodo_pago);


--
-- TOC entry 4933 (class 2606 OID 31116)
-- Name: moneda moneda_codigo_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_codigo_key UNIQUE (codigo);


--
-- TOC entry 4935 (class 2606 OID 31114)
-- Name: moneda moneda_nombre_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_nombre_key UNIQUE (nombre);


--
-- TOC entry 4937 (class 2606 OID 31112)
-- Name: moneda moneda_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_pkey PRIMARY KEY (id_moneda);


--
-- TOC entry 4939 (class 2606 OID 31129)
-- Name: tasa_cambio tasa_cambio_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio
    ADD CONSTRAINT tasa_cambio_pkey PRIMARY KEY (id_tasa);


--
-- TOC entry 4943 (class 2606 OID 31188)
-- Name: inventario inventario_id_producto_id_sucursal_key; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_producto_id_sucursal_key UNIQUE (id_producto, id_sucursal);


--
-- TOC entry 4945 (class 2606 OID 31186)
-- Name: inventario inventario_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_pkey PRIMARY KEY (id_inventario);


--
-- TOC entry 4941 (class 2606 OID 31151)
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- TOC entry 4927 (class 2606 OID 31084)
-- Name: auditoria auditoria_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.auditoria
    ADD CONSTRAINT auditoria_pkey PRIMARY KEY (id_auditoria);


--
-- TOC entry 4917 (class 2606 OID 31041)
-- Name: rol rol_nombre_rol_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol
    ADD CONSTRAINT rol_nombre_rol_key UNIQUE (nombre_rol);


--
-- TOC entry 4919 (class 2606 OID 31039)
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- TOC entry 4921 (class 2606 OID 31059)
-- Name: usuario usuario_cedula_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_cedula_key UNIQUE (cedula);


--
-- TOC entry 4923 (class 2606 OID 31061)
-- Name: usuario usuario_email_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_email_key UNIQUE (email);


--
-- TOC entry 4925 (class 2606 OID 31057)
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- TOC entry 4949 (class 2606 OID 31239)
-- Name: detalle_venta detalle_venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_pkey PRIMARY KEY (id_detalle);


--
-- TOC entry 4951 (class 2606 OID 31266)
-- Name: pago_venta pago_venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_pkey PRIMARY KEY (id_pago);


--
-- TOC entry 4947 (class 2606 OID 31211)
-- Name: venta venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_pkey PRIMARY KEY (id_venta);


--
-- TOC entry 4955 (class 2606 OID 31130)
-- Name: tasa_cambio tasa_cambio_id_moneda_fkey; Type: FK CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio
    ADD CONSTRAINT tasa_cambio_id_moneda_fkey FOREIGN KEY (id_moneda) REFERENCES finanzas.moneda(id_moneda) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4960 (class 2606 OID 31189)
-- Name: inventario inventario_id_producto_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4961 (class 2606 OID 31194)
-- Name: inventario inventario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4956 (class 2606 OID 31152)
-- Name: producto producto_id_categoria_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_categoria_fkey FOREIGN KEY (id_categoria) REFERENCES core.categoria(id_categoria) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4957 (class 2606 OID 31157)
-- Name: producto producto_id_color_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_color_fkey FOREIGN KEY (id_color) REFERENCES core.color(id_color) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4958 (class 2606 OID 31167)
-- Name: producto producto_id_proveedor_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_proveedor_fkey FOREIGN KEY (id_proveedor) REFERENCES core.proveedor(id_proveedor) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4959 (class 2606 OID 31162)
-- Name: producto producto_id_talla_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_talla_fkey FOREIGN KEY (id_talla) REFERENCES core.talla(id_talla) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4954 (class 2606 OID 31085)
-- Name: auditoria auditoria_id_usuario_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.auditoria
    ADD CONSTRAINT auditoria_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES seguridad_acceso.usuario(id_usuario) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4952 (class 2606 OID 31062)
-- Name: usuario usuario_id_rol_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES seguridad_acceso.rol(id_rol) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4953 (class 2606 OID 31067)
-- Name: usuario usuario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4964 (class 2606 OID 31245)
-- Name: detalle_venta detalle_venta_id_producto_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4965 (class 2606 OID 31240)
-- Name: detalle_venta detalle_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES ventas.venta(id_venta) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4966 (class 2606 OID 31272)
-- Name: pago_venta pago_venta_id_metodo_pago_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_metodo_pago_fkey FOREIGN KEY (id_metodo_pago) REFERENCES finanzas.metodo_pago(id_metodo_pago) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4967 (class 2606 OID 31277)
-- Name: pago_venta pago_venta_id_moneda_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_moneda_fkey FOREIGN KEY (id_moneda) REFERENCES finanzas.moneda(id_moneda) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4968 (class 2606 OID 31267)
-- Name: pago_venta pago_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES ventas.venta(id_venta) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4962 (class 2606 OID 31212)
-- Name: venta venta_id_cliente_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES core.cliente(id_cliente) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4963 (class 2606 OID 31217)
-- Name: venta venta_id_usuario_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES seguridad_acceso.usuario(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT;


-- Completed on 2025-11-23 11:39:33

--
-- PostgreSQL database dump complete
--

\unrestrict Kvi8Ch4dFbTlXTXCclP0dlVGycdiMMNlMU28oega8A7B8Aib3wzk8j7RjoZtbtX

