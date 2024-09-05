<?php
require_once '../src/Router.php';
require_once '../src/Controller/LeagueController.php';


$router = new Router();



$router->get('/api/table', 'LeagueController@getTable');
$router->get('/api/play', 'LeagueController@playNextRound');
$router->get('/api/reset', 'LeagueController@resetLeague');

$router->run();
