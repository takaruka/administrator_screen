<?php
    session_start();

    //セッションが入ってなければログイン画面に戻る
    if(empty($_SESSION)){
        header('Location: login.php');
        exit;  
    }

    //var_dump($_SESSION);

    echo "こんにちは".$_SESSION["name"]."さん";

    //ログアウトボタンを押したら
    if(!empty($_POST['logout'])){
        //ログアウト処理
        $_SESSION=array();

        if(ini_get("session_use_cookies")){
            $params=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,
                $params["path"],$params["domain"],
                $params["secure"],$params["httponly"]
            );
        }

        session_destroy();

        //Cookie情報も削除
        setcookie('Email','',time()-3600);

        //ログイン画面へ偏移
        header('Location: login.php');
        exit;  
    }
?>

<!DOCTYPE html>
<html>
    <form action="" method="post">
        <input type="submit" name="logout" value="ログアウト">
    </form>
    </html>

    <h1>申込者一覧</h1>

    <table border="1">
    <tr><th>ID</th><th>Name</th><th>Email</th></tr>

    <?php
        require_once('db.php');

        //dbセット
        $dbclass = new db();
        $db=$dbclass->db_set("mysql:dbname=test;host=localhost;charaset=utf8","root","");

        //SQLセット
        $sql="SELECT * FROM contact_form";
        $stmt=$db->prepare($sql);

        //SQL実行
        $stmt->execute();

        $results=$stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($results as $result):   
    ?>

        <tr>
            <td><?= $result['id']; ?></td>
            <td><?= $result['name']; ?></td>
            <td><?= $result['mail']; ?></td>
            <!-- 更新 -->
            <td>
                <form action="edit.php" method="post">
                    <input type="submit" value="更新">
                    <input type="hidden" name="correction" value="<?=$result['id']?>">
                    <input type="hidden" name="name" value="<?=$result['name']?>">
                    <input type="hidden" name="mail" value="<?=$result['mail']?>">
                </form>
            </td>
            <!-- 削除 -->
            <td>
                <form action="delete.php" method="post" onsubmit="return submitChk()">
                    <input type="submit" value="削除">
                    <input type="hidden" name="delete" value="<?=$result['id']?>">
                </form>
            </td>
      </tr>
    <?php endforeach; ?>

    </table>

</html>

<script>
    //削除ボタンが押されたら
    function submitChk(){
        var flag = confirm ( "削除してもよろしいですか？");
        return flag;
    }
</script>