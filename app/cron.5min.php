<?php 
include('noserve/config.inc');
include('webroot/code/lib/functions.inc');
include('webroot/code/lib/common.inc');
include('webroot/code/lib/bitcoin.inc');

// should be run every 5 min
$result=array();
db_connect();

BC_process_AddFunds();
BC_process_Merch();


?>