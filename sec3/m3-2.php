<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_03_02 </title>
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
    ?>

    <body>
        <form action="" method="post">
            <input type="text" name="name" placeholder="名前を入力してください">
            <input type="text" name="comment" placeholder="コメントを入力">
            <input type="text" name="filename" placeholder="ファイル名を記入">
            <input type="submit" name="submit">
        </form>

        <?php
            // 区切り文字は固定
            $sep = "<>";
            
            if (!empty($_POST)){
                if (!empty($_POST["filename"])){
                    $filename = $_POST["filename"];

                    // nameが入力されている場合ファイルに追加
                    if (!empty($_POST["name"])){
                        write_data_in_file($filename, $_POST, $sep);
                    }

                    // ファイルの中身の表示
                    display_file($filename, $sep);
                }
                else{
                    echo "ファイル名を記入してください<br>";
                }
            }
            
        ?>
    </body>
</html>