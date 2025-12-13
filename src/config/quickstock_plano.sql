--
-- PostgreSQL database dump
--

\restrict p6xPLSYehzn8LWom8w81TMDzxiRdtjPtGuYV4tWL8Cb6PO1IZpURmyMU9N4tpcZ

-- Dumped from database version 18.0
-- Dumped by pg_dump version 18.0

-- Started on 2025-12-13 15:07:18

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
-- TOC entry 5174 (class 0 OID 0)
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
-- TOC entry 5175 (class 0 OID 0)
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
-- TOC entry 5176 (class 0 OID 0)
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
-- TOC entry 5177 (class 0 OID 0)
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
-- TOC entry 5178 (class 0 OID 0)
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
-- TOC entry 5179 (class 0 OID 0)
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
-- TOC entry 5180 (class 0 OID 0)
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
-- TOC entry 5181 (class 0 OID 0)
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
-- TOC entry 5182 (class 0 OID 0)
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
-- TOC entry 5183 (class 0 OID 0)
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
    precio_unitario numeric(12,2) NOT NULL
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
-- TOC entry 5184 (class 0 OID 0)
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
-- TOC entry 5185 (class 0 OID 0)
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
-- TOC entry 5186 (class 0 OID 0)
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
-- TOC entry 5187 (class 0 OID 0)
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
-- TOC entry 5188 (class 0 OID 0)
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
    activo boolean DEFAULT true,
    CONSTRAINT detalle_venta_cantidad_check CHECK ((cantidad > 0)),
    CONSTRAINT detalle_venta_precio_unitario_check CHECK ((precio_unitario > (0)::numeric))
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
-- TOC entry 5189 (class 0 OID 0)
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
-- TOC entry 5190 (class 0 OID 0)
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
    activo boolean DEFAULT true
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
-- TOC entry 5191 (class 0 OID 0)
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
-- TOC entry 4886 (class 2604 OID 32226)
-- Name: compra id_compra; Type: DEFAULT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra ALTER COLUMN id_compra SET DEFAULT nextval('inventario.compra_id_compra_seq'::regclass);


--
-- TOC entry 4891 (class 2604 OID 32265)
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
-- TOC entry 4883 (class 2604 OID 31254)
-- Name: pago_venta id_pago; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta ALTER COLUMN id_pago SET DEFAULT nextval('ventas.pago_venta_id_pago_seq'::regclass);


--
-- TOC entry 4878 (class 2604 OID 31203)
-- Name: venta id_venta; Type: DEFAULT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta ALTER COLUMN id_venta SET DEFAULT nextval('ventas.venta_id_venta_seq'::regclass);


--
-- TOC entry 5134 (class 0 OID 30948)
-- Dependencies: 225
-- Data for Name: categoria; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.categoria (id_categoria, nombre, descripcion, activo, id_categoria_padre) FROM stdin;
6	zapatos deportivos unisex	zapato para cualquier tipo de genero	t	5
5	zapatos deportivos	zapatos para hacer deporte	t	\N
4	Zapatos De Nieve	\N	t	\N
7	zandalias dama	calzado para mujer	t	\N
8	botas	\N	t	\N
\.


--
-- TOC entry 5136 (class 0 OID 30962)
-- Dependencies: 227
-- Data for Name: cliente; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.cliente (id_cliente, nombre, apellido, cedula, telefono, correo, direccion, activo) FROM stdin;
5	madga	perozo	v-19621445	0424-1111111	magda@gmail.com	aiscnianscinasci	t
6	ejemplo ejemplo	ejemplo ejemplo	v-11111111	0424-1111111	ejemplo@gmail.com	ejemplo ejemplo ejemplo ejemplo	t
1	Rafael Andres	Alvarez Tortoza	V-31.757.781	0412-555-10-41	alvarezrafaelat@gmail.com	cabudare centro las mercedes	t
10	juandiego jose	alejos tortoza	V-11.111.111	0424-555-55-55	diego@gmail.com	piritu portuguesa	t
\.


--
-- TOC entry 5138 (class 0 OID 30978)
-- Dependencies: 229
-- Data for Name: color; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.color (id_color, nombre, activo) FROM stdin;
4	Rojo	t
5	Blanco	t
6	Marron	t
8	Azul	t
7	Verde	t
9	Negro	t
10	amarillo	t
11	Beige	t
\.


--
-- TOC entry 5140 (class 0 OID 30990)
-- Dependencies: 231
-- Data for Name: proveedor; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.proveedor (id_proveedor, nombre, telefono, correo, direccion, activo) FROM stdin;
1	Proveedor Ejemplo S.A.	0412-555-55-55	contacto@proveedorejemplo.com	Av. Principal #123, Ciudad	t
2	Calzados Juandiegiño	0412-555-10-55	juagdiego@gmail.com	piritu portuguesa	t
\.


--
-- TOC entry 5142 (class 0 OID 31002)
-- Dependencies: 233
-- Data for Name: sucursal; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.sucursal (id_sucursal, nombre, direccion, telefono, rif, activo, fecha_registro) FROM stdin;
6	Fifty Four Cabudare	Cabudare Centro	04125551041	J-12345678-9	t	2025-11-22
8	Ginza	Baquisimeto Av Vargas	04125551041	J-12345678-3	t	2025-11-23
9	Ginza Outlet	Oasdkoaskdoaksoddkoasd	0424-589-87-63	J-12345678-1	t	2025-11-30
5	Global Sport	Yaracuy	0412-555-10-41	J-12345678-9	t	2025-11-22
10	Barquicenter	Centro De Barquisimeto Av Vargas Carrera X Entre Calles X Y X	+58 412-555-90-90	J-12345678-0	t	2025-12-13
\.


--
-- TOC entry 5144 (class 0 OID 31017)
-- Dependencies: 235
-- Data for Name: talla; Type: TABLE DATA; Schema: core; Owner: postgres
--

COPY core.talla (id_talla, rango_talla, activo) FROM stdin;
5	40-41-42	t
6	35-40	t
7	30-40	t
4	39-44	t
8	20-28	t
9	35-41	t
\.


--
-- TOC entry 5150 (class 0 OID 31091)
-- Dependencies: 241
-- Data for Name: metodo_pago; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.metodo_pago (id_metodo_pago, nombre, activo, descripcion, referencia) FROM stdin;
1	Zelle	t		t
3	Efectivo En Dolares	t	money	f
2	Transferencia	t	transferencia bancara por medio de plataforma bancaria	t
\.


--
-- TOC entry 5152 (class 0 OID 31103)
-- Dependencies: 243
-- Data for Name: moneda; Type: TABLE DATA; Schema: finanzas; Owner: postgres
--

