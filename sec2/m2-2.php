<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_02_02 </title>
    </head>

    <body>
        <form action="" method="post">
            <input type="text" name="text" placeholder="書き込む内容を記入"> <br>
            <input type="text" name="filename" placeholder="書き込むファイルを指定"> <br>
            <input type="submit" name="submit">
        </form>

        <?php
            // どちらも適切に入力されている場合
            if (!empty($_POST["text"]) && !empty($_POST["filename"])){
                $text = $_POST["text"];
                $filename = $_POST["filename"];

                // 書き込み
                $fp = fopen($filename, "w");
                fwrite($fp, $text.PHP_EOL);
                fclose($fp);

                if ($text == "完成！"){
                    echo "おめでとう！<br>";
                }
                else{
                    echo $text."<br>";
                }
            }
        ?>
    </body>
</html>