-- SET konfigurasi dasar
SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

-- Tabel jenis_kelamin
CREATE TABLE IF NOT EXISTS public.jenis_kelamin (
    id integer NOT NULL,
    nama character(1) NOT NULL
);
CREATE SEQUENCE IF NOT EXISTS public.jenis_kelamin_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.jenis_kelamin_id_seq OWNED BY public.jenis_kelamin.id;
ALTER TABLE ONLY public.jenis_kelamin ALTER COLUMN id SET DEFAULT nextval('public.jenis_kelamin_id_seq'::regclass);
ALTER TABLE ONLY public.jenis_kelamin ADD CONSTRAINT jenis_kelamin_pkey PRIMARY KEY (id);
INSERT INTO public.jenis_kelamin (id, nama) VALUES (1, 'L'), (2, 'P');
SELECT pg_catalog.setval('public.jenis_kelamin_id_seq', 2, true);

-- Tabel pekerjaan
CREATE TABLE IF NOT EXISTS public.pekerjaan (
    id integer NOT NULL,
    nama character varying NOT NULL
);
CREATE SEQUENCE IF NOT EXISTS public.pekerjaan_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.pekerjaan_id_seq OWNED BY public.pekerjaan.id;
ALTER TABLE ONLY public.pekerjaan ALTER COLUMN id SET DEFAULT nextval('public.pekerjaan_id_seq'::regclass);
ALTER TABLE ONLY public.pekerjaan ADD CONSTRAINT pekerjaan_pkey PRIMARY KEY (id);
INSERT INTO public.pekerjaan (id, nama) VALUES 
(1, 'PNS'), (2, 'TNI'), (3, 'POLISI'), (4, 'SWASTA'), (5, 'WIRAUSAHA'), (6, 'LAINNYA');
SELECT pg_catalog.setval('public.pekerjaan_id_seq', 6, true);

-- Tabel pelayanan
CREATE TABLE IF NOT EXISTS public.pelayanan (
    id integer NOT NULL,
    nama character varying
);
CREATE SEQUENCE IF NOT EXISTS public.pelayanan_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.pelayanan_id_seq OWNED BY public.pelayanan.id;
ALTER TABLE ONLY public.pelayanan ALTER COLUMN id SET DEFAULT nextval('public.pelayanan_id_seq'::regclass);
ALTER TABLE ONLY public.pelayanan ADD CONSTRAINT pelayanan_pkey PRIMARY KEY (id);
INSERT INTO public.pelayanan (id, nama) VALUES 
(1, 'ADMISI'), (2, 'GIZI'), (3, 'IGD'), (4, 'ICU'), (5, 'OPERASI'), 
(6, 'RADIOLOGI'), (7, 'LABORATORIUM'), (8, 'FARMASI'), (9, 'RAWAT INAP'), (10, 'RAWAT JALAN');
SELECT pg_catalog.setval('public.pelayanan_id_seq', 10, true);

-- Tabel pendidikan
CREATE TABLE IF NOT EXISTS public.pendidikan (
    id integer NOT NULL,
    nama character varying NOT NULL
);
CREATE SEQUENCE IF NOT EXISTS public.pendidikan_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.pendidikan_id_seq OWNED BY public.pendidikan.id;
ALTER TABLE ONLY public.pendidikan ALTER COLUMN id SET DEFAULT nextval('public.pendidikan_id_seq'::regclass);
ALTER TABLE ONLY public.pendidikan ADD CONSTRAINT pendidikan_pkey PRIMARY KEY (id);
INSERT INTO public.pendidikan (id, nama) VALUES 
(1, 'SD'), (2, 'SMP'), (3, 'SMA'), (4, 'S1'), (5, 'S2'), (6, 'S3'), (7, 'LAINNYA');
SELECT pg_catalog.setval('public.pendidikan_id_seq', 7, true);

