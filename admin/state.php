<?
$server = "scan4jobs.db.9771910.hostedresource.com";
$db_user = "scan4jobs";
$db_pass = "Tech_jobs@2012";
$database = "scan4jobs";
$connect=mysql_connect($server, $db_user, $db_pass);
$db = mysql_select_db($database);
function get_cities($id)
{
	$cities='';
	$sql = 'SELECT id,name FROM cities where state_id = '.$id.' ORDER BY name ASC';
	$cities='<select name="city_id" id="city_id"><option value="" selected="selected"> - Select City-</option>';
	$result = mysql_query($sql);
	while ($rows =mysql_fetch_assoc($result))
	{
		//$cities[$row['id']] = $row['name'];
	$cities=$cities."<option value=". $rows['id'].">".  $rows['name']."</option>";
	
	}
	$cities=$cities."</select>";
	return $cities;
}
if($_REQUEST['state_id'] != ''){
$choices=$_REQUEST['state_id'];
print  get_cities($choices);
}
?>