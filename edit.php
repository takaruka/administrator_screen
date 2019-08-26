<?php
    session_start();
    require_once('db.php');
    require_once('validate.php');

    //戻るボタンが押されたら
    if(isset($_POST["back"])){
        //list.phpへ遷移
        header('Location: list.php');
        exit;  
    }


    //確認ボタンが押されたら
    if(isset($_POST["confirm"])){
        //Varidateクラスを読み込む
        $check = new Validate();

        //戻るボタンから引き継いだセッションを削除
        unset($_SESSION['backName'],$_SESSION['backEmail']);
            
        //空白チェック
        $errors['Name']= $check->empty_check($_POST['Name'],"名前");
        $errors['Email']= $check->empty_check($_POST['Email'],"メールアドレス");

        //文字数チェック
        $errors['NameMax']= $check->max_check($_POST['Name'],20,"名前");

        //メールの形式チェック
        $errors['EmailFormat']= $check->mailformat_check($_POST['Email']);

        //空白を取り除く
        $errors = array_filter($errors, 'strlen');

        //バリデーションクリアしたら
        if (empty($errors)) {
            
            $_SESSION["Name"]=$_POST["Name"];
            $_SESSION["Email"]=$_POST["Email"];

            header('Location: edit_confirm.php');
            exit;  
        }
    }

    if(!empty($_POST["correction"])){
        //セッションにIDを保管
        $_SESSION["correction"]=$_POST["correction"];
    }

    //dbセット
    $dbclass = new db();
    $db=$dbclass->db_set("mysql:dbname=test;host=localhost;charaset=utf8","root","");

    //SQLセット
    $sql="SELECT * FROM contact_form WHERE id=:id";
    $stmt=$db->prepare($sql);

    //SQLにフォーム入力値をバインド
    $stmt->bindParam(':id',$_SESSION["correction"],PDO::PARAM_STR);

    //SQL実行
    $stmt->execute();

    $results=$stmt->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($results); exit;
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="form.css">
    </head>
    <body>
        <form action="" method="post">

            
            <p class="heading">Name</p>
            <input type="text" name="Name" value="<?php if(!empty($_POST['Name'])) {echo $_POST['Name'];}elseif(!empty($_SESSION["backName"])){echo $_SESSION["backName"];}else{echo $results[0]['name'];} ?>">
            <?php 
                if(!empty($errors['Name'])){
                echo '<span class="errorMessage">'.$errors['Name'].'</span>';
                }
                if(!empty($errors['NameMax'])){
                echo '<span class="errorMessage">'.$errors['NameMax'].'</span>';
                }
            ?>
            <br>
            <p class="heading">Email</p>
            <input type="text" name="Email" value="<?php if(!empty($_POST['Email'])) {echo $_POST['Email'];}elseif(!empty($_SESSION["backEmail"])){echo $_SESSION["backEmail"];}else{echo $results[0]['mail'];} ?>">
            <?php 
                if(!empty($errors['Email'])){
                echo '<span class="errorMessage">'.$errors['Email'].'</span>';
                }
                if(!empty($errors['EmailFormat'])){
                echo '<span class="errorMessage">'.$errors['EmailFormat'].'</span>';
                }
            ?>
            <br>
            <input type="submit" name="back" value="戻る">
            <input type="submit" name="confirm" value="確認">
        </form>
    </body>
</html>