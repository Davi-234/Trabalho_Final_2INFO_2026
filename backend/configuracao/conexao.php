<?php
require_once __DIR__ . '/ambiente.php';
carregarAmbiente();

$servername = getenv('MYSQL_HOST') ?: 'localhost';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';
$dbname = getenv('MYSQL_DATABASE') ?: 'dev_share';
$portaConfigurada = getenv('MYSQL_PORT');
$porta = $portaConfigurada === false || $portaConfigurada === ''
    ? 3306
    : filter_var($portaConfigurada, FILTER_VALIDATE_INT);

if ($porta === false || $porta < 1 || $porta > 65535) {
    throw new RuntimeException('MYSQL_PORT deve ser uma porta válida entre 1 e 65535.');
}

$conn = mysqli_connect($servername, $username, $password, $dbname, $porta);

if ($conn === false) {
    throw new RuntimeException('Não foi possível conectar ao banco de dados.');
}

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    throw new RuntimeException('Não foi possível configurar a codificação do banco de dados.');
}
?>