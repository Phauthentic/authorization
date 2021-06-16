<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

if (!getenv('PDO_DB_DSN')) {
    putenv('PDO_DB_DSN=sqlite::memory:');
}
