<?php
    session_start();
    require_once('db.php');

    //戻るボタンが押されたら
    if(isset($_POST["back"])){

        $_SESSION["backName"]=$_SESSION["Name"];
        $_SESSION["backEmail"]=$_SESSION["Email"];

        //list.phpへ遷移
        header('Location: edit.php');
        exit;  
    }

    //修正ボタンが押されたら
    if(isset($_POST["edit"])){

        //戻るボタンから引き継いだセッションを削除
        unset($_SESSION['backname'],$_SESSION['backgender'],$_SESSION['backmail'],$_SESSION['backcontents']);

        //dbセット
        $dbclass = new db();
        $db=$dbclass->db_set("mysql:dbname=test;host=localhost;charaset=utf8","root","");

        //SQLセット 
        $sql="UPDATE contact_form SET name=:Name, mail=:Email WHERE id=:id";
        $stmt=$db->prepare($sql);

        //SQLにフォーム入力値をバインド
        $stmt->bindParam(':id',$_SESSION["correction"],PDO::PARAM_STR);
        $stmt->bindParam(':Name',$_SESSION["Name"],PDO::PARAM_STR);
        $stmt->bindParam(':Email',$_SESSION["Email"],PDO::PARAM_STR);

        //SQL実行
        $stmt->execute();
        
        header('Location: list.php');
        exit;  
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="form.css">
    </head>
    <body>
        <form action="" method="post">
            <p>Name</p>
            <?= $_SESSION["Name"]; ?>
            <p>Email</p>
            <?= $_SESSION["Email"]; ?>
            <br>
            <input type="submit" name="back" value="戻る">
            <input type="submit" name="edit" value="修正">
        </form>
    </body>
</html>