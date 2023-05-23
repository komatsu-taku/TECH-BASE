<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_03_05 </title>
    </head>

    <?php
        function make_new_file($filename){
            /*
                新規ファイルの作成
            
            Args:
                filename str: ファイル名
            */
            $fp = fopen($filename, "w");
            fclose($fp);
        }

        function del_contents_in_file($filename){
            /*
                ファイルの中身を削除する関数
            
            Args:
                filename str: ファイル名
            */
            $handle = fopen($filename, "r+");
            ftruncate($handle, 0);
            fclose($handle);
        }

        function get_next_index($filename){
            /*
                新規データの追加時、次のindexを取得する関数
            
            Args:
                filename str: ファイル名
            Returns:
                index int: 次のデータのindex
            */
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            return count($lines) + 1;
        }

        function add_line_in_file($filename, $line){
            /*
                行単位に整形済みのデータをファイルに追記する
            
            Args:
                filename str: ファイル名
                line: 追加するデータ(行)
            */
            $fp = fopen($filename, "a");
            fwrite($fp, $line.PHP_EOL);
            fclose($fp);
        }

        function add_data_to_file($filename, $contents, $sep){
            /*
                新しいデータをファイルに追加する
            
            Args:
                filename str: ファイル名
                contents array: 入力データをkeyとして持つ連想配列
                sep str: 区切り文字
            */

            $index = get_next_index($filename);
            $password = $_POST["password"];
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $date = date("Y/m/d g:i:s");

            $line = $password.$sep.$index.$sep.$name.$sep.$comment.$sep.$date;
            add_line_in_file($filename, $line);
        }

        function add_new_data($filename, $contents, $sep){
            /*
                POST送信を受け付け、新しいデータをファイルに追記する
            
            Args:
                filename str: ファイル名
                conetnts array: 各入力をkeyとして持つ連想配列
                sep str: 区切り文字
            */

            // passwordが空欄
            if (empty($contents["password"])){
                echo "パスワードを入力してください<br>";
                return;
            }

            // 名前が空欄
            if (empty($contents["name"])){
                echo "名前を入力してください";
                return;
            }
            
            // passwordが一致しない場合
            if ($contents["password"] != $contents["confirm"]){
                echo "パスワードが一致しません<br>";
                return;
            }

            // ファイルに追記
            add_data_to_file($filename, $contents, $sep);
            return;
        }

        function select_data($contents, $data){
            /*
                入力情報と元の情報から新規保存する情報を選択
            
            Args:
                contents array: 入力された編集情報をもつ連想配列
                data array: 元の情報をもつインデックス配列
                    0: password 1:index 2:name 3:comment 4:date
            */
            $password = $data[0];
            $index = $data[1];
            if (empty($contents["name"])){
                $name = $data[2];
            }
            else {
                $name = $contents["name"];
            }

            if (empty($contents["comment"])){
                $comment = $data[3];
            }
            else{
                $comment = $contents["comment"];
            }

            $date = date("Y/m/d g:i:s");
            
            return array($password, $index, $name, $comment, $date);
        }

        function update_data_in_file($filename, $contents, $sep){
            /*
                ファイル内容を編集し保存する
            
            Args:
                filename str: ファイル名
                contents array: 編集内容をもつ連想配列
                sep str: 区切り文字
            */

            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            // ファイルの中身を削除
            del_contents_in_file($filename);

            foreach($lines as $line){
                // 区切り文字で行を分割
                $data = explode($sep, $line);

                // indexが一致しない場合そのまま追記
                if ($data[1] != $contents["index"]){
                    add_line_in_file($filename, $line);
                    continue;
                }

                // passwordが一致しない場合
                if ($contents["password"] != $data[0]){
                    echo "パスワードが一致しません<br>";
                    return;
                }

                // 更新情報の選択
                list($password, $index, $name, $comment, $date) = select_data($contents, $data);
                $line = $password.$sep.$index.$sep.$name.$sep.$comment.$sep.$date;

                // 更新情報を追記
                add_line_in_file($filename, $line);
            }
            return;
        }

        function update_data($filename, $contents, $sep){
            /*
                ファイルの内容を編集する
            
            Args:
                filename str: ファイル名
                contents array: 編集内容をkeyとして持つ連想配列
                sep str: 区切り文字
            */

            // passwordが空欄
            if (empty($contents["password"])){
                echo "パスワードを入力してください<br>";
                return;
            }

            // 編集内容が含まれていない
            if (empty($contents["name"]) && empty($contents["comment"])){
                echo "編集内容を入力してください<br>";
                return;
            }

            // データの編集
            update_data_in_file($filename, $contents, $sep);
            return;
        }

        function delete_data_in_file($filename, $contents, $sep){
            /*
                入力された削除情報をファイルから削除する
            
            Args:
                filename str : ファイル名
                contents array: 削除対象のpasswordおよびindexを持つ連想配列
            */
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            // ファイルの中身を削除
            del_contents_in_file($filename);

            foreach($lines as $line){
                // 区切り文字で分割
                $data = explode($sep, $line);

                // indexが一致しない場合、そのまま追記
                if ($contents["index"] != $data[1]){
                    add_line_in_file($filename, $line);
                    continue;
                }

                // passwordが一致しない場合
                if ($contents["password"] != $data[0]){
                    echo "パスワードが一致しません<br>";
                    add_line_in_file($filename, $line);
                }
            }
            return;
        }

        function delete_data($filename, $contents, $sep){
            /*
                入力されたindexに基づき情報を削除する関数
            
            Args:
                filename str: ファイル名
                contents array: 削除情報をもつ連想配列
            */

            // passwordが空欄
            if (empty($contents["password"])){
                echo "パスワードを入力してください<br>";
                return;
            }

            // indexが空欄
            if (empty($contents["index"])){
                echo "削除するインデックスを指定してください<br>";
                return;
            }

            delete_data_in_file($filename, $contents, $sep);
        }

        function print_file($filename, $sep){
            /*
                ファイル内容の一覧を表示
            
            Args:
                filename str: ファイル名
            */
            $lines = file($filename, FILE_IGNORE_NEW_LINES);

            if (count($lines) > 0){
                foreach($lines as $line){
                    $contents = explode($sep, $line);

                    $idx = $contents[1];
                    $name = $contents[2];
                    $comment = $contents[3];
                    $date = $contents[4];

                    echo "index: ".$idx." name: ".$name." comment: ".$comment." date: ".$date."<br>";
                }
            }
        }
    ?>

    <body>
        <form action="" method="post">
            <input type="text" name="password" placeholder="パスワードを入力">
            <input type="text" name="confirm" placeholder="パスワード確認用">
            <input type="text" name="name" placeholder="名前を入力">
            <input type="text" name="comment" placeholder="コメントを入力">
            <input type="hidden" name="kind" value="new">
            <input type="submit" name="submit">
        </form>

        <form action="" method="post">
            <input type="text" name="password" placeholder="パスワードを入力">
            <input type="number" name="index" placeholder="編集するインデックスを入力">
            <input type="text" name="name" placeholder="名前を入力(任意)">
            <input type="text" name="comment" placeholder="コメントを入力(任意)">
            <input type="hidden" name="kind" value="update">
            <input type="submit" name="submit">
        </form>

        <form action="" method="post">
            <input type="text" name="password" placeholder="パスワードを入力">
            <input type="number" name="index" placeholder="削除するパスワードを入力">
            <input type="hidden" name="kind" value="delete">
            <input type="submit" name="submit">
        </form>

        <?php
            // ファイル名と区切り文字は指定
            $filename = "mission_3_5.txt";
            $sep = "<>";

            // ファイルがない場合、ファイルを作成
            if (!file_exists($filename)){
                make_new_file($filename);
            }

            if(!empty($_POST)){
                // 新規入力
                if ($_POST["kind"] == "new"){
                    add_new_data($filename, $_POST, $sep);
                }

                // 編集
                if ($_POST["kind"] == "update"){
                    update_data($filename, $_POST, $sep);
                }

                // 削除
                if ($_POST["kind"] == "delete"){
                    delete_data($filename, $_POST, $sep);
                }
            }

            // ファイル内容を表示
            print_file($filename, $sep);
        ?>
    </body>
</html>