COPY finanzas.moneda (id_moneda, nombre, codigo, activo, simbolo) FROM stdin;
1	Bolívar	VES	t	Bs.
2	Dólar	USD	t	$
3	Euro	EUR	t	€
\.


--
-- TOC entry 5154 (class 0 OID 31118)
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
22	1	2025-12-06	257.9287	t	API
23	3	2025-12-06	0.8586	t	API
24	1	2025-12-08	257.9287	t	API
25	3	2025-12-08	0.8589	t	API
26	1	2025-12-08	257.9287	t	API
27	3	2025-12-08	0.8589	t	API
28	1	2025-12-08	257.9287	t	API
29	3	2025-12-08	0.8589	t	API
30	1	2025-12-10	262.1036	t	API
31	3	2025-12-10	0.8597	t	API
32	1	2025-12-10	262.1036	t	API
33	3	2025-12-10	0.8597	t	API
34	1	2025-12-10	262.1036	t	API
35	3	2025-12-10	0.8597	t	API
36	1	2025-12-10	262.1036	t	API
37	3	2025-12-10	0.8597	t	API
38	1	2025-12-10	262.1036	t	API
39	3	2025-12-10	0.8597	t	API
40	1	2025-12-10	262.1036	t	API
41	3	2025-12-10	0.8597	t	API
42	1	2025-12-10	262.1036	t	API
43	3	2025-12-10	0.8597	t	API
44	1	2025-12-10	262.1036	t	API
45	3	2025-12-10	0.8597	t	API
46	1	2025-12-10	262.1036	t	API
47	3	2025-12-10	0.8597	t	API
48	1	2025-12-10	262.1036	t	API
49	3	2025-12-10	0.8597	t	API
50	1	2025-12-10	262.1036	t	API
51	3	2025-12-10	0.8597	t	API
52	1	2025-12-10	262.1036	t	API
53	3	2025-12-10	0.8597	t	API
54	1	2025-12-10	262.1036	t	API
55	3	2025-12-10	0.8597	t	API
56	1	2025-12-10	262.1036	t	API
57	3	2025-12-10	0.8597	t	API
58	1	2025-12-10	262.1036	t	API
59	3	2025-12-10	0.8597	t	API
60	1	2025-12-10	262.1036	t	API
61	3	2025-12-10	0.8597	t	API
62	1	2025-12-10	262.1036	t	API
63	3	2025-12-10	0.8597	t	API
64	1	2025-12-10	262.1036	t	API
65	3	2025-12-10	0.8597	t	API
66	1	2025-12-10	262.1036	t	API
67	3	2025-12-10	0.8597	t	API
68	1	2025-12-10	262.1036	t	API
69	3	2025-12-10	0.8597	t	API
70	1	2025-12-10	262.1036	t	API
71	3	2025-12-10	0.8597	t	API
72	1	2025-12-10	262.1036	t	API
73	3	2025-12-10	0.8597	t	API
74	1	2025-12-10	262.1036	t	API
75	3	2025-12-10	0.8597	t	API
76	1	2025-12-10	262.1036	t	API
77	3	2025-12-10	0.8597	t	API
78	1	2025-12-10	262.1036	t	API
79	3	2025-12-10	0.8597	t	API
80	1	2025-12-10	262.1036	t	API
81	3	2025-12-10	0.8597	t	API
82	1	2025-12-10	262.1036	t	API
83	3	2025-12-10	0.8597	t	API
84	1	2025-12-10	262.1036	t	API
85	3	2025-12-10	0.8597	t	API
86	1	2025-12-10	262.1036	t	API
87	3	2025-12-10	0.8597	t	API
88	1	2025-12-10	262.1036	t	API
89	3	2025-12-10	0.8597	t	API
90	1	2025-12-10	262.1036	t	API
91	3	2025-12-10	0.8597	t	API
92	1	2025-12-10	262.1036	t	API
93	3	2025-12-10	0.8597	t	API
94	1	2025-12-10	262.1036	t	API
95	3	2025-12-10	0.8597	t	API
96	1	2025-12-10	262.1036	t	API
97	3	2025-12-10	0.8597	t	API
98	1	2025-12-10	262.1036	t	API
99	3	2025-12-10	0.8597	t	API
100	1	2025-12-10	262.1036	t	API
101	3	2025-12-10	0.8597	t	API
102	1	2025-12-10	262.1036	t	API
103	3	2025-12-10	0.8597	t	API
104	1	2025-12-10	262.1036	t	API
105	2	2025-12-10	1.0000	t	API
106	3	2025-12-10	0.8597	t	API
107	1	2025-12-10	262.1036	t	API
108	2	2025-12-10	1.0000	t	API
109	3	2025-12-10	0.8597	t	API
110	1	2025-12-10	262.1036	t	API
111	2	2025-12-10	1.0000	t	API
112	3	2025-12-10	0.8597	t	API
113	1	2025-12-10	262.1036	t	API
114	2	2025-12-10	1.0000	t	API
115	3	2025-12-10	0.8597	t	API
116	1	2025-12-10	262.1036	t	API
117	2	2025-12-10	1.0000	t	API
118	3	2025-12-10	0.8597	t	API
119	1	2025-12-10	262.1036	t	API
120	2	2025-12-10	1.0000	t	API
121	3	2025-12-10	0.8597	t	API
122	1	2025-12-10	262.1036	t	API
123	2	2025-12-10	1.0000	t	API
124	3	2025-12-10	0.8597	t	API
125	1	2025-12-10	262.1036	t	API
126	2	2025-12-10	1.0000	t	API
127	3	2025-12-10	0.8597	t	API
128	1	2025-12-10	262.1036	t	API
129	2	2025-12-10	1.0000	t	API
130	3	2025-12-10	0.8597	t	API
131	1	2025-12-10	262.1036	t	API
132	2	2025-12-10	1.0000	t	API
133	3	2025-12-10	0.8597	t	API
134	1	2025-12-10	262.1036	t	API
135	2	2025-12-10	1.0000	t	API
136	3	2025-12-10	0.8597	t	API
137	1	2025-12-10	262.1036	t	API
138	2	2025-12-10	1.0000	t	API
139	3	2025-12-10	0.8597	t	API
140	1	2025-12-10	262.1036	t	API
141	2	2025-12-10	1.0000	t	API
142	3	2025-12-10	0.8597	t	API
143	1	2025-12-10	262.1036	t	API
144	2	2025-12-10	1.0000	t	API
145	3	2025-12-10	0.8597	t	API
146	1	2025-12-10	262.1036	t	API
147	2	2025-12-10	1.0000	t	API
148	3	2025-12-10	0.8597	t	API
149	1	2025-12-10	262.1036	t	API
150	2	2025-12-10	1.0000	t	API
151	3	2025-12-10	0.8597	t	API
152	1	2025-12-10	262.1036	t	API
153	2	2025-12-10	1.0000	t	API
154	3	2025-12-10	0.8597	t	API
155	1	2025-12-10	262.1036	t	API
156	2	2025-12-10	1.0000	t	API
157	3	2025-12-10	0.8597	t	API
158	1	2025-12-10	262.1036	t	API
159	2	2025-12-10	1.0000	t	API
160	3	2025-12-10	0.8597	t	API
161	1	2025-12-10	262.1036	t	API
162	2	2025-12-10	1.0000	t	API
163	3	2025-12-10	0.8597	t	API
164	1	2025-12-10	262.1036	t	API
165	2	2025-12-10	1.0000	t	API
166	3	2025-12-10	0.8597	t	API
167	1	2025-12-10	262.1036	t	API
168	2	2025-12-10	1.0000	t	API
169	3	2025-12-10	0.8597	t	API
170	1	2025-12-10	262.1036	t	API
171	2	2025-12-10	1.0000	t	API
172	3	2025-12-10	0.8597	t	API
173	1	2025-12-10	262.1036	t	API
174	2	2025-12-10	1.0000	t	API
175	3	2025-12-10	0.8597	t	API
176	1	2025-12-10	262.1036	t	API
177	2	2025-12-10	1.0000	t	API
178	3	2025-12-10	0.8597	t	API
179	1	2025-12-10	262.1036	t	API
180	2	2025-12-10	1.0000	t	API
181	3	2025-12-10	0.8597	t	API
182	1	2025-12-10	262.1036	t	API
183	2	2025-12-10	1.0000	t	API
184	3	2025-12-10	0.8597	t	API
185	1	2025-12-10	262.1036	t	API
186	2	2025-12-10	1.0000	t	API
187	3	2025-12-10	0.8597	t	API
188	1	2025-12-10	262.1036	t	API
189	2	2025-12-10	1.0000	t	API
190	3	2025-12-10	0.8597	t	API
191	1	2025-12-10	262.1036	t	API
192	2	2025-12-10	1.0000	t	API
193	3	2025-12-10	0.8597	t	API
194	1	2025-12-10	262.1036	t	API
195	2	2025-12-10	1.0000	t	API
196	3	2025-12-10	0.8597	t	API
197	1	2025-12-10	262.1036	t	API
198	2	2025-12-10	1.0000	t	API
199	3	2025-12-10	0.8597	t	API
200	1	2025-12-10	262.1036	t	API
201	2	2025-12-10	1.0000	t	API
202	3	2025-12-10	0.8597	t	API
203	1	2025-12-10	262.1036	t	API
204	2	2025-12-10	1.0000	t	API
205	3	2025-12-10	0.8597	t	API
206	1	2025-12-10	262.1036	t	API
207	2	2025-12-10	1.0000	t	API
208	3	2025-12-10	0.8597	t	API
209	1	2025-12-10	262.1036	t	API
210	2	2025-12-10	1.0000	t	API
211	3	2025-12-10	0.8597	t	API
212	1	2025-12-10	262.1036	t	API
213	2	2025-12-10	1.0000	t	API
214	3	2025-12-10	0.8597	t	API
215	1	2025-12-10	262.1036	t	API
216	2	2025-12-10	1.0000	t	API
217	3	2025-12-10	0.8597	t	API
218	1	2025-12-10	262.1036	t	API
219	2	2025-12-10	1.0000	t	API
220	3	2025-12-10	0.8597	t	API
221	1	2025-12-10	262.1036	t	API
222	2	2025-12-10	1.0000	t	API
223	3	2025-12-10	0.8597	t	API
224	1	2025-12-10	262.1036	t	API
225	2	2025-12-10	1.0000	t	API
226	3	2025-12-10	0.8597	t	API
227	1	2025-12-10	262.1036	t	API
228	2	2025-12-10	1.0000	t	API
229	3	2025-12-10	0.8597	t	API
230	1	2025-12-10	262.1036	t	API
231	2	2025-12-10	1.0000	t	API
232	3	2025-12-10	0.8597	t	API
233	1	2025-12-10	262.1036	t	API
234	2	2025-12-10	1.0000	t	API
235	3	2025-12-10	0.8597	t	API
236	1	2025-12-10	262.1036	t	API
237	2	2025-12-10	1.0000	t	API
238	3	2025-12-10	0.8597	t	API
239	1	2025-12-10	262.1036	t	API
240	2	2025-12-10	1.0000	t	API
241	3	2025-12-10	0.8597	t	API
242	1	2025-12-10	262.1036	t	API
243	2	2025-12-10	1.0000	t	API
244	3	2025-12-10	0.8597	t	API
245	1	2025-12-10	262.1036	t	API
246	2	2025-12-10	1.0000	t	API
247	3	2025-12-10	0.8597	t	API
248	1	2025-12-10	262.1036	t	API
249	2	2025-12-10	1.0000	t	API
250	3	2025-12-10	0.8597	t	API
251	1	2025-12-10	262.1036	t	API
252	2	2025-12-10	1.0000	t	API
253	3	2025-12-10	0.8597	t	API
254	1	2025-12-10	262.1036	t	API
255	2	2025-12-10	1.0000	t	API
256	3	2025-12-10	0.8597	t	API
257	1	2025-12-10	262.1036	t	API
258	2	2025-12-10	1.0000	t	API
259	3	2025-12-10	0.8597	t	API
260	1	2025-12-10	262.1036	t	API
261	2	2025-12-10	1.0000	t	API
262	3	2025-12-10	0.8597	t	API
263	1	2025-12-10	262.1036	t	API
264	2	2025-12-10	1.0000	t	API
265	3	2025-12-10	0.8597	t	API
266	1	2025-12-10	262.1036	t	API
267	2	2025-12-10	1.0000	t	API
268	3	2025-12-10	0.8597	t	API
269	1	2025-12-10	262.1036	t	API
270	2	2025-12-10	1.0000	t	API
271	3	2025-12-10	0.8597	t	API
272	1	2025-12-10	262.1036	t	API
273	2	2025-12-10	1.0000	t	API
274	3	2025-12-10	0.8597	t	API
275	1	2025-12-10	262.1036	t	API
276	2	2025-12-10	1.0000	t	API
277	3	2025-12-10	0.8597	t	API
278	1	2025-12-10	262.1036	t	API
279	2	2025-12-10	1.0000	t	API
280	3	2025-12-10	0.8597	t	API
281	1	2025-12-10	262.1036	t	API
282	2	2025-12-10	1.0000	t	API
283	3	2025-12-10	0.8597	t	API
284	1	2025-12-10	262.1036	t	API
285	2	2025-12-10	1.0000	t	API
286	3	2025-12-10	0.8597	t	API
287	1	2025-12-10	262.1036	t	API
288	2	2025-12-10	1.0000	t	API
289	3	2025-12-10	0.8597	t	API
290	1	2025-12-10	262.1036	t	API
291	2	2025-12-10	1.0000	t	API
292	3	2025-12-10	0.8597	t	API
293	1	2025-12-10	262.1036	t	API
294	2	2025-12-10	1.0000	t	API
295	3	2025-12-10	0.8597	t	API
296	1	2025-12-10	262.1036	t	API
297	2	2025-12-10	1.0000	t	API
298	3	2025-12-10	0.8597	t	API
299	1	2025-12-10	262.1036	t	API
300	2	2025-12-10	1.0000	t	API
301	3	2025-12-10	0.8597	t	API
302	1	2025-12-10	262.1036	t	API
303	2	2025-12-10	1.0000	t	API
304	3	2025-12-10	0.8597	t	API
305	1	2025-12-10	262.1036	t	API
306	2	2025-12-10	1.0000	t	API
307	3	2025-12-10	0.8597	t	API
308	1	2025-12-10	262.1036	t	API
309	2	2025-12-10	1.0000	t	API
310	3	2025-12-10	0.8597	t	API
311	1	2025-12-10	262.1036	t	API
312	2	2025-12-10	1.0000	t	API
313	3	2025-12-10	0.8597	t	API
314	1	2025-12-10	262.1036	t	API
315	2	2025-12-10	1.0000	t	API
316	3	2025-12-10	0.8597	t	API
317	1	2025-12-10	262.1036	t	API
318	2	2025-12-10	1.0000	t	API
319	3	2025-12-10	0.8597	t	API
320	1	2025-12-10	262.1036	t	API
321	2	2025-12-10	1.0000	t	API
322	3	2025-12-10	0.8597	t	API
323	1	2025-12-10	262.1036	t	API
324	2	2025-12-10	1.0000	t	API
325	3	2025-12-10	0.8597	t	API
326	1	2025-12-10	262.1036	t	API
327	2	2025-12-10	1.0000	t	API
328	3	2025-12-10	0.8597	t	API
329	1	2025-12-10	262.1036	t	API
330	2	2025-12-10	1.0000	t	API
331	3	2025-12-10	0.8597	t	API
332	1	2025-12-10	262.1036	t	API
333	2	2025-12-10	1.0000	t	API
334	3	2025-12-10	0.8597	t	API
335	1	2025-12-10	262.1036	t	API
336	2	2025-12-10	1.0000	t	API
337	3	2025-12-10	0.8597	t	API
338	1	2025-12-10	262.1036	t	API
339	2	2025-12-10	1.0000	t	API
340	3	2025-12-10	0.8597	t	API
341	1	2025-12-10	262.1036	t	API
342	2	2025-12-10	1.0000	t	API
343	3	2025-12-10	0.8597	t	API
344	1	2025-12-10	262.1036	t	API
345	2	2025-12-10	1.0000	t	API
346	3	2025-12-10	0.8597	t	API
347	1	2025-12-10	262.1036	t	API
348	2	2025-12-10	1.0000	t	API
349	3	2025-12-10	0.8597	t	API
350	1	2025-12-10	262.1036	t	API
351	2	2025-12-10	1.0000	t	API
352	3	2025-12-10	0.8597	t	API
353	1	2025-12-10	262.1036	t	API
354	2	2025-12-10	1.0000	t	API
355	3	2025-12-10	0.8597	t	API
356	1	2025-12-10	262.1036	t	API
357	2	2025-12-10	1.0000	t	API
358	3	2025-12-10	0.8597	t	API
359	1	2025-12-10	262.1036	t	API
360	2	2025-12-10	1.0000	t	API
361	3	2025-12-10	0.8597	t	API
362	1	2025-12-10	262.1036	t	API
363	2	2025-12-10	1.0000	t	API
364	3	2025-12-10	0.8597	t	API
365	1	2025-12-10	262.1036	t	API
366	2	2025-12-10	1.0000	t	API
367	3	2025-12-10	0.8597	t	API
368	1	2025-12-10	262.1036	t	API
369	2	2025-12-10	1.0000	t	API
370	3	2025-12-10	0.8597	t	API
371	3	2025-12-10	304.8500	t	Manual
372	1	2025-12-10	262.1036	t	API
373	2	2025-12-10	1.0000	t	API
374	3	2025-12-10	0.8597	t	API
375	1	2025-12-10	262.1036	t	API
376	2	2025-12-10	1.0000	t	API
377	3	2025-12-10	0.8597	t	API
378	1	2025-12-10	262.1036	t	API
379	2	2025-12-10	1.0000	t	API
380	3	2025-12-10	0.8597	t	API
381	1	2025-12-10	262.1036	t	API
382	2	2025-12-10	1.0000	t	API
383	3	2025-12-10	0.8597	t	API
384	1	2025-12-10	262.1036	t	API
385	2	2025-12-10	1.0000	t	API
386	3	2025-12-10	0.8597	t	API
387	1	2025-12-10	262.1036	t	API
388	2	2025-12-10	1.0000	t	API
389	3	2025-12-10	0.8597	t	API
390	1	2025-12-10	262.1036	t	API
391	2	2025-12-10	1.0000	t	API
392	3	2025-12-10	0.8597	t	API
393	1	2025-12-10	262.1036	t	API
394	2	2025-12-10	1.0000	t	API
395	3	2025-12-10	0.8597	t	API
396	1	2025-12-10	262.1036	t	API
397	2	2025-12-10	1.0000	t	API
398	3	2025-12-10	0.8597	t	API
399	1	2025-12-10	262.1036	t	API
400	2	2025-12-10	1.0000	t	API
401	3	2025-12-10	0.8597	t	API
402	1	2025-12-10	262.1036	t	API
403	2	2025-12-10	1.0000	t	API
404	3	2025-12-10	0.8597	t	API
405	2	2025-12-10	1.0000	t	API
406	2	2025-12-10	1.0000	t	API
407	2	2025-12-10	1.0000	t	API
408	2	2025-12-10	1.0000	t	API
409	2	2025-12-10	1.0000	t	API
410	2	2025-12-10	1.0000	t	API
411	2	2025-12-10	1.0000	t	API
412	2	2025-12-10	1.0000	t	API
413	2	2025-12-10	1.0000	t	API
414	2	2025-12-10	1.0000	t	API
415	2	2025-12-10	1.0000	t	API
416	3	2025-12-10	304.8500	t	Manual
417	2	2025-12-10	1.0000	t	API
418	2	2025-12-10	1.0000	t	API
419	2	2025-12-10	1.0000	t	API
420	2	2025-12-10	1.0000	t	API
421	2	2025-12-10	1.0000	t	API
422	2	2025-12-10	1.0000	t	API
423	2	2025-12-10	1.0000	t	API
424	2	2025-12-10	1.0000	t	API
425	2	2025-12-10	1.0000	t	API
426	2	2025-12-10	1.0000	t	API
427	2	2025-12-10	1.0000	t	API
428	2	2025-12-10	1.0000	t	API
429	2	2025-12-10	1.0000	t	API
430	2	2025-12-10	1.0000	t	API
431	2	2025-12-10	1.0000	t	API
432	2	2025-12-10	1.0000	t	API
433	2	2025-12-10	1.0000	t	API
434	2	2025-12-10	1.0000	t	API
435	2	2025-12-10	1.0000	t	API
436	2	2025-12-10	1.0000	t	API
437	2	2025-12-10	1.0000	t	API
438	2	2025-12-10	1.0000	t	API
439	2	2025-12-10	1.0000	t	API
440	2	2025-12-10	1.0000	t	API
441	2	2025-12-10	1.0000	t	API
442	2	2025-12-10	1.0000	t	API
443	2	2025-12-10	1.0000	t	API
444	2	2025-12-10	1.0000	t	API
445	2	2025-12-10	1.0000	t	API
446	2	2025-12-10	1.0000	t	API
447	2	2025-12-10	1.0000	t	API
448	2	2025-12-10	1.0000	t	API
449	2	2025-12-10	1.0000	t	API
450	2	2025-12-10	1.0000	t	API
451	2	2025-12-10	1.0000	t	API
452	2	2025-12-10	1.0000	t	API
453	2	2025-12-10	1.0000	t	API
454	2	2025-12-10	1.0000	t	API
455	2	2025-12-10	1.0000	t	API
456	2	2025-12-10	1.0000	t	API
457	2	2025-12-10	1.0000	t	API
458	2	2025-12-10	1.0000	t	API
459	2	2025-12-10	1.0000	t	API
460	2	2025-12-10	1.0000	t	API
461	2	2025-12-10	1.0000	t	API
462	2	2025-12-10	1.0000	t	API
463	2	2025-12-10	1.0000	t	API
464	2	2025-12-10	1.0000	t	API
465	2	2025-12-10	1.0000	t	API
466	2	2025-12-10	1.0000	t	API
467	2	2025-12-10	1.0000	t	API
468	2	2025-12-10	1.0000	t	API
469	2	2025-12-10	1.0000	t	API
470	2	2025-12-10	1.0000	t	API
471	2	2025-12-10	1.0000	t	API
472	2	2025-12-10	1.0000	t	API
473	2	2025-12-10	1.0000	t	API
474	2	2025-12-10	1.0000	t	API
475	2	2025-12-10	1.0000	t	API
476	2	2025-12-10	1.0000	t	API
477	2	2025-12-10	1.0000	t	API
478	2	2025-12-10	1.0000	t	API
479	2	2025-12-10	1.0000	t	API
480	2	2025-12-10	1.0000	t	API
481	2	2025-12-10	1.0000	t	API
482	2	2025-12-10	1.0000	t	API
483	2	2025-12-10	1.0000	t	API
484	3	2025-12-10	304.5000	t	Manual
485	2	2025-12-10	1.0000	t	API
486	2	2025-12-10	1.0000	t	API
487	2	2025-12-10	1.0000	t	API
488	2	2025-12-10	1.0000	t	API
489	3	2025-12-10	304.5000	t	Manual
490	3	2025-12-10	304.5000	t	Manual
491	2	2025-12-10	262.1036	t	API
492	3	2025-12-10	304.5000	t	Manual
493	2	2025-12-10	265.0662	t	API
494	3	2025-12-10	309.1717	t	API
495	3	2025-12-10	205.5000	t	Manual
496	3	2025-12-10	309.1717	t	API
497	3	2025-12-10	305.5000	t	Manual
498	3	2025-12-10	309.1717	t	API
499	1	2025-12-10	262.1000	t	Manual
500	1	2025-12-10	262.1000	t	Manual
501	3	2025-12-10	263.1000	t	Manual
502	2	2025-12-10	262.1000	t	Manual
503	2	2025-12-10	265.0662	t	API
504	3	2025-12-10	309.1717	t	API
505	2	2025-12-10	262.1000	t	Manual
506	3	2025-12-10	304.8500	t	Manual
507	2	2025-12-10	265.0662	t	API
508	3	2025-12-10	309.1717	t	API
509	2	2025-12-10	262.1000	t	Manual
510	2	2025-12-10	265.0662	t	API
511	2	2025-12-10	262.1000	t	Manual
512	3	2025-12-10	304.5000	t	Manual
513	2	2025-12-10	265.0662	t	API
514	3	2025-12-10	309.1717	t	API
515	2	2025-12-10	254.1000	t	Manual
516	3	2025-12-10	304.5000	t	Manual
517	2	2025-12-10	265.0662	t	API
518	3	2025-12-10	309.1717	t	API
519	2	2025-12-10	257.1000	t	Manual
520	2	2025-12-10	265.0662	t	API
521	3	2025-12-10	304.5000	t	Manual
522	3	2025-12-10	309.1717	t	API
523	2	2025-12-11	267.7499	t	API
524	3	2025-12-11	314.1461	t	API
525	2	2025-12-13	270.7893	t	API
526	3	2025-12-13	317.8879	t	API
527	3	2025-12-13	317.8879	t	API
\.


