<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チーム新規作成</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">

    <?php
    session_start();
    $_SESSION = array();
    session_destroy();
    ?>

    <style>
        .form {
            padding-top: 40px;
        }

        .form>h2 {
            padding-top: 40px;
        }

        .form>div{
            padding: 10px 0px;
        }
    </style>

</head>


<body>
    <header>
        <div class="header_contents">
            <div class="titlepic">
                <img src="./contens/header.png">
            </div>
            <div class="pagetitle">
                <h1>新規チーム作成</h1>
            </div>
            <div class="adminlogin">

            </div>
        </div>
    </header>

    <div class="body">
        <form action="" method="post">
            <div class="form">
                <h2>
                    チーム情報
                </h2>
                <div>
                    チーム名<input type="text" class="form-text" name="team_name" size=20 required>
                </div>
                <div>
                    パスワード <input type="password" class="form-text" name="team_pass" required>
                </div>
                <h2>
                    管理者情報
                </h2>
                <div>
                    メールアドレス<input type="text" class="form-text" name="admin_mail" size=20 required>
                </div>
                <div>
                    パスワード<input type="password" class="form-text" name="admin_pass" required>
                </div>
                <div class="buttons">
                    <div>
                        <input type="submit" class="form-submit-button" value="新規チーム作成" required>
                    </div>
                    <div>
                        <input type="button" class="form-submit-button" value="戻る" onClick="location.href='1.php'">
                    </div>
                </div>
            </div>

            <?php
            $is_add_team = true;
            if (isset($_POST['admin_mail'])) {
                $team_name = $_POST['team_name'];
                $team_pass = $_POST['team_pass'];
                $admin_mail = $_POST['admin_mail'];
                $admin_pass = $_POST['admin_pass'];

                require('./DatabaseManager.php');
                $db_m = new DatabaseManager();

                $teamnames = $db_m->GetAllTeamName();

                if ($teamnames != false) {
                    foreach ($teamnames as $team) {
                        if ($team[0] == $team_name) {
                            $is_add_team = false;
                            break;
                        }
                    }
                }

                if ($is_add_team == true) {
                    $db_m->SetTeamData($team_name, $team_pass, $admin_mail, $admin_pass);
                    header('Location:1.php');
                } else {
                    echo '<p class="error_text">すでに存在するチーム名です</p>';
                }
            }
            ?>
        </form>
    </div>
</body>

</html>