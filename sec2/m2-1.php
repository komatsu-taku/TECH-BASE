<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title> mission_02_01 </title>
    </head>

    <body>
        <form action="" method="post">
            <input type="text" name="text" placeholder="コメントを入力">
            <input type="submit" name="submit">
        </form>

        <?php
            if (!empty($_POST["text"])){
                $text = $_POST["text"];
                echo "<br>「".$text."を受け付けました」<br>";
            }
        ?>
    </body>
</html>