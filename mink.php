<?php

require __DIR__.'/vendor/autoload.php';

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;

$driver = new GoutteDriver();

$session = new Session($driver);

$session->visit('http://jurassicpark.wikia.com');

var_dump($session->getStatusCode(),$session->getCurrentUrl());

//DocumentElement
$page = $session->getPage();

var_dump(substr($page->getText(), 0, 75));

