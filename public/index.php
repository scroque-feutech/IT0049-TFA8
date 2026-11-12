<?php
use Illuminate\Http\Request;
define('LARAVEL_START',microtime(true));
$base=is_file(__DIR__.'/../vendor/autoload.php') ? dirname(__DIR__) : __DIR__.'/_app';
if (file_exists($maintenance=$base.'/storage/framework/maintenance.php')) require $maintenance;
require $base.'/vendor/autoload.php';
$app=require_once $base.'/bootstrap/app.php';
$app->usePublicPath(__DIR__);
$app->handleRequest(Request::capture());
