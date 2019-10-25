<?php
// Zip Code Updater
// Metatheria, LLC. Alex Clark 2019. GPLv3


$ftp_server = 'ftp.zip-codes.com';
$ftp_user_name = '';
$ftp_user_pass = ''; 
$dbname = '';
$dbusername = '';
$dbpassword = '';


$local_file = '/tmp/zipcodes.zip';
$server_file = '/ZIP-BUSINESS/zip-codes-database-DELUXE-BUSINESS-csv.zip';

$handle = fopen($local_file, 'w');


$conn_id = ftp_connect($ftp_server);
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

ftp_pasv($conn_id, true);

// try to download $server_file and save to $local_file
if (ftp_fget($conn_id, $handle, $server_file, FTP_BINARY, 0)) {
    echo "Successfully written to $local_file\n";
} else {
    echo "There was a problem\n";
    print_r(error_get_last());
}

// close the connection
ftp_close($conn_id);


$zip = new ZipArchive;

if ($zip->open('/tmp/zipcodes.zip') === TRUE) {
  $zip->extractTo('/tmp/', array('zip-codes-database-DELUXE-BUSINESS.csv'));
  $zip->close();
  echo 'ok';
} else {
  echo 'failed';
}


$link = mysqli_connect("localhost", $dbusername, $dbpassword, $dbname);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
mysqli_query($link, "truncate table zip_codes");

$csv_file = fopen('/tmp/zip-codes-database-DELUXE-BUSINESS.csv', 'r');
while (($line = fgetcsv($csv_file)) !== FALSE) {
  if ($line[1] == 'P') {
    // city, state, zip, areacode, county
    $safe_city = mysqli_real_escape_string($link, $line[27]);
    $safe_state = mysqli_real_escape_string($link, $line[22]);
    $safe_zip = mysqli_real_escape_string($link, $line[0]);
    $safe_areacode = mysqli_real_escape_string($link, substr($line[26], 0, 3));
    $safe_county = mysqli_real_escape_string($link, $line[29]);
    $sql = "insert into `zip_codes` (city, state, zip, area_code, county) values (";
    $sql .= "'" . $safe_city . "','" . $safe_state . "','" . $safe_zip . "','" . $safe_areacode . "','" . $safe_county . "'";
    $sql .= ")"; 
    echo $sql . "\n";
    mysqli_query($link, $sql) or die(mysqli_error($link));
  }
}


fclose($csv_file);
mysqli_close($link);

shell_exec("rm /tmp/zip-codes-database-DELUXE-BUSINESS.csv");
shell_exec("rm /tmp/zipcodes.zip");
?>
