<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
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
                <h1>ログイン</h1>
            </div>
            <div class="adminlogin">

            </div>
        </div>
    </header>

    <div class="body">

        <form action="" method="post">
            <div class="form">
                <div>
                    チーム名 <input type="text" class="form-text" name="team_name" size=20>
                </div>
                <div>
                    パスワード <input type="password" class="form-text" name="team_pass">
                </div>
                <div class="buttons">
                    <div>
                        <input type="submit" class="form-submit-button" value="ログイン" required>
                    </div>
                    <div>
                        <input type="button" onclick="location.href='2.php'" class="form-submit-button" value="新規チーム作成" required>
                    </div>
                </div>
            </div>

            <?php
            if (!isset($_SESSION)) {
                session_start();
            }

            $_SESSION = array();

            require 'DatabaseManager.php';
            $db_m = new DatabaseManager();

            if (isset($_POST['team_name'])) {
                $name = $_POST['team_name'];
                $pass = $_POST['team_pass'];

                //ログインチェック
                $flag = $db_m->CheckTeamLogin($name, $pass);

                if ($flag == true) {
                    //データベースでユーザー名とパスワードが一致した場合,セッションを記録し，3.phpに飛ぶ
                    $_SESSION['teamName'] = $name;
                    $_SESSION['isLogin'] = true;

                    header('Location:3.php');
                    exit();
                } else {
                    echo '<p class="error_text">チーム名またはパスワードが違います</p>';
                }
            }
            ?>
        </form>
    </div>
</body>

</html>