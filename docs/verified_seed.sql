-- Verified seed data generated from 2025-2026 source files
-- districts
INSERT INTO districts (name) VALUES
('Anahawan'),
('Bontoc 1'),
('Bontoc 2'),
('Hinunangan'),
('Hinundayan'),
('Libagon'),
('Liloan'),
('Limasawa'),
('Macrohon'),
('Malitbog'),
('Padre Burgos'),
('Pintuyan'),
('Saint Bernard'),
('San Francisco'),
('San Juan'),
('San Ricardo'),
('Silago'),
('Sogod I'),
('Sogod II'),
('Tomas Oppus')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- schools
INSERT INTO schools (district_id, name)
SELECT d.id, x.school_name
FROM districts d
JOIN (
    SELECT 'Anahawan' AS district_name, 'Amagusan ES' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Anahawan Central School' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Anahawan CS' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Anahawan NVHS' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Calinta-an MGES' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Capacuhan ES' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Lewing MGES' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Mahalo ES' AS school_name
    UNION ALL
    SELECT 'Anahawan' AS district_name, 'Manigawong MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Banahao MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Baugo MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Beniton Integrated School' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Beniton IS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Bontoc CS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Bontoc National High School' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Bontoc NHS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Casao MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Esperanza ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Guinsangaan MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Hibagwan PS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Hilaan ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Hilaan NHS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Himakilo PS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Hitawos IS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Lawgawan PS' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Malbago MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Olisihan MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Pamigsian MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Pangi MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Sta. Cruz ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 1' AS district_name, 'Taa ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Anahao PS' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Buenavista IS' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Bunga ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Catmon MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Catuogan MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Cawayanan ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Divisoria ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Divisoria NHS' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Lanao MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Mahayahay ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Mauylab MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Paku ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Paku NHS' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Pamahawan MGES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Sampongon Elementary School' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Sampongon ES' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'San Vicente PS' AS school_name
    UNION ALL
    SELECT 'Bontoc 2' AS district_name, 'Union ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Ambacon ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Biasong ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Bugho Es' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Calag-itan ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Calinao ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Canipaan ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Canipaan NHS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Catublian ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Hinunangan Bethel Christian School, Inc.' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Hinunangan East CS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Hinunangan NHS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Hinunangan West CS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Ilaya Elementary School' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Ingan ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Kabaskan ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Kabaskan PS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Libas ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Lumbog ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Manalog Elementary School' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Manalog ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Manlico ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Matin-ao ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Nava ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Nava National High School' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Nava NHS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Nueva Esperanza ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Otama ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Palongpong ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Patong ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Pondol ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'San Pablo IS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'San Pedro ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Senda PS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Sto. NINO II ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Sto. Nino NHS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Tahusan ES' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Tawog PS' AS school_name
    UNION ALL
    SELECT 'Hinunangan' AS district_name, 'Tuburan ES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Amaga ES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Amaga MG Elementary School' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Ambao ES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'An-An MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Baculod MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Biasong MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Bugho ES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Cabulisan MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Cat-iwing MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Hinundayan Catholic Institute' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Hinundayan Catholic Institute, Inc.' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Hinundayan CS' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Hubasan ES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Lungsodaan ES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Lungsodaan NHS' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Navalita MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Plaridel MGES' AS school_name
    UNION ALL
    SELECT 'Hinundayan' AS district_name, 'Sagbok ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Gakat ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Kawayan ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Libagon CS' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Libagon NHS' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Magkasag ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Mayuga ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Nahulid ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Otikon Elementary School' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Otikon ES' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'RMSMNHS' AS school_name
    UNION ALL
    SELECT 'Libagon' AS district_name, 'Tigbao ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Amaga ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Anilao Elementary School' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Anilao ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Bahay ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Cagbungalon ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Calian ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Caligangan ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Candayuman ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'CATIG ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Catig ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Estela ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Estela NHS' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Fatima PS' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Guintoylan ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Himay-angan ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Himayangan NHS' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Ilag ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Liloan CS' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Liloan NTVHS' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Magaupas ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Mariano Silot Memorial ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'New Malangza ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Pandan ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Pres. Quezon ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Pres. Roxas ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'San Isidro ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'San Roque ES' AS school_name
    UNION ALL
    SELECT 'Liloan' AS district_name, 'Tabugon ES' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'Limasawa National High School' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'Limasawa NHS' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'Lugsongan ES' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'Magallanes ES' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'San Agustin ES' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'San Bernardo ES' AS school_name
    UNION ALL
    SELECT 'Limasawa' AS district_name, 'Triana ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Aguinaldo ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Amparo ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Asuncion MGES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Bagong Silang MGS' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Bus-Canlusay ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Cambaro ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Flordeliz ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Ichon ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Ichon NHS' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Ilihan ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Laray ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Mabini ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Macrohon CS' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Molopolo ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Rizal ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Salvador MGES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'San Isidro ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'San Joaquin ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'San Roque Elementary School' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'San Roque ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'San Roque NHS' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Sindangan ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Sto. Nino ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Villa Jacinta ES' AS school_name
    UNION ALL
    SELECT 'Macrohon' AS district_name, 'Villa Jacinta NVHS' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Abgao ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Aurora ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Benit Elementary School' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Benit ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Cadaruhan ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Cadaruhan Integrated School' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Cambalhin ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Cantamuac ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Caraatan ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Concepcion ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Concepcion NHS' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Guinabonan ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Juangon ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Kauswagan ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Lambonao ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Malitbog CS' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Malitbog NHS' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Maningning ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'San Jose ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'San Vicente ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Sangahon ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Sta. Cruz ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Sta. Cruz NHS' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Tigbawan ES' AS school_name
    UNION ALL
    SELECT 'Malitbog' AS district_name, 'Timba ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Bunga EMGS' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Bunga ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Cantutang ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Dinahugan PS' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Laca ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Lungsodaan ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Padre Burgos Central School' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Padre Burgos CS' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Padre Burgos NTVHS' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'San Juan ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'San Juan ES w/ SPED' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Santo Rosario ES' AS school_name
    UNION ALL
    SELECT 'Padre Burgos' AS district_name, 'Tangkaan ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Buenavista ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Bulawan PS' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Catbawan ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Cogon ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Dan-an ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Manglit ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Nueva Estrella ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Pintuyan CS' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Pintuyan National High School' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Pintuyan NHS' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Pintuyan NVHS' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Punod PS' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'San Agustin ES' AS school_name
    UNION ALL
    SELECT 'Pintuyan' AS district_name, 'Son-ok ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Atuyan MGES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Ayahag ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Bantawon MGES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Bolodbolod ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Carnaga MGES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Catmon IS' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Himbangan ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Himbangan NHS' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Himos-onan ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Hindag-an ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Libas MGES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Lipanto ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Ma. Asuncion ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Magbagacay ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Mahayag ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Mahayahay ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Mahika ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Malinao MGES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'New Guinsaugon Elementary School' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'New Guinsaugon ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'New Guinsaugon NHS' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Nueva Esperanza ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Panian ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Saint Bernard CS' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'San Isidro ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Sta.Cruz MGES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Sug-Angon PS' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Tabontabon ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Tambis I ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Tambis II MG ES' AS school_name
    UNION ALL
    SELECT 'Saint Bernard' AS district_name, 'Tambis NHS' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Anislagon PS' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Bongawisan ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Bongbong ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Celestino A. Ablas Sr. Academy Foundation Inc. - Saint Joseph College Extension' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Celestino A. Ablas Sr. Academy Foundation Inc.- Saint Joseph College Extension' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Habay ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Marayag ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Marayag NHS' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Napantao ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Pinamudlan ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'San Francisco CS' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'San Francisco NHS' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Sta. Paz ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Sta. Paz NHS' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Sudmon Elementary School' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Sudmon ES' AS school_name
    UNION ALL
    SELECT 'San Francisco' AS district_name, 'Tuno ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Agay-ay ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Basak ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Bobon ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Dayanog ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Garsavic ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Pong-oy ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'San Juan Central Elementary with SPED Center' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'San Juan ES w/ SPED' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'San Juan NHS' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Somoje ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Sta. Filomena ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Sua ES' AS school_name
    UNION ALL
    SELECT 'San Juan' AS district_name, 'Timba ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Benit ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Camang ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Esperanza ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Esperanza National High School' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Esperanza NHS' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Kinachawa ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Pinut-an ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Pinut-an NHS' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'San Ramon ES' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'San Ricardo CS' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'San Ricardo NHS' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Saub Integrated School' AS school_name
    UNION ALL
    SELECT 'San Ricardo' AS district_name, 'Saub IS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Awayon ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Balagawan ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Catmon ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Hingatungan ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Hingatungan NHS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Imelda PS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Katipunan ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Katipunan NHS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Lagoma ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Mercedes NHS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Puntana PS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Salvacion ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Sap-ang ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Silago Central School' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Silago CS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Silago NVHS' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Sudmon ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Tuba-on ES' AS school_name
    UNION ALL
    SELECT 'Silago' AS district_name, 'Tubod ES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Cabadbaran PS' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Hindangan PS' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Kauswagan ES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Libas ES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Libas NHS' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Lum-an MGES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Lum-an PS' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Mabicay ES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Milagroso ES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Rizal PS' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'San Pedro Elementary School' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'San Pedro ES' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Sogod CS w/ SPED Center' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Sogod NHS' AS school_name
    UNION ALL
    SELECT 'Sogod I' AS district_name, 'Sta. Maria PS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Benit PS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Buac ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Concepcion ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Consolacion Elementary School' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Consolacion ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Consolacion NHS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Dagsa MGES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Hipantag MGES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Hipantag PS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Kahupian IS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Kanangkaan ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Mac ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Magatas ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Olisihan ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Pancho Villa ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Pandan-San Miguel ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'San Francisco Mabuhay PS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'San Isidro ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'San Isidro NHS' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'San Juan ES' AS school_name
    UNION ALL
    SELECT 'Sogod II' AS district_name, 'Suba ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Anahawan ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Cabascan ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Camansi ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Cambite ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Canlupao ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Carnaga ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Cawayan ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'DAFENHS' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Hinagtican ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Hinapo ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Hugpa ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Maanyag ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Mapgap ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Maslog ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Rizal ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Rizal NHS' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'San Antonio ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'San Isidro ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'San Isidro NHS' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Tinago ES' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Tomas Oppus Central School' AS school_name
    UNION ALL
    SELECT 'Tomas Oppus' AS district_name, 'Tomas Oppus CS' AS school_name
) x ON x.district_name = d.name
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- client types
INSERT INTO client_types (code, label) VALUES
('division_office_personnel', 'Division Office Personnel'),
('field_personnel', 'Field Personnel'),
('visitor', 'Visitor')
ON DUPLICATE KEY UPDATE label = VALUES(label);

