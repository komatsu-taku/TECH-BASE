<!DOCTYPE html>
<html>
    <head>
        <meta charset="urf-8">
        <title> mission_03_01 </title>
    </head>

    <?php
        function make_file($filename){
            /* ファイルを新規作成する関数 */
            $fp = fopen($filename, "w");
            fclose($fp);
        }

        function get_next_index($filename){
            /*
                書き込むデータのindexを取得する関数
            
            Args:
                fiename str: ファイル名
            Returns:
                書き込むデータ用のindex
            */
            $data = file($filename, FILE_IGNORE_NEW_LINES);
            // ファイルに記入済みのデータ数
            $length = count($data);
            // 次のindex = length + 1を返す
            return $length + 1;
        }

        function make_content($POST, $idx, $sep){
            /*
                送信された情報から書き込む内容を作成する
            
            Args:
                $POST array: $_POST (POST送信された内容)
                $idx  int  : 書き込むデータのindex
                #sep  str  : 区切り文字
            Return:
                idx<>name<>comment<>YYYY/MM/DD xx:xx:xx
            */
            $name = $POST["name"];
            $comment = $POST["comment"];
            $date = date("Y/m/d g:i:s");

            return $idx.$sep.$name.$sep.$comment.$sep.$date;
        }

        function write_data_in_file($filename, $POST, $sep){
            /*
                データをファイルに書き込む関数
            
            Args:
                filename str: ファイル名
                POST   array: $_POST (POST送信された内容)
                $idx     int: 書き込むデータのindex
                $sep     str: 区切り文字
            */
            
            $idx = get_next_index($filename);
            $content = make_content($POST, $idx, $sep);
            // 書き込み
            $fp = fopen($filename, "a");
            fwrite($fp, $content.PHP_EOL);
            fclose($fp);
        }
    ?>

    <body>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前を入力してください"> <br>
            <input type="text" name="comment" placeholder="コメントを入力"> <br>
            <input type="submit" name="submit">
        </form>

        <?php
            // ファイル名・区切り文字は一旦ハードコーディング
            $filename = "mission_3_1.txt";
            $sep = "<>";

            if (!empty($_POST)){
                if (!empty($_POST["name"])){
                    
                    // ファイルがない場合は作成
                    if (!file_exists($filename)){
                        make_file($filename);
                    }
                    // データの書き込み
                    write_data_in_file($filename, $_POST, $sep);
                }
                else{
                    echo "名前を入力してください!!<br>";
                }
            }
        ?>
    </body>
</html>