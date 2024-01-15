<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
// Password protect this content
require_once('protect-this.php');
/* $host = 'localhost';
$username = 'root';
$pass = '';
$db = 'pettown'; */

$host = 'localhost';
$username = 'cileyaco_pettownshop';
$pass = '7QA{d#ue2+}3';
$db = 'cileyaco_pettownshop';

$conn = mysqli_connect($host, $username, $pass, $db);
$itemId = isset($_GET['id']) ? $_GET['id'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
// echo $itemId.'<br>';
// var_dump(isset($_GET['id']));
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
?>

<html>
<head>
<style>
table, th, td {
	border: 1px solid black;
}
</style>
</head>
<body>
	<form method="get" action="<?php $_SERVER['PHP_SELF']?>">
		<input type="text" value="<?=$itemId;?>" name="id"> <input type="date"
			value="<?=$date?>" name="date"> <input type="submit" value="SUBMIT">
	</form>
	<hr>
	<hr>
<?php if (isset($_GET) && $_GET['id'] !="" && $_GET['date']!=""){?>

	<table>
		<thead>
			<tr bgcolor="#FF0000" align="center" style="color: white">
				<td width="100px">ID</td>
				<td width="200px">Branch</td>
				<td width="100px">In/Out</td>
				<td width="300px">Type</td>
				<td width="300px">Date</td>
			</tr>
		</thead>

		<tbody>
		<?php

$sql = "SELECT
          a.*,
          b.name
        FROM
          inventories a
          LEFT OUTER JOIN locations b
            ON a.`location_id` = b.`id`
        WHERE a.`item_id` = $itemId
          AND DATE (a.`updated_at`) > '$date'
        ORDER BY updated_at DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        ?>
		       <tr <?php if ($row['remarks']=='Manual Edit of Category'){echo 'bgcolor="lightblue"';}?>>
				<td align="center"><?=$row['item_id']?></td>
				<td align="center"><?=$row['name']?></td>
				<td align="center"><?=$row['in_out_qty']?></td>
				<td align="center"><?=$row['remarks']?></td>
				<td align="center"><?=date('Y-m-d', strtotime($row['updated_at']));?></td>
			</tr>
		    <?php
    
}
}
$conn->close();
?>


		</tbody>
	</table>
<?php }?>
</body>
</html>

<?php

?>