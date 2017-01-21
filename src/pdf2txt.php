<?php

require 'vendor/autoload.php';
require 'app/PdfDirectoryParser.php';

if(count($argv) > 1 && is_dir($argv[1])) {
    $parser = new PdfDirectoryParser($argv[1], array_key_exists(2,$argv) && $argv[2] == '--log');
    $parser->run();

} else {
    echo 'Empty arguments. Enter pdf directory path.' . PHP_EOL;
}