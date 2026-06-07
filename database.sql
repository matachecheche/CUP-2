-- ============================================================================
-- Base de datos cup_ficct (proyecto CUP-si1) — Esquema SIN sentencias ALTER
-- Cada FOREIGN KEY está declarada DENTRO de su propia tabla.
-- Las tablas están ordenadas por dependencias: primero las que no dependen
-- de nadie, después las que las referencian. (Por eso el orden cambia
-- respecto al script de pgAdmin, que las ordenaba alfabéticamente.)
-- Validado contra el script original en PostgreSQL 16: esquema idéntico.
-- ============================================================================

BEGIN;

-- ============================================================================
-- 1) INFRAESTRUCTURA LARAVEL (tablas sin relaciones con el resto)
-- ============================================================================

CREATE TABLE IF NOT EXISTS public.migrations
(
    id serial NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL,
    CONSTRAINT migrations_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS public.cache
(
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL,
    CONSTRAINT cache_pkey PRIMARY KEY (key)
);

CREATE TABLE IF NOT EXISTS public.cache_locks
(
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL,
    CONSTRAINT cache_locks_pkey PRIMARY KEY (key)
);

CREATE TABLE IF NOT EXISTS public.jobs
(
    id bigserial NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL,
    CONSTRAINT jobs_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS public.job_batches
(
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer,
    CONSTRAINT job_batches_pkey PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS public.failed_jobs
(
    id bigserial NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT failed_jobs_pkey PRIMARY KEY (id),
    CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid)
);

CREATE TABLE IF NOT EXISTS public.password_reset_tokens
(
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email)
);

CREATE TABLE IF NOT EXISTS public.sessions
(
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL,
    CONSTRAINT sessions_pkey PRIMARY KEY (id)
);

-- Bitácora del sistema (CU-19). user_id no lleva FK: conserva el registro
-- histórico aunque el usuario se elimine.
CREATE TABLE IF NOT EXISTS public.bitacoras
(
    id bigserial NOT NULL,
    user_id bigint,
    usuario character varying(120),
    accion character varying(250) NOT NULL,
    modulo character varying(60),
    metodo_http character varying(10),
    ruta character varying(255),
    fecha_hora timestamp(0) without time zone NOT NULL,
    id_operacion bigint,
    ip character varying(45),
    user_agent character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    descripcion text,
    CONSTRAINT bitacoras_pkey PRIMARY KEY (id)
);

-- ============================================================================
-- 2) CATÁLOGOS BASE (no dependen de nadie; el resto depende de ellos)
-- ============================================================================

CREATE TABLE IF NOT EXISTS public.gestiones
(
    id bigserial NOT NULL,
    descripcion character varying(50) NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_fin date NOT NULL,
    estado character varying(255) NOT NULL DEFAULT 'planificacion'::character varying,
    costo_inscripcion numeric(10,2) NOT NULL DEFAULT 850.00,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT gestiones_pkey PRIMARY KEY (id),
    CONSTRAINT gestiones_descripcion_unique UNIQUE (descripcion)
);

CREATE TABLE IF NOT EXISTS public.carreras
(
    id bigserial NOT NULL,
    nombre character varying(100) NOT NULL,
    sigla character varying(10),
    descripcion text,
    estado boolean NOT NULL DEFAULT true,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT carreras_pkey PRIMARY KEY (id),
    CONSTRAINT carreras_nombre_unique UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS public.materias
(
    id bigserial NOT NULL,
    nombre character varying(100) NOT NULL,
    area_formacion character varying(80),
    descripcion text,
    pond_examen1 integer NOT NULL DEFAULT 30,
    pond_examen2 integer NOT NULL DEFAULT 30,
    pond_examen3 integer NOT NULL DEFAULT 40,
    nota_minima_aprobacion integer NOT NULL DEFAULT 60,
    orden integer NOT NULL DEFAULT 0,
    estado boolean NOT NULL DEFAULT true,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT materias_pkey PRIMARY KEY (id),
    CONSTRAINT materias_nombre_unique UNIQUE (nombre)
);

CREATE TABLE IF NOT EXISTS public.docentes
(
    id bigserial NOT NULL,
    ci character varying(20) NOT NULL,
    nombres character varying(100) NOT NULL,
    apellidos character varying(100) NOT NULL,
    telefono character varying(20),
    email character varying(100) NOT NULL,
    titulo_profesional character varying(150) NOT NULL,
    maestria character varying(150) NOT NULL,
    diplomado_educacion_superior character varying(150) NOT NULL,
    certificacion_ingles character varying(100),
    otras_certificaciones text,
    area_formacion character varying(80),
    estado boolean NOT NULL DEFAULT true,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT docentes_pkey PRIMARY KEY (id),
    CONSTRAINT docentes_ci_unique UNIQUE (ci),
    CONSTRAINT docentes_email_unique UNIQUE (email)
);

-- ============================================================================
-- 3) SEGURIDAD — ROLES Y PERMISOS (paquete Spatie Permission)
-- ============================================================================

CREATE TABLE IF NOT EXISTS public.permissions
(
    id bigserial NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT permissions_pkey PRIMARY KEY (id),
    CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name)
);

CREATE TABLE IF NOT EXISTS public.roles
(
    id bigserial NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT roles_pkey PRIMARY KEY (id),
    CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name)
);

-- Qué permisos tiene cada rol
CREATE TABLE IF NOT EXISTS public.role_has_permissions
(
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL,
    CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id),
    CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id)
        REFERENCES public.permissions (id) ON DELETE CASCADE,
    CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id)
        REFERENCES public.roles (id) ON DELETE CASCADE
);

-- Permisos asignados directamente a un usuario (modelo)
CREATE TABLE IF NOT EXISTS public.model_has_permissions
(
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type),
    CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id)
        REFERENCES public.permissions (id) ON DELETE CASCADE
);

-- Roles asignados a cada usuario (modelo)
CREATE TABLE IF NOT EXISTS public.model_has_roles
(
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type),
    CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id)
        REFERENCES public.roles (id) ON DELETE CASCADE
);

-- ============================================================================
-- 4) NÚCLEO ACADÉMICO (cada tabla con sus FOREIGN KEY integradas)
-- ============================================================================

-- Postulantes: pertenecen a una gestión y eligen 2 carreras como opción
CREATE TABLE IF NOT EXISTS public.postulantes
(
    id bigserial NOT NULL,
    gestion_id bigint NOT NULL,
    primera_opcion_id bigint NOT NULL,
    segunda_opcion_id bigint NOT NULL,
    ci character varying(20) NOT NULL,
    nombres character varying(100) NOT NULL,
    apellidos character varying(100) NOT NULL,
    fecha_nacimiento date,
    sexo character varying(255),
    direccion character varying(200),
    telefono character varying(20),
    email character varying(100) NOT NULL,
    colegio_procedencia character varying(150),
    ciudad character varying(80),
    doc_ci boolean NOT NULL DEFAULT false,
    doc_libreta_colegio boolean NOT NULL DEFAULT false,
    doc_titulo_bachiller boolean NOT NULL DEFAULT false,
    estado character varying(255) NOT NULL DEFAULT 'inscrito'::character varying,
    promedio_general numeric(5, 2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT postulantes_pkey PRIMARY KEY (id),
    CONSTRAINT postulantes_ci_unique UNIQUE (ci),
    CONSTRAINT postulantes_email_unique UNIQUE (email),
    CONSTRAINT postulantes_estado_check CHECK (estado IN ('preinscrito','inscrito','en_curso','aprobado','no_aprobado','admitido','admitido_segunda_opcion','no_admitido')),
    CONSTRAINT postulantes_gestion_id_foreign FOREIGN KEY (gestion_id)
        REFERENCES public.gestiones (id),
    CONSTRAINT postulantes_primera_opcion_id_foreign FOREIGN KEY (primera_opcion_id)
        REFERENCES public.carreras (id),
    CONSTRAINT postulantes_segunda_opcion_id_foreign FOREIGN KEY (segunda_opcion_id)
        REFERENCES public.carreras (id)
);

-- Usuarios del sistema: pueden estar vinculados a un docente o a un postulante.
-- ON DELETE SET NULL: si se borra el docente/postulante, el usuario queda
-- pero pierde el vínculo.
CREATE TABLE IF NOT EXISTS public.users
(
    id bigserial NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    docente_id bigint,
    postulante_id bigint,
    activo boolean NOT NULL DEFAULT true,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT users_pkey PRIMARY KEY (id),
    CONSTRAINT users_email_unique UNIQUE (email),
    CONSTRAINT users_docente_id_foreign FOREIGN KEY (docente_id)
        REFERENCES public.docentes (id) ON DELETE SET NULL,
    CONSTRAINT users_postulante_id_foreign FOREIGN KEY (postulante_id)
        REFERENCES public.postulantes (id) ON DELETE SET NULL
);

-- Grupos del curso preuniversitario, por gestión
CREATE TABLE IF NOT EXISTS public.grupos
(
    id bigserial NOT NULL,
    gestion_id bigint NOT NULL,
    codigo character varying(20) NOT NULL,
    turno character varying(255) NOT NULL,
    modalidad character varying(255) NOT NULL DEFAULT 'presencial'::character varying,
    capacidad_maxima integer NOT NULL DEFAULT 60,
    estado boolean NOT NULL DEFAULT true,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT grupos_pkey PRIMARY KEY (id),
    CONSTRAINT grupos_codigo_unique UNIQUE (codigo),
    CONSTRAINT grupos_gestion_id_foreign FOREIGN KEY (gestion_id)
        REFERENCES public.gestiones (id)
);

-- Cupos disponibles por carrera en cada gestión
CREATE TABLE IF NOT EXISTS public.cupos_carrera
(
    id bigserial NOT NULL,
    carrera_id bigint NOT NULL,
    gestion_id bigint NOT NULL,
    cantidad_maxima integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT cupos_carrera_pkey PRIMARY KEY (id),
    CONSTRAINT cupos_carrera_carrera_id_gestion_id_unique UNIQUE (carrera_id, gestion_id),
    CONSTRAINT cupos_carrera_carrera_id_foreign FOREIGN KEY (carrera_id)
        REFERENCES public.carreras (id) ON DELETE CASCADE,
    CONSTRAINT cupos_carrera_gestion_id_foreign FOREIGN KEY (gestion_id)
        REFERENCES public.gestiones (id) ON DELETE CASCADE
);

-- Asignación docente–materia–grupo con horario.
-- UNIQUE (grupo, materia): una materia solo tiene un docente por grupo.
CREATE TABLE IF NOT EXISTS public.asignaciones
(
    id bigserial NOT NULL,
    grupo_id bigint NOT NULL,
    docente_id bigint NOT NULL,
    materia_id bigint NOT NULL,
    dia character varying(255) NOT NULL,
    hora_inicio time(0) without time zone NOT NULL,
    hora_fin time(0) without time zone NOT NULL,
    aula character varying(30),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT asignaciones_pkey PRIMARY KEY (id),
    CONSTRAINT asignaciones_grupo_id_materia_id_unique UNIQUE (grupo_id, materia_id),
    CONSTRAINT asignaciones_grupo_id_foreign FOREIGN KEY (grupo_id)
        REFERENCES public.grupos (id) ON DELETE CASCADE,
    CONSTRAINT asignaciones_docente_id_foreign FOREIGN KEY (docente_id)
        REFERENCES public.docentes (id) ON DELETE CASCADE,
    CONSTRAINT asignaciones_materia_id_foreign FOREIGN KEY (materia_id)
        REFERENCES public.materias (id) ON DELETE CASCADE
);

-- Inscripción de postulantes a grupos (tabla pivote N:M).
-- UNIQUE (grupo, postulante): no puede inscribirse 2 veces al mismo grupo.
CREATE TABLE IF NOT EXISTS public.grupo_postulante
(
    id bigserial NOT NULL,
    grupo_id bigint NOT NULL,
    postulante_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT grupo_postulante_pkey PRIMARY KEY (id),
    CONSTRAINT grupo_postulante_grupo_id_postulante_id_unique UNIQUE (grupo_id, postulante_id),
    CONSTRAINT grupo_postulante_grupo_id_foreign FOREIGN KEY (grupo_id)
        REFERENCES public.grupos (id) ON DELETE CASCADE,
    CONSTRAINT grupo_postulante_postulante_id_foreign FOREIGN KEY (postulante_id)
        REFERENCES public.postulantes (id) ON DELETE CASCADE
);

-- Notas por postulante/materia/grupo (CU-13 a CU-15).
-- UNIQUE triple: una sola nota por postulante en cada materia de cada grupo.
CREATE TABLE IF NOT EXISTS public.notas
(
    id bigserial NOT NULL,
    postulante_id bigint NOT NULL,
    materia_id bigint NOT NULL,
    grupo_id bigint NOT NULL,
    examen1 numeric(5, 2),
    examen2 numeric(5, 2),
    examen3 numeric(5, 2),
    nota_final numeric(5, 2),
    aprobado boolean,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT notas_pkey PRIMARY KEY (id),
    CONSTRAINT notas_postulante_id_materia_id_grupo_id_unique UNIQUE (postulante_id, materia_id, grupo_id),
    CONSTRAINT notas_postulante_id_foreign FOREIGN KEY (postulante_id)
        REFERENCES public.postulantes (id) ON DELETE CASCADE,
    CONSTRAINT notas_materia_id_foreign FOREIGN KEY (materia_id)
        REFERENCES public.materias (id) ON DELETE CASCADE,
    CONSTRAINT notas_grupo_id_foreign FOREIGN KEY (grupo_id)
        REFERENCES public.grupos (id) ON DELETE CASCADE
);

-- Resultado de admisión (CU-16 a CU-18).
-- UNIQUE (postulante_id): un postulante tiene un único registro de admisión.
CREATE TABLE IF NOT EXISTS public.admisiones
(
    id bigserial NOT NULL,
    postulante_id bigint NOT NULL,
    gestion_id bigint NOT NULL,
    promedio_general numeric(5, 2),
    carrera_asignada_id bigint,
    resultado character varying(255) NOT NULL DEFAULT 'pendiente'::character varying,
    publicado boolean NOT NULL DEFAULT false,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT admisiones_pkey PRIMARY KEY (id),
    CONSTRAINT admisiones_postulante_id_unique UNIQUE (postulante_id),
    CONSTRAINT admisiones_postulante_id_foreign FOREIGN KEY (postulante_id)
        REFERENCES public.postulantes (id) ON DELETE CASCADE,
    CONSTRAINT admisiones_gestion_id_foreign FOREIGN KEY (gestion_id)
        REFERENCES public.gestiones (id),
    CONSTRAINT admisiones_carrera_asignada_id_foreign FOREIGN KEY (carrera_asignada_id)
        REFERENCES public.carreras (id)
);

-- Pago de la inscripción al CUP (CU-20). El postulante 'preinscrito' paga vía
-- Stripe Checkout; el webhook confirma y lo promueve a 'inscrito'.
-- stripe_session_id identifica la Checkout Session y es único (idempotencia).
-- metodo deja la tabla agnóstica a la pasarela (stripe | qr | banco).
CREATE TABLE IF NOT EXISTS public.pagos
(
    id bigserial NOT NULL,
    postulante_id bigint NOT NULL,
    gestion_id bigint NOT NULL,
    monto numeric(10, 2) NOT NULL,
    moneda character varying(3) NOT NULL DEFAULT 'BOB'::character varying,
    metodo character varying(30) NOT NULL DEFAULT 'stripe'::character varying,
    stripe_session_id character varying(255),
    stripe_payment_intent_id character varying(255),
    estado character varying(20) NOT NULL DEFAULT 'pendiente'::character varying,
    fecha_pago timestamp(0) without time zone,
    comprobante character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT pagos_pkey PRIMARY KEY (id),
    CONSTRAINT pagos_stripe_session_id_unique UNIQUE (stripe_session_id),
    CONSTRAINT pagos_postulante_id_foreign FOREIGN KEY (postulante_id)
        REFERENCES public.postulantes (id) ON DELETE CASCADE,
    CONSTRAINT pagos_gestion_id_foreign FOREIGN KEY (gestion_id)
        REFERENCES public.gestiones (id)
);

END;