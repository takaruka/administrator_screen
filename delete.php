<?php
    require_once('db.php');


    //var_dump($_SESSION["delete"]); exit;

    //dbセット
    $dbclass = new db();
    $db=$dbclass->db_set("mysql:dbname=test;host=localhost;charaset=utf8","root","");

    //SQLセット
    $sql="DELETE FROM contact_form WHERE id=:id";
    $stmt=$db->prepare($sql);

    //SQLにフォーム入力値をバインド
    $stmt->bindParam(':id',$_POST["delete"],PDO::PARAM_STR);

    //SQL実行
    $stmt->execute();
    
    //メッセージ
    echo "削除しました"

?>

<!DOCTYPE html>
<html>
    <br>
    <a href="list.php">一覧画面へ</a>
</html>