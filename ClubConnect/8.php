<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント一覧</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">
    <style>
        tr .c_left {
            text-align: left;
        }

        .member_table {
            padding-top: 40px;
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
            </div>
        </div>
    </header>
    <div class="body">
        <div class="member_table">

            <?php  //DBから参加申請者一覧とメモを取得
            require("./DatabaseManager.php");
            $db_m = new DatabaseManager();

            if (!isset($_SESSION)) {
                session_start();
            }

            if (isset($_SESSION['isLogin'])) {
                if ($_SESSION['isLogin'] != true) {
                    header('Location: 1.php');
                }
            } else {
                header('Location: 1.php');
            }

            $is_admin = false;
            if (isset($_SESSION['is_admin'])) {
                if ($_SESSION['is_admin'] == true) {
                    $is_admin = true;
                }
            }

            if (isset($_POST['event_id'])) {
                $event_id = $_POST['event_id'];
            } else {
                echo '<p class="error_text">イベント一覧から開きなおしてください　<a href="3.php">もどる</a></p>';
                exit();
            }

            // delete 処理
            if (isset($_POST['delete_event'])) {
                $event_id = $_POST['event_id'];
                $result = $db_m->DeleteEvent($event_id);
                if ($result == true) {
                    header('Location:3.php');
                    exit();
                } else {
                    echo '<p class="error_text">削除に失敗しました</p>';
                }
            }

            // 抽選処理
            if (isset($_POST['lottery'])) {
                $event_id = $_POST['event_id'];
                $result = $db_m->LotteryMember($event_id);
                if ($result == true) {
                    echo '<p class="info_text">抽選に成功しました</p>';
                } else {
                    echo '<p class="error_text">抽選に失敗しました</p>';
                }
            }
            ?>

            <p class="center">
                <?php
                $result = $db_m->GetAllEventReqInfo($event_id);

                echo '参加申請者一覧　現在' . count($result) . '人';
                ?>
            </p>


            <table class="standard_table">
                <colgroup>
                    <col width="30%">
                    <col width="70%">
                </colgroup>
                <?php
                //DBから申請者一覧を取得
                foreach ($result as $value) {
                    echo '<tr>';
                    echo '<td>' . $value['member_name'] . '</td><td class="c_left">' . $value['note'] . "</td>";
                    echo '</tr>';
                }
                ?>
            </table>
        </div>

        <form action="" method="post">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <div class="buttons">
                <div>
                    <?php
                    if ($is_admin) {
                        echo '<input type="submit" name="lottery" class="form-submit-button" value="抽選(10人)">';
                    } ?>
                </div>
                <div>
                <?php
                    if ($is_admin) {
                        echo '<input type="submit" name="delete_event" class="form-submit-button" value="削除">';
                    } ?>
                </div>
                <div>
                    <input type="button" class="form-submit-button" value="戻る" onClick="location.href='3.php'">
                </div>
            </div>
        </form>
    </div>
</body>

</html>