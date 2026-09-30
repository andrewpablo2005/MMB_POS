-- MMBPOS demo seed: 20 products and 20 inventory batches.
-- Product IDs 10101-10120 follow the IDs used by seed_products_inventory_100.sql.
-- Demo costs, quantities, and expiry dates should be reviewed before production use.
-- Run this file in the mmbpos database.

USE `mmbpos`;

START TRANSACTION;

INSERT INTO `suppliers`
(`id`, `supplier_name`, `contact_person`, `contact_number`, `email`, `address`, `supplier_type`, `is_active`)
SELECT 1, 'ABC PHARMA', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'Manila Philippines', 'Pharmaceutical Distributor', 1
WHERE NOT EXISTS (SELECT 1 FROM `suppliers` WHERE `id` = 1);

DROP TEMPORARY TABLE IF EXISTS `seed_catalog_20`;

CREATE TEMPORARY TABLE `seed_catalog_20` (
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

INSERT INTO `seed_catalog_20` VALUES
(1,'Dolfenal','Mefenamic Acid',500,11,18,20,'Box','Tablet',1,10,'pcs',20.00,80),
(2,'Lagundi Syrup','Lagundi Leaf Extract',600,15,23,60,'Bottle','Syrup',3,5,'mL',90.00,50),
(3,'Carbocisteine Syrup','Carbocisteine',250,15,18,60,'Bottle','Syrup',3,5,'mL',75.00,50),
(4,'Cetirizine Syrup','Cetirizine',5,15,18,60,'Bottle','Syrup',3,5,'mL',65.00,50),
(5,'Omeprazole','Omeprazole',20,12,17,14,'Box','Capsule',2,14,'pcs',22.00,60),
(6,'Amlodipine','Amlodipine Besylate',10,11,17,30,'Box','Tablet',1,30,'pcs',7.00,80),
(7,'Vitamin C Chewable','Ascorbic Acid',500,11,20,30,'Bottle','Chewable Tablet',20,30,'pcs',30.00,60),
(8,'Zinc Plus C','Zinc + Ascorbic Acid',500,11,20,30,'Bottle','Tablet',1,30,'pcs',45.00,60),
(9,'Calcium D3','Calcium Carbonate + Vitamin D3',600,11,20,30,'Bottle','Tablet',1,30,'pcs',55.00,50),
(10,'ORS Sachet','Oral Rehydration Salts',1,14,24,1,'Sachet','Powder',9,1,'sachet',6.00,100),
(11,'Povidone Iodine 10%','Povidone Iodine',10,15,21,60,'Bottle','Solution',11,60,'mL',80.00,40),
(12,'Hydrogen Peroxide 3%','Hydrogen Peroxide',3,15,21,120,'Bottle','Solution',11,120,'mL',28.00,40),
(13,'Cotton Roll','Absorbent Cotton',1,10,19,1,'Roll',NULL,NULL,1,'roll',35.00,50),
(14,'Sterile Gauze','Sterile Gauze Pad',1,20,19,25,'Pack',NULL,NULL,25,'pcs',60.00,40),
(15,'Crepe Bandage','Elastic Crepe Bandage',1,10,19,1,'Roll',NULL,NULL,1,'roll',50.00,40),
(16,'Digital Thermometer','Digital Clinical Thermometer',1,19,22,1,'Box',NULL,NULL,1,'pc',150.00,25),
(17,'Baby Shampoo','Baby Shampoo',200,15,26,1,'Bottle',NULL,NULL,200,'mL',110.00,30),
(18,'Baby Lotion','Baby Lotion',200,15,26,1,'Bottle',NULL,NULL,200,'mL',135.00,30),
(19,'Baby Diaper Medium','Disposable Baby Diaper',1,20,26,24,'Pack',NULL,NULL,24,'pcs',260.00,25),
(20,'Infant Formula','Infant Formula',800,10,26,1,'Can',NULL,NULL,800,'g',850.00,20);

INSERT INTO `products`
(`id`, `branded_name`, `generic_name`, `strength`, `measurement_id`, `barcode`, `category_id`, `classification_id`, `units_per_package`, `imageproduct`, `is_basic_necessities`, `package_type`, `dosage_form`, `dosage_form_id`, `strength_per_quantity`, `strength_per_quantity_unit`, `is_hidden`)
SELECT
    10100 + `seed`.`n`, `seed`.`brand`, `seed`.`generic_name`,
    CASE WHEN `seed`.`strength` <= 0 THEN 1 ELSE `seed`.`strength` END,
    COALESCE(`seed`.`measurement_id`, 15),
    CONCAT('299901', LPAD(`seed`.`n`, 7, '0')),
    `seed`.`category_id`, NULL, 1,
    '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, `seed`.`package_type`,
    COALESCE(`seed`.`dosage_form`, 'Powder'), COALESCE(`seed`.`dosage_form_id`, 9),
    COALESCE(`seed`.`strength_per_quantity`, COALESCE(`seed`.`units_per_package`, 1)),
    COALESCE(`seed`.`strength_per_quantity_unit`, 'pcs'), 0
FROM `seed_catalog_20` AS `seed`
WHERE NOT EXISTS (
    SELECT 1 FROM `products` AS `existing`
    WHERE `existing`.`id` = 10100 + `seed`.`n`
);

INSERT INTO `inventory`
(`product_id`, `supplier_id`, `batch_number`, `date_received`, `expiry_date`, `purchase_cost`, `markup`, `sale_price`, `received_quantity`, `current_quantity`)
SELECT
    `product`.`id`, 1, CONCAT('SEED20-BATCH-', LPAD(`seed`.`n`, 3, '0')), CURRENT_DATE,
    DATE_ADD(CURRENT_DATE, INTERVAL (180 + (`seed`.`n` * 7)) DAY),
    `seed`.`purchase_cost`, 20.00, ROUND(`seed`.`purchase_cost` * 1.20, 2),
    `seed`.`received_quantity`, `seed`.`received_quantity`
FROM `seed_catalog_20` AS `seed`
JOIN `products` AS `product`
    ON `product`.`id` = 10100 + `seed`.`n`
    AND `product`.`barcode` = CONCAT('299901', LPAD(`seed`.`n`, 7, '0'))
WHERE NOT EXISTS (
    SELECT 1 FROM `inventory` AS `existing`
    WHERE `existing`.`batch_number` = CONCAT('SEED20-BATCH-', LPAD(`seed`.`n`, 3, '0'))
);

COMMIT;

SELECT COUNT(*) AS `seed_products_added`
FROM `products`
WHERE `id` BETWEEN 10101 AND 10120
  AND `barcode` LIKE '299901%';

SELECT COUNT(*) AS `seed_inventory_batches_added`
FROM `inventory`
WHERE `batch_number` LIKE 'SEED20-BATCH-%';