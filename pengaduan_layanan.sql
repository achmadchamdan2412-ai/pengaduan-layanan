-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               PostgreSQL 18.1 on x86_64-windows, compiled by msvc-19.44.35221, 64-bit
-- Server OS:                    
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES  */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table public.jenis_kelamin
CREATE TABLE IF NOT EXISTS "jenis_kelamin" (
	"id" SERIAL NOT NULL,
	"nama" CHAR(1) NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.jenis_kelamin: -1 rows
/*!40000 ALTER TABLE "jenis_kelamin" DISABLE KEYS */;
INSERT INTO "jenis_kelamin" ("id", "nama") VALUES
	(1, 'L'),
	(2, 'P');
/*!40000 ALTER TABLE "jenis_kelamin" ENABLE KEYS */;

-- Dumping structure for table public.kuisioner
CREATE TABLE IF NOT EXISTS "kuisioner" (
	"id" SERIAL NOT NULL,
	"pertanyaan_id" INTEGER NULL DEFAULT NULL,
	"nilai" SMALLINT NULL DEFAULT NULL,
	"profil_id" INTEGER NULL DEFAULT NULL,
	"survey_date" DATE NULL DEFAULT NULL,
	"survey_time" VARCHAR(10) NULL DEFAULT NULL,
	"created_at" TIMESTAMP NULL DEFAULT now(),
	PRIMARY KEY ("id"),
	CONSTRAINT "FK_kuisioner_pertanyaan" FOREIGN KEY ("pertanyaan_id") REFERENCES "pertanyaan" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT "FK_kuisioner_profil" FOREIGN KEY ("profil_id") REFERENCES "profil" ("id") ON UPDATE CASCADE ON DELETE CASCADE
);

-- Dumping data for table public.kuisioner: 9 rows
/*!40000 ALTER TABLE "kuisioner" DISABLE KEYS */;
INSERT INTO "kuisioner" ("id", "pertanyaan_id", "nilai", "profil_id", "survey_date", "survey_time", "created_at") VALUES
	(1, 4, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.5507'),
	(2, 5, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.553953'),
	(3, 6, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.554319'),
	(4, 7, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.554601'),
	(5, 8, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.554919'),
	(6, 9, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.555204'),
	(7, 10, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.555467'),
	(8, 11, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.555733'),
	(9, 12, 4, 5, '2026-02-03', '12-18', '2026-02-03 20:21:04.556022');
/*!40000 ALTER TABLE "kuisioner" ENABLE KEYS */;

-- Dumping structure for table public.pekerjaan
CREATE TABLE IF NOT EXISTS "pekerjaan" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pekerjaan: -1 rows
/*!40000 ALTER TABLE "pekerjaan" DISABLE KEYS */;
INSERT INTO "pekerjaan" ("id", "nama") VALUES
	(1, 'PNS'),
	(2, 'TNI'),
	(3, 'POLISI'),
	(4, 'SWASTA'),
	(5, 'WIRAUSAHA'),
	(6, 'LAINNYA');
/*!40000 ALTER TABLE "pekerjaan" ENABLE KEYS */;

-- Dumping structure for table public.pelayanan
CREATE TABLE IF NOT EXISTS "pelayanan" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pelayanan: -1 rows
/*!40000 ALTER TABLE "pelayanan" DISABLE KEYS */;
INSERT INTO "pelayanan" ("id", "nama") VALUES
	(1, 'ADMISI'),
	(2, 'GIZI'),
	(3, 'IGD'),
	(4, 'ICU'),
	(5, 'OPERASI'),
	(6, 'RADIOLOGI'),
	(7, 'LABORATORIUM'),
	(8, 'FARMASI'),
	(9, 'RAWAT INAP'),
	(10, 'RAWAT JALAN');
/*!40000 ALTER TABLE "pelayanan" ENABLE KEYS */;

-- Dumping structure for table public.pendidikan
CREATE TABLE IF NOT EXISTS "pendidikan" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pendidikan: -1 rows
/*!40000 ALTER TABLE "pendidikan" DISABLE KEYS */;
INSERT INTO "pendidikan" ("id", "nama") VALUES
	(1, 'SD'),
	(2, 'SMP'),
	(3, 'SMA'),
	(4, 'S1'),
	(5, 'S2'),
	(6, 'S3'),
	(7, 'LAINNYA');
/*!40000 ALTER TABLE "pendidikan" ENABLE KEYS */;

-- Dumping structure for table public.penjamin
CREATE TABLE IF NOT EXISTS "penjamin" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.penjamin: -1 rows
/*!40000 ALTER TABLE "penjamin" DISABLE KEYS */;
INSERT INTO "penjamin" ("id", "nama") VALUES
	(1, 'UMUM'),
	(2, 'BPJS');
/*!40000 ALTER TABLE "penjamin" ENABLE KEYS */;

-- Dumping structure for table public.pertanyaan
CREATE TABLE IF NOT EXISTS "pertanyaan" (
	"id" SERIAL NOT NULL,
	"deskripsi" TEXT NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pertanyaan: -1 rows
/*!40000 ALTER TABLE "pertanyaan" DISABLE KEYS */;
INSERT INTO "pertanyaan" ("id", "deskripsi") VALUES
	(4, 'Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?'),
	(5, 'Bagaimana pemahaman saudara tentang kemudahan prosedur pelayanan di unit ini?'),
	(6, 'Bagaimana pendapat saudara tentang kecepatan waktu dalam memberikan pelayanan?'),
	(7, 'Bagaimana pendapat saudara tentang kewajaran biaya atau tarif dalam pelayanan? (Jika saudara peserta BPJS/Asuransi tidak perlu diisi)'),
	(8, 'Bagaimana pendapat saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?'),
	(9, 'Bagaimana pendapat saudara tentang kompetensi atau kemampuan petugas dalam pelayanan?'),
	(10, 'Bagaimana pendapat saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?'),
	(11, 'Bagaimana pendapat saudara tentang penanganan pengaduan pengguna layanan?'),
	(12, 'Bagaimana pendapat saudara tentang kualitas sarana dan prasarana?');
/*!40000 ALTER TABLE "pertanyaan" ENABLE KEYS */;

-- Dumping structure for table public.profil
CREATE TABLE IF NOT EXISTS "profil" (
	"id" SERIAL NOT NULL,
	"jenis_kelamin_id" INTEGER NULL DEFAULT NULL,
	"pendidikan_id" INTEGER NULL DEFAULT NULL,
	"pekerjaan_id" INTEGER NULL DEFAULT NULL,
	"pelayanan_id" INTEGER NULL DEFAULT NULL,
	"penjamin_id" INTEGER NULL DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "FK_profil_jenis_kelamin" FOREIGN KEY ("jenis_kelamin_id") REFERENCES "jenis_kelamin" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT "FK_profil_pekerjaan" FOREIGN KEY ("pekerjaan_id") REFERENCES "pekerjaan" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT "FK_profil_pelayanan" FOREIGN KEY ("pelayanan_id") REFERENCES "pelayanan" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT "FK_profil_pendidikan" FOREIGN KEY ("pendidikan_id") REFERENCES "pendidikan" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT "FK_profil_penjamin" FOREIGN KEY ("penjamin_id") REFERENCES "penjamin" ("id") ON UPDATE CASCADE ON DELETE CASCADE
);

-- Dumping data for table public.profil: -1 rows
/*!40000 ALTER TABLE "profil" DISABLE KEYS */;
INSERT INTO "profil" ("id", "jenis_kelamin_id", "pendidikan_id", "pekerjaan_id", "pelayanan_id", "penjamin_id") VALUES
	(4, 2, 3, 4, 7, 1),
	(5, 1, 5, 5, 6, 1);
/*!40000 ALTER TABLE "profil" ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
