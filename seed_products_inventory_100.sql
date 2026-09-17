-- MMBPOS demo seed: 100 products and 100 inventory batches.
-- Based on the schema in mmbpos.sql (MariaDB 10.4+).
-- Product IDs 10001-10100 are used to avoid the existing product IDs.
-- Supplier ID 1 (ABC PHARMA) is created only if it is missing, then used for every batch.
-- Run this file once in the mmbpos database.

USE `mmbpos`;

START TRANSACTION;

INSERT INTO `suppliers`
(`id`, `supplier_name`, `contact_person`, `contact_number`, `email`, `address`, `supplier_type`, `is_active`)
SELECT 1, 'ABC PHARMA', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'Manila Philippines', 'Pharmaceutical Distributor', 1
WHERE NOT EXISTS (SELECT 1 FROM `suppliers` WHERE `id` = 1);

CREATE TEMPORARY TABLE `seed_catalog` (
    `n` INT NOT NULL,
    `brand` VARCHAR(255) NOT NULL,
    `generic_name` VARCHAR(255) NOT NULL,
    `strength` DECIMAL(10,2) NOT NULL,
    `measurement_id` INT NULL,
    `category_id` INT NULL,
    `units_per_package` INT NULL,
    `package_type` VARCHAR(100) NULL,
    `dosage_form` VARCHAR(100) NULL,
    `dosage_form_id` INT NULL,
    `strength_per_quantity` DECIMAL(10,2) NULL,
    `strength_per_quantity_unit` VARCHAR(50) NULL,
    `purchase_cost` DECIMAL(10,2) NOT NULL,
    `received_quantity` INT NOT NULL
);

