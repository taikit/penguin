<?php
require_once "../connect/mysql_account.php";
	try{
		$pdo=new PDO("mysql:host=". $ms_host . "; dbname=" . $ms_name,
		$ms_user, $ms_pass,
		array(
				PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf-8'")
			);
	}catch(PDOException $e){
		echo "ごめんwできてないから(爆)"
		die($e->getMessage());
	}



	echo "OK<br>";
	$sql="CREATE table message(
		id INT auto_increment,
		content blob not null ,
		time timestamp,
		room_id int,
		primary key(id),
		foreign key(room_id)
		references room(id)
	)";
	$result=$pdo->query($sql);
	


?>
