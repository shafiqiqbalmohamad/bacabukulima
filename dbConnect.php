<?php

$db = new mysqli('localhost', 'root', '', 'bacabuku_db');

if ($db->connect_errno) {
    printf("Connect failed: %s\n", $db->connect_error);
    exit();
}
