-- Se crea la tabla customers
sql
Copy
CREATE TABLE `customers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `phone` varchar(15) DEFAULT NULL,
    `mail` varchar(100) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `blocked` tinyint(1) NOT NULL DEFAULT 0,
    `notes` text DEFAULT NULL,
    `nota_bloqueo` text DEFAULT NULL,
    PRIMARY KEY (`id`)
)

-- Se crea la tabla company_customers para asociar clientes a empresas
CREATE TABLE `company_customers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) NOT NULL,
    `customer_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `company_id` (`company_id`, `customer_id`),
    KEY `customer_id` (`customer_id`),
    CONSTRAINT `company_customers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
    CONSTRAINT `company_customers_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) 

CREATE TABLE `customer_incidents` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) NOT NULL,
    `description` varchar(255) NOT NULL,
    `incident_date` timestamp NOT NULL DEFAULT current_timestamp(),
    `note` text DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `customer_id` (`customer_id`),
    CONSTRAINT `customer_incidents_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
)

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
ADD CONSTRAINT `fk_appointments_customers`
FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
ON DELETE CASCADE;
