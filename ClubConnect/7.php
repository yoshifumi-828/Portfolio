<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント参加申請</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">
    <style>
        .buttons {
            padding-top: 20px !important;
        }

        .form {
            padding-top: 50px;
        }
    </style>
</head>

<!-- セッションで必要なもの：イベント名SESSION[even_name] イベントID SESSION[event_id] -->

<body>

    <?php
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

    if (isset($_POST['event_id'])) {
        $event_id = $_POST['event_id'];
    } else {
        echo "開きなおしてください";
    }
    ?>

    <header>
        <div class="header_contents">
            <div class="titlepic">
                <img src="./contens/header.png">
            </div>
            <div class="pagetitle">
                <h1>イベント参加申請</h1>
            </div>
            <div class="adminlogin">

            </div>
        </div>
    </header>

    <div class="body">

        <form action="" method="post">
            <?php
            echo  '<input type="hidden" name="event_id" value=' . $event_id . '>'
            ?>
            <div class="form">
                <div>
                    学生証番号<input type="text" class="form-text" name="student_id_number" required>
                </div>
                <div>
                    記入欄<textarea name="entry_column" class="form-textarea"></textarea>
                </div>
                <div class="buttons">
                    <div>
                        <input type="submit" class="form-submit-button" value="申請" required>
                    </div>
                    <div>
                        <input type="button" class="form-submit-button" value="戻る" onClick="location.href='3.php'">
                    </div>
                </div>
            </div>
        </form>
        <?php
        //入力された後
        if (isset($_POST['student_id_number'])) {

            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['student_number'] = $_POST['student_id_number'];
            $member_num = $_POST['student_id_number'];
            $_SESSION['note'] = $_POST['entry_column'];
            $note = $_POST['entry_column'];
            require 'DatabaseManager.php';
            $db_m = new DatabaseManager();

            $flag = false;

            // メンバー情報に含まれるかチェックする
            $meminfo_array = $db_m->GetAllMemberInfo();
            foreach ($meminfo_array as $col) {
                if ($col['member_num'] == $member_num) {
                    $flag = true;
                    break;
                }
            }

            // 同じ学生証番号で登録されていないかチェックする
            $reqinfo_array = $db_m->GetAllEventReqInfo($event_id);
            foreach ($reqinfo_array as $col) {
                if ($col['student_id'] == $member_num) {
                    $flag = false;
                    break;
                }
            }

            if ($flag == true) {
                $is_add_request = $db_m->CreateEventRequest($event_id, $member_num, $note);

                if ($is_add_request) {
                    header('Location:3.php');
                    exit();
                } else {
                    echo '<p class="error_text">登録できませんでした</p>';
                }
            } else {
                echo "<p class='error_text'>メンバー登録済みで参加申請を行っていない学生証番号を入力してください</p>";
            }
        }

        ?>
    </div>
</body>

</html>