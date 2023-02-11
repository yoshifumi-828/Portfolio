<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント一覧</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">

    <style>
        .event_table {
            padding-top: 30px;
        }

        .evevt_button {
            display: flex;
        }

        .form-submit-button {
            margin: 5px;

        }

        tr th {
            color: white;
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
                <h1>イベント一覧</h1>
            </div>
            <div class="adminlogin">
                <form>
                    <input type="button" onclick="location.href='6.php'" class="form-admin-button" name="admin_login" value="管理者ログイン">
                </form>
            </div>
        </div>
    </header>
    <div class="body">
        <div class="event_table">
            <table class="standard_table">
                <colgroup>
                    <col width="40%">
                    <col width="30%">
                    <col width="30%">
                </colgroup>
                <tr>
                    <th>イベント名</th>
                    <th>開催日時</th>
                    <th>操作</th>
                </tr>

                <?php
                require('./DatabaseManager.php');
                $dbm = new DatabaseManager();

                session_start();

                if(isset($_SESSION['isLogin'])){
                    if($_SESSION['isLogin'] != true){
                        header('Location: 1.php');
                    }
                }else{
                    header('Location: 1.php');
                }

                $cols = $dbm->GetAllEventInfo();
                foreach ($cols as $data) {
                    $event_name = $data['event_name'];
                    $event_date = $data['event_date'];
                    $event_id = $data['event_id'];

                    echo '<tr>';
                    echo '<td>' . $event_date . '</td>' . '<td>' . $event_name . "</td>";
                    echo '<td><form class="evevt_button" method="POST" action="?">';
                    echo '<input type="submit" class="form-submit-button" name=' . $event_id . ' value="参加申請" formaction="7.php">';
                    echo '<input type="submit" class="form-submit-button" name=' . $event_id . ' value="詳細" formaction="8.php">';
                    echo '<input type="hidden" name="event_id" value=' . $event_id . '>';
                    echo '</form></td>';

                    echo '</tr>';
                }

                $is_admin = false;
                if (isset($_SESSION['is_admin'])) {
                    if ($_SESSION['is_admin'] == true) {
                        $is_admin = true;
                    }
                }
                ?>
            </table>
        </div>
    </div>
    <form method="POST">
        <div class="buttons">
            <div>
                <?php
                if ($is_admin) {
                    echo '<input type="button" class="form-submit-button" name="admin_create_event" onclick="location.href = \'9.php\'" value="イベント作成" >';
                }
                ?>
            </div>
            <div>
                <?php
                if ($is_admin) {
                    echo '<input type="button" class="form-submit-button" name="member_list_page" onclick="location.href = \'5.php\'" value="部員名簿">';
                }
                ?>
            </div>
            <div>
                <input type='button' class="form-submit-button" onclick="location.href = '4.php'" name="member_info_regisration" value="部員情報登録">
            </div>
        </div>
    </form>
</body>

</html>