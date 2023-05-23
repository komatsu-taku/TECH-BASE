<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_03_03 </title>
    </head>

    <?php
        // ファイルを作成する関数
        function make_file($filename){
            $fp = fopen($filename, "w");
            fclose($fp);
        }

        // ファイルの中身を表示する関数
        function display_file($filename, $sep){
            $lines = file($filename, FILE_IGNORE_NEW_LINES);

            if (count($lines) > 0){
                foreach($lines as $line){
                    $data = explode($sep, $line);

                    $idx = $data[0];
                    $name = $data[1];
                    $comment = $data[2];
                    $date = $data[3];

                    echo "index: ".$idx." name: ".$name." cooment: ".$comment." date: ".$date."<br>";
                }
            }
        }

        // ファイルの中身を空にする
        function del_contents_in_file($filename){
            $handle = fopen($filename, "r+");
            ftruncate($handle, 0);
            fclose($handle);
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

        // 送信されたデータをファイルに追記する
        function add_data_to_file($filename, $POST, $sep){
            // nameがない場合はエラー
            if (empty($POST["name"])){
                echo "名前を入力してください<br>";
                return;
            }

            $name = $_POST["name"];
            $commnet = $_POST["comment"];
            $date = date("Y/m/d g:i:s");

            // ファイルの読み込み
            $idx = fetch_index($filename, $sep);

            $sep = "<>";
            $data = $idx.$sep.$name.$sep.$commnet.$sep.$date;

            $fp = fopen($filename, "a");
            fwrite($fp, $data.PHP_EOL);
            fclose($fp);
        }

        function delete_data_in_file($filename, $del_index, $sep){
            // ファイルを読み込み
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            
            // ファイルの中身を削除
            del_contents_in_file($filename);

            // ファイルの書き込み
            foreach($lines as $line){
                $data = explode($sep, $line);

                $idx = $data[0];
                // 削除対象の場合はスキップ
                if ($idx == $del_index) continue;
                
                $name = $data[1];
                $commnet = $data[2];
                $date = $data[3];

                $contents = $idx.$sep.$name.$sep.$commnet.$sep.$date;

                // 書き込み
                $fp = fopen($filename, "a");
                fwrite($fp, $contents.PHP_EOL);
                fclose($fp);
            }
        }

        function remove_data($filename, $del_index, $sep){
            // idxが入力されていない場合エラー
            if (empty($del_index)){
                echo "indexを入力してください<br>";
                return;
            }
            // ファイルから指定されたデータを削除
            delete_data_in_file($filename, $del_index, $sep);
        }
    ?>

    <body>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前を入力">
            <input type="text" name="comment" placeholder="コメントを入力">
            <input type="hidden" name="kind" value="add">
            <input type="submit" name="submit">
        </form>

        <br>

        <form action="" method="post">
            <input type="number" name="idx" placeholder="削除するindexを指定">
            <input type="hidden" name="kind" value="del">
            <input type="submit" name="submit">
        </form>

        <?php
            // ファイル名・区切り文字は一旦ハードコーディング
            $filename = "mission_3_3.txt";
            $sep = "<>";

            // ファイルがない場合は作成
            if (!file_exists($filename)){
                make_file($filename);
            }

            if (!empty($_POST)){
                // 追記
                if ($_POST["kind"] == "add"){
                    add_data_to_file($filename, $_POST, $sep);
                }
                // 削除
                if ($_POST["kind"] == "del"){
                    remove_data($filename, $_POST["idx"], $sep);
                }
            }
            // ファイル内容の表示
            display_file($filename, $sep);
        ?>
    </body>
</html>