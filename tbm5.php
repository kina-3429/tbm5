<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="stylesheet.css">
    <title>mission_5</title>
</head>
<body>
<?php
//空データによるエラーを表示しない
error_reporting(E_ALL & ~E_NOTICE);

//データベースの作成
$dsn = "データベース名";
$user = "ユーザー名";
$password = "パスワード";
$pdo = new PDO ($dsn,$user,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルの作成（id,name,comment,date,pass）
//CREATE table_name ();
$sql = "CREATE table if not exists table_221241"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT,"
    ."date datetime,"
    ."pass char(32)"
    .");";
$stmt = $pdo->query($sql);

//フォーム内に送る編集用のデータを作成
//編集用データの格納
$edit_num = $_POST["edit_num"];
$edit_pass = $_POST["edit_pass"];

if(!empty($edit_num)){
    $sql = "SELECT * from table_221241 where id ='$edit_num';";
    $results = $pdo -> query($sql);
    foreach($results as $edit_date){
        if($edit_pass == $edit_date[4]){
        $edit_id = $edit_date[0];
        $edit_name = $edit_date[1];
        $edit_come = $edit_date[2];
        $edit_password = $edit_date[4];
        }else{
            echo "パスが違う";
        }
    }
}
?>
<div class = "title">
<?php
echo "ひとこと掲示板";
echo "<hr>";
//データベース消去用
/*
$sql = 'DROP TABLE table_221241';
$stmt = $pdo->query($sql);*/
?>
</div>
<div class = "form">  
    <form action="" method = "post" class = "aaa">
    <p>
        <input type = "hidden" name = "edit_key" value=<?php echo $edit_id?>>
    </p>
    <p class = "comment">
        <input type="text" name="str" placeholder= "名前" value=<?php echo $edit_name?>>
        <input  type="text" name="come" class = "aab" placeholder= "コメント" value=<?php echo $edit_come?>>
    </p>
    <p>
        <input type = "text" name = "pass" placeholder = "パスワード" value=<?php echo $edit_password?>>
    </p>
    <p>
        <input type="submit" name="submit">
    </p>
    </form>
    <form action="" method = "post" class = "aaa">
        <p><input type="text" name="del_num" placeholder= "削除する番号"></p>
        <p><input type = "text" name = "del_pass" placeholder = "パスワード"></p>
        <input type="submit" name="btn" value ="削除">
    </form>
    <form action="" method = "post" class = "aaa">
        <p><input type="text" name="edit_num" placeholder= "編集する番号"></p>
        <p><input type = "text" name = "edit_pass" placeholder = "パスワード"></p>
        <input type="submit" name="btn" value ="編集">
    </form>
</div>
<div class = "form2">
※新規書き込みを行う場合は、名前・コメント・パスワードを入力した後で送信ボタンを押してください。<br>
　投稿を編集・削除する場合は投稿番号とパスワードを入力してください。<br>
　削除はボタンを押してすぐに反映されます。<br>
　編集ボタンを押した後、投稿フォーム内を変更した後で再度送信ボタンを押してください。<br>
</div>
<?php
/*本文開始*/

//全体で使う変数置き場 
$date1 = date("Y/m/d h:m:s");
$edit_key = $_POST["edit_key"];
$str = $_POST["str"];
$come = $_POST["come"];
$wpass = $_POST["pass"];
$del_num = $_POST["del_num"];
$del_pass = $_POST["del_pass"];

//編集Noが存在せず投稿フォーム記入されている場合の処理
if(empty($edit_key)==true && !empty($str && $come && $wpass)){
    //データベースへの書き込み
    //INSERT INTO tbl_name () VALUES();
    $dbw = "INSERT into table_221241(name,comment,date,pass) value(:name,:comment,:date,:pass)";
    $sql = $pdo -> prepare($dbw);
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $password, PDO::PARAM_STR);
    $name = $str;
    $comment = $come;
    $date = $date1;
    $password = $wpass;
    $sql -> execute();
}elseif(!empty($edit_key)){//編集Noが存在する場合の処理     
    //データベース内容の変更
        $id = $edit_key;
        $name = $str;
        $comment = $come;
        //$date = ;
        $pass = $wpass;//変更内容の指定
        //UPDATE テーブル名 SET カラム名 = 値 WHERE 条件;
        $sql = "UPDATE table_221241 SET id=:id, name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id2";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);        
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date1, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':id2', $edit_key, PDO::PARAM_INT);
        $stmt->execute();
}
/*削除フォームに記入している場合の処理*/
if(!empty($del_num)){
//データベース内容の破棄
    $sql = "SELECT pass FROM table_221241 where id = '$del_num';";
    $results = $pdo->query($sql);
    foreach ($results as $row){
        $key = $row[0];
    }
    if($key == $del_pass){
        //DELETE FROM テーブル名 WHERE 条件;
        $sql = "DELETE from table_221241 WHERE id = '$del_num';";
        $stmt = $pdo ->query($sql);
    }
}
//フォームと表示の境界線
echo "<hr>";
?>
<div class = "main">
<?php
//データベース内のデータをブラウザ側へ書き出す
$sql = 'SELECT * FROM table_221241';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();//fetchAll()、該当するすべてのデータを配列で返す
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo "<b>".$row['id']."</b>".',';
    echo $row['name'].',';
    echo $row["date"]."<br>";  
    echo "<div class = 'kakikomi'>".$row['comment']."</div>";
    echo "<div class = 'kakikomi'>"."<br>"."</div>";  
}
?>
</div>
<?php
echo "<hr>";
?>
</body>
</html>