--
-- TOC entry 5166 (class 0 OID 32223)
-- Dependencies: 257
-- Data for Name: compra; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.compra (id_compra, id_proveedor, id_sucursal, id_usuario, id_moneda, numero_factura, fecha_compra, fecha_registro, observaciones, estado, activo) FROM stdin;
1	1	6	5	1	0254	2025-11-23	2025-11-23 23:08:53.116799		pendiente	t
2	1	5	5	1	0450	2025-11-23	2025-11-23 23:12:25.204223	ninguna	pendiente	t
4	1	6	5	2	0111	2025-11-24	2025-11-24 17:54:13.023619		pendiente	t
6	1	6	5	1	0192	2025-11-24	2025-11-24 21:35:41.085196	ninguna	pendiente	t
5	1	8	5	1	0456	2025-11-24	2025-11-24 21:28:56.378523	aisdiasdiajsd	pendiente	t
7	1	5	5	3	0509	2025-12-04	2025-12-04 10:24:47.87401		pendiente	t
8	1	8	5	1	0578	2025-12-04	2025-12-04 11:04:22.032844		pendiente	t
9	1	5	5	1	1823	2025-12-06	2025-12-06 12:33:15.280993		pendiente	t
10	2	6	5	2	1093	2025-12-06	2025-12-06 16:40:39.990396		pendiente	t
11	2	9	9	2	0912	2025-12-10	2025-12-10 21:08:25.526097		pendiente	t
12	2	6	12	2	5051	2025-12-13	2025-12-13 14:39:11.377263		pendiente	t
13	2	10	5	2	5052	2025-12-13	2025-12-13 14:47:03.403565		pendiente	t
\.


