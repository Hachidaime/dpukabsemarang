<?php

// TODO: Starting Session when no session started
if (!session_id()) session_start();

// ! Showing Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// * Initiation Program
require_once 'app/init.php';
