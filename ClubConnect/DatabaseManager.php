<?php
/*
データベースを一元管理するクラス

# 利用方法
require('[DatabaseManager.php path]')
dbm = new DatabaseManager();
dbm->[関数名]

#利用上の注意
timestamp型はdate('Y-m-d H:i:s');

# 実装関数
## OpenDB():bool
    - ログイン用のデータベースを開く
    - return(成功:true, 失敗:false)

## OpenTeamDB():bool
    - 現在ログインしているチームのデータベースを開く
    - $_SESSION['teamName']からチームを判定
    - return(成功:true, 失敗:false)

## SetTeamData(string $team_name, string $team_pass, string $admin_mail, string $admin_pass):bool
    - チームを新規登録する
    - return(成功:true, 失敗:false)

## CheckTeamLogin(string $team_name, string $team_pass):bool
    - チームへのログインを判定する
    - return(ログイン成功:true, 失敗:false)

## CheckAdminLogin(string $admin_mail, string $admin_pass):bool
    - チームの管理者へのログインを判定する
    - return(ログイン成功:true, 失敗:false)

## GetAllEventInfo():bool|list[][]
    - イベントの一覧を出力する
    - return(成功:イベント情報を各行に含んだlist, 失敗:false)

## GetAllMemberInfo():bool|list[][]
    - メンバー情報の一覧を出力する
    - return(成功:メンバー情報を各行に含んだlist, 失敗:false)

## CreateEvent(string $event_date, string $event_name):bool|list[][]
    - イベント情報をdbに保存し、メンバーのメールアドレスリストを出力する
    - return(成功:メールアドレスリスト, 失敗:false)

## SetMemberData($member_name, $member_num, $member_course, $part, $home, $return_home, $member_age, $member_gen, $member_mail):bool
    - メンバー情報を保存する
    - return(成功:true, 失敗:false)

## CreateEventRequest(int $event_id, string $member_num, string $note):bool
    - イベント参加申請を登録する
    - return(成功:true, 失敗:false)

## GetAllEventReqInfo(int $event_id):bool|list
    - イベント参加申請を取得する
    - return(成功:イベント参加メンバーの名前とnoteを含む配列を出力, 失敗:false)

## LotteryMember(int $event_id):bool
    - イベントの参加申請を抽選により$lottery_numの数まで減らす
    - return(成功:true, 失敗:false)
*/


class DatabaseManager
{

    private $login_db; // ログイン用データベース
    private $current_team_db; // 現在開かれているチームのデータベース
    private $lottery_num = 10; // 抽選時に残す人数

    //データベースの起動
    public function OpenDB()
    {

        // login_dbの初期設定
        if ($this->login_db == null) {
            try {
                $this->login_db = new SQLite3("./database/login.db");
            } catch (Exception $e) {
                $errormessage = $e->getMessage();
                echo "$errormessage";
                echo "Login Databaseを開けませんでした";
                return false;
            }
        }

        // テーブルがあるか確認
        $result = $this->login_db->query("SELECT COUNT(*) FROM sqlite_master WHERE TYPE='table' AND name='login_table'");
        $cols = $result->fetchArray();

        // テーブルが存在しないとき
        if ($cols[0] == "0") {
            // テーブルの作成
            $this->login_db->query("CREATE TABLE login_table(
            team_name TEXT NOT NULL PRIMARY KEY,
            team_pass TEXT NOT NULL,
            admin_mail TEXT NOT NULL,
            admin_pass TEXT NOT NULL
            )");
        }

        if (!isset($_SESSION)) {
            session_start();
        }

        return true;
    }

    // teamデータベースを開く
    public function OpenTeamDB()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['teamName'])) {
            $team_db_name = $_SESSION['teamName'];

            try {
                $this->current_team_db = new SQLite3("./database/$team_db_name.db");
            } catch (Exception $e) {
                $errormessage = $e->getMessage();
                echo "$errormessage";
                echo "Team Databaseを開けませんでした";
                return false;
            }

            // member_info,event_req,event_infoのテーブルがあるか確認し、なければ生成する
            // テーブルがあるか確認
            $result = $this->current_team_db->query("SELECT COUNT(*) FROM sqlite_master WHERE TYPE='table' AND name='member_info'");
            $cols = $result->fetchArray();

            // member_infoテーブルが存在しないとき
            if ($cols[0] == "0") {
                // テーブルの作成
                $this->current_team_db->query("CREATE TABLE member_info(
                member_id INTEGER PRIMARY KEY,
                member_name TEXT NOT NULL,
                member_num TEXT NOT NULL,
                member_course TEXT NOT NULL,
                member_age INTEGER NOT NULL,
                member_gen TEXT NOT NULL,
                member_mail TEXT,
                part TEXT,
                home TEXT,
                return_home TEXT,
                created_date TIMESTAMP NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP, 'localtime')),
                update_date TIMESTAMP NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP, 'localtime')),
                delete_flag INTEGER DEFAULT 0
            )");
            }


            // event_infoテーブル取得
            $result = $this->current_team_db->query("SELECT COUNT(*) FROM sqlite_master WHERE TYPE='table' AND name='event_info'");
            $cols = $result->fetchArray();

            // テーブルが存在しないとき
            if ($cols[0] == "0") {
                // テーブルの作成
                $this->current_team_db->query("CREATE TABLE event_info(
                event_id INTEGER PRIMARY KEY,
                event_name TEXT NOT NULL,
                event_date TEXT NOT NULL,
                created_date TIMESTAMP NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP, 'localtime')),
                update_date TIMESTAMP NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP, 'localtime')),
                delete_flag INTEGER DEFAULT 0
            )");
            }


            // event_reqテーブル取得
            $result = $this->current_team_db->query("SELECT COUNT(*) FROM sqlite_master WHERE TYPE='table' AND name='event_req'");
            $cols = $result->fetchArray();

            // テーブルが存在しないとき
            if ($cols[0] == "0") {
                // テーブルの作成
                $this->current_team_db->query("CREATE TABLE event_req(
                req_id INTEGER PRIMARY KEY,
                event_id INTEGER,
                member_id INTEGER,
                note TEXT,
                created_date TIMESTAMP NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP, 'localtime')),
                delete_flag INTEGER DEFAULT 0
            )");
            }

            return true;
        }

        return false;
    }

    // 新規チーム登録
    public function SetTeamData($team_name, $team_pass, $admin_mail, $admin_pass)
    {
        if ($this->OpenDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }
        // SQL作成、設定
        $prepare = $this->login_db->prepare("INSERT INTO login_table(team_name, team_pass, admin_mail, admin_pass)
                                                    VALUES(:team_name, :team_pass, :admin_mail, :admin_pass)");
        $prepare->bindValue(':team_name', $team_name, SQLITE3_TEXT);
        $prepare->bindValue(':team_pass', $team_pass, SQLITE3_TEXT);
        $prepare->bindValue(':admin_mail', $admin_mail, SQLITE3_TEXT);
        $prepare->bindValue(':admin_pass', $admin_pass, SQLITE3_TEXT);
        // SQL登録
        $result = $prepare->execute();

        if ($result == false) {
            echo "データ登録失敗";
            return false;
        }
        return true;
    }

    // ログイン判定
    public function CheckTeamLogin($team_name, $team_pass)
    {
        if ($this->OpenDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }
        // ログイン判定
        $result = $this->login_db->query("SELECT EXISTS(SELECT *
        FROM login_table
        WHERE team_name = '$team_name'
        AND team_pass = '$team_pass');");
        // 一致するデータがあればtrueを返す
        if ($result->fetchArray()[0] == "1") {
            return true;
        }
        return false;
    }

    // Adminログイン判定
    public function CheckAdminLogin($admin_mail, $admin_pass)
    {
        if ($this->OpenDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }

        $teamname = $_SESSION['teamName'];

        $result = $this->login_db->query("SELECT EXISTS(SELECT *
        FROM login_table
        WHERE team_name = '$teamname'
        AND admin_mail = '$admin_mail'
        AND admin_pass = '$admin_pass');");
        // 一致するデータがあればtrueを返す
        if ($result->fetchArray()[0] == "1") {
            return true;
        }
        return false;
    }

    // イベント一覧取得
    public function GetAllEventInfo()
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }

        $event_array = array();

        $result = $this->current_team_db->query("SELECT * FROM event_info WHERE delete_flag = 0 
        ORDER BY event_date DESC;");

        while ($cols = $result->fetchArray()) {
            array_push($event_array, $cols);
        }

        return $event_array;
    }


    // 部員情報一覧取得
    public function GetAllMemberInfo()
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }

        $member_array = array();

        $result = $this->current_team_db->query("SELECT * FROM member_info;");

        while ($cols = $result->fetchArray()) {
            array_push($member_array, $cols);
        }

        return $member_array;
    }


    // イベント参加申請取得 有効なもの
    public function GetAllEventReqInfo($event_id)
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }

        $event_reqs = array();

        $result = $this->current_team_db->query("SELECT * FROM event_req WHERE event_id = '$event_id' AND delete_flag = 0;");
        while ($cols = $result->fetchArray()) {
            array_push($event_reqs, $cols);
        }

        $list = array();

        foreach ($event_reqs as $value) {
            $member_id = $value['member_id'];
            $result = $this->current_team_db->query("SELECT * FROM member_info WHERE member_id = $member_id");
            $resultarray = $result->fetchArray();
            $name = $resultarray['member_name'];
            $id = $resultarray['member_num'];
            $note = $value['note'];

            $reqlistcol = array("member_name" => $name, "note" => $note, "student_id" => $id);
            array_push($list, $reqlistcol);
        }

        return $list;
    }



    // イベント作成
    public function CreateEvent($event_date, $event_name)
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }
        // SQL作成、設定
        $prepare = $this->current_team_db->prepare("INSERT INTO event_info(event_name, event_date)
                                                    VALUES(:event_name, :event_date)");
        $prepare->bindValue(':event_name', $event_name, SQLITE3_TEXT);
        $prepare->bindValue(':event_date', $event_date, SQLITE3_TEXT);
        // SQL登録
        $result = $prepare->execute();
        if ($result == false) {
            echo "データ登録失敗";
            return false;
        }
        // 全部員メールアドレス取得
        $prepare = $this->current_team_db->prepare("SELECT member_mail FROM member_info;");
        $result = $prepare->execute();
        $mail_list = array();
        while ($data = $result->fetchArray()) {
            array_push($mail_list, $data);
        }
        if ($mail_list == false) {
            return true;
        }
        return $mail_list;
    }

    // 部員情報登録
    public function SetMemberData($member_name, $member_num, $member_course, $part, $home, $return_home, $member_age, $member_gen, $member_mail)
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }
        // SQL作成、設定
        $prepare = $this->current_team_db->prepare("INSERT INTO member_info(member_name, member_num, member_course, part, home, return_home, member_age, member_gen, member_mail)
                                                    VALUES(:member_name, :member_num, :member_course, :part, :home, :return_home, :member_age, :member_gen, :member_mail)");
        $prepare->bindValue(':member_name', $member_name, SQLITE3_TEXT);
        $prepare->bindValue(':member_num', $member_num, SQLITE3_TEXT);
        $prepare->bindValue(':member_course', $member_course, SQLITE3_TEXT);
        $prepare->bindValue(':part', $part, SQLITE3_TEXT);
        $prepare->bindValue(':home', $home, SQLITE3_TEXT);
        $prepare->bindValue(':return_home', $return_home, SQLITE3_TEXT);
        $prepare->bindValue(':member_age', $member_age, SQLITE3_TEXT);
        $prepare->bindValue(':member_gen', $member_gen, SQLITE3_TEXT);
        $prepare->bindValue(':member_mail', $member_mail, SQLITE3_TEXT);
        // SQL登録
        $result = $prepare->execute();
        if ($result == false) {
            echo "データ登録失敗";
            return false;
        }
        return true;
    }

    // 参加申請
    public function CreateEventRequest($event_id, $member_num, $note)
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }
        // membr_num(学生証番号)をmember_id(主キー)に変換
        $prepare = $this->current_team_db->prepare("SELECT member_id FROM member_info
                                            WHERE member_num = :member_num");
        $prepare->bindValue(':member_num', $member_num, SQLITE3_TEXT);
        $result = $prepare->execute();
        if ($result == false) {
            echo "データ登録失敗";
            return false;
        }

        $member_id = $result->fetchArray()['member_id'];

        // SQL作成、設定
        $prepare = $this->current_team_db->prepare("INSERT INTO event_req(event_id, member_id, note)
                                                    VALUES(:event_id, :member_id, :note)");
        $prepare->bindValue(':event_id', $event_id, SQLITE3_INTEGER);
        $prepare->bindValue(':member_id', $member_id, SQLITE3_TEXT);
        $prepare->bindValue(':note', $note, SQLITE3_TEXT);
        // SQL登録
        $result = $prepare->execute();
        if ($result == false) {
            echo "データ登録失敗";
            return false;
        }
        return true;
    }

    public function DeleteEvent($event_id)
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }
        //削除フラグを１にするSQL
        $prepare = $this->current_team_db->prepare("UPDATE event_info SET delete_flag = 1 WHERE event_id = :event_id");
        $prepare->bindValue(':event_id', $event_id, SQLITE3_INTEGER);
        //SQL実行
        $result = $prepare->execute();
        if ($result == false) {
            echo "データ登録失敗";
            return false;
        }
        return true;
    }


    public function LotteryMember($event_id)
    {
        if ($this->OpenTeamDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }

        $result = $this->current_team_db->query("SELECT count(event_id) FROM event_req WHERE event_id = '$event_id';");
        $req_num = $result->fetchArray()["count(event_id)"];

        $num = $req_num - $this->lottery_num;

        if ($num > 0) {
            $result = $this->current_team_db->query("UPDATE event_req 
            SET delete_flag = 1 
            WHERE req_id 
            IN (SELECT req_id 
            FROM event_req 
            WHERE event_id = '$event_id'
            ORDER BY RANDOM() 
            LIMIT $num);");
            if ($result == false) {
                echo "データ登録更新失敗";
                return false;
            }
        }

        return true;
    }

    public function GetAllTeamName()
    {
        if ($this->OpenDB() == false) {
            echo "データベースを開けませんでした";
            return false;
        }

        $teamname_array = array();

        $result = $this->login_db->query("SELECT team_name FROM login_table;");

        while ($cols = $result->fetchArray()) {
            array_push($teamname_array, $cols);
        }

        return $teamname_array;
    }
}
