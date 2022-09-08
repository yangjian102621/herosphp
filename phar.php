<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
$config = [
    'enable' => true,
    'phar_file_output_dir' => BASE_PATH . DIRECTORY_SEPARATOR . 'build',
    'phar_filename' => 'herosphp.phar',
    'signature_algorithm' => Phar::SHA256, //set the signature algorithm for a phar and apply it. The signature algorithm must be one of Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512, or Phar::OPENSSL.
    'private_key_file' => '', // The file path for certificate or OpenSSL private key file.
    'exclude_pattern' => '#^(?!.*(LICENSE|.github|.idea|doc|docs|.git|.setting|runtime|test|test_old|tests|Tests|vendor-bin|.md))(.*)$#',
    'exclude_files' => [
        'process.php','phar.php','boot.php','Monitor.php','.php-cs-fixer.cache','.php-cs-fixer.php','client.php','LICENSE.md', 'README.md', 'web.php'
    ]
];

// 终端高亮打印红色
function printError(string $message): void
{
    printf("\033[31m\033[1m%s\033[0m\n", $message);
    exit(1);
}

if (!class_exists(Phar::class, false)) {
    printError("The 'phar' extension is required for build phar package");
}

if (ini_get('phar.readonly')) {
    printError(
        "The 'phar.readonly' is 'On', build phar must setting it 'Off' or exec with 'php -d phar.readonly=0 phar.php'"
    );
}

$phar_file_output_dir = $config['phar_file_output_dir'];
if (empty($phar_file_output_dir)) {
    printError('Please set the phar file output directory.');
}
if (!file_exists($phar_file_output_dir) && !is_dir($phar_file_output_dir)) {
    if (!mkdir($phar_file_output_dir, 0777, true)) {
        printError('Failed to create phar file output directory. Please check the permission.');
    }
}

$phar_filename = $config['phar_filename'];
if (empty($phar_filename)) {
    printError('Please set the phar filename.');
}

$phar_file = rtrim($phar_file_output_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $phar_filename;
if (file_exists($phar_file)) {
    unlink($phar_file);
}

$exclude_pattern = $config['exclude_pattern'];
$phar = new Phar($phar_file, 0, 'herosphp');
$phar->compressFiles(Phar::GZ);
$phar->startBuffering();

$signature_algorithm = $config['signature_algorithm'];
if (!in_array($signature_algorithm, [Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512, Phar::OPENSSL])) {
    throw new RuntimeException('The signature algorithm must be one of Phar::MD5, Phar::SHA1, Phar::SHA256, Phar::SHA512, or Phar::OPENSSL.');
}
if ($signature_algorithm === Phar::OPENSSL) {
    $private_key_file = $config['private_key_file'];
    if (!file_exists($private_key_file)) {
        printError("If the value of the signature algorithm is 'Phar::OPENSSL', you must set the private key file.");
    }
    $private = openssl_get_privatekey(file_get_contents($private_key_file));
    $pKey = '';
    openssl_pkey_export($private, $pKey);
    $phar->setSignatureAlgorithm($signature_algorithm, $pKey);
} else {
    $phar->setSignatureAlgorithm($signature_algorithm);
}

$phar->buildFromDirectory(BASE_PATH, $exclude_pattern);

$exclude_files = $config['exclude_files'];

foreach ($exclude_files as $file) {
    if ($phar->offsetExists($file)) {
        $phar->delete($file);
    }
}

echo 'Files collect complete, begin add file to Phar.' . PHP_EOL;

$phar->setStub("#!/usr/bin/env php
<?php
define('IN_PHAR', true);
Phar::mapPhar('herosphp');
require 'phar://herosphp/herosphp';
__HALT_COMPILER();
");

echo 'Write requests to the Phar archive, save changes to disk.' . PHP_EOL;

$phar->stopBuffering();
unset($phar);
