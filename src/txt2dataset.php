<?php

require_once __DIR__ . '/vendor/autoload.php';
require 'app/Dataseter.php';

$cmd = new \Commando\Command();

$cmd->option('pfolder')
    ->require()
    ->must(function($value){
        return is_dir($value)  && !is_null($value);
    })
    ->describedAs('Enter txt directory path.');

$cmd->option('dfolder')
    ->require()
    ->must(function($value){
        return is_dir($value)  && !is_null($value);
    })
    ->describedAs('Enter dataset directory path.');

$cmd->option('order')
    ->require()
    ->must(function($value){
        return in_array($value, array("chrono", "rev-chrono", "bi-dir", "random"));
    })
    ->describedAs('Enter order. Can be "chrono", "rev-chrono", "bi-dir", "random"');

$cmd->option('incrsize')
    ->require()
    ->must(function($value){
        return filter_var($value, FILTER_VALIDATE_INT) > 0;
    })
    ->describedAs('Enter order. Can be "chrono", "rev-chrono", "bi-dir", "random"');

if(isset($cmd['pfolder']) && isset($cmd['dfolder']) && isset($cmd['order']) && isset($cmd['incrsize'])) {
    $datasets = new Dataseter($cmd['pfolder'], $cmd['dfolder'] , $cmd['order'], $cmd['incrsize']);
    $datasets->run();
}