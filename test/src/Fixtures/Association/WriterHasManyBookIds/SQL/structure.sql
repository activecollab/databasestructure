# Create `writers` table
CREATE TABLE IF NOT EXISTS `writers` (
    `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(191) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Create `books` table
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    `writer_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(191) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    INDEX `writer_id` (`writer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Create `belongs_to_7bc2c7ad6efe48090372672f57cf2fa1` constraint
ALTER TABLE `books`
    ADD CONSTRAINT `belongs_to_7bc2c7ad6efe48090372672f57cf2fa1`
    FOREIGN KEY (`writer_id`) REFERENCES `writers`(`id`)
    ON UPDATE SET NULL ON DELETE SET NULL;