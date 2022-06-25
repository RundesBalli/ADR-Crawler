<?php
/**
 * config.php
 * 
 * SQL credentials and connection, cURL variables, timezone
 */

/**
 * MySQL-Database
 * @param string Hostname
 * @param string Username
 * @param string Password
 * @param string Database
 */
$dbl = mysqli_connect(
  "localhost",
  "",
  "",
  "adr"
) OR DIE(MYSQLI_ERROR($dbl));
mysqli_set_charset($dbl, "utf8") OR DIE(MYSQLI_ERROR($dbl));

/**
 * cURL Einstellungen
 * @var string Interface with which cURL should establish the connection, e.g. eth0 (sudo ifconfig).
 * @var string The UserAgent with which the request should be sent.
 */
$cURL['bindTo'] = "eth0";
$cURL['userAgent'] = "";

/**
 * Timezone
 * @param string Timezone
 */
date_default_timezone_set("Europe/Berlin");
?>
