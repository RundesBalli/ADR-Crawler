<?php
/**
 * Ambient dose rate crawler for austrian data (ADR-Crawler)
 * 
 * Crawler to fetch the ambient dose rate of all measuring stations in Austria.
 * 
 * @author    RundesBalli
 * @copyright 2022 RundesBalli
 * @version   1.0
 * @license   MIT-License
 * @see       https://github.com/RundesBalli/ADR-Crawler
 * 
 * @see https://sfws.lfrz.at/json.php
 */

/**
 * Capture of the start time for calculation of the script runtime.
 * 
 * @var int $startTime Time of the script start.
 */
$startTime = time();

/**
 * Import the necessary configuration and function files.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."functions.php");

/**
 * Initialize all counting variables.
 * 
 * @var int $qc Query count
 * @var int $qs Select queries
 * @var int $qi Insert queries
 * @var int $qu Update queries
 */
$qc = 0;
$qs = 0;
$qi = 0;
$qu = 0;

/**
 * Executing the cURL request to fetch all measuring stations.
 * 
 * @var array All measuring stations.
 */
$response = fetchStations();

/**
 * Iterate through all returned stations.
 */
foreach($response as $key => $val) {
  /**
   * Check if the measuring point already exists, if yes it will be updated, if not it will be created.
   */
  $identifier = intval(defuse(str_replace("AT", "", $key)));
  $result = mysqli_query($dbl, "SELECT `id` FROM `stations` WHERE `identifier`='".$identifier."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;$qs++;
  if(mysqli_num_rows($result) == 0) {
    mysqli_query($dbl, "INSERT INTO `stations` (`identifier`, `name`) VALUES ('".$identifier."', '".defuse($val['n'])."')") OR DIE(MYSQLI_ERROR($dbl));$qc++;$qi++;
  } else {
    mysqli_query($dbl, "UPDATE `stations` SET `name`='".defuse($val['n'])."' WHERE `identifier`=".$identifier." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;$qu++;
  }
}

/**
 * Executing the cURL request to fetch all measurement values.
 * 
 * @var array Records returned by the API.
 */
$response = fetchMeasurements()['values'];

/**
 * Iterate through all returned measurement values.
 */
foreach($response as $key => $val) {
  /**
   * Check if the measuring point already exists, if yes it will be updated, if not it will be created.
   */
  $identifier = intval(defuse(str_replace("AT", "", $key)));

  /**
   * Select the station.
   */
  $qc++;
  $qs++;
  $result = mysqli_query($dbl, "SELECT `id` FROM `stations` WHERE `identifier`='".$identifier."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    continue;
  }
  $row = mysqli_fetch_assoc($result);
  $stationId = $row['id'];

  /**
   * Insert the values into the database.
   */
  $qc++;
  $qi++;
  if(!mysqli_query($dbl, "INSERT INTO `entries` (`stationId`, `timestamp`, `value`) VALUES ('".$stationId."', '".date("Y-m-d H:i:s", intval($val['d']))."', '".floatval(defuse($val['v']))."')")) {
    if(mysqli_errno($dbl) != 1062) { // 1062 = The same entry already exists (unique).
      die(mysqli_error($dbl));
    }
  }
}

/**
 * Output the querycount.
 */
echo "Queries: ".$qc."; Selects: ".$qs.", Inserts: ".$qi.", Updates: ".$qu."\n";

/**
 * Calculate and output the execution time.
 */
echo "Execution Time: ".(time()-$startTime)." seconds\n";
?>
