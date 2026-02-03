-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               PostgreSQL 17.7 on x86_64-windows, compiled by msvc-19.44.35221, 64-bit
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
DROP TABLE IF EXISTS "jenis_kelamin";
CREATE TABLE IF NOT EXISTS "jenis_kelamin" (
	"id" SERIAL NOT NULL,
	"nama" CHAR(1) NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.jenis_kelamin: -1 rows
DELETE FROM "jenis_kelamin";
/*!40000 ALTER TABLE "jenis_kelamin" DISABLE KEYS */;
/*!40000 ALTER TABLE "jenis_kelamin" ENABLE KEYS */;

-- Dumping structure for table public.kuisioner
DROP TABLE IF EXISTS "kuisioner";
CREATE TABLE IF NOT EXISTS "kuisioner" (
	"id" SERIAL NOT NULL,
	"pertanyaan_id" INTEGER NULL DEFAULT NULL,
	"nilai" SMALLINT NULL DEFAULT NULL,
	"survey_id" INTEGER NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.kuisioner: -1 rows
DELETE FROM "kuisioner";
/*!40000 ALTER TABLE "kuisioner" DISABLE KEYS */;
/*!40000 ALTER TABLE "kuisioner" ENABLE KEYS */;

-- Dumping structure for table public.pekerjaan
DROP TABLE IF EXISTS "pekerjaan";
CREATE TABLE IF NOT EXISTS "pekerjaan" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pekerjaan: -1 rows
DELETE FROM "pekerjaan";
/*!40000 ALTER TABLE "pekerjaan" DISABLE KEYS */;
/*!40000 ALTER TABLE "pekerjaan" ENABLE KEYS */;

-- Dumping structure for table public.pelayanan
DROP TABLE IF EXISTS "pelayanan";
CREATE TABLE IF NOT EXISTS "pelayanan" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NULL DEFAULT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pelayanan: -1 rows
DELETE FROM "pelayanan";
/*!40000 ALTER TABLE "pelayanan" DISABLE KEYS */;
/*!40000 ALTER TABLE "pelayanan" ENABLE KEYS */;

-- Dumping structure for table public.pendidikan
DROP TABLE IF EXISTS "pendidikan";
CREATE TABLE IF NOT EXISTS "pendidikan" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pendidikan: -1 rows
DELETE FROM "pendidikan";
/*!40000 ALTER TABLE "pendidikan" DISABLE KEYS */;
/*!40000 ALTER TABLE "pendidikan" ENABLE KEYS */;

-- Dumping structure for table public.penjamin
DROP TABLE IF EXISTS "penjamin";
CREATE TABLE IF NOT EXISTS "penjamin" (
	"id" SERIAL NOT NULL,
	"nama" VARCHAR NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.penjamin: -1 rows
DELETE FROM "penjamin";
/*!40000 ALTER TABLE "penjamin" DISABLE KEYS */;
/*!40000 ALTER TABLE "penjamin" ENABLE KEYS */;

-- Dumping structure for table public.pertanyaan
DROP TABLE IF EXISTS "pertanyaan";
CREATE TABLE IF NOT EXISTS "pertanyaan" (
	"id" SERIAL NOT NULL,
	"deskripsi" TEXT NOT NULL,
	PRIMARY KEY ("id")
);

-- Dumping data for table public.pertanyaan: -1 rows
DELETE FROM "pertanyaan";
/*!40000 ALTER TABLE "pertanyaan" DISABLE KEYS */;
/*!40000 ALTER TABLE "pertanyaan" ENABLE KEYS */;

-- Dumping structure for table public.profil
DROP TABLE IF EXISTS "profil";
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
DELETE FROM "profil";
/*!40000 ALTER TABLE "profil" DISABLE KEYS */;
/*!40000 ALTER TABLE "profil" ENABLE KEYS */;

-- Dumping structure for table public.survey
DROP TABLE IF EXISTS "survey";
CREATE TABLE IF NOT EXISTS "survey" (
	"id" SERIAL NOT NULL,
	"profil_id" INTEGER NULL DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "FK_survey_profil" FOREIGN KEY ("profil_id") REFERENCES "profil" ("id") ON UPDATE CASCADE ON DELETE CASCADE
);

-- Dumping data for table public.survey: -1 rows
DELETE FROM "survey";
/*!40000 ALTER TABLE "survey" DISABLE KEYS */;
/*!40000 ALTER TABLE "survey" ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
