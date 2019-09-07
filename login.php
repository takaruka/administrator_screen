<?php
    session_start();
    require_once('validate.php');
    require_once('db.php');

    //ログイン状態(クッキー)を確認
    if(!empty($_COOKIE['Email'])){
        //ログイン状態(クッキー)が保持されていたら
        $_POST['Email']=$_COOKIE['Email'];
        $_POST['Password']=$_COOKIE['Password'];
        $_POST['save']='on';
    }

    //$_POST['login']か$_POST['Email']が空じゃなければ
    if(!empty($_POST['login'])||!empty($_POST['Email'])){

        //Varidateクラスを読み込む
        $check = new Validate();
    
        //空白チェック
        $errors['Email']= $check->empty_check($_POST['Email'],"メールアドレス");
        $errors['Password']= $check->empty_check($_POST['Password'],"Password");

        //空白を取り除く
        $errors = array_filter($errors, 'strlen');

        //バリデーションクリアしたら
        if (empty($errors)) {

            //dbセット
            $dbclass = new db();
            $db=$dbclass->db_set("mysql:dbname=test;host=localhost;charaset=utf8","root","");

            //SQLセット
            $sql="SELECT * FROM admin WHERE Email=:Email";
            $stmt=$db->prepare($sql);

            //SQLにフォーム入力値をバインド
            $stmt->bindParam(':Email',$_POST["Email"],PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

            //メールアドレスが見つかれば
            if(!empty($result)){
                //パスワードがあっているか判定
                if(password_verify($_POST["Password"], $result[0]['Password'])){
                    
                    //ログイン成功
                    
                    //セッションの保存
                    $_SESSION=$_POST;
                    $_SESSION["name"]=$result[0]["Name"];

                    //ログイン情報を記録する
                    if($_POST['save']=='on'){
                        setcookie('Email',$_POST['Email'],time()+60*60*24*14);
                        setcookie('Password',$_POST['Password'],time()+60*60*24*14);
                    }

                    //list.phpへ遷移
                    header('Location: list.php');
                    exit;  
                }else{
                    $errors['loginPassword']="パスワードが違います。";    
                }
            }else{
                $errors['loginEmail']="メールアドレスが違います。";
            }
        }
    }

    //新規登録画面へボタンが押されたら
    if(isset($_POST["register"])){
        //admin_register.phpへ遷移
        header('Location: admin_register.php');
        exit;  
    }


?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="form.css">
    </head>
    <body>
        <h1>ログイン画面</h1>
        <form actiion="" method="POST">
        <?php if(!empty($errors['loginEmail'])){echo '<span class="errorMessage">'.$errors['loginEmail'].'</span><br>';}  ?>
        <?php if(!empty($errors['loginPassword'])){echo '<span class="errorMessage">'.$errors['loginPassword'].'</span><br>';} ?>
        <p class="heading">Email</p>
        <input type="text" name="Email">
        <?php 
            if(!empty($errors['Email'])){
                echo '<span class="errorMessage">'.$errors['Email'].'</span>';
            }
        ?>
        <br>
        <p class="heading">Password</p>
        <input type="password" name="Password">
        <?php 
            if(!empty($errors['Password'])){
                echo '<span class="errorMessage">'.$errors['Password'].'</span>';
            }
        ?>
        <br>
        <input type="submit" name="register" value="新規登録画面へ">
        <input type="submit" name="login" value="ログイン">
        <p>ログイン状態の記録</p>
        <input type="checkbox" name="save" id="save">
        <label for="save">次回からは自動的にログインする</label>
    </body>
</html>