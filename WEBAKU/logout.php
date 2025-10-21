<?php
session_start();
// End admin session and redirect
session_unset();
session_destroy();
header('Location: index.php');
exit;
