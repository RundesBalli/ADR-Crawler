<?php
/**
 * functions.php
 * 
 * File with functions for operating the crawler.
 */

/**
 * Defuse function for database insert
 * 
 * @param  string $defuse_string String that is to be defused in order to pass it into a DB query.
 * @param  bool   $trim          Specifies whether to remove spaces/lines at the beginning and end.
 * 
 * @return string The prepared "defused" string.
 */
function defuse($defuse_string, $trim = TRUE) {
  if($trim === TRUE) {
    $defuse_string = trim($defuse_string);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, strip_tags($defuse_string));
}

/**
 * cURL function to fetch all stations
 * 
 * @return array Array with all measuring stations.
 */
function fetchStations() {
  /**
   * Importing the configuration variables into the function.
   */
  global $cURL;

  /**
   * Initialize cURL.
   */
  $ch = curl_init();

  /**
   * Set options for the cURL connection.
   * @see https://www.php.net/manual/de/function.curl-setopt.php
   */
  $options = array(
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_USERAGENT => $cURL['userAgent'],
    CURLOPT_INTERFACE => $cURL['bindTo'],
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 30
  );

  /**
   * Set and prepare the GET parameters for the cURL connection.
   */
  $url = "https://sfws.lfrz.at/json.php";
  $params['command'] = 'getstations';
  $data = http_build_query($params, '', '&', PHP_QUERY_RFC1738);
  $options[CURLOPT_URL] = $url."?".$data;

  /**
   * Insert the prepared cURL options in the cURL handle.
   */
  curl_setopt_array($ch, $options);

  /**
   * Execute the prepared cURL operation and save the response from the API.
   * Only keep all measurement points and their measurement values.
   */
  $response = json_decode(curl_exec($ch), TRUE);

  /**
   * If an error occurred during execution, the script run will be aborted.
   */
  $errno = curl_errno($ch);
  if($errno != 0) {
    die("cURL error - errno: ".$errno);
  }

  /**
   * If the API does not return HTTP200, the script run is also aborted.
   */
  $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  if($httpCode != 200) {
    die("cURL error: HTTP-Code: ".$httpCode);
  }

  /**
   * Closing the cURL handle.
   */
  curl_close($ch);

  /**
   * Returning the API response to the crawler.
   */
  return $response;
}

/**
 * cURL function to fetch all measurements
 * 
 * @return array Array with all measuring values.
 */
function fetchMeasurements() {
  /**
   * Importing the configuration variables into the function.
   */
  global $cURL;

  /**
   * Initialize cURL.
   */
  $ch = curl_init();

  /**
   * Set options for the cURL connection.
   * @see https://www.php.net/manual/de/function.curl-setopt.php
   */
  $options = array(
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_USERAGENT => $cURL['userAgent'],
    CURLOPT_INTERFACE => $cURL['bindTo'],
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 30
  );

  /**
   * Set and prepare the GET parameters for the cURL connection.
   */
  $url = "https://sfws.lfrz.at/json.php";
  $params['command'] = 'getdata';
  $data = http_build_query($params, '', '&', PHP_QUERY_RFC1738);
  $options[CURLOPT_URL] = $url."?".$data;

  /**
   * Insert the prepared cURL options in the cURL handle.
   */
  curl_setopt_array($ch, $options);

  /**
   * Execute the prepared cURL operation and save the response from the API.
   * Only keep all measurement points and their measurement values.
   */
  $response = json_decode(curl_exec($ch), TRUE);

  /**
   * If an error occurred during execution, the script run will be aborted.
   */
  $errno = curl_errno($ch);
  if($errno != 0) {
    die("cURL error - errno: ".$errno);
  }

  /**
   * If the API does not return HTTP200, the script run is also aborted.
   */
  $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  if($httpCode != 200) {
    die("cURL error: HTTP-Code: ".$httpCode);
  }

  /**
   * Closing the cURL handle.
   */
  curl_close($ch);

  /**
   * Returning the API response to the crawler.
   */
  return $response;
}
?>
