<?php
session_start();
session_destroy(); // Clear all session data
header("Location: index.php"); // Redirect to homepage
exit();