--
-- TOC entry 5168 (class 0 OID 32262)
-- Dependencies: 259
-- Data for Name: detalle_compra; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.detalle_compra (id_detalle_compra, id_compra, id_producto, cantidad, precio_unitario) FROM stdin;
1	1	6	50	30.00
2	2	7	24	30.00
4	4	8	100	20.00
7	6	15	50	60.00
8	6	16	200	5.00
5	5	12	50	60.00
9	7	17	90	50.00
10	8	18	60	1500.00
11	9	17	10	50.00
12	10	21	50	45.00
13	11	22	100	35.00
14	12	25	100	15.00
15	12	26	100	40.00
16	13	27	200	50.00
\.


--
-- TOC entry 5158 (class 0 OID 31173)
-- Dependencies: 249
-- Data for Name: inventario; Type: TABLE DATA; Schema: inventario; Owner: postgres
--

COPY inventario.inventario (id_inventario, id_producto, id_sucursal, cantidad, minimo, activo) FROM stdin;
4	6	6	50	0	t
6	8	6	100	0	t
7	12	8	50	0	t
9	15	6	50	0	t
10	16	6	200	0	t
8	13	8	0	0	t
12	18	8	60	0	t
13	19	9	100	5	t
14	20	9	50	10	t
15	21	6	50	0	t
16	22	9	99	0	t
17	23	9	50	50	t
11	17	5	85	0	t
5	7	5	46	0	t
19	25	6	100	0	t
20	26	6	100	0	t
21	27	10	200	10	t
3	5	5	9	10	t
18	24	9	99	10	t
\.


