<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/vendor/autoload.php';

$HashIds = new \Hashids\Hashids( CONFIG_HASHIDS_SALT, CONFIG_HASHIDS_LENGTH );

$Database = @new PDO(
	CONFIG_PDO_DSN,
	CONFIG_PDO_USER,
	CONFIG_PDO_PASS,
	[
		PDO::ATTR_TIMEOUT            => 1,
		PDO::ATTR_EMULATE_PREPARES   => false,
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]
);
