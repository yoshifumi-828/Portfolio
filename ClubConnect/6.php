<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者認証</title>
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
                <h1>管理者認証</h1>
            </div>
            <div class="adminlogin">

            </div>
        </div>
    </header>

    <div class="body">

        <form action="" method="post">
            <div class="form">
                <div>
                    管理者メールアドレス <input type="text" class="form-text" name="admin_mail_login" size=20 required>
                </div>
                <div>
                    管理者パスワード <input type="password" class="form-text" name="admin_pass_login" required>
                </div>
                <div class="buttons">
                    <div>
                        <input type="submit" class="form-submit-button" value="認証" required>
                    </div>
                    <div>
                        <input type="button" class="form-submit-button" value="戻る" onClick="location.href='3.php'">
                    </div>
                </div>
            </div>

            <?php

            require 'DatabaseManager.php';
            $dbm = new DatabaseManager();

            if (!isset($_SESSION)) {
                session_start();
            }

            if(isset($_SESSION['isLogin'])){
                if($_SESSION['isLogin'] != true){
                    header('Location: 1.php');
                }
            }else{
                header('Location: 1.php');
            }

            if (isset($_POST['admin_mail_login']) && isset($_POST['admin_pass_login'])) {
                $admin_mail = $_POST['admin_mail_login'];
                $admin_pass = $_POST['admin_pass_login'];

                if ($dbm->CheckAdminLogin($admin_mail, $admin_pass)) {
                    header('Location:3.php');
                    $_SESSION['is_admin'] = true;
                    exit();
                } else {
                    echo '<p class="error_text">管理者のメールアドレスまたはパスワードが違います</p>';
                }
            }
            ?>
        </form>
    </div>

</body>

</html>