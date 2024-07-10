DELIMITER $$

CREATE PROCEDURE `validar_registro`(IN `p_nombre` VARCHAR(50), IN `p_correo` VARCHAR(50), IN `p_clave` VARCHAR(50), IN `p_clave2` VARCHAR(50), OUT `p_error` VARCHAR(255))
BEGIN
    DECLARE v_count INT;
SET NAMES utf8mb4 COLLATE utf8mb4_general_ci;
    -- Verificar si el nombre de usuario ya existe
    SELECT COUNT(*) INTO v_count FROM users WHERE name = p_nombre;
    IF v_count > 0 THEN
        SET p_error = 'Este nombre de usuario ya ha sido registrado. Intente de nuevo';
    ELSE
        -- Verificar si el correo ya existe
        SELECT COUNT(*) INTO v_count FROM users WHERE email = p_correo;
        IF v_count > 0 THEN
            SET p_error = 'Este correo ya ha sido registrado. Intente de nuevo';
        ELSE
            -- Verificar otros campos (correo válido, contraseñas iguales, etc.)
            IF p_nombre = '' OR p_correo = '' OR p_clave = '' OR p_clave2 = '' THEN
                SET p_error = 'Registro incorrecto. Debe llenar todos los campos';
            ELSEIF NOT p_correo REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$' THEN
                SET p_error = 'Formato de correo incorrecto';
            ELSEIF p_clave != p_clave2 THEN
                SET p_error = 'Las contraseñas deben ser iguales';
            ELSE
                -- Si todas las validaciones pasaron, no hay error
                SET p_error = '';
            END IF;
        END IF;
    END IF;
END$$

DELIMITER;