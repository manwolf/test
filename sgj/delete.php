<?php
$pn = $_REQUEST ['pn'];
if(!$pn){
	die('$pn is null \n');
}
$con = mysql_connect("localhost","maimall","mmall2015");
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}
 mysql_select_db("MMall", $con);
$result = mysql_query("select * from app where packageName = '".$pn."' ");
if(!$result){
	die('query fail: ' . mysql_error());
}else{
	var_dump($result);
}

while($row = mysql_fetch_array($result))
{
	print_r($row);
	echo "<tr>";
	echo "<td>" . $row['id'] . "</td>";
	echo "<td>" . $row['name'] . "</td>";
	echo "<td>" . $row['packageName'] . "</td>";
	echo "</tr>";
}
echo "</table>";
mysql_close($con);