INSERT INTO `seed_catalog` VALUES
(1,'Biogesic','Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',8.00,100),
(2,'Tempra','Paracetamol',250,2,18,60,'Bottle','Syrup',3,5,'mL',55.00,80),
(3,'Advil','Ibuprofen',200,2,18,20,'Box','Capsule',2,10,'pcs',12.00,100),
(4,'Alaxan FR','Ibuprofen + Paracetamol',200,2,18,20,'Box','Capsule',2,10,'pcs',14.00,100),
(5,'Mefinamic','Mefenamic Acid',500,2,18,20,'Box','Capsule',2,10,'pcs',18.00,90),
(6,'Kremil-S','Aluminum Hydroxide + Magnesium Hydroxide',178,2,18,20,'Box','Tablet',1,10,'pcs',9.00,100),
(7,'Buscopan','Hyoscine Butylbromide',10,2,18,20,'Box','Tablet',1,10,'pcs',22.00,80),
(8,'Diatabs','Loperamide',2,2,18,20,'Box','Capsule',2,4,'pcs',10.00,100),
(9,'Imodium','Loperamide',2,2,18,20,'Box','Capsule',2,4,'pcs',25.00,60),
(10,'Zyrtec','Cetirizine',10,2,18,20,'Box','Tablet',1,10,'pcs',18.00,90),
(11,'Claritin','Loratadine',10,2,18,20,'Box','Tablet',1,10,'pcs',20.00,90),
(12,'Neozep','Phenylephrine + Chlorphenamine + Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',12.00,100),
(13,'Bioflu','Phenylephrine + Chlorphenamine + Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',14.00,100),
(14,'Decolgen','Phenylephrine + Chlorphenamine + Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',12.00,80),
(15,'Solmux','Carbocisteine',500,2,18,20,'Box','Capsule',2,10,'pcs',16.00,80),
(16,'Ascof','Lagundi Leaf Extract',600,2,18,60,'Bottle','Syrup',3,60,'mL',95.00,60),
(17,'Ventolin','Salbutamol',2,2,17,60,'Bottle','Syrup',3,60,'mL',110.00,40),
(18,'Strepsils','Amylmetacresol + Dichlorobenzyl Alcohol',1,2,18,20,'Box','Tablet',1,16,'pcs',45.00,80),
(19,'Difflam','Benzydamine Hydrochloride',3,2,18,60,'Bottle','Solution',11,100,'mL',180.00,40),
(20,'Gaviscon','Sodium Alginate + Sodium Bicarbonate',500,2,18,60,'Bottle','Suspension',3,150,'mL',220.00,40),
(21,'Losec','Omeprazole',20,2,17,14,'Box','Capsule',2,14,'pcs',35.00,60),
(22,'Amoxil','Amoxicillin',500,2,17,14,'Box','Capsule',2,20,'pcs',12.00,80),
(23,'Augmentin','Amoxicillin + Clavulanic Acid',625,2,17,14,'Box','Tablet',1,14,'pcs',75.00,50),
(24,'Keflex','Cephalexin',500,2,17,14,'Box','Capsule',2,20,'pcs',18.00,60),
(25,'Cipro','Ciprofloxacin',500,2,17,14,'Box','Tablet',1,10,'pcs',20.00,50),
(26,'Flagyl','Metronidazole',500,2,17,14,'Box','Tablet',1,20,'pcs',8.00,60),
(27,'Bactrim','Sulfamethoxazole + Trimethoprim',800,2,17,14,'Box','Tablet',1,10,'pcs',16.00,50),
(28,'Diflucan','Fluconazole',150,2,17,14,'Box','Capsule',2,1,'pcs',65.00,40),
(29,'Losartan','Losartan Potassium',50,2,17,14,'Box','Tablet',1,30,'pcs',6.00,100),
(30,'Norvasc','Amlodipine',5,2,17,14,'Box','Tablet',1,30,'pcs',5.00,100),
(31,'Metformin','Metformin Hydrochloride',500,2,17,14,'Box','Tablet',1,30,'pcs',4.00,100),
(32,'Glucophage','Metformin Hydrochloride',850,2,17,14,'Box','Tablet',1,30,'pcs',8.00,80),
(33,'Lipitor','Atorvastatin',20,2,17,14,'Box','Tablet',1,30,'pcs',18.00,60),
(34,'Plavix','Clopidogrel',75,2,17,14,'Box','Tablet',1,28,'pcs',25.00,50),
(35,'Levothyroxine','Levothyroxine Sodium',50,2,17,14,'Box','Tablet',1,30,'pcs',10.00,50),
(36,'Ventolin','Salbutamol',100,2,17,1,'Box','Inhaler',16,1,'pc',320.00,30),
(37,'Insulin Pen','Human Insulin',100,12,17,3,'Box','Injection',8,1,'pc',450.00,20),
(38,'PediaSure','Complete Nutrition Formula',850,3,20,1,'Can',NULL,NULL,850,'g',950.00,20),
(39,'Ceelin','Vitamin C',30,2,20,60,'Bottle','Drops',7,30,'mL',125.00,50),
(40,'Cherifer','Multivitamins + Lysine',500,2,20,30,'Bottle','Syrup',3,60,'mL',180.00,40),
(41,'Conzace','Vitamin A + C + E + Zinc',500,2,20,30,'Box','Capsule',2,30,'pcs',12.00,80),
(42,'Enervon','B-Complex + Vitamin C',500,2,20,30,'Box','Tablet',1,30,'pcs',9.00,100),
(43,'Stresstabs','Multivitamins + Minerals',1,15,20,30,'Box','Tablet',1,30,'pcs',18.00,80),
(44,'Immunomax','Vitamin C + Zinc',500,2,20,30,'Box','Tablet',1,30,'pcs',15.00,70),
(45,'Caltrate','Calcium Carbonate + Vitamin D3',600,2,20,60,'Box','Tablet',1,30,'pcs',20.00,60),
(46,'Fern-C','Ascorbic Acid + Zinc',500,2,20,30,'Box','Tablet',1,30,'pcs',12.00,70),
(47,'Potencee','Ascorbic Acid',500,2,20,30,'Box','Tablet',1,30,'pcs',10.00,80),
(48,'Kirkland','Fish Oil',1000,2,20,100,'Bottle','Capsule',2,100,'pcs',8.00,40),
(49,'Iron Plus','Ferrous Sulfate',325,2,20,30,'Box','Tablet',1,30,'pcs',7.00,70),
(50,'Folart','Folic Acid',5,2,20,30,'Box','Tablet',1,30,'pcs',5.00,70),
(51,'Betadine','Povidone Iodine',10,11,21,60,'Bottle','Solution',11,60,'mL',95.00,60),
(52,'Alcohol 70%','Ethyl Alcohol',70,11,21,500,'Bottle','Solution',11,500,'mL',85.00,80),
(53,'Agua Oxigenada','Hydrogen Peroxide',3,11,21,120,'Bottle','Solution',11,120,'mL',35.00,60),
(54,'Bactroban','Mupirocin',2,11,21,15,'Tube','Ointment',6,15,'g',180.00,40),
(55,'Fucidin','Fusidic Acid',2,11,21,15,'Tube','Cream',5,15,'g',160.00,40),
(56,'Caladryl','Calamine + Diphenhydramine',8,11,21,60,'Bottle','Lotion',13,60,'mL',125.00,40),
(57,'Salonpas','Methyl Salicylate + Menthol',1,15,21,20,'Box','Patch',15,20,'pcs',12.00,80),
(58,'Omega Plaster','Adhesive Bandage',1,15,21,100,'Box','Patch',15,100,'pcs',2.00,100),
(59,'Betadine Gargle','Povidone Iodine',1,11,21,120,'Bottle','Solution',11,120,'mL',110.00,40),
(60,'Efficascent Oil','Menthol + Methyl Salicylate',1,15,21,100,'Bottle','Solution',11,100,'mL',95.00,50),
(61,'N95 Mask','Particulate Respirator',0,NULL,19,20,'Box',NULL,NULL,NULL,NULL,250.00,40),
(62,'Surgical Mask','Disposable Face Mask',0,NULL,19,50,'Box',NULL,NULL,NULL,NULL,120.00,50),
(63,'Latex Gloves','Examination Gloves',0,NULL,19,100,'Box',NULL,NULL,NULL,NULL,350.00,30),
(64,'Syringe 5mL','Sterile Disposable Syringe',0,NULL,19,100,'Box',NULL,NULL,NULL,NULL,250.00,30),
(65,'Syringe 10mL','Sterile Disposable Syringe',0,NULL,19,50,'Box',NULL,NULL,NULL,NULL,220.00,30),
(66,'Cotton Balls','Sterile Cotton Balls',0,NULL,19,100,'Pack',NULL,NULL,NULL,NULL,45.00,60),
(67,'Gauze Pads','Sterile Gauze Pad',0,NULL,19,25,'Pack',NULL,NULL,NULL,NULL,75.00,50),
(68,'Elastic Bandage','Elastic Crepe Bandage',0,NULL,19,1,'Roll',NULL,NULL,NULL,NULL,65.00,40),
(69,'Digital Thermometer','Digital Clinical Thermometer',0,NULL,22,1,'Box',NULL,NULL,NULL,NULL,180.00,30),
(70,'Blood Glucose Strips','Blood Glucose Test Strips',0,NULL,22,50,'Box',NULL,NULL,NULL,NULL,450.00,25),
(71,'Safeguard','Antibacterial Soap',90,3,25,1,'Bar',NULL,NULL,NULL,NULL,35.00,80),
(72,'Cetaphil','Gentle Cleansing Bar',127,3,25,1,'Box',NULL,NULL,NULL,NULL,180.00,40),
(73,'Lactacyd','Feminine Wash',200,6,25,1,'Bottle',NULL,NULL,NULL,NULL,160.00,40),
(74,'Nivea','Moisturizing Lotion',250,6,25,1,'Bottle',NULL,NULL,NULL,NULL,180.00,40),
(75,'Kojic','Kojic Acid Soap',65,3,25,1,'Bar',NULL,NULL,NULL,NULL,45.00,60),
(76,'Sunscreen SPF50','Broad Spectrum Sunscreen',50,11,25,1,'Tube',NULL,NULL,NULL,NULL,350.00,30),
(77,'Pond''s','Facial Moisturizer',50,3,25,1,'Jar',NULL,NULL,NULL,NULL,220.00,30),
(78,'Colgate','Fluoride Toothpaste',1450,11,25,100,'Tube',NULL,NULL,NULL,NULL,95.00,60),
(79,'Listerine','Mouthwash',250,6,25,1,'Bottle',NULL,NULL,NULL,NULL,180.00,40),
(80,'Vaseline','Petroleum Jelly',100,3,25,1,'Jar',NULL,NULL,NULL,NULL,85.00,40),
(81,'Johnson''s Baby','Baby Powder',200,3,26,1,'Bottle',NULL,NULL,NULL,NULL,120.00,40),
(82,'Huggies','Baby Diaper Small',0,NULL,26,24,'Pack',NULL,NULL,NULL,NULL,280.00,30),
(83,'Huggies','Baby Diaper Medium',0,NULL,26,24,'Pack',NULL,NULL,NULL,NULL,300.00,30),
(84,'Huggies','Baby Diaper Large',0,NULL,26,20,'Pack',NULL,NULL,NULL,NULL,310.00,30),
(85,'Enfamil','Infant Formula',800,3,26,1,'Can',NULL,NULL,NULL,NULL,950.00,20),
(86,'Similac','Infant Formula',800,3,26,1,'Can',NULL,NULL,NULL,NULL,900.00,20),
(87,'Nursicare','Baby Wipes',80,15,26,1,'Pack',NULL,NULL,NULL,NULL,85.00,50),
(88,'Johnson''s Baby','Baby Shampoo',200,6,26,1,'Bottle',NULL,NULL,NULL,NULL,150.00,40),
(89,'Baby Dove','Baby Lotion',200,6,26,1,'Bottle',NULL,NULL,NULL,NULL,180.00,40),
(90,'Pigeon','Feeding Bottle',240,6,26,1,'Bottle',NULL,NULL,NULL,NULL,220.00,30),
(91,'ORS Hydrite','Oral Rehydration Salts',1,15,24,1,'Sachet',NULL,NULL,NULL,NULL,12.00,100),
(92,'Daktarin','Miconazole Nitrate',2,11,24,15,'Tube','Cream',5,15,'g',180.00,40),
(93,'Canesten','Clotrimazole',1,11,24,15,'Tube','Cream',5,15,'g',150.00,40),
(94,'Tears Naturale','Hypromellose',3,11,24,15,'Bottle','Drops',7,15,'mL',250.00,30),
(95,'Nafcon-A','Naphazoline + Pheniramine',0,11,24,15,'Bottle','Drops',7,15,'mL',210.00,30),
(96,'Vicks','Menthol + Camphor',1,15,24,50,'Jar','Ointment',6,50,'g',150.00,40),
(97,'Tiger Balm','Camphor + Menthol',1,15,24,20,'Jar','Ointment',6,20,'g',120.00,40),
(98,'Salonpas Gel','Methyl Salicylate + Menthol',1,15,24,30,'Tube','Gel',12,30,'g',180.00,30),
(99,'Gaviscon Double Action','Sodium Alginate + Calcium Carbonate',500,2,18,300,'Bottle','Suspension',3,150,'mL',260.00,30),
(100,'Pharex','Vitamin B Complex',1,15,20,30,'Box','Tablet',1,30,'pcs',8.00,70);

INSERT INTO `products`
(`id`, `branded_name`, `generic_name`, `strength`, `measurement_id`, `barcode`, `category_id`, `classification_id`, `units_per_package`, `imageproduct`, `is_basic_necessities`, `package_type`, `dosage_form`, `dosage_form_id`, `strength_per_quantity`, `strength_per_quantity_unit`, `is_hidden`)
SELECT
    10000 + `n`, `brand`, `generic_name`,
    CASE WHEN `strength` <= 0 THEN 1 ELSE `strength` END,
    COALESCE(`measurement_id`, 15),
    CONCAT('299900', LPAD(`n`, 7, '0')),
    `category_id`, NULL, 1,
    '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, `package_type`,
    COALESCE(`dosage_form`, 'Powder'), COALESCE(`dosage_form_id`, 9),
    COALESCE(`strength_per_quantity`, COALESCE(`units_per_package`, 1)),
    COALESCE(`strength_per_quantity_unit`, 'pcs'), 0
FROM `seed_catalog`;

CREATE TEMPORARY TABLE `seed_stock` (
    `n` INT NOT NULL,
    `purchase_cost` DECIMAL(10,2) NOT NULL,
    `received_quantity` INT NOT NULL
);

INSERT INTO `seed_stock` VALUES
(1,8.00,100),(2,55.00,80),(3,12.00,100),(4,14.00,100),(5,18.00,90),(6,9.00,100),(7,22.00,80),(8,10.00,100),(9,25.00,60),(10,18.00,90),
        (11,20.00,90),(12,12.00,100),(13,14.00,100),(14,12.00,80),(15,16.00,80),(16,95.00,60),(17,110.00,40),(18,45.00,80),(19,180.00,40),(20,220.00,40),
        (21,35.00,60),(22,12.00,80),(23,75.00,50),(24,18.00,60),(25,20.00,50),(26,8.00,60),(27,16.00,50),(28,65.00,40),(29,6.00,100),(30,5.00,100),
        (31,4.00,100),(32,8.00,80),(33,18.00,60),(34,25.00,50),(35,10.00,50),(36,320.00,30),(37,450.00,20),(38,950.00,20),(39,125.00,50),(40,180.00,40),
        (41,12.00,80),(42,9.00,100),(43,18.00,80),(44,15.00,70),(45,20.00,60),(46,12.00,70),(47,10.00,80),(48,8.00,40),(49,7.00,70),(50,5.00,70),
        (51,95.00,60),(52,85.00,80),(53,35.00,60),(54,180.00,40),(55,160.00,40),(56,125.00,40),(57,12.00,80),(58,2.00,100),(59,110.00,40),(60,95.00,50),
        (61,250.00,40),(62,120.00,50),(63,350.00,30),(64,250.00,30),(65,220.00,30),(66,45.00,60),(67,75.00,50),(68,65.00,40),(69,180.00,30),(70,450.00,25),
        (71,35.00,80),(72,180.00,40),(73,160.00,40),(74,180.00,40),(75,45.00,60),(76,350.00,30),(77,220.00,30),(78,95.00,60),(79,180.00,40),(80,85.00,40),
        (81,120.00,40),(82,280.00,30),(83,300.00,30),(84,310.00,30),(85,950.00,20),(86,900.00,20),(87,85.00,50),(88,150.00,40),(89,180.00,40),(90,220.00,30),
        (91,12.00,100),(92,180.00,40),(93,150.00,40),(94,250.00,30),(95,210.00,30),(96,150.00,40),(97,120.00,40),(98,180.00,30),(99,260.00,30),(100,8.00,70);

INSERT INTO `inventory`
(`product_id`, `supplier_id`, `batch_number`, `date_received`, `expiry_date`, `purchase_cost`, `markup`, `sale_price`, `received_quantity`, `current_quantity`)
SELECT
    10000 + `n`, 1, CONCAT('SEED-BATCH-', LPAD(`n`, 3, '0')), CURRENT_DATE,
    DATE_ADD(CURRENT_DATE, INTERVAL (180 + (`n` * 7)) DAY),
    `purchase_cost`, 20.00, ROUND(`purchase_cost` * 1.20, 2), `received_quantity`, `received_quantity`
FROM `seed_stock`;

/* The catalog rows above are intentionally explicit; the following old
   generated catalog is retained only as a disabled reference block. */
/*
        (1,'Biogesic','Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',8.00,100),
        (2,'Tempra','Paracetamol',250,2,18,60,'Bottle','Syrup',3,5,'mL',55.00,80),
        (3,'Advil','Ibuprofen',200,2,18,20,'Box','Capsule',2,10,'pcs',12.00,100),
        (4,'Alaxan FR','Ibuprofen + Paracetamol',200,2,18,20,'Box','Capsule',2,10,'pcs',14.00,100),
        (5,'Mefinamic','Mefenamic Acid',500,2,18,20,'Box','Capsule',2,10,'pcs',18.00,90),
        (6,'Kremil-S','Aluminum Hydroxide + Magnesium Hydroxide',178,2,18,20,'Box','Tablet',1,10,'pcs',9.00,100),
        (7,'Buscopan','Hyoscine Butylbromide',10,2,18,20,'Box','Tablet',1,10,'pcs',22.00,80),
        (8,'Diatabs','Loperamide',2,2,18,20,'Box','Capsule',2,4,'pcs',10.00,100),
        (9,'Imodium','Loperamide',2,2,18,20,'Box','Capsule',2,4,'pcs',25.00,60),
        (10,'Zyrtec','Cetirizine',10,2,18,20,'Box','Tablet',1,10,'pcs',18.00,90),
        (11,'Claritin','Loratadine',10,2,18,20,'Box','Tablet',1,10,'pcs',20.00,90),
        (12,'Neozep','Phenylephrine + Chlorphenamine + Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',12.00,100),
        (13,'Bioflu','Phenylephrine + Chlorphenamine + Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',14.00,100),
        (14,'Decolgen','Phenylephrine + Chlorphenamine + Paracetamol',500,2,18,20,'Box','Tablet',1,10,'pcs',12.00,80),
        (15,'Solmux','Carbocisteine',500,2,18,20,'Box','Capsule',2,10,'pcs',16.00,80),
        (16,'Ascof','Lagundi Leaf Extract',600,2,18,60,'Bottle','Syrup',3,60,'mL',95.00,60),
        (17,'Ventolin','Salbutamol',2,2,17,60,'Bottle','Syrup',3,60,'mL',110.00,40),
        (18,'Strepsils','Amylmetacresol + Dichlorobenzyl Alcohol',1,2,18,20,'Box','Tablet',1,16,'pcs',45.00,80),
        (19,'Difflam','Benzydamine Hydrochloride',3,2,18,60,'Bottle','Solution',11,100,'mL',180.00,40),
        (20,'Gaviscon','Sodium Alginate + Sodium Bicarbonate',500,2,18,60,'Bottle','Suspension',3,150,'mL',220.00,40),
        (21,'Losec','Omeprazole',20,2,17,14,'Box','Capsule',2,14,'pcs',35.00,60),
        (22,'Amoxil','Amoxicillin',500,2,17,14,'Box','Capsule',2,20,'pcs',12.00,80),
        (23,'Augmentin','Amoxicillin + Clavulanic Acid',625,2,17,14,'Box','Tablet',1,14,'pcs',75.00,50),
        (24,'Keflex','Cephalexin',500,2,17,14,'Box','Capsule',2,20,'pcs',18.00,60),
        (25,'Cipro','Ciprofloxacin',500,2,17,14,'Box','Tablet',1,10,'pcs',20.00,50),
        (26,'Flagyl','Metronidazole',500,2,17,14,'Box','Tablet',1,20,'pcs',8.00,60),
        (27,'Bactrim','Sulfamethoxazole + Trimethoprim',800,2,17,14,'Box','Tablet',1,10,'pcs',16.00,50),
        (28,'Diflucan','Fluconazole',150,2,17,14,'Box','Capsule',2,1,'pcs',65.00,40),
        (29,'Losartan','Losartan Potassium',50,2,17,14,'Box','Tablet',1,30,'pcs',6.00,100),
        (30,'Norvasc','Amlodipine',5,2,17,14,'Box','Tablet',1,30,'pcs',5.00,100),
        (31,'Metformin','Metformin Hydrochloride',500,2,17,14,'Box','Tablet',1,30,'pcs',4.00,100),
        (32,'Glucophage','Metformin Hydrochloride',850,2,17,14,'Box','Tablet',1,30,'pcs',8.00,80),
        (33,'Lipitor','Atorvastatin',20,2,17,14,'Box','Tablet',1,30,'pcs',18.00,60),
        (34,'Plavix','Clopidogrel',75,2,17,14,'Box','Tablet',1,28,'pcs',25.00,50),
        (35,'Levothyroxine','Levothyroxine Sodium',50,2,17,14,'Box','Tablet',1,30,'pcs',10.00,50),
        (36,'Ventolin','Salbutamol',100,2,17,1,'Box','Inhaler',8,1,'pc',320.00,30),
        (37,'Insulin Pen','Human Insulin',100,12,17,3,'Box','Injection',8,1,'pc',450.00,20),
        (38,'PediaSure','Complete Nutrition Formula',850,3,20,1,'Can',NULL,NULL,NULL,NULL,850,'g',950.00,20),
        (39,'Ceelin','Vitamin C',30,2,20,60,'Bottle','Drops',7,30,'mL',125.00,50),
        (40,'Cherifer','Multivitamins + Lysine',500,2,20,30,'Bottle','Syrup',3,60,'mL',180.00,40),
        (41,'Conzace','Vitamin A + C + E + Zinc',500,2,20,30,'Box','Capsule',2,30,'pcs',12.00,80),
        (42,'Enervon','B-Complex + Vitamin C',500,2,20,30,'Box','Tablet',1,30,'pcs',9.00,100),
        (43,'Stresstabs','Multivitamins + Minerals',1,15,20,30,'Box','Tablet',1,30,'pcs',18.00,80),
        (44,'Immunomax','Vitamin C + Zinc',500,2,20,30,'Box','Tablet',1,30,'pcs',15.00,70),
        (45,'Caltrate','Calcium Carbonate + Vitamin D3',600,2,20,60,'Box','Tablet',1,30,'pcs',20.00,60),
        (46,'Fern-C','Ascorbic Acid + Zinc',500,2,20,30,'Box','Tablet',1,30,'pcs',12.00,70),
        (47,'Potencee','Ascorbic Acid',500,2,20,30,'Box','Tablet',1,30,'pcs',10.00,80),
        (48,'Kirkland','Fish Oil',1000,2,20,100,'Bottle','Capsule',2,100,'pcs',8.00,40),
        (49,'Iron Plus','Ferrous Sulfate',325,2,20,30,'Box','Tablet',1,30,'pcs',7.00,70),
        (50,'Folart','Folic Acid',5,2,20,30,'Box','Tablet',1,30,'pcs',5.00,70),
        (51,'Betadine','Povidone Iodine',10,11,21,60,'Bottle','Solution',11,60,'mL',95.00,60),
        (52,'Alcohol 70%','Ethyl Alcohol',70,11,21,500,'Bottle','Solution',11,500,'mL',85.00,80),
        (53,'Agua Oxigenada','Hydrogen Peroxide',3,11,21,120,'Bottle','Solution',11,120,'mL',35.00,60),
        (54,'Bactroban','Mupirocin',2,11,21,15,'Tube','Ointment',6,15,'g',180.00,40),
        (55,'Fucidin','Fusidic Acid',2,11,21,15,'Tube','Cream',5,15,'g',160.00,40),
        (56,'Caladryl','Calamine + Diphenhydramine',8,11,21,60,'Bottle','Lotion',13,60,'mL',125.00,40),
        (57,'Salonpas','Methyl Salicylate + Menthol',1,15,21,20,'Box','Patch',NULL,20,'pcs',12.00,80),
        (58,'Omega Plaster','Adhesive Bandage',1,15,21,100,'Box','Patch',NULL,100,'pcs',2.00,100),
        (59,'Betadine Gargle','Povidone Iodine',1,11,21,120,'Bottle','Solution',11,120,'mL',110.00,40),
        (60,'Efficascent Oil','Menthol + Methyl Salicylate',1,15,21,100,'Bottle','Solution',11,100,'mL',95.00,50),
        (61,'N95 Mask','Particulate Respirator',0,NULL,19,20,'Box',NULL,NULL,NULL,NULL,250.00,40),
        (62,'Surgical Mask','Disposable Face Mask',0,NULL,19,50,'Box',NULL,NULL,NULL,NULL,120.00,50),
        (63,'Latex Gloves','Examination Gloves',0,NULL,19,100,'Box',NULL,NULL,NULL,NULL,350.00,30),
        (64,'Syringe 5mL','Sterile Disposable Syringe',0,NULL,19,100,'Box',NULL,NULL,NULL,NULL,250.00,30),
        (65,'Syringe 10mL','Sterile Disposable Syringe',0,NULL,19,50,'Box',NULL,NULL,NULL,NULL,220.00,30),
        (66,'Cotton Balls','Sterile Cotton Balls',0,NULL,19,100,'Pack',NULL,NULL,NULL,NULL,45.00,60),
        (67,'Gauze Pads','Sterile Gauze Pad',0,NULL,19,25,'Pack',NULL,NULL,NULL,NULL,75.00,50),
        (68,'Elastic Bandage','Elastic Crepe Bandage',0,NULL,19,1,'Roll',NULL,NULL,NULL,NULL,65.00,40),
        (69,'Digital Thermometer','Digital Clinical Thermometer',0,NULL,22,1,'Box',NULL,NULL,NULL,NULL,180.00,30),
        (70,'Blood Glucose Strips','Blood Glucose Test Strips',0,NULL,22,50,'Box',NULL,NULL,NULL,NULL,450.00,25),
        (71,'Safeguard','Antibacterial Soap',90,3,25,1,'Bar',NULL,NULL,NULL,NULL,35.00,80),
        (72,'Cetaphil','Gentle Cleansing Bar',127,3,25,1,'Box',NULL,NULL,NULL,NULL,180.00,40),
        (73,'Lactacyd','Feminine Wash',200,6,25,1,'Bottle',NULL,NULL,NULL,NULL,160.00,40),
        (74,'Nivea','Moisturizing Lotion',250,6,25,1,'Bottle',NULL,NULL,NULL,NULL,180.00,40),
        (75,'Kojic','Kojic Acid Soap',65,3,25,1,'Bar',NULL,NULL,NULL,NULL,45.00,60),
        (76,'Sunscreen SPF50','Broad Spectrum Sunscreen',50,11,25,1,'Tube',NULL,NULL,NULL,NULL,350.00,30),
        (77,'Pond''s','Facial Moisturizer',50,3,25,1,'Jar',NULL,NULL,NULL,NULL,220.00,30),
        (78,'Colgate','Fluoride Toothpaste',1450,11,25,100,'Tube',NULL,NULL,NULL,NULL,95.00,60),
        (79,'Listerine','Mouthwash',250,6,25,1,'Bottle',NULL,NULL,NULL,NULL,180.00,40),
        (80,'Vaseline','Petroleum Jelly',100,3,25,1,'Jar',NULL,NULL,NULL,NULL,85.00,40),
        (81,'Johnson''s Baby','Baby Powder',200,3,26,1,'Bottle',NULL,NULL,NULL,NULL,120.00,40),
        (82,'Huggies','Baby Diaper Small',0,NULL,26,24,'Pack',NULL,NULL,NULL,NULL,280.00,30),
        (83,'Huggies','Baby Diaper Medium',0,NULL,26,24,'Pack',NULL,NULL,NULL,NULL,300.00,30),
        (84,'Huggies','Baby Diaper Large',0,NULL,26,20,'Pack',NULL,NULL,NULL,NULL,310.00,30),
        (85,'Enfamil','Infant Formula',800,3,26,1,'Can',NULL,NULL,NULL,NULL,950.00,20),
        (86,'Similac','Infant Formula',800,3,26,1,'Can',NULL,NULL,NULL,NULL,900.00,20),
        (87,'Nursicare','Baby Wipes',80,15,26,1,'Pack',NULL,NULL,NULL,NULL,85.00,50),
        (88,'Johnson''s Baby','Baby Shampoo',200,6,26,1,'Bottle',NULL,NULL,NULL,NULL,150.00,40),
        (89,'Baby Dove','Baby Lotion',200,6,26,1,'Bottle',NULL,NULL,NULL,NULL,180.00,40),
        (90,'Pigeon','Feeding Bottle',240,6,26,1,'Bottle',NULL,NULL,NULL,NULL,220.00,30),
        (91,'ORS Hydrite','Oral Rehydration Salts',1,15,24,1,'Sachet',NULL,NULL,NULL,NULL,12.00,100),
        (92,'Daktarin','Miconazole Nitrate',2,11,24,15,'Tube','Cream',5,15,'g',180.00,40),
        (93,'Canesten','Clotrimazole',1,11,24,15,'Tube','Cream',5,15,'g',150.00,40),
        (94,'Tears Naturale','Hypromellose',3,11,24,15,'Bottle','Drops',7,15,'mL',250.00,30),
        (95,'Nafcon-A','Naphazoline + Pheniramine',0,11,24,15,'Bottle','Drops',7,15,'mL',210.00,30),
        (96,'Vicks','Menthol + Camphor',1,15,24,50,'Jar','Ointment',6,50,'g',150.00,40),
        (97,'Tiger Balm','Camphor + Menthol',1,15,24,20,'Jar','Ointment',6,20,'g',120.00,40),
        (98,'Salonpas Gel','Methyl Salicylate + Menthol',1,15,24,30,'Tube','Gel',12,30,'g',180.00,30),
        (99,'Gaviscon Double Action','Sodium Alginate + Calcium Carbonate',500,2,18,300,'Bottle','Suspension',3,150,'mL',260.00,30),
        (100,'Pharex','Vitamin B Complex',1,15,20,30,'Box','Tablet',1,30,'pcs',8.00,70)
    ) AS `catalog`(`n`,`brand`,`generic_name`,`strength`,`measurement_id`,`category_id`,`units_per_package`,`package_type`,`dosage_form`,`dosage_form_id`,`strength_per_quantity`,`strength_per_quantity_unit`,`purchase_cost`,`received_quantity`)
) AS `seed_catalog`;

INSERT INTO `inventory`
(`product_id`, `supplier_id`, `batch_number`, `date_received`, `expiry_date`, `purchase_cost`, `markup`, `sale_price`, `received_quantity`, `current_quantity`)
SELECT
    10000 + `n`, 1, CONCAT('SEED-BATCH-', LPAD(`n`, 3, '0')), CURRENT_DATE,
    DATE_ADD(CURRENT_DATE, INTERVAL (180 + (`n` * 7)) DAY),
    `purchase_cost`, 20.00, ROUND(`purchase_cost` * 1.20, 2), `received_quantity`, `received_quantity`
FROM (
    SELECT * FROM (VALUES
        (1,8.00,100),(2,55.00,80),(3,12.00,100),(4,14.00,100),(5,18.00,90),(6,9.00,100),(7,22.00,80),(8,10.00,100),(9,25.00,60),(10,18.00,90),
        (11,20.00,90),(12,12.00,100),(13,14.00,100),(14,12.00,80),(15,16.00,80),(16,95.00,60),(17,110.00,40),(18,45.00,80),(19,180.00,40),(20,220.00,40),
        (21,35.00,60),(22,12.00,80),(23,75.00,50),(24,18.00,60),(25,20.00,50),(26,8.00,60),(27,16.00,50),(28,65.00,40),(29,6.00,100),(30,5.00,100),
        (31,4.00,100),(32,8.00,80),(33,18.00,60),(34,25.00,50),(35,10.00,50),(36,320.00,30),(37,450.00,20),(38,950.00,20),(39,125.00,50),(40,180.00,40),
        (41,12.00,80),(42,9.00,100),(43,18.00,80),(44,15.00,70),(45,20.00,60),(46,12.00,70),(47,10.00,80),(48,8.00,40),(49,7.00,70),(50,5.00,70),
        (51,95.00,60),(52,85.00,80),(53,35.00,60),(54,180.00,40),(55,160.00,40),(56,125.00,40),(57,12.00,80),(58,2.00,100),(59,110.00,40),(60,95.00,50),
        (61,250.00,40),(62,120.00,50),(63,350.00,30),(64,250.00,30),(65,220.00,30),(66,45.00,60),(67,75.00,50),(68,65.00,40),(69,180.00,30),(70,450.00,25),
        (71,35.00,80),(72,180.00,40),(73,160.00,40),(74,180.00,40),(75,45.00,60),(76,350.00,30),(77,220.00,30),(78,95.00,60),(79,180.00,40),(80,85.00,40),
        (81,120.00,40),(82,280.00,30),(83,300.00,30),(84,310.00,30),(85,950.00,20),(86,900.00,20),(87,85.00,50),(88,150.00,40),(89,180.00,40),(90,220.00,30),
        (91,12.00,100),(92,180.00,40),(93,150.00,40),(94,250.00,30),(95,210.00,30),(96,150.00,40),(97,120.00,40),(98,180.00,30),(99,260.00,30),(100,8.00,70)
    ) AS `stock`(`n`,`purchase_cost`,`received_quantity`)
) AS `seed_stock`;
*/

COMMIT;

SELECT COUNT(*) AS `seed_products_added`
FROM `products`
WHERE `id` BETWEEN 10001 AND 10100;

SELECT COUNT(*) AS `seed_inventory_batches_added`
FROM `inventory`
WHERE `product_id` BETWEEN 10001 AND 10100;
