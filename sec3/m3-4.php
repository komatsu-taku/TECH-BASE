<?php
    function make_file($filename){
        // ファイルを作成する関数
        $fp = fopen($filename, "w");
        fclose($fp);
    }

    function fetch_index($filename, $sep){
        // 次のindexをとってくる
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $length = count($lines);
        // 最後の行のデータを抽出
        $last_data = $lines[$length-1];

        // 最後の行のデータのindexを取得 -> +1した値を返す
        $final_index = explode($sep, $last_data)[0];
        return $final_index + 1;
    }

    function add_data_to_file($filename, $arr, $sep){
        // arrayに格納されたデータをファイルに追加する
        $name = $arr["name"];
        $comment = $arr["comment"];
        $index = fetch_index($filename, $sep);
        $date = date("Y/m/d g:i:s");

        $line = $index.$sep.$name.$sep.$comment.$sep.$date;

        $fp = fopen($filename, "a");
        fwrite($fp, $line.PHP_EOL);
        fclose($fp);
    }

    function del_content_file($filename){
        // ファイルの中身を空にする
        $handle = fopen($filename, "r+");
        ftruncate($handle, 0);
        fclose($handle);
    }

    function add_lines_to_file($filename, $line){
        // 行単位の内容をファイルに追記する
        $fp = fopen($filename, "a");
        fwrite($fp, $line.PHP_EOL);
        fclose($fp);
    }
    
    // ファイルの内容を編集する関数
    function update_data($filename, $arr, $sep){
        // ファイルを読み込む
        $lines = file($filename, FILE_IGNORE_NEW_LINES);

        // ファイルを空にする
        del_content_file($filename);

        $update_index = $arr["index"];
        foreach($lines as $line){
            $contents = explode($sep, $line);

            $index = $contents[0];
            if ($update_index != $index){
                // updateしない場合はそのまま追加
                add_lines_to_file($filename, $line);
                continue;
            }

            // 空欄があれば元のデータで補完
            if (!empty($arr["name"])){
                $new_name = $arr["name"];
            }
            else{
                $new_name = $contents[1];
            }

            if (!empty($arr["comment"])){
                $new_comment = $arr["comment"];
            }
            else{
                $new_comment = $contents[2];
            }

            $new_date = date("Y/m/d g:i:s");
            $line = $index.$sep.$new_name.$sep.$new_comment.$sep.$new_date;

            // 書き込み
            add_lines_to_file($filename, $line);
        }
    }

    // ファイルから特定のデータを削除する
    function del_data_in_file($filename, $del_index, $sep){
        $lines = file($filename, FILE_IGNORE_NEW_LINES);

        // ファイルを空にする
        del_content_file($filename);

        foreach($lines as $line){
            $contents = explode($sep, $line);
            $index = $contents[0];

            // 削除対象でない場合そのまま追加
            if ($index != $del_index){
                add_lines_to_file($filename, $line);
            }
        }
    }

    // ファイルの中身を表示
    function print_file($filename, $sep){
        $lines = file($filename, FILE_IGNORE_NEW_LINES);

        if (count($lines) > 0){
            foreach($lines as $line){
                $contents = explode($sep, $line);

                $idx = $contents[0];
                $name = $contents[1];
                $comment = $contents[2];
                $date = $contents[3];

                echo "index: ".$idx." name: ".$name." comment: ".$comment." date: ".$date."<br>";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_03_04 </title>
    </head>

    <body>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前を入力">
            <input type="text" name="comment" placeholder="コメントを入力">
            <input type="hidden" name="kind" value="add">
            <input type="submit" name="submit" placeholder="送信">
        </form>

        <form action="" method="post">
            <input type="number" name="index" placeholder="編集するindexを指定">
            <input type="text" name="name" placeholder="名前">
            <input type="text" name="comment" placeholder="コメント">
            <input type="hidden" name="kind" value="update">
            <input type="submit" name="submit">
        </form>

        <form action="" method="post">
            <input type="number" name="index" placeholder="削除するindexを指定">
            <input type="hidden" name="kind" value="delete">
            <input type="submit" name="submit">
        </form>

        <?php
            // 今回ファイル名および区切り文字はハードコーディング
            $filename = "mission_3_4.txt";
            $sep = "<>";

            // ファイルがない場合作成
            if (!file_exists($filename)){
                make_file($filename);
            }

            // 送信を確認
            if (!empty($_POST)){
                // (1) ファイルに追記
                if ($_POST["kind"]=="add" && !empty($_POST["name"])){
                    add_data_to_file($filename, $_POST, $sep);
                }
    
                // (2) ファイルの編集
                if ($_POST["kind"]=="update" && !empty($_POST["index"])){
                    update_data($filename, $_POST, $sep);
                }
    
                // (3) ファイルの要素の削除
                if ($_POST["kind"]=="delete" && !empty($_POST["index"])){
                    del_data_in_file($filename, $_POST["index"], $sep);
                }
            }

            // ファイルの内容を表示
            print_file($filename, $sep);
        ?>
    </body>
</html>