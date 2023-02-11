<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント登録</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">
</head>

<body>
    <header>
        <div class="header_contents">
            <div class="titlepic">
                <img src="./contens/header.png">
            </div>
            <div class="pagetitle">
                <h1>イベント登録</h1>
            </div>
            <div class="adminlogin">

            </div>
        </div>
    </header>
    <div class="body">
        <form action="" method="post">
            <div class="form">
                <div>
                    日時 <input type="text" class="form-text" name="event_date" required>
                </div>
                <div>
                    イベント名 <input type="text" class="form-text" name="event_name" required>
                </div>
                <div class="buttons">
                    <div>
                        <input type="submit" class="form-submit-button" value="作成" required>
                    </div>
                    <div>
                        <input type="button" class="form-submit-button" value="戻る" onClick="location.href='3.php'">
                    </div>
                </div>
            </div>

            <?php

            if (!isset($_SESSION)) {
                session_start();
            }

            if (isset($_SESSION['isLogin'])) {
                if ($_SESSION['isLogin'] != true) {
                    header('Location: 1.php');
                }else if($_SESSION['is_admin'] != true){
                    header('Location: 3.php');
                }
            }else{
                header('Location: 1.php');
            }

            if (isset($_POST['event_name'])) {

                if (!isset($_SESSION)) {
                    session_start();
                }

                require('./DatabaseManager.php');
                $db_m = new DatabaseManager();

                $event_name = $_POST['event_name'];
                $event_date = $_POST['event_date'];

                if ($db_m->CreateEvent($event_name, $event_date) == false) {
                    echo "<p class='error_text'>データ登録失敗</p>";
                } else {
                    header('Location:3.php');
                }
            }
            ?>
        </form>
    </div>
</body>

</html>