--
-- TOC entry 5156 (class 0 OID 31136)
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
22	Jordan retro 5	\N	5	4	4	50.00	2	t	zap-jordan	35.00
23	Botas De Seguridad		5	6	4	40.00	2	t	zap-9090	50.00
24	Botas Negras	\N	8	9	4	60.00	2	t	zap-9091	50.00
25	Zapatos feos	\N	5	10	6	16.00	2	t	zap-1500	15.00
26	Timberlands clasicas	\N	8	11	8	45.00	2	t	timber-01	40.00
27	Macasines dama	\N	7	6	9	65.00	2	t	zap-1501	50.00
\.


--
-- TOC entry 5146 (class 0 OID 31029)
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
-- TOC entry 5148 (class 0 OID 31043)
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
-- TOC entry 5162 (class 0 OID 31223)
-- Dependencies: 253
-- Data for Name: detalle_venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.detalle_venta (id_detalle, id_venta, id_producto, cantidad, precio_unitario, activo) FROM stdin;
1	1	5	1	20.00	t
2	2	5	1	20.00	t
6	6	5	2	20.00	t
8	8	5	1	20.00	t
9	9	5	1	20.00	t
10	10	17	1	60.00	t
13	13	17	1	60.00	t
16	16	17	10	60.00	t
17	17	5	1	20.00	t
18	18	5	1	20.00	t
19	19	5	1	20.00	t
20	20	5	2	20.00	t
21	21	17	1	60.00	t
22	22	22	1	50.00	t
23	23	5	1	20.00	t
24	24	5	1	20.00	t
25	28	7	1	45.00	t
26	29	17	2	60.00	t
27	29	5	1	20.00	t
28	29	7	1	45.00	t
30	31	5	1	20.00	t
31	32	24	1	60.00	t
\.


