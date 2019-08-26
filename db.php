<?php
class db{
    public function db_set($dsn,$user,$password){

        //接続できるかチェックする流れ
        try{
            $db=new PDO($dsn,$user,$password);
            //echo "接続成功";
        }catch(PDOException $e){
            //接続失敗したら、"接続失敗"と表示する
            echo "接続失敗:".$e->getMessage()."\n";
            exit();
        }

        //存在しないテーブル名やカラム名をSQL文に持つプリペアドステートメントを発行したとき、エミュレーションOFFの場合はすぐにエラーが発生する
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        //SQLでエラーが起こった際、例外をスローします。
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
?>