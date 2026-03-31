<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

session_start();
session_destroy();
redirect(ADMIN_URL . 'auth/login.php');