-- personnel
INSERT INTO personnel (full_name, position_title, district_id, area, client_type_id)
SELECT x.full_name, x.position_title, d.id, x.area, ct.id
FROM (
    SELECT 'Aireen Erasmo' AS full_name, 'P & R Secretary' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Alfredo M. Bayon' AS full_name, 'Chief CID' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Allan M. Rosello' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'MAPEH/Math' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Andrew' AS full_name, 'Anduyo' AS position_title, NULL AS district_name, 'ADMINISTRATIVE SERVICES-PERSONNEL' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Aprilyn V. Gaviola' AS full_name, 'PDO I' AS position_title, NULL AS district_name, 'Student Services' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Atty. Felipe Sanchez' AS full_name, 'SEPS' AS position_title, NULL AS district_name, 'SocMob' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Betelino V. Amigo' AS full_name, 'EPSA' AS position_title, NULL AS district_name, 'LS A' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Charity M. Nogra' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'Science' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Clarense Pena' AS full_name, 'Sport Coordinator' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Dexter Tantoy' AS full_name, 'Secretary' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Edril' AS full_name, 'Betonio ,' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Eduardo E. Legantin' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'LR' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Elizabeth Garvez' AS full_name, 'Secretary' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Emmanuel A. Gerardo' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'Araling Panlipunan' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Ethel' AS full_name, 'Acuna' AS position_title, NULL AS district_name, 'ADMINISTRATIVE SERVICES-RECORDS' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Evangelina B. Laroa' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'Values' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Farrah' AS full_name, 'Bandibas' AS position_title, NULL AS district_name, 'ADMINISTRATIVE SERVICES-PROPERTY & SUPPLY' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Glaiza Mea D. Rin' AS full_name, 'EPS-II' AS position_title, NULL AS district_name, 'SMM&E' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Gracelda Macaldo' AS full_name, 'Utility' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Hilda D. Olvina' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'Filipino' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Hilda G. Fernandez' AS full_name, 'EPS II' AS position_title, NULL AS district_name, 'HRD' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Isabelo D. O rais' AS full_name, 'Chief SGOD' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Isidro Catubig' AS full_name, 'ASDS' AS position_title, NULL AS district_name, 'ASDS' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jenelyn' AS full_name, 'Inting' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Joan' AS full_name, 'Malasaga' AS position_title, NULL AS district_name, 'ADMINISTRATIVE SERVICES-CASH' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jonah' AS full_name, 'Balata' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jose Carmelo Gaviola' AS full_name, 'Dentist' AS position_title, NULL AS district_name, 'Medical Section' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Josilyn n Solana' AS full_name, 'SDS' AS position_title, NULL AS district_name, 'SDS' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Judy' AS full_name, 'Sy' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Lloyd C. Carbonilla' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'ALS' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Loise O. Solomon' AS full_name, 'Medical Officer' AS position_title, NULL AS district_name, 'Medical Section' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Lorlin P. Malbas' AS full_name, 'SEPS' AS position_title, NULL AS district_name, 'P & R' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Lyna Gayas' AS full_name, 'Administrative V' AS position_title, NULL AS district_name, 'ADMINISTRATIVE SERVICES' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Ma. Leila V. Aguilar' AS full_name, 'EPS II' AS position_title, NULL AS district_name, 'SocMob' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Marnuld F. Climaco' AS full_name, 'Teacher' AS position_title, NULL AS district_name, 'Saint Joseph College' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Mary Edaline' AS full_name, 'Perez' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Railan Saavedra' AS full_name, 'COS' AS position_title, NULL AS district_name, 'DRRM' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Ronald Cuevas' AS full_name, 'Planning Officer III' AS position_title, NULL AS district_name, 'P & R' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Rosabel Matacot' AS full_name, NULL AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Ruth G. Poblete' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'Kindergarten' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Salvador A. Artigo , Jr.' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'English' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Samson Clarus' AS full_name, 'PDO II' AS position_title, NULL AS district_name, 'DRRM' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Sherwin Segovia' AS full_name, 'Dentist' AS position_title, NULL AS district_name, 'Medical Section' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Victor D. Dumaguit' AS full_name, 'EPS' AS position_title, NULL AS district_name, 'TLE' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Yden Earl' AS full_name, 'Billiones' AS position_title, NULL AS district_name, NULL AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Zedrick G. Malbas' AS full_name, 'SEPS' AS position_title, NULL AS district_name, 'SMM&E' AS area, 'division_office_personnel' AS client_type_code
    UNION ALL
    SELECT 'Amalia A. Medilo' AS full_name, 'Teacher III' AS position_title, 'San Juan' AS district_name, 'San Juan Central Elementary with SPED Center' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Angelito T. Paca Jr.' AS full_name, 'PSDS' AS position_title, 'Liloan' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Antonio A. Magallanes' AS full_name, 'DIC' AS position_title, 'Hinundayan' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Brenda E. Canillo' AS full_name, 'Principal I' AS position_title, 'Liloan' AS district_name, 'Anilao Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Divina F. Tanque' AS full_name, 'Principal II' AS position_title, 'Macrohon' AS district_name, 'San Roque Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Dotegrace C. Simagala' AS full_name, 'Teacher III' AS position_title, 'San Ricardo' AS district_name, 'Esperanza National High School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Edgardo M. Resos' AS full_name, 'Teacher III/TIC' AS position_title, 'Bontoc 1' AS district_name, 'Bontoc National High School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Edna J. Inocentes' AS full_name, 'PSDS' AS position_title, 'Hinunangan' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Elisa R. Edilo' AS full_name, 'DIC' AS position_title, 'San Juan' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Elsie Jane M. Mantilla' AS full_name, 'PSDS' AS position_title, 'Macrohon' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Evangeline A. Gorduiz' AS full_name, 'PSDS' AS position_title, 'Bontoc 2' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Evelyn L. Muncada' AS full_name, 'Teacher III/TIC' AS position_title, 'Saint Bernard' AS district_name, 'New Guinsaugon Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Felipe R. Paulo Jr.' AS full_name, 'Principal I' AS position_title, 'Sogod I' AS district_name, 'San Pedro Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Flodeliza O. Dalupere' AS full_name, 'DIC' AS position_title, 'Limasawa' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Gergie Fel E. Paler' AS full_name, 'DIC' AS position_title, 'Malitbog' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Gina C. Sajol' AS full_name, 'DIC' AS position_title, 'San Ricardo' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jejoma Ray B. Dumaran' AS full_name, 'Teacher I' AS position_title, 'Pintuyan' AS district_name, 'Pintuyan National High School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jenibeth L. Amod' AS full_name, 'DIC' AS position_title, 'Silago' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jenifer P. Engalan' AS full_name, 'DIC' AS position_title, 'Sogod II' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Jovie R. Sabillo' AS full_name, 'Principal I' AS position_title, 'Bontoc 2' AS district_name, 'Sampongon Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Lalaine Ruby N. Patual' AS full_name, 'DIC' AS position_title, 'Anahawan' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Lourdes E. Castil' AS full_name, 'PSDS' AS position_title, 'Libagon' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Ma. Morena A. Bendulo' AS full_name, 'PSDS' AS position_title, 'Sogod I' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Margie B. Quisado' AS full_name, 'Teacher III/TIC' AS position_title, 'Tomas Oppus' AS district_name, 'Tomas Oppus Central School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Merry Ann B. Tagon' AS full_name, 'Teacher III/TIC' AS position_title, 'Malitbog' AS district_name, 'Benit Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Minerva V. Engano' AS full_name, 'DIC' AS position_title, 'Pintuyan' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Monalisa S. Integro' AS full_name, 'Teacher I' AS position_title, 'Limasawa' AS district_name, 'Limasawa National High School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Nancy T. Maraon' AS full_name, 'PSDS' AS position_title, 'Padre Burgos' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Raul T. Duarte' AS full_name, 'PSDS' AS position_title, 'Bontoc 1' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Rey B. Moca' AS full_name, 'Principal I' AS position_title, 'Anahawan' AS district_name, 'Anahawan Central School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Reynaldo L. Maranga' AS full_name, 'School Principal' AS position_title, 'Hinunangan' AS district_name, 'Nava National High School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Rocelyn A. Turcal' AS full_name, 'Teacher III/Asst. School Head' AS position_title, 'Padre Burgos' AS district_name, 'Padre Burgos Central School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Roinasol L. Pobadora' AS full_name, 'DIC' AS position_title, 'Tomas Oppus' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Rony T. Gono' AS full_name, 'PSDS' AS position_title, 'San Francisco' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Rowena H. Pedrera' AS full_name, 'Teacher II/TIC' AS position_title, 'Hinundayan' AS district_name, 'Amaga MG Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Sarah E. Pagula' AS full_name, 'Prinicipal I' AS position_title, 'Libagon' AS district_name, 'Otikon Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Susie Z. Pajaron' AS full_name, 'Principal I' AS position_title, 'Silago' AS district_name, 'Silago Central School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Teresita G. Lolo' AS full_name, 'DIC' AS position_title, 'Saint Bernard' AS district_name, 'District Office' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Um F. Pomaloy' AS full_name, 'Head Teacher III' AS position_title, 'San Francisco' AS district_name, 'Sudmon Elementary School' AS area, 'field_personnel' AS client_type_code
    UNION ALL
    SELECT 'Victoria M. Medalle' AS full_name, 'Principal III' AS position_title, 'Sogod II' AS district_name, 'Consolacion Elementary School' AS area, 'field_personnel' AS client_type_code
) x
INNER JOIN client_types ct ON ct.code = x.client_type_code
LEFT JOIN districts d ON d.name = x.district_name
ON DUPLICATE KEY UPDATE
    position_title = VALUES(position_title),
    district_id = VALUES(district_id),
    area = VALUES(area),
    client_type_id = VALUES(client_type_id);