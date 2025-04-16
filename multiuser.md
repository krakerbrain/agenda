<!-- Agrega user_id como campo nullable (como ya lo habías planteado): -->

ALTER TABLE `company_schedules`
ADD COLUMN `user_id` BIGINT(20) NULL AFTER `company_id`,
ADD CONSTRAINT `fk_user_schedule` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

<!-- Relación entre tablas:
Unimos company_schedules (horarios) con users mediante company_id.
Filtramos solo usuarios con rol de administrador (role_id = [ID_DEL_ROL_ADMIN]).
Actualización:
Asignamos el id del admin como user_id en los horarios donde user_id es NULL. -->

UPDATE `company_schedules` cs
JOIN `users` u ON cs.`company_id` = u.`company_id` AND u.`role_id` = 2
SET cs.`user_id` = u.`id`
WHERE cs.`user_id` IS NULL;

<!-- se cra tabla user_services -->

CREATE TABLE `user_services` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`user_id` BIGINT(20) NOT NULL,
`service_id` INT(11) NOT NULL,
`available_days` VARCHAR(50) NOT NULL DEFAULT '1,2,3,4,5,6,7' COMMENT 'Días disponibles (1=Lunes...)',
`is_active` TINYINT(1) DEFAULT 1,
PRIMARY KEY (`id`),
FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE,
UNIQUE KEY `unique_user_service` (`user_id`, `service_id`) -- Evita duplicados
);

<!-- se llena la tabla user_services -->

INSERT INTO `user_services` (`user_id`, `service_id`, `available_days`, `is_active`)
SELECT
u.`id` AS `user_id`,
s.`id` AS `service_id`,
s.`available_days`, -- Hereda los días definidos en services
1 AS `is_active` -- Por defecto, activo
FROM
`users` u
CROSS JOIN
`services` s
WHERE
u.`company_id` = s.`company_id` -- Solo usuarios y servicios de la misma compañía
AND u.`role_id` = 2 -- Solo usuarios normales (ajusta el role_id según tu esquema)
AND NOT EXISTS (
SELECT 1 FROM `user_services` us
WHERE us.`user_id` = u.`id` AND us.`service_id` = s.`id`
);

-- actualizamos appointment para que created_At no tenga null o valores en 0
UPDATE `appointments`
SET `created_at` = '2023-01-01 00:00:00'
WHERE `created_at` = '0000-00-00' OR `created_at` IS NULL;

-- Paso 1: Agregar la columna user_id permitiendo valores nulos temporalmente
ALTER TABLE `appointments`
ADD COLUMN `user_id` BIGINT(20) NULL AFTER `company_id`;

-- Paso 2: Asignar usuarios con role_id = 2 de la misma compañía
UPDATE `appointments` a
JOIN (
SELECT u.id, u.company_id
FROM `users` u
WHERE u.role_id = 2
) u ON a.company_id = u.company_id
SET a.user_id = u.id
WHERE a.user_id IS NULL;

-- Paso 3: Para compañías que no tengan usuarios con role_id = 2, asignar cualquier usuario de la compañía
UPDATE `appointments` a
JOIN (
SELECT u.id, u.company_id
FROM `users` u
WHERE u.company_id = a.company_id
LIMIT 1
) u ON 1=1
SET a.user_id = u.id
WHERE a.user_id IS NULL;

-- Paso 4: Hacer que la columna sea NOT NULL
ALTER TABLE `appointments`
MODIFY COLUMN `user_id` BIGINT(20) NOT NULL;

-- Paso 5: Agregar la restricción de clave foránea
ALTER TABLE `appointments`
ADD CONSTRAINT `fk_appointments_user`
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

hay que cmabiar la clave foranea de company_integrations para que sea on cascade

se agrega select de usuarios para el admin quien al hacer change permitira que se muestren los distintos horarios y pueda modificarlos
se haa lo mismo con los servicios
luego hacer que usarios normales no puedan modificar sus horarios ni servicios (definir si puede verlos o se bloquean)
