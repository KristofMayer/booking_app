<?php
// /public/logout.php

// Optionally, if you're storing the JWT in a cookie or session, clear it here.
// For example, if you stored it in a cookie:
// setcookie("token", "", time()-3600, "/");

// For this simple example, simply redirect the user to the login page.
header("Location: login.php");
exit;
