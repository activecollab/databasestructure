# Create `writers` table
CREATE TABLE IF NOT EXISTS `writers` (
    `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(191) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Create `writers_books` table
CREATE TABLE IF NOT EXISTS `books_writers` (
    `writer_id` INT UNSIGNED NOT NULL DEFAULT '0',
    `book_id` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`writer_id`, `book_id`),
    INDEX `book_id` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Create `books` table
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(191) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Create `books_writers` table
CREATE TABLE IF NOT EXISTS `books_writers` (
    `book_id` INT UNSIGNED NOT NULL DEFAULT '0',
    `writer_id` INT UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`book_id`, `writer_id`),
    INDEX `writer_id` (`writer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Create `has_and_belongs_to_many_fd3387dbe3c2d25f9b758831ca235bea` constraint
ALTER TABLE `books_writers`
    ADD CONSTRAINT `has_and_belongs_to_many_fd3387dbe3c2d25f9b758831ca235bea`
    FOREIGN KEY (writer_id) REFERENCES `writers`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE;



# Create `has_and_belongs_to_many_b05370dbc0a6deb4212075423c2163f9` constraint
ALTER TABLE `books_writers`
    ADD CONSTRAINT `has_and_belongs_to_many_b05370dbc0a6deb4212075423c2163f9`
    FOREIGN KEY (book_id) REFERENCES `books`(`id`)
    ON UPDATE CASCADE ON DELETE CASCADE;