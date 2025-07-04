<?php
require_once '../configurations/config.php';
session_destroy();
header("Location:" .ROOT. "/login");