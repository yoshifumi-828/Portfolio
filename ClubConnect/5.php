<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>部員名簿</title>
    <link rel="stylesheet" href="main.css">
    <link rel="icon" type="image/png" href="./contens/icon.png">
    <style>
        .member_table{
            padding-top: 30px;
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
                <h1>部員名簿</h1>
            </div>
            <div class="adminlogin">
            </div>
        </div>
    </header>
    <div class="body">
        <div class="member_table">
            <table class="standard_table">
                <colgroup>
                    <col width="12%">
                    <col width="12%">
                    <col width="12%">
                    <col width="12%">
                    <col width="8%">
                    <col width="8%">
                    <col width="8%">
                    <col width="28%">
                </colgroup>
                <tr>
                    <th>名前</th>
                    <th>学籍番号</th>
                    <th>アルバイト</th>
                    <th>帰省先</th>
                    <th>帰省の有無</th>
                    <th>年齢</th>
                    <th>性別</th>
                    <th>メールアドレス</th>
                </tr>
                <?php
                session_start();

                if (isset($_SESSION['isLogin'])) {
                    if ($_SESSION['isLogin'] != true) {
                        header('Location: 1.php');
                    }else if($_SESSION['is_admin'] != true){
                        header('Location: 3.php');
                    }
                }else{
                    header('Location: 1.php');
                }

                require 'DatabaseManager.php';
                $dbm = new DatabaseManager();

                $member_array = $dbm->GetAllMemberInfo();

                $tag = ['member_name', 'member_num', 'part', 'home', 'return_home', 'member_age', 'member_gen', 'member_mail'];

                foreach ($member_array as $member) {
                    echo "<tr>";
                    foreach ($tag as $info) {
                        echo "<td>", $member[$info], "</td>";
                    }
                    echo "</tr>";
                }

                ?>
            </table>
        </div>
        <div class="buttons">
            <div>
                <input type="button" class="form-submit-button" value="戻る" onClick="location.href='3.php'">
            </div>
        </div>
    </div>

</body>

</html>