--
-- TOC entry 5164 (class 0 OID 31251)
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
23	21	2	15726.00	1	262.1000	t	\N
24	22	3	50.00	2	262.1000	t	\N
25	23	2	5415.79	1	262.1000	t	\N
26	24	2	5415.79	1	1.0000	t	1287
27	28	3	45.00	2	270.7893	t	\N
28	29	2	157.59	3	317.8879	t	\N
29	31	2	5415.79	1	1.0000	t	1238
30	32	3	60.00	2	270.7893	t	\N
\.


--
-- TOC entry 5160 (class 0 OID 31200)
-- Dependencies: 251
-- Data for Name: venta; Type: TABLE DATA; Schema: ventas; Owner: postgres
--

COPY ventas.venta (id_venta, id_cliente, id_usuario, fecha, activo) FROM stdin;
1	1	7	2025-11-30 15:38:15.419179	t
2	1	7	2025-11-30 22:04:53.407207	t
6	1	7	2025-12-04 10:21:29.070794	t
8	5	7	2025-12-04 11:10:17.980034	t
9	1	7	2025-12-04 20:06:38.923812	t
10	6	7	2025-12-05 14:50:36.777199	t
13	1	7	2025-12-06 12:07:06.107264	t
16	1	7	2025-12-06 12:50:13.255384	t
17	1	7	2025-12-06 14:51:35.977054	t
18	1	7	2025-12-06 14:54:22.070657	t
19	1	7	2025-12-06 15:16:17.486444	t
20	1	7	2025-12-06 16:37:58.393636	t
21	1	7	2025-12-10 21:05:56.768181	t
22	1	11	2025-12-10 21:20:10.899544	t
23	1	7	2025-12-13 13:14:26.279542	t
24	1	7	2025-12-13 13:22:58.494544	t
28	10	7	2025-12-13 14:33:20.874338	t
29	10	7	2025-12-13 14:35:23.592641	t
31	1	7	2025-12-13 15:01:21.021772	t
32	10	11	2025-12-13 15:06:00.104961	t
\.


--
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 224
-- Name: categoria_id_categoria_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.categoria_id_categoria_seq', 8, true);


--
-- TOC entry 5193 (class 0 OID 0)
-- Dependencies: 226
-- Name: cliente_id_cliente_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.cliente_id_cliente_seq', 10, true);


--
-- TOC entry 5194 (class 0 OID 0)
-- Dependencies: 228
-- Name: color_id_color_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.color_id_color_seq', 11, true);


--
-- TOC entry 5195 (class 0 OID 0)
-- Dependencies: 230
-- Name: proveedor_id_proveedor_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.proveedor_id_proveedor_seq', 2, true);


--
-- TOC entry 5196 (class 0 OID 0)
-- Dependencies: 232
-- Name: sucursal_id_sucursal_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.sucursal_id_sucursal_seq', 10, true);


--
-- TOC entry 5197 (class 0 OID 0)
-- Dependencies: 234
-- Name: talla_id_talla_seq; Type: SEQUENCE SET; Schema: core; Owner: postgres
--

SELECT pg_catalog.setval('core.talla_id_talla_seq', 9, true);


--
-- TOC entry 5198 (class 0 OID 0)
-- Dependencies: 240
-- Name: metodo_pago_id_metodo_pago_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.metodo_pago_id_metodo_pago_seq', 3, true);


--
-- TOC entry 5199 (class 0 OID 0)
-- Dependencies: 242
-- Name: moneda_id_moneda_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.moneda_id_moneda_seq', 3, true);


--
-- TOC entry 5200 (class 0 OID 0)
-- Dependencies: 244
-- Name: tasa_cambio_id_tasa_seq; Type: SEQUENCE SET; Schema: finanzas; Owner: postgres
--

SELECT pg_catalog.setval('finanzas.tasa_cambio_id_tasa_seq', 527, true);


--
-- TOC entry 5201 (class 0 OID 0)
-- Dependencies: 256
-- Name: compra_id_compra_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.compra_id_compra_seq', 13, true);


--
-- TOC entry 5202 (class 0 OID 0)
-- Dependencies: 258
-- Name: detalle_compra_id_detalle_compra_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.detalle_compra_id_detalle_compra_seq', 16, true);


--
-- TOC entry 5203 (class 0 OID 0)
-- Dependencies: 248
-- Name: inventario_id_inventario_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.inventario_id_inventario_seq', 21, true);


--
-- TOC entry 5204 (class 0 OID 0)
-- Dependencies: 246
-- Name: producto_id_producto_seq; Type: SEQUENCE SET; Schema: inventario; Owner: postgres
--

SELECT pg_catalog.setval('inventario.producto_id_producto_seq', 27, true);


--
-- TOC entry 5205 (class 0 OID 0)
-- Dependencies: 236
-- Name: rol_id_rol_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.rol_id_rol_seq', 6, true);


--
-- TOC entry 5206 (class 0 OID 0)
-- Dependencies: 238
-- Name: usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: seguridad_acceso; Owner: postgres
--

SELECT pg_catalog.setval('seguridad_acceso.usuario_id_usuario_seq', 12, true);


--
-- TOC entry 5207 (class 0 OID 0)
-- Dependencies: 252
-- Name: detalle_venta_id_detalle_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.detalle_venta_id_detalle_seq', 31, true);


--
-- TOC entry 5208 (class 0 OID 0)
-- Dependencies: 254
-- Name: pago_venta_id_pago_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.pago_venta_id_pago_seq', 30, true);


--
-- TOC entry 5209 (class 0 OID 0)
-- Dependencies: 250
-- Name: venta_id_venta_seq; Type: SEQUENCE SET; Schema: ventas; Owner: postgres
--

SELECT pg_catalog.setval('ventas.venta_id_venta_seq', 32, true);


--
-- TOC entry 4902 (class 2606 OID 30960)
-- Name: categoria categoria_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria
    ADD CONSTRAINT categoria_nombre_key UNIQUE (nombre);


--
-- TOC entry 4904 (class 2606 OID 30958)
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id_categoria);


--
-- TOC entry 4906 (class 2606 OID 30974)
-- Name: cliente cliente_cedula_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_cedula_key UNIQUE (cedula);


--
-- TOC entry 4908 (class 2606 OID 30976)
-- Name: cliente cliente_correo_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_correo_key UNIQUE (correo);


--
-- TOC entry 4910 (class 2606 OID 30972)
-- Name: cliente cliente_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id_cliente);


--
-- TOC entry 4912 (class 2606 OID 30988)
-- Name: color color_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color
    ADD CONSTRAINT color_nombre_key UNIQUE (nombre);


--
-- TOC entry 4914 (class 2606 OID 30986)
-- Name: color color_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.color
    ADD CONSTRAINT color_pkey PRIMARY KEY (id_color);


--
-- TOC entry 4916 (class 2606 OID 31000)
-- Name: proveedor proveedor_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.proveedor
    ADD CONSTRAINT proveedor_pkey PRIMARY KEY (id_proveedor);


--
-- TOC entry 4918 (class 2606 OID 31015)
-- Name: sucursal sucursal_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal
    ADD CONSTRAINT sucursal_nombre_key UNIQUE (nombre);


--
-- TOC entry 4920 (class 2606 OID 31013)
-- Name: sucursal sucursal_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.sucursal
    ADD CONSTRAINT sucursal_pkey PRIMARY KEY (id_sucursal);


--
-- TOC entry 4922 (class 2606 OID 31027)
-- Name: talla talla_nombre_key; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla
    ADD CONSTRAINT talla_nombre_key UNIQUE (rango_talla);


--
-- TOC entry 4924 (class 2606 OID 31025)
-- Name: talla talla_pkey; Type: CONSTRAINT; Schema: core; Owner: postgres
--

ALTER TABLE ONLY core.talla
    ADD CONSTRAINT talla_pkey PRIMARY KEY (id_talla);


--
-- TOC entry 4936 (class 2606 OID 31101)
-- Name: metodo_pago metodo_pago_nombre_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago
    ADD CONSTRAINT metodo_pago_nombre_key UNIQUE (nombre);


--
-- TOC entry 4938 (class 2606 OID 31099)
-- Name: metodo_pago metodo_pago_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.metodo_pago
    ADD CONSTRAINT metodo_pago_pkey PRIMARY KEY (id_metodo_pago);


--
-- TOC entry 4940 (class 2606 OID 31116)
-- Name: moneda moneda_codigo_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_codigo_key UNIQUE (codigo);


--
-- TOC entry 4942 (class 2606 OID 31114)
-- Name: moneda moneda_nombre_key; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_nombre_key UNIQUE (nombre);


--
-- TOC entry 4944 (class 2606 OID 31112)
-- Name: moneda moneda_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.moneda
    ADD CONSTRAINT moneda_pkey PRIMARY KEY (id_moneda);


--
-- TOC entry 4946 (class 2606 OID 31129)
-- Name: tasa_cambio tasa_cambio_pkey; Type: CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio
    ADD CONSTRAINT tasa_cambio_pkey PRIMARY KEY (id_tasa);


--
-- TOC entry 4962 (class 2606 OID 32245)
-- Name: compra compra_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_pkey PRIMARY KEY (id_compra);


--
-- TOC entry 4964 (class 2606 OID 32273)
-- Name: detalle_compra detalle_compra_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra
    ADD CONSTRAINT detalle_compra_pkey PRIMARY KEY (id_detalle_compra);


--
-- TOC entry 4950 (class 2606 OID 31188)
-- Name: inventario inventario_id_producto_id_sucursal_key; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_producto_id_sucursal_key UNIQUE (id_producto, id_sucursal);


--
-- TOC entry 4952 (class 2606 OID 31186)
-- Name: inventario inventario_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_pkey PRIMARY KEY (id_inventario);


--
-- TOC entry 4948 (class 2606 OID 31151)
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- TOC entry 4926 (class 2606 OID 31041)
-- Name: rol rol_nombre_rol_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol
    ADD CONSTRAINT rol_nombre_rol_key UNIQUE (nombre_rol);


--
-- TOC entry 4928 (class 2606 OID 31039)
-- Name: rol rol_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.rol
    ADD CONSTRAINT rol_pkey PRIMARY KEY (id_rol);


--
-- TOC entry 4930 (class 2606 OID 31059)
-- Name: usuario usuario_cedula_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_cedula_key UNIQUE (cedula);


--
-- TOC entry 4932 (class 2606 OID 31061)
-- Name: usuario usuario_email_key; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_email_key UNIQUE (email);


--
-- TOC entry 4934 (class 2606 OID 31057)
-- Name: usuario usuario_pkey; Type: CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id_usuario);


--
-- TOC entry 4956 (class 2606 OID 31239)
-- Name: detalle_venta detalle_venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_pkey PRIMARY KEY (id_detalle);


--
-- TOC entry 4958 (class 2606 OID 31266)
-- Name: pago_venta pago_venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_pkey PRIMARY KEY (id_pago);


--
-- TOC entry 4960 (class 2606 OID 32290)
-- Name: pago_venta pago_venta_referencia_key; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_referencia_key UNIQUE (referencia);


--
-- TOC entry 4954 (class 2606 OID 31211)
-- Name: venta venta_pkey; Type: CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_pkey PRIMARY KEY (id_venta);


--
-- TOC entry 4967 (class 2606 OID 31130)
-- Name: tasa_cambio tasa_cambio_id_moneda_fkey; Type: FK CONSTRAINT; Schema: finanzas; Owner: postgres
--

ALTER TABLE ONLY finanzas.tasa_cambio
    ADD CONSTRAINT tasa_cambio_id_moneda_fkey FOREIGN KEY (id_moneda) REFERENCES finanzas.moneda(id_moneda) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4981 (class 2606 OID 32246)
-- Name: compra compra_id_proveedor_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_id_proveedor_fkey FOREIGN KEY (id_proveedor) REFERENCES core.proveedor(id_proveedor) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4982 (class 2606 OID 32251)
-- Name: compra compra_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4983 (class 2606 OID 32256)
-- Name: compra compra_id_usuario_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.compra
    ADD CONSTRAINT compra_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES seguridad_acceso.usuario(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4984 (class 2606 OID 32274)
-- Name: detalle_compra detalle_compra_id_compra_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra
    ADD CONSTRAINT detalle_compra_id_compra_fkey FOREIGN KEY (id_compra) REFERENCES inventario.compra(id_compra) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4985 (class 2606 OID 32279)
-- Name: detalle_compra detalle_compra_id_producto_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.detalle_compra
    ADD CONSTRAINT detalle_compra_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4972 (class 2606 OID 31189)
-- Name: inventario inventario_id_producto_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4973 (class 2606 OID 31194)
-- Name: inventario inventario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.inventario
    ADD CONSTRAINT inventario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4968 (class 2606 OID 31152)
-- Name: producto producto_id_categoria_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_categoria_fkey FOREIGN KEY (id_categoria) REFERENCES core.categoria(id_categoria) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4969 (class 2606 OID 31157)
-- Name: producto producto_id_color_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_color_fkey FOREIGN KEY (id_color) REFERENCES core.color(id_color) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4970 (class 2606 OID 31167)
-- Name: producto producto_id_proveedor_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_proveedor_fkey FOREIGN KEY (id_proveedor) REFERENCES core.proveedor(id_proveedor) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4971 (class 2606 OID 31162)
-- Name: producto producto_id_talla_fkey; Type: FK CONSTRAINT; Schema: inventario; Owner: postgres
--

ALTER TABLE ONLY inventario.producto
    ADD CONSTRAINT producto_id_talla_fkey FOREIGN KEY (id_talla) REFERENCES core.talla(id_talla) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4965 (class 2606 OID 31062)
-- Name: usuario usuario_id_rol_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_id_rol_fkey FOREIGN KEY (id_rol) REFERENCES seguridad_acceso.rol(id_rol) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4966 (class 2606 OID 31067)
-- Name: usuario usuario_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: seguridad_acceso; Owner: postgres
--

ALTER TABLE ONLY seguridad_acceso.usuario
    ADD CONSTRAINT usuario_id_sucursal_fkey FOREIGN KEY (id_sucursal) REFERENCES core.sucursal(id_sucursal) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4976 (class 2606 OID 31245)
-- Name: detalle_venta detalle_venta_id_producto_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_id_producto_fkey FOREIGN KEY (id_producto) REFERENCES inventario.producto(id_producto) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4977 (class 2606 OID 31240)
-- Name: detalle_venta detalle_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.detalle_venta
    ADD CONSTRAINT detalle_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES ventas.venta(id_venta) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4978 (class 2606 OID 31272)
-- Name: pago_venta pago_venta_id_metodo_pago_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_metodo_pago_fkey FOREIGN KEY (id_metodo_pago) REFERENCES finanzas.metodo_pago(id_metodo_pago) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4979 (class 2606 OID 31277)
-- Name: pago_venta pago_venta_id_moneda_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_moneda_fkey FOREIGN KEY (id_moneda) REFERENCES finanzas.moneda(id_moneda) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 4980 (class 2606 OID 31267)
-- Name: pago_venta pago_venta_id_venta_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.pago_venta
    ADD CONSTRAINT pago_venta_id_venta_fkey FOREIGN KEY (id_venta) REFERENCES ventas.venta(id_venta) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4974 (class 2606 OID 31212)
-- Name: venta venta_id_cliente_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES core.cliente(id_cliente) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 4975 (class 2606 OID 31217)
-- Name: venta venta_id_usuario_fkey; Type: FK CONSTRAINT; Schema: ventas; Owner: postgres
--

ALTER TABLE ONLY ventas.venta
    ADD CONSTRAINT venta_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES seguridad_acceso.usuario(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT;


-- Completed on 2025-12-13 15:07:19

--
-- PostgreSQL database dump complete
--

\unrestrict p6xPLSYehzn8LWom8w81TMDzxiRdtjPtGuYV4tWL8Cb6PO1IZpURmyMU9N4tpcZ

