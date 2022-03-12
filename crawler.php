<?php
/**
 * Ambient dose rate crawler (ADR-Crawler)
 * 
 * Crawler to fetch the ambient dose rate of around 1700 measurement points in Germany.
 * 
 * @author    RundesBalli
 * @copyright 2022 RundesBalli
 * @version   1.0
 * @license   MIT-License
 * @see       https://github.com/RundesBalli/ADR-Crawler
 * 
 * @see https://odlinfo.bfs.de/ODL/EN/service/data-interface/data-interface_node.html (English)
 * @see https://odlinfo.bfs.de/ODL/DE/service/datenschnittstelle/datenschnittstelle_node.html (German)
 * 
 * Note: The data collected by this crawler falls under certain terms of use, which can be viewed at the bfs.de links above.
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
 * Executing and saving the cURL request to the API of the Federal Office for Radiation Protection of Germany.
 * 
 * @var array Records returned by the API.
 */
$response = fetchMeasurements();

/**
 * Iterate through all returned measurement points.
 */
foreach($response as $key => $val) {
  /**
   * Check if the measuring point already exists, if yes it will be updated, if not it will be created.
   */
  $result = mysqli_query($dbl, "SELECT `id` FROM `stations` WHERE `kenn`='".defuse($val['properties']['kenn'])."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;$qs++;
  if(mysqli_num_rows($result) == 0) {
    mysqli_query($dbl, "INSERT INTO `stations` (`intId`, `kenn`, `plz`, `name`, `status`, `height`) VALUES ('".defuse($val['id'])."', '".defuse($val['properties']['kenn'])."', '".defuse($val['properties']['plz'])."', '".defuse($val['properties']['name'])."', '".defuse($val['properties']['site_status'])."', '".defuse($val['properties']['height_above_sea'])."')") OR DIE(MYSQLI_ERROR($dbl));$qc++;$qi++;
    $id = mysqli_insert_id($dbl);
  } else {
    $row = mysqli_fetch_assoc($result);
    mysqli_query($dbl, "UPDATE `stations` SET `intId`='".defuse($val['id'])."', `kenn`='".defuse($val['properties']['kenn'])."', `plz`='".defuse($val['properties']['plz'])."', `name`='".defuse($val['properties']['name'])."', `status`='".defuse($val['properties']['site_status'])."', `height`='".defuse($val['properties']['height_above_sea'])."' WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));$qc++;$qu++;
    $id = $row['id'];
  }

  /**
   * Only values of active stations are stored.
   */
  if($val['properties']['site_status'] == 1) {
    /**
     * Check if the time formats are valid.
     */
    if(preg_match('/(?\'y\'\d{4})-(?\'m\'\d{2})-(?\'d\'\d{2})T(?\'h\'\d{2}):(?\'i\'\d{2}):(?\'s\'\d{2})Z/mi', $val['properties']['start_measure'], $match) === 1) {
      $startMeasure = $match['y']."-".$match['m']."-".$match['d']." ".$match['h'].":".$match['i'].":".$match['s'];
      if(preg_match('/(?\'y\'\d{4})-(?\'m\'\d{2})-(?\'d\'\d{2})T(?\'h\'\d{2}):(?\'i\'\d{2}):(?\'s\'\d{2})Z/mi', $val['properties']['end_measure'], $match) === 1) {
        $endMeasure = $match['y']."-".$match['m']."-".$match['d']." ".$match['h'].":".$match['i'].":".$match['s'];
        /**
         * Insertion the new measure value. If the measure value has not been updated, the database blocks a new insert of the same value (unique).
         */
        $qc++;
        $qi++;
        if(!mysqli_query($dbl, "INSERT INTO `entries` (`stationId`, `startMeasure`, `endMeasure`, `value`, `valueCosmic`, `valueTerrestrial`) VALUES ('".$id."', '".defuse($startMeasure)."', '".defuse($endMeasure)."', '".floatval(defuse($val['properties']['value']))."', '".floatval(defuse($val['properties']['value_cosmic']))."', '".floatval(defuse($val['properties']['value_terrestrial']))."')")) {
          if(mysqli_errno($dbl) != 1062) { // 1062 = The same entry already exists (unique).
            die(mysqli_error($dbl));
          }
        }
      }
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
