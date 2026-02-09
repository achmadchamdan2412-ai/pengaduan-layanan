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
DROP TABLE IF EXISTS "jenis_kelamin";

CREATE TABLE
    IF NOT EXISTS "jenis_kelamin" (
        "id" SERIAL NOT NULL,
        "nama" CHAR(1) NOT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.jenis_kelamin: 2 rows
/*!40000 ALTER TABLE "jenis_kelamin" DISABLE KEYS */;

INSERT INTO
    "jenis_kelamin" ("id", "nama")
VALUES
    (1, 'L'),
    (2, 'P');

/*!40000 ALTER TABLE "jenis_kelamin" ENABLE KEYS */;

-- Dumping structure for table public.keluhan
DROP TABLE IF EXISTS "keluhan";

CREATE TABLE
    IF NOT EXISTS "keluhan" (
        "id" SERIAL NOT NULL,
        "alamat" TEXT NOT NULL,
        "no_hp" VARCHAR(15) NOT NULL,
        "masukan" TEXT NOT NULL,
        "pukul" TIME NOT NULL,
        "created_at" TIMESTAMP NULL DEFAULT now (),
        "tanggal" DATE NOT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.keluhan: 2 rows
/*!40000 ALTER TABLE "keluhan" DISABLE KEYS */;

INSERT INTO
    "keluhan" (
        "id",
        "alamat",
        "no_hp",
        "masukan",
        "pukul",
        "created_at",
        "tanggal"
    )
VALUES
    (
        1,
        'bypass krian',
        '085299988877',
        'baik',
        '16:59:00',
        '2026-02-04 16:59:30.402874',
        '2026-02-04'
    ),
    (
        2,
        'bypass krian',
        '089236132213',
        'Lama',
        '16:59:00',
        '2026-02-04 16:59:55.020825',
        '2026-02-04'
    );

/*!40000 ALTER TABLE "keluhan" ENABLE KEYS */;

-- Dumping structure for table public.kuisioner
DROP TABLE IF EXISTS "kuisioner";

CREATE TABLE
    IF NOT EXISTS "kuisioner" (
        "id" SERIAL NOT NULL,
        "pertanyaan_id" INTEGER NULL DEFAULT NULL,
        "nilai" SMALLINT NULL DEFAULT NULL,
        "survei_id" INTEGER NULL DEFAULT NULL,
        "survey_date" DATE NULL DEFAULT NULL,
        "survey_time" VARCHAR(10) NULL DEFAULT NULL,
        "created_at" TIMESTAMP NULL DEFAULT now (),
        PRIMARY KEY ("id"),
        CONSTRAINT "FK_kuisioner_pertanyaan" FOREIGN KEY ("pertanyaan_id") REFERENCES "pertanyaan" ("id") ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT "FK_kuisioner_survei" FOREIGN KEY ("survei_id") REFERENCES "survei" ("id") ON UPDATE CASCADE ON DELETE CASCADE
    );

-- Dumping data for table public.kuisioner: 36 rows
/*!40000 ALTER TABLE "kuisioner" DISABLE KEYS */;

INSERT INTO
    "kuisioner" (
        "id",
        "pertanyaan_id",
        "nilai",
        "survei_id",
        "survey_date",
        "survey_time",
        "created_at"
    )
VALUES
    (
        47,
        4,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        48,
        5,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        49,
        6,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        50,
        7,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        51,
        8,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        52,
        9,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        53,
        10,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        54,
        11,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        55,
        12,
        4,
        5,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:03.026132'
    ),
    (
        56,
        4,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        57,
        5,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        58,
        6,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        59,
        7,
        0,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        60,
        8,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        61,
        9,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        62,
        10,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        63,
        11,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        64,
        12,
        4,
        6,
        '2026-02-09',
        '11:50',
        '2026-02-09 11:50:42.580918'
    ),
    (
        65,
        4,
        4,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        66,
        5,
        4,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        67,
        6,
        4,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        68,
        7,
        0,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        69,
        8,
        4,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        70,
        9,
        1,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        71,
        10,
        1,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        72,
        11,
        1,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        73,
        12,
        1,
        7,
        '2026-02-09',
        '11:56',
        '2026-02-09 11:56:20.6548'
    ),
    (
        74,
        4,
        4,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        75,
        5,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        76,
        6,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        77,
        7,
        0,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        78,
        8,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        79,
        9,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        80,
        10,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        81,
        11,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    ),
    (
        82,
        12,
        1,
        8,
        '2026-02-09',
        '12:34',
        '2026-02-09 12:34:20.549767'
    );

/*!40000 ALTER TABLE "kuisioner" ENABLE KEYS */;

-- Dumping structure for table public.pekerjaan
DROP TABLE IF EXISTS "pekerjaan";

CREATE TABLE
    IF NOT EXISTS "pekerjaan" (
        "id" SERIAL NOT NULL,
        "nama" VARCHAR NOT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.pekerjaan: 6 rows
/*!40000 ALTER TABLE "pekerjaan" DISABLE KEYS */;

INSERT INTO
    "pekerjaan" ("id", "nama")
VALUES
    (1, 'PNS'),
    (2, 'TNI'),
    (3, 'POLISI'),
    (4, 'SWASTA'),
    (5, 'WIRAUSAHA'),
    (6, 'LAINNYA');

/*!40000 ALTER TABLE "pekerjaan" ENABLE KEYS */;

-- Dumping structure for table public.pelayanan
DROP TABLE IF EXISTS "pelayanan";

CREATE TABLE
    IF NOT EXISTS "pelayanan" (
        "id" SERIAL NOT NULL,
        "nama" VARCHAR NULL DEFAULT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.pelayanan: -1 rows
/*!40000 ALTER TABLE "pelayanan" DISABLE KEYS */;

INSERT INTO
    "pelayanan" ("id", "nama")
VALUES
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
DROP TABLE IF EXISTS "pendidikan";

CREATE TABLE
    IF NOT EXISTS "pendidikan" (
        "id" SERIAL NOT NULL,
        "nama" VARCHAR NOT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.pendidikan: -1 rows
/*!40000 ALTER TABLE "pendidikan" DISABLE KEYS */;

INSERT INTO
    "pendidikan" ("id", "nama")
VALUES
    (1, 'SD'),
    (2, 'SMP'),
    (3, 'SMA'),
    (4, 'S1'),
    (5, 'S2'),
    (6, 'S3'),
    (7, 'LAINNYA');

/*!40000 ALTER TABLE "pendidikan" ENABLE KEYS */;

-- Dumping structure for table public.penjamin
DROP TABLE IF EXISTS "penjamin";

CREATE TABLE
    IF NOT EXISTS "penjamin" (
        "id" SERIAL NOT NULL,
        "nama" VARCHAR NOT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.penjamin: -1 rows
/*!40000 ALTER TABLE "penjamin" DISABLE KEYS */;

INSERT INTO
    "penjamin" ("id", "nama")
VALUES
    (1, 'UMUM'),
    (2, 'BPJS');

/*!40000 ALTER TABLE "penjamin" ENABLE KEYS */;

-- Dumping structure for table public.pertanyaan
DROP TABLE IF EXISTS "pertanyaan";

CREATE TABLE
    IF NOT EXISTS "pertanyaan" (
        "id" SERIAL NOT NULL,
        "deskripsi" TEXT NOT NULL,
        PRIMARY KEY ("id")
    );

-- Dumping data for table public.pertanyaan: 9 rows
/*!40000 ALTER TABLE "pertanyaan" DISABLE KEYS */;

INSERT INTO
    "pertanyaan" ("id", "deskripsi")
VALUES
    (
        4,
        'Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?'
    ),
    (
        5,
        'Bagaimana pemahaman saudara tentang kemudahan prosedur pelayanan di unit ini?'
    ),
    (
        6,
        'Bagaimana pendapat saudara tentang kecepatan waktu dalam memberikan pelayanan?'
    ),
    (
        7,
        'Bagaimana pendapat saudara tentang kewajaran biaya atau tarif dalam pelayanan? (Jika saudara peserta BPJS/Asuransi tidak perlu diisi)'
    ),
    (
        8,
        'Bagaimana pendapat saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?'
    ),
    (
        9,
        'Bagaimana pendapat saudara tentang kompetensi atau kemampuan petugas dalam pelayanan?'
    ),
    (
        10,
        'Bagaimana pendapat saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?'
    ),
    (
        11,
        'Bagaimana pendapat saudara tentang penanganan pengaduan pengguna layanan?'
    ),
    (
        12,
        'Bagaimana pendapat saudara tentang kualitas sarana dan prasarana?'
    );

/*!40000 ALTER TABLE "pertanyaan" ENABLE KEYS */;

-- Dumping structure for table public.profil
DROP TABLE IF EXISTS "profil";

CREATE TABLE
    IF NOT EXISTS "profil" (
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

-- Dumping data for table public.profil: 4 rows
/*!40000 ALTER TABLE "profil" DISABLE KEYS */;

INSERT INTO
    "profil" (
        "id",
        "jenis_kelamin_id",
        "pendidikan_id",
        "pekerjaan_id",
        "pelayanan_id",
        "penjamin_id"
    )
VALUES
    (24, 1, 2, 3, 2, 1),
    (25, 2, 5, 5, 5, 2),
    (26, 1, 6, 2, 6, 2),
    (27, 2, 7, 6, 10, 2);

/*!40000 ALTER TABLE "profil" ENABLE KEYS */;

-- Dumping structure for table public.survei
DROP TABLE IF EXISTS "survei";

CREATE TABLE
    IF NOT EXISTS "survei" (
        "id" SERIAL NOT NULL,
        "profil_id" INTEGER NOT NULL DEFAULT 0,
        "created_at" TIMESTAMP NULL DEFAULT now (),
        PRIMARY KEY ("id"),
        CONSTRAINT "FK_survei_profil" FOREIGN KEY ("profil_id") REFERENCES "profil" ("id") ON UPDATE NO ACTION ON DELETE NO ACTION
    );

-- Dumping data for table public.survei: 2 rows
/*!40000 ALTER TABLE "survei" DISABLE KEYS */;

INSERT INTO
    "survei" ("id", "profil_id", "created_at")
VALUES
    (5, 24, '2026-02-09 11:50:03.026132'),
    (6, 25, '2026-02-09 11:50:42.580918'),
    (7, 26, '2026-02-09 11:56:20.6548'),
    (8, 27, '2026-02-09 12:34:20.549767');

/*!40000 ALTER TABLE "survei" ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;

/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;