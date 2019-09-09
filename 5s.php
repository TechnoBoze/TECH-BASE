<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset="utf-8">
        <title>Mission 5</title>
    </head>

    <body>
        <h1>Mission 5</h1>
        
        <?php
        //編集モードのときフォームに値をセット        
        $editNum = "";
        $editName = "";
        $editComment = "";
        $editPass = "";
        $editFlag1 = true;
        $editFlag2 = true;

        if(isset($_POST["edit"])){
            echo "【編集モードです。】";
            if(!empty($_POST["editNum"])){
                //DBに接続する
                $dsn = 'データベース名';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                 
                $editNum = $_POST["editNum"];
                $sql = "SELECT * FROM DB WHERE id = $editNum";
                $stmt = $pdo->query($sql);
                foreach($stmt as $row){
                    if($row['pass']==$_POST["pass"]){
                        $editName = $row['name'];
                        $editComment = $row['comment'];
                        $editPass = $row['pass'];
                    }else{
                        $editFlag1 = false;
                    }
                }
            }else{
                $editFlag2 = false;
            }
        }

        ?>
        <form method = "POST">
            【投稿フォーム】
            <input type ="hidden" value="<?=$editNum?>" name="editNum"><br>
            名前：<br>    
            <input type ="text" value="<?=$editName?>" name="name"><br>
            コメント：<br>
            <input type ="text" value="<?=$editComment?>" name="comment"><br>
            パスワード：<br>
            <input type="password" value="<?=$editPass?>" name="pass">
            <input type = "submit" value = "投稿" name = "submit"><br>

        <?php
        //「送信」処理
        if(isset($_POST["submit"])){
            //入力欄のいずれかが空
            if(empty($_POST["name"]) || empty($_POST["comment"]) || empty($_POST["pass"])){
                echo "---入力欄を埋めてください。---";
            } 
            //入力欄が埋まっている。
            else{
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $pass = $_POST["pass"];
                $time = date("Y/m/d H:i:s");
                
                //DBに接続する
                $dsn = 'データベース名';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
               
                //新規書き込み処理
                if(empty($_POST["editNum"])){
                    echo "【新規書き込み】<br>";
                    //(2)テーブルを作成
                    $sql = "CREATE TABLE IF NOT EXISTS DB"
                    ." ("
                    . "id INT AUTO_INCREMENT PRIMARY KEY,"
                    . "name char(32),"
                    . "comment TEXT,"
                    . "time TEXT,"
                    . "pass char(32)"
                    .");";
                    $stmt = $pdo->query($sql);

                    //(5)INSERT
                    $time = date("Y/m/d H:i:s");
                    $sql = $pdo -> prepare("INSERT INTO DB (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':time', $time, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    $sql -> execute();               
                }
                //編集処理
                else{
                    $editNum = $_POST["editNum"];
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $sql = "UPDATE DB SET name = :name, comment = :comment WHERE id = :id";    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $editNum, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
        ?>
        </form>
        <br>
        <form method="POST">
            【削除フォーム】<br>
            投稿番号：<br>
            <input type="text" name ="deleteNum"><br>
            パスワード：<br>
            <input type="password" name="pass">
            <input type="submit" value="削除" name="delete"><br>

            
            <?php
            if(isset($_POST["delete"])){
                echo "【削除】<br>";
                //DBに接続する
                $dsn = 'データベース名';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                 
                $deleteNum = $_POST["deleteNum"];
                //0を指定するとテーブルごと消去する
                if(!empty($deleteNum)){
                    //(8)DELETE
                    $sql = "SELECT * FROM DB WHERE id = $deleteNum";
                    $stmt = $pdo->query($sql);
                    foreach($stmt as $row){
                        if($row['pass']==$_POST["pass"]){
                            $id = $deleteNum;
                            $sql = 'delete from DB where id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                        }else{
                            echo "---無効なパスワードです。---";
                        }
                    }
                }else{
                    echo "---入力欄を埋めてください。---";
                }
            }
            ?>

            
        </form>
        <br>
        <form method="post">
            【編集用フォーム】<br>
            投稿番号：<br>
            <input type="text" name="editNum"><br>
            パスワード：<br>
            <input type="password" name="pass">
            <input type="submit" value="編集" name="edit"><br>

            <?php
            if($editFlag1==false){
                echo "---無効なパスワードです。---";
            }
            if($editFlag2==false){
                echo "---入力欄を埋めてください。---";
            }
            ?>
        </form>

        <hr>

        <?php

            //DBに接続する
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                   
            //(6)SELECT
            $sql = 'SELECT * FROM DB';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
            echo $row['id'];
            echo ','. $row['name'];
            echo ','. $row['comment'];
            echo ','. $row['time'];
            //echo ','. $row['pass'];
            echo "<hr>";
            }
        ?>

    </body>
</html>