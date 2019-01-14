<?php
require_once 'container.php';
require_once 'Traveller.php';

$app = new Container();

$app->bind('Traveller', 'Train');
$app->bind('traveller', 'Traveller');

$traveller = $app->make('traveller');
$traveller->go('北京');