<?php
//https://developers.google.com/resources/api-libraries/documentation/sheets/v4/php/latest/
require_once __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('America/Santiago');

echo date("c")."[INFO]Process started".PHP_EOL;

if(file_exists(__DIR__.DIRECTORY_SEPARATOR."config.ini.php")){
    $configs = parse_ini_file(__DIR__.DIRECTORY_SEPARATOR."config.ini.php", TRUE);
}else{
    die(date("c")."[ERROR]Can't find parameter file: ".__DIR__.DIRECTORY_SEPARATOR."config.ini.php".PHP_EOL);
}

$scopes = array(constant("Google_Service_Sheets::".$configs['GOOGLE_API']['spreadsheet_scope']));

$client = new Google_Client();
$client->setAuthConfig($configs['GOOGLE_API']['json_config_file']);
$client->setScopes($scopes);

$service = new Google_Service_Sheets($client);

//Read sheet example
//$response = $service->spreadsheets_values->get($spreadsheetId, $range);
//$values = $response->getValues();

try{
  echo date("c")."[INFO]Connecting to DB:".$configs['DB']['user']."@".$configs['DB']['host'].PHP_EOL;
  $pdo = new PDO("mysql:dbname=".$configs['DB']['schema'].";host=".$configs['DB']['host'].";charset=".$configs['DB']['charset'], $configs['DB']['user'], $configs['DB']['password']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e){
  echo date("c")."[ERROR]Error connecting to the DB: " . $e->getMessage() . PHP_EOL;
  die();
}

$sql = file_get_contents($configs['MISC']['sql_file']);


try{
  echo date("c")."[INFO]Starting query".PHP_EOL;
  $query = $pdo->query($sql, PDO::FETCH_ASSOC);
} catch (PDOException $e){
  echo date("c")."[ERROR]Error executing the query: " . $e->getMessage() . PHP_EOL;
  die();
}

echo date("c")."[INFO]Query results count:" . $query->rowCount() . PHP_EOL;

$results = array();

if($query->rowCount() > 0){
  $fetchAll = $query->fetchAll();
  foreach($fetchAll as $row){
    $rowTemp = array();
    foreach($row as $column){
      if(empty($column)){ //checks if the column is null, Google API throws errors if sending null values.
        $column = "";
      }
      $rowTemp[] = $column;
    }
    $results[] = $rowTemp;
  }
}

//print_r($results); //DEBUG

$body = new Google_Service_Sheets_ValueRange([
  'values' => $results
]);
$params = [
  'valueInputOption' => $configs['GOOGLE_API']['spreadsheet_input_option']
];

echo date("c")."[INFO]Spreadsheet ID targeted:".$configs['GOOGLE_API']['spreadsheet_id'].PHP_EOL;

echo date("c")."[INFO]Clearing sheet range:".$configs['GOOGLE_API']['spreadsheet_range'].PHP_EOL;

$clearRequest = new Google_Service_Sheets_BatchClearValuesRequest();
$clearRequest->setRanges($configs['GOOGLE_API']['spreadsheet_range']);

try {
  $service->spreadsheets_values->batchClear($configs['GOOGLE_API']['spreadsheet_id'], $clearRequest);
} catch(Google_Service_Exception $e) {
  $error = json_decode($e->getMessage(), TRUE);
  echo date("c")."[ERROR]Code:".$error['error']['code']." - Message:".$error['error']['message'].PHP_EOL;
}

echo date("c")."[INFO]Pushing array to range:".$configs['GOOGLE_API']['spreadsheet_range'].PHP_EOL;

try {
  $result = $service->spreadsheets_values->update($configs['GOOGLE_API']['spreadsheet_id'], $configs['GOOGLE_API']['spreadsheet_range'], $body, $params);  
  echo date("c")."[INFO]" . $result->getUpdatedCells() . " cells updated" .PHP_EOL;
} catch(Google_Service_Exception $e) {
  $error = json_decode($e->getMessage(), TRUE);
  echo date("c")."[ERROR]Code:".$error['error']['code']." - Message:".$error['error']['message'].PHP_EOL;
}

echo date("c")."[INFO]Process finished".PHP_EOL;
