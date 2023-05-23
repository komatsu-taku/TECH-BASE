<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_02_04 </title>
    </head>

    <?php
        function print_file_contents($filename){
            // 読み込み
            $contents = file($filename, FILE_IGNORE_NEW_LINES);

            echo "***** ファイルの中身 *****<br>";
            // 表示
            foreach($contents as $content){
                echo $content."<br>";
            }
            return;
        }

        function print_text($text){
            /*
                textに応じて出力を決定する関数
            */
            if ($text == "完成！"){
                echo "おめでとう！<br>";
            }
            else{
                echo "「".$text."」を書き込みました<br>";
            }
        }

        function write_text_in_file($filename, $text){
            /*
                テキストをファイルに追記する関数
            
            Args:
                filename str: ファイル名
                text str: 書き込む内容
            */
            $fp = fopen($filename, "a");
            fwrite($fp, $text.PHP_EOL);
            fclose($fp);

            // textの表示
            print_text($text);
        }
    ?>

    <body>
        <form action="" method="post">
            <input type="text" name="text" placeholder="コメント">
            <input type="text" name="filename" placeholder="ファイルを指定">
            <input type="submit" name="submit">
        </form>

        <?php
            if (!empty($_POST)){
                if (!empty($_POST["filename"])){
                    $filename = $_POST["filename"];
    
                    // コメントが記入されている場合->ファイルに書き込む
                    if (!empty($_POST["text"])){
                        write_text_in_file($filename, $_POST["text"]);
                    }
    
                    // ファイル内容を表示
                    print_file_contents($filename);
                }
                else{
                    echo "ファル名を記入してください<br>";
                }
            }
        ?>
    </body>
</html>