-- Tabel penjamin
CREATE TABLE IF NOT EXISTS public.penjamin (
    id integer NOT NULL,
    nama character varying NOT NULL
);
CREATE SEQUENCE IF NOT EXISTS public.penjamin_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.penjamin_id_seq OWNED BY public.penjamin.id;
ALTER TABLE ONLY public.penjamin ALTER COLUMN id SET DEFAULT nextval('public.penjamin_id_seq'::regclass);
ALTER TABLE ONLY public.penjamin ADD CONSTRAINT penjamin_pkey PRIMARY KEY (id);
INSERT INTO public.penjamin (id, nama) VALUES (1, 'UMUM'), (2, 'BPJS');
SELECT pg_catalog.setval('public.penjamin_id_seq', 2, true);

-- Tabel pertanyaan
CREATE TABLE IF NOT EXISTS public.pertanyaan (
    id integer NOT NULL,
    deskripsi text NOT NULL
);
CREATE SEQUENCE IF NOT EXISTS public.pertanyaan_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.pertanyaan_id_seq OWNED BY public.pertanyaan.id;
ALTER TABLE ONLY public.pertanyaan ALTER COLUMN id SET DEFAULT nextval('public.pertanyaan_id_seq'::regclass);
ALTER TABLE ONLY public.pertanyaan ADD CONSTRAINT pertanyaan_pkey PRIMARY KEY (id);
INSERT INTO public.pertanyaan (id, deskripsi) VALUES 
(4, 'Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?'),
(5, 'Bagaimana pemahaman saudara tentang kemudahan prosedur pelayanan di unit ini?'),
(6, 'Bagaimana pendapat saudara tentang kecepatan waktu dalam memberikan pelayanan?'),
(7, 'Bagaimana pendapat saudara tentang kewajaran biaya atau tarif dalam pelayanan? (Jika saudara peserta BPJS/Asuransi tidak perlu diisi)'),
(8, 'Bagaimana pendapat saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?'),
(9, 'Bagaimana pendapat saudara tentang kompetensi atau kemampuan petugas dalam pelayanan?'),
(10, 'Bagaimana pendapat saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?'),
(11, 'Bagaimana pendapat saudara tentang penanganan pengaduan pengguna layanan?'),
(12, 'Bagaimana pendapat saudara tentang kualitas sarana dan prasarana?');
SELECT pg_catalog.setval('public.pertanyaan_id_seq', 12, true);

-- Tabel profil
CREATE TABLE IF NOT EXISTS public.profil (
    id integer NOT NULL,
    jenis_kelamin_id integer,
    pendidikan_id integer,
    pekerjaan_id integer,
    pelayanan_id integer,
    penjamin_id integer
);
CREATE SEQUENCE IF NOT EXISTS public.profil_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.profil_id_seq OWNED BY public.profil.id;
ALTER TABLE ONLY public.profil ALTER COLUMN id SET DEFAULT nextval('public.profil_id_seq'::regclass);
ALTER TABLE ONLY public.profil ADD CONSTRAINT profil_pkey PRIMARY KEY (id);
ALTER TABLE ONLY public.profil ADD CONSTRAINT fk_profil_jenis_kelamin FOREIGN KEY (jenis_kelamin_id) REFERENCES public.jenis_kelamin(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY public.profil ADD CONSTRAINT fk_profil_pendidikan FOREIGN KEY (pendidikan_id) REFERENCES public.pendidikan(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY public.profil ADD CONSTRAINT fk_profil_pekerjaan FOREIGN KEY (pekerjaan_id) REFERENCES public.pekerjaan(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY public.profil ADD CONSTRAINT fk_profil_pelayanan FOREIGN KEY (pelayanan_id) REFERENCES public.pelayanan(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY public.profil ADD CONSTRAINT fk_profil_penjamin FOREIGN KEY (penjamin_id) REFERENCES public.penjamin(id) ON UPDATE CASCADE ON DELETE CASCADE;
INSERT INTO public.profil (id, jenis_kelamin_id, pendidikan_id, pekerjaan_id, pelayanan_id, penjamin_id) VALUES 
(1, 1, 2, 2, 2, 2),
(2, 1, 2, 2, 2, 2),
(3, 1, 2, 2, 2, 2),
(4, 2, 3, 4, 7, 1),
(5, 1, 5, 5, 6, 1),
(6, 1, 4, 1, 2, 1),
(7, 1, 1, 1, 2, 2);
SELECT pg_catalog.setval('public.profil_id_seq', 7, true);

-- Tabel kuisioner
CREATE TABLE IF NOT EXISTS public.kuisioner (
    id integer NOT NULL,
    pertanyaan_id integer,
    nilai smallint,
    profil_id integer,
    survey_date date,
    survey_time character varying(10),
    created_at timestamp without time zone DEFAULT now()
);
CREATE SEQUENCE IF NOT EXISTS public.kuisioner_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.kuisioner_id_seq OWNED BY public.kuisioner.id;
ALTER TABLE ONLY public.kuisioner ALTER COLUMN id SET DEFAULT nextval('public.kuisioner_id_seq'::regclass);
ALTER TABLE ONLY public.kuisioner ADD CONSTRAINT kuisioner_pkey PRIMARY KEY (id);
ALTER TABLE ONLY public.kuisioner ADD CONSTRAINT fk_kuisioner_pertanyaan FOREIGN KEY (pertanyaan_id) REFERENCES public.pertanyaan(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE ONLY public.kuisioner ADD CONSTRAINT fk_kuisioner_profil FOREIGN KEY (profil_id) REFERENCES public.profil(id) ON UPDATE CASCADE ON DELETE CASCADE;
INSERT INTO public.kuisioner (id, pertanyaan_id, nilai, profil_id, survey_date, survey_time, created_at) VALUES 
(1, 4, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.5507'),
(2, 5, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.553953'),
(3, 6, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.554319'),
(4, 7, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.554601'),
(5, 8, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.554919'),
(6, 9, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.555204'),
(7, 10, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.555467'),
(8, 11, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.555733'),
(9, 12, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.556022'),
(10, 4, 1, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.123678'),
(11, 5, 1, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.124722'),
(12, 6, 1, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.12502'),
(13, 7, 2, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.12531'),
(14, 8, 4, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.125621'),
(15, 9, 3, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.125992'),
(16, 10, 3, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.126484'),
(17, 11, 3, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.126762'),
(18, 12, 3, 6, '2026-02-04', '08-12', '2026-02-04 10:09:31.12702'),
(19, 4, 1, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.691534'),
(20, 5, 2, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.693053'),
(21, 6, 2, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.693399'),
(22, 7, 0, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.69373'),
(23, 8, 3, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.694029'),
(24, 9, 3, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.694405'),
(25, 10, 3, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.694725'),
(26, 11, 3, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.694977'),
(27, 12, 3, 7, '2026-02-04', '10:20', '2026-02-04 10:20:56.695214');
SELECT pg_catalog.setval('public.kuisioner_id_seq', 27, true);

-- Tabel keluhan
CREATE TABLE IF NOT EXISTS public.keluhan (
    id integer NOT NULL,
    alamat text NOT NULL,
    no_hp character varying(15) NOT NULL,
    masukan text,
    pukul time without time zone NOT NULL,
    tanggal date NOT NULL,
    created_at timestamp without time zone DEFAULT now()
);
CREATE SEQUENCE IF NOT EXISTS public.keluhan_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER SEQUENCE public.keluhan_id_seq OWNED BY public.keluhan.id;
ALTER TABLE ONLY public.keluhan ALTER COLUMN id SET DEFAULT nextval('public.keluhan_id_seq'::regclass);
ALTER TABLE ONLY public.keluhan ADD CONSTRAINT keluhan_pkey PRIMARY KEY (id);
INSERT INTO public.keluhan (id, alamat, no_hp, masukan, pukul, tanggal, created_at) VALUES 
(1, 'Sidoarjo', '082244567815', 'lama antriannya', '10:52:00', '2026-02-04', '2026-02-04 10:52:34.212533');
SELECT pg_catalog.setval('public.keluhan_id_seq', 1, true);
