<!-- 
    使用時の注意
        <database>
        <username>
        <password>
        <dbname> 9ヶ所
    を適切な名前に変更して実行してください。
-->
<?php
    // データベースの情報: 適宜修正
    $dsn = "<databese>";
    $user = "<username>";
    $pass_DB = "<password>";
    $opt = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
    
    // PDOの作成
    $pdo = new PDO($dsn, $user, $pass_DB, $opt);
?>

<?php
    // テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS <dbname>"."(".
            "id INT AUTO_INCREMENT PRIMARY KEY,".
            "name char(32),".
            "comment TEXT,".
            "pass char(32)".
            ");";

    // 作成
    $state = $pdo -> query($sql);
?>

<?php
    function check_table_exist(string $DB_name, PDO $pdo){
        /*
            tableの存在をチェックする関数
        */

        $sql = "SHOW TABLES";
        $names = $pdo -> query($sql);

        foreach($names as $name){
            if ($name == $DB_name) return true;
        }

        return false;
    }

    function extract_info(array $arr, string $kind="post"){
        /*
            配列から各種情報を返す関数
        */
        if ($kind == "select"){
            return array($arr["id"], $arr["pass"], $arr["name"], $arr["comment"]);
        }
        else if($kind == "post"){
            return array($arr["pass"], $arr["name"], $arr["comment"]);
        }
    }

    function display_db(string $DB_name, PDO $pdo){
        /*
            DB内の全データを表示する関数
        */
        if (check_table_exist($DB_name, $pdo)){
            $sql = "SELECT * FROM <dbname>";
    
            $state = $pdo -> query($sql);
            $res = $state -> fetchAll();
            foreach($res as $row){
                list($id, $pass, $name, $comment) = extract_info($row, "select");
                echo "id: ".$id." name: ".$name." comment: ".$comment."<br>";
            }
        }else{
            echo "DBが存在しません<br>";
            return;
        }
    }

    function add_info_to_db(array $POST, PDO $pdo){
        /*
            適切に入力された情報をDBに保存する関数
        
        Args:
            $POST array: $_POST
        */

        list($pass, $name, $comment) = extract_info($POST, "post");

        $sql = "INSERT INTO <dbname> (name, comment, pass) VALUES (:name, :comment, :pass)";
        
        $state = $pdo -> prepare($sql);
        $state -> bindParam(":name", $name, PDO::PARAM_STR);
        $state -> bindParam(":comment", $comment, PDO::PARAM_STR);
        $state -> bindParam(":pass", $pass, PDO::PARAM_STR);
        $state -> execute();
    }


    function add_db(array $POST, PDO $pdo){
        /*
            送信された内容をDBに保存する関数
        Args:
            $POST array: $_POST
        */
        
        // パスワードが入力されいない場合
        if (empty($POST["pass"])){
            echo "パスワードを入力してください<br>";
            return;
        }

        // パスワードが一致しない場合
        if ($POST["pass"] != $POST["confirm"]){
            echo "パスワードが一致しません<br>";
            return;
        }

        // 名前が入力されていない場合(コメントは空欄可)
        if (empty($POST["name"])){
            echo "名前を入力してください<br>";
            return;
        }

        // 適切に入力されている場合
        add_info_to_db($POST, $pdo);
    }

    function fetch_before_data(int $idx, PDO $pdo){
        /*
            編集前のname / commentを取得
        */
        $sql = "SELECT * FROM <dbname> WHERE id=:id";
        $state = $pdo -> prepare($sql);
        $state -> bindParam(":id", $idx, PDO::PARAM_INT);
        $state -> execute();

        $res = $state -> fetchAll();
        $name = $res[0]["name"];
        $comment = $res[0]["comment"];
        
        return array($name, $comment);
    }

    function select_data(array $POST, PDO $pdo){
        // 編集対象のインデックス
        $idx = $POST["idx"];

        // 編集前のデータを取ってくる
        list($bf_name, $bf_comment) = fetch_before_data($idx, $pdo);

        // 編集後の名前が空欄
        if (empty($POST["name"])){
            $name = $bf_name;
        }else{
            $name = $POST["name"];
        }

        // 編集後のコメントが空欄
        if (empty($POST["comment"])){
            $comment = $bf_comment;
        }else{
            $comment = $POST["comment"];
        }

        return array($name, $comment);
    }

    function get_password(int $idx, PDO $pdo){
        /*
            indexに対応するデータのpasswordを取得する
        */
        $sql = "SELECT * FROM <dbname> WHERE id=:id";
        $state = $pdo -> prepare($sql);
        $state -> bindParam(":id", $idx, PDO::PARAM_INT);
        $state -> execute();

        $res = $state->fetchAll();
        return $res[0]["pass"];
    }

    function update_info_in_db(array $POST, PDO $pdo){
        /*
            SQLを利用してDBをupdateする関数
        */

        // 編集対象のindex
        $idx = $POST["idx"];

        // passwordを取得
        $pass = get_password($idx, $pdo);

        // passwordが一致しない場合
        if ($POST["pass"] != $pass){
            echo "パスワードが一致しません<br>";
            return;
        }
        
        // 編集後の名前およびコメントを選択
        list($af_name, $af_comment) = select_data($POST, $pdo);

        $sql = "UPDATE <dbname> SET name=:name, comment=:comment WHERE id=:id";
        $state = $pdo -> prepare($sql);
        $state -> bindParam(":id", $idx, PDO::PARAM_INT);
        $state -> bindParam(":name", $af_name, PDO::PARAM_STR);
        $state -> bindParam(":comment", $af_comment, PDO::PARAM_STR);
        $state -> execute();
    }

    function update_db(array $POST, PDO $pdo){
        /*
            送信された内容に応じてDBを更新する関数
        */

        // passwordが入力されていない
        if (empty($POST["pass"])){
            echo "パスワードを入力してください<br>";
            return;
        }

        // indexが入力されていない
        if (empty($POST["idx"])){
            echo "編集するインデックスを入力してください<br>";
            return;
        }

        // 名前もコメントも入力されていない場合
        if (empty($POST["name"]) && empty($POST["comment"])){
            echo "編集内容を入力してください<br>";
            return;
        }

        update_info_in_db($POST, $pdo);
    }

    function delete_row_in_db(int $idx, PDO $pdo){
        /*
            該当するindexのデータをDBから削除
        */
        $sql = "DELETE FROM <dbname> WHERE id=:id";
        $state = $pdo -> prepare($sql);
        $state -> bindParam(":id", $idx, PDO::PARAM_INT);
        $state -> execute();
    }

    function delete_data(array $POST, PDO $pdo){
        /*
            DBから指定されたindexの行を削除
        */
        // passwordが入力されていない
        if (empty($POST["pass"])){
            echo "パスワードを入力してください<br>";
            return;
        }

        // indexが入力されて胃あない
        if (empty($POST["idx"])){
            echo "削除対象のインデックスを入力してください<br>";
            return;
        }

        // 削除対象のindex
        $idx = $POST["idx"];

        // 編集前のパスワード
        $bf_pass = get_password($idx, $pdo);

        // パスワードが一致しない
        if ($POST["pass"] != $bf_pass){
            echo "パスワードが一致しません<br>";
            return;
        }

        delete_row_in_db($idx, $pdo);
    }

    function delete_table(array $POST, string $pass_DB, PDO $pdo){
        /*
            tableを削除する関数
        */
        // DBのpasswordと一致しない
        if ($POST["pass"] != $pass_DB){
            echo "パスワードが一致しません<br>";
            return;
        }

        $sql = "DROP TABLE <dbname>";
        $pdo -> query($sql);
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <title> mission_5_1 </title>
    </head>

    <body>
        <!-- 入力 / 編集 / 削除 用のformの作成 -->
        <form action="" method="post">
            <input type="text" name="pass" placeholder="パスワードを入力">
            <input type="text" name="confirm" placeholder="確認用:パスワードを入力">
            <input type="text" name="name" placeholder="名前を入力">
            <input type="text" name="comment" placeholder="コメントを入力">
            <input type="hidden" name="kind" value="add">
            <input type="submit" name="submit">
        </form>

        <form action="" method="post">
            <input type="text" name="pass" placeholder="パスワードを入力">
            <input type="number" name="idx" placeholder="編集するインデックスを入力">
            <input type="text" name="name" placeholder="変更後の名前を入力">
            <input type="text" name="comment" placeholder="変更後のコメントを入力">
            <input type="hidden" name="kind" value="update">
            <input type="submit" name="submit">
        </form>

        <form action="" method="post">
            <input type="text" name="pass" placeholder="パスワードを入力">
            <input type="number" name="idx" placeholder="削除するindexを指定">
            <input type="hidden" name="kind" value="del">
            <input type="submit" name="submit">
        </form>

        <form action="" method="post">
            <input type="text" name="pass" placeholder="DBのパスワードを入力">
            <input type="hidden" name="kind" value="del_DB">
            <input type="submit" name="submit">
        </form>

        <?php
            // 送信があった場合
            if (!empty($_POST)){
                // 入力フォームからの送信
                if ($_POST["kind"] == "add"){
                    add_db($_POST, $pdo);
                }

                // 編集フォームからの送信
                if ($_POST["kind"] == "update"){
                    update_db($_POST, $pdo);
                }

                // 削除フォームからの送信
                if ($_POST["kind"] == "del"){
                    delete_data($_POST, $pdo);
                }

                // DBの削除
                if ($_POST["kind"] == "del_DB"){
                    delete_table($_POST, $pass_DB, $pdo);
                }
            }
            // DBの中身を表示
            display_db("<dbname>", $pdo);
        ?>
    </body>
</html>