<?php
// FILE: admin/logout.php
require_once '../config/database.php';
session_destroy();
redirect(BASE_URL . '/admin/login.php');
