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

//NodeElement
$header = $page->find('css', '.WikiHeader .WikiNav h2');
var_dump($header->getText());

$nav = $page->find('css', '.subnav-2');
$linkEl = $nav->find('css', 'li a');

$selectorHandler = $session->getSelectorsHandler();
$linkEl = $page->findLink('Wiki Activity');
//$page->findField('Description');
//$page->findButton('Save');
var_dump($linkEl->getAttribute('href'));

$linkEl->click();

var_dump($session->getCurrentUrl());
