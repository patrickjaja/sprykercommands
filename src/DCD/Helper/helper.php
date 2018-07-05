<?php

use SebastianBergmann\CodeCoverage\CodeCoverage;

require_once 'current/vendor/autoload.php';
$coverage = new CodeCoverage();

$files = glob('/tmp/cov/*');
foreach($files as $file) {
    $s = file_get_contents($file);
    $data = unserialize($s);
    $coverage->merge($data);
}

$writer = new \SebastianBergmann\CodeCoverage\Report\Html\Facade;
$writer->process($coverage, __DIR__ . '/coverage/');
