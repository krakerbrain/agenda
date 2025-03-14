-- Se crea la tabla customers
sql
Copy
CREATE TABLE `customers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(15) DEFAULT NULL,
    `mail` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `blocked` TINYINT(1) NOT NULL DEFAULT 0,
    `notes` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
);

-- Se crea la tabla company_customers para asociar clientes a empresas
CREATE TABLE `company_customers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) NOT NULL,
    `customer_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE (`company_id`, `customer_id`), -- Evita que un cliente se asocie más de una vez a la misma empresa
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
);

CREATE TABLE `customer_incidents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `customer_id` INT(11) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `incident_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `note` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
);

-- Se agrega la columna customer_id a appointments
ALTER TABLE `appointments` 
ADD COLUMN `customer_id` INT(11) NOT NULL AFTER `company_id`;

-- Insertar clientes únicos en la tabla customers
INSERT INTO customers (name, phone, mail)
SELECT DISTINCT name, phone, mail
FROM appointments
WHERE phone IS NOT NULL OR mail IS NOT NULL;

-- Actualizar appointments con el customer_id correspondiente
UPDATE appointments a
JOIN customers c
ON a.name = c.name
AND (a.phone = c.phone OR (a.phone IS NULL AND c.phone IS NULL))
AND (a.mail = c.mail OR (a.mail IS NULL AND c.mail IS NULL))
SET a.customer_id = c.id;

    -- Se crea la clave foránea en appointments
ALTER TABLE `appointments`
ADD CONSTRAINT `fk_customer_id`
FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE;

ALTER TABLE `appointments` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;