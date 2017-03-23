<?php
// #########################################################
// #                     PHP Signing                       #
// #########################################################
// Sample key.  Replace with one used for CSR generation
$KEY = 'private-key.pem';
//$PASS = 'S3cur3P@ssw0rd'; //Comment out/delete if the private key is not password protected

$req = $_GET['request']; //GET method
//$req = $_POST['request']; //POST method
//$privateKey = openssl_get_privatekey(file_get_contents($KEY), $PASS); //use syntax below if file is not password protected
$privateKey = openssl_get_privatekey(file_get_contents($KEY));

$signature = null;
openssl_sign($req, $signature, $privateKey);

if ($signature) {
  header("Content-type: text/plain");
  echo base64_encode($signature);
  exit(0);
}

echo '<h1>Error signing message</h1>';
exit(1);
?>