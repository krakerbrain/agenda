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

hay que cmabiar la clave foranea de company_integrations para que sea on cascade
