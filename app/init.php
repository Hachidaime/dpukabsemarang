<?php

// * Call Configuration file
require_once 'config/Config.php';
require_once 'config/Local.php';

// * Call Library
include_once("libs/adodb5/adodb.inc.php"); // ? ADODB
include_once('libs/smarty/Smarty.class.php'); // ? Smarty Template Engine
include_once('libs/RandomStringGenerator.php'); // ? Random String Generator
include_once('libs/dompdf/autoload.inc.php'); // ? Random String Generator

require_once DOC_ROOT . "vendor/autoload.php";

// * Call Core
include_once("core/Controller.php");
include_once("core/App.php");
include_once("core/Database.php");
include_once("core/Functions.php");
include_once("core/Auth.php");

// * Call Global Controller
require_once("controllers/FileHandler.php");

// TODO: Connect to Database
$db = &NewADOConnection('mysqli');
$db->Connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_DB) or die("Failed to connect database");
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; // ? Force ADODB return ASSOC array
$db->debug = false; // ? Set DB Debug Mode

// TODO: Smarty Template Engine Initiation
$smarty = new Smarty;
$smarty->compile_check = true;
$smarty->setTemplateDir(DOC_ROOT . 'app/views')->setCompileDir(DOC_ROOT . 'app/views_c');

// TODO: Call Program
$app = new App;
