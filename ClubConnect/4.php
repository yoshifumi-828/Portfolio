<?php
// このファイルだけエラーが出たので処理を先頭に記述

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

if (isset($_POST['member_name'])) {
    // code...
    $member_name = $_POST['member_name'];
    $member_num = $_POST['member_number​'];
    $member_course = $_POST['member_course​'];
    $part = $_POST['member_job'];
    $home = $_POST['member_home'];
    $return_home = $_POST['return_home'];
    $member_age = $_POST['member_age​'];
    $member_gen = $_POST['member_gender'];
    $member_mail = $_POST['member_mail'];

    require('DatabaseManager.php');
    $dbm = new DatabaseManager();

    $flag = true;

    // 同じ学生証番号が登録されていないか判定する
    $meminfo_array = $dbm->GetAllMemberInfo();
    foreach ($meminfo_array as $col) {
        if ($col['member_num'] == $member_num) {
            $flag = false;
            break;
        }
    }

    if ($flag == true) {
        $reg_dbm = $dbm->SetMemberData($member_name, $member_num, $member_course, $part, $home, $return_home, $member_age, $member_gen, $member_mail);
        if ($reg_dbm) {
            header('Location:3.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メンバー情報登録</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">
    <style>
        .form_table {
            padding: 0px 100px;
            padding-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .explain {
            background-color: #eee;
        }

        .content,
        .explain {
            border: solid 1px gray;
            padding: 10px;
        }

        label {
            padding-right: 20px;
        }

        .buttons {
            padding-top: 20px !important;
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
                <h1>メンバー情報登録</h1>
            </div>
            <div class="adminlogin">

            </div>
        </div>
    </header>

    <div class="body">
        <div class="form_table">
            <form action="" method="post">
                <table>
                    <colgroup>
                        <col width="30%">
                        <col width="70%">
                    </colgroup>
                    <tr>
                        <td class="explain">名前</td>
                        <td class="content"><input type="text" class="form-text" name="member_name" required></td>
                    </tr>
                    <tr>
                        <td class="explain">学籍番号</td>
                        <td class="content"><input type="text" class="form-text" name="member_number​" required></td>
                    </tr>
                    <tr>
                        <td class="explain">学部</td>
                        <td class="content"><input type="text" class="form-text" name="member_course​" required></td>
                    </tr>
                    <tr>
                        <td class="explain">アルバイト先</td>
                        <td class="content"><input type="text" class="form-text" name="member_job"></td>
                    </tr>
                    <tr>
                        <td class="explain">帰省先</td>
                        <td class="content"><input type="text" class="form-text" name="member_home"></td>
                    </tr>
                    <tr>
                        <td class="explain">帰省の有無</td>
                        <td class="content">
                            <div class="form-radio">
                                <label>
                                    <input type="radio" name="return_home" value="有">
                                    <span class="form-radio-name">
                                        帰省する
                                    </span>
                                </label>
                                <label>
                                    <input type="radio" name="return_home" value="無">
                                    <span class="form-radio-name">
                                        帰省しない
                                    </span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="explain">年齢</td>
                        <td class="content"><input type="text" class="form-text" name="member_age​" required></td>
                    </tr>
                    <tr>
                        <td class="explain">性別</td>
                        <td class="content"><input type="text" class="form-text" name="member_gender" required></td>
                    </tr>
                    <tr>
                        <td class="explain">メールアドレス</td>
                        <td class="content"><input type="text" class="form-text" name="member_mail" required></td>
                    </tr>
                </table>
                <div class="buttons">
                    <div>
                        <input type="submit" class="form-submit-button" value="登録" required>
                    </div>
                    <div>
                        <input type="button" class="form-submit-button" value="戻る" onClick="location.href='3.php'">
                    </div>
                </div>
            </form>
        </div>
        <?php
        if (isset($_POST['member_name'])) {
            echo '<p class="error_text">登録されていない学生証番号を入力してください</p>';
        }
        ?>
    </div>
</body>

</html>