<?php
require_once 'db_imfo.php';


$mysqli = new mysqli($db["host"],$db["user"],$db["pass"]);
	if ($mysqli->connect_errno) {
	print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
	exit();
}


$mysqli->select_db("2014");



$query = "SELECT detail FROM gian";

if ($result = $mysqli->query($query)) {

    /* 連想配列を取得します */
    while ($row = $result->fetch_assoc()) {
        printf ("%s\n", $row["detail"]);
    }
}



echo "<br>";
print("道路についてがクリックされたら");
echo "<br>";

print("道路について発言した議員は");
echo "<br>";


$query = "SELECT name from person_giin where ID IN(SELECT person_g_id from hatugen where gian_ID IN (select ID from gian where detail = 'douronituite'))";

if ($result = $mysqli->query($query)) {

    /* 連想配列を取得します */
    while ($row = $result->fetch_assoc()) {
        printf ("%s\n", $row["name"]);
    }
}

<form action="index1.php" method="post">
  <input type="text" name="message">
  <input type="submit">
</form>

if(!empty($_POST['message']))
{
print($_POST['message']);
}









$row = $result->fetch_array(MYSQLI_NUM);
printf ("%s %s\n", $row[0], $row[1]);



?>