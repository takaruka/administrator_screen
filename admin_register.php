<?php
    session_start();
    require_once('validate.php');
    require_once('db.php');
    
    //登録ボタンを押したら
    if(isset($_POST['register'])){

        //Varidateクラスを読み込む
        $check = new Validate();
    
        //空白チェック
        $errors['name']= $check->empty_check($_POST['name'],"名前");
        $errors['Email']= $check->empty_check($_POST['Email'],"メールアドレス");
        $errors['Password']= $check->empty_check($_POST['Password'],"Password");

        //文字数チェック
        $errors['namemax']= $check->max_check($_POST['name'],20,"名前");

        //メールの形式チェック
        $errors['mailformat']= $check->mailformat_check($_POST['Email']);

        //空白を取り除く
        $errors = array_filter($errors, 'strlen');

        //バリデーションクリアしたら
        if (empty($errors)) {

            //dbセット
            $dbclass = new db();
            $db=$dbclass->db_set("mysql:dbname=test;host=localhost;charaset=utf8","root","");


            //メールアドレスが既に使われていないか確認
            
            //SQLセット
            $sql="SELECT * FROM admin WHERE Email=:Email";
            $stmt=$db->prepare($sql);

            //SQLにフォーム入力値をバインド
            $stmt->bindParam(':Email',$_POST["Email"],PDO::PARAM_STR);

            //SQL実行
            $stmt->execute();

            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

            //メールアドレスが使われていなければ
            if(empty($result)){
                //パスワードハッシュ化
            $password = $_POST['Password'];
            $pass = password_hash($password, PASSWORD_DEFAULT);

            //SQLセット
            $sql="INSERT INTO admin(id,Name,Password,Email)VALUES(NULL,:Name,:Password,:Email)";
            $stmt=$db->prepare($sql);

            //SQLにフォーム入力値をバインド
            $stmt->bindParam(':Name',$_POST["name"],PDO::PARAM_STR);
            $stmt->bindParam(':Email',$_POST["Email"],PDO::PARAM_STR);
            $stmt->bindParam(':Password',$pass,PDO::PARAM_STR);
            
            //SQL実行
            $stmt->execute();
            

            //セッション保存
            $_SESSION["name"]=$_POST["name"];
            $_SESSION["Email"]=$_POST["Email"];
            $_SESSION["Password"]=$_POST["Password"];

            //list.phpへ遷移
            header('Location: list.php');
            exit;  
            }else{
                $errors["EmailUse"]="そのメールアドレスは既に使われています。";
            }
        }
    }

        //ログイン画面へボタンが押されたら
        if(isset($_POST["login"])){
            //login.phpへ遷移
            header('Location: login.php');
            exit;  
        }
?>


<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="form.css">
    </head>
    <body>
        <h1>管理者登録</h1>
        <form action="" method="post">
            <p class="heading">name:</p>
            <input type="text" name="name" value="<?php if(!empty($_POST['name'])) {echo $_POST['name'];}?>">
            <?php 
                if(!empty($errors['name'])){
                echo '<span class="errorMessage">'.$errors['name'].'</span>';
                }
                if(!empty($errors['namemax'])){
                echo '<span class="errorMessage">'.$errors['namemax'].'</span>';
                }
            ?>
        <br>
        <p class="heading">Email:</p>
        <input type="text" name="Email" value="<?php if(!empty($_POST['Email'])) {echo $_POST['Email'];}?>">
        <?php 
            if(!empty($errors['Email'])){
            echo '<span class="errorMessage">'.$errors['Email'].'</span>';
            }
            if(!empty($errors['mailformat'])){
            echo '<span class="errorMessage">'.$errors['mailformat'].'</span>';
            }
            if(!empty($errors['EmailUse'])){
                echo '<span class="errorMessage">'.$errors['EmailUse'].'</span>';
            }
        ?>
        <br>
        <p class="heading">Password:</p>
        <input type="password" name="Password" value="<?php if(!empty($_POST['Password'])) {echo $_POST['Password'];}?>">
        <?php 
            if(!empty($errors['Password'])){
            echo '<span class="errorMessage">'.$errors['Password'].'</span>';
            }
        ?>
        <br>
        <input type="submit" name="login" value="ログイン画面へ">
        <input type="submit" name="register" value="登録">
        </form>
    </body>
</html>