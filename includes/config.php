<?php
$con = mysql_connect("localhost","cobeg_dbadmin","qweasdzxc123+_");
if (!$con) {
  	//die('Could not connect: ' . mysql_error());
    print(mysql_error());
}
mysql_select_db("cobeg_managerdb", $con);
//$con = mysql_connect("localhost","root","root");
//if (!$con)
//  {
//  	die('Could not connect: ' . mysql_error());
//  }
//mysql_select_db("event-management-db", $con);
?>