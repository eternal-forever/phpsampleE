<?php
try {
    $dsn = 'mysql:host=db;dbname=bbs;charset=utf8';
    $db = new PDO($dsn, 'testuser', 'testpass');
    echo "接続に成功しました";
} catch (PDOException $e) {
    echo "接続に失敗しました";
    echo $e->getMessage();
    exit();
}
?>
<?php
// $mname = 'test';
// $email = 'test@abc.com';
// $motopass = '12345';
// $pass = password_hash($motopass,PASSWORD_DEFAULT);   
// try {
//     $statement = $db->prepare('insert into members (mname,email,pass,created) values(?,?,?,now());');
//     $statement->bindParam(1, $mname,PDO::PARAM_STR);
//     $statement->bindParam(2, $email,PDO::PARAM_STR);
//     $statement->bindParam(3, $pass,PDO::PARAM_STR);
//     $statement->execute();
//     echo "Insert 成功";
// } catch(PDOException $e) {
//     echo "Insert 失敗";
// }
// $statement = null;
// $db = null;
?>
<?php
// $mid = 3;
// try {
//     $statement = $db->prepare('select * from members;');
//     $statement->execute();
//     $record = $statement->fetch(PDO::FETCH_COLUMN);
//     echo "SELECT 成功";
// } catch(PDOException $e) {
//     echo "SELECT 失敗";
//     exit();
// }

// foreach($record as $rec) {
//     echo sprintf("<br>mid=%d mname=%s pass=%s",$rec[0],$rec[1],$rec[3]);
// }

// $statement = null;
// $db = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
//user1
$mname = 'user1';
$email = 'user1@abc.com';
$motopass = '12345678';
$pass = password_hash($motopass,PASSWORD_DEFAULT);   
$image = 'noimage.jpg';
try {
    $statement = $db->prepare('insert into members (mname,email,pass,picture,created) values(?,?,?,?,now());');
    $statement->bindParam(1, $mname,PDO::PARAM_STR);
    $statement->bindParam(2, $email,PDO::PARAM_STR);
    $statement->bindParam(3, $pass,PDO::PARAM_STR);
    $statement->bindParam(4, $image,PDO::PARAM_STR);
    $statement->execute();
    echo "Insert 成功";
} catch(PDOException $e) {
    echo "Insert 失敗";
}
//user2
$mname = 'user2';
$email = 'user2@abc.com';
$motopass = '12345678';
$pass = password_hash($motopass,PASSWORD_DEFAULT);   
try {
    $statement = $db->prepare('insert into members (mname,email,pass,picture,created) values(?,?,?,?,now());');
    $statement->bindParam(1, $mname,PDO::PARAM_STR);
    $statement->bindParam(2, $email,PDO::PARAM_STR);
    $statement->bindParam(3, $pass,PDO::PARAM_STR);
    $statement->bindParam(4, $image,PDO::PARAM_STR);
    $statement->execute();
    echo "Insert 成功";
} catch(PDOException $e) {
    echo "Insert 失敗";
}
//user3
$mname = 'user3';
$email = 'user3@abc.com';
$motopass = '12345678';
$pass = password_hash($motopass,PASSWORD_DEFAULT);   
try {
    $statement = $db->prepare('insert into members (mname,email,pass,picture,created) values(?,?,?,?,now());');
    $statement->bindParam(1, $mname,PDO::PARAM_STR);
    $statement->bindParam(2, $email,PDO::PARAM_STR);
    $statement->bindParam(3, $pass,PDO::PARAM_STR);
    $statement->bindParam(4, $image,PDO::PARAM_STR);
    $statement->execute();
    echo "Insert 成功";
} catch(PDOException $e) {
    echo "Insert 失敗";
}
$statement = null;
$db = null;
?>
</body>
</html>