<?php
// データベースの接続情報
define( 'DB_HOST', ホスト名);
define( 'DB_USER', ユーザ名);
define( 'DB_PASS', パス);
define( 'DB_NAME', DB名);

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$clean = array();

session_start();

if( !empty($_POST['form_button']) ) {
	
	// 表示名の入力チェック
	if( empty($_POST['name']) ) {
		$error_message[] = '※ニックネームを入力してください！';
	} else {
		$clean['name'] = htmlspecialchars( $_POST['name'], ENT_QUOTES);
        //セッションにニックネームを保存
        $_SESSION['name'] = $clean['name'];
    }
	
	// メッセージの入力チェック
	if( empty($_POST['message']) ) {
		$error_message[] = '※内容を入力してください！';
	} else {
		$clean['message'] = htmlspecialchars( $_POST['message'], ENT_QUOTES);
    }
    //入力
    if( empty($error_message) ) {
        // データベースに接続
        $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
        // 接続エラーの確認
        if( $mysqli->connect_errno ) {
            $error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
        } else {
            // 文字コード設定
            $mysqli->set_charset('utf8');
            // 書き込み日時を取得
            $now_date = date("Y-m-d H:i:s");
            // データを登録するSQL作成
            $sql = "INSERT INTO post_list (name, message, post_date) VALUES ( '$clean[name]', '$clean[message]', '$now_date')";
            // データを登録
            $res = $mysqli->query($sql);

            if( $res ) {
                $_SESSION['success_message'] = '書き込みました!';
            } else {
                $error_message[] = '書き込みに失敗しました';
            }
            // データベースの接続を閉じる
            $mysqli->close();
        }
        header('Location: ./');
    }
}
//出力
// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
	// ここにデータを取得する処理が入る
    $sql = "SELECT name,message,post_date FROM post_list ORDER BY post_date DESC";
	$res = $mysqli->query($sql);
	
	if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
	$mysqli->close();
}
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <meta http-equiv = "X-UA-Compatible" content = "IE=edge">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>勉強のあしあと掲示板</title>
    <link href = "https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href = "https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
    <link href = "https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">
    <link rel = "stylesheet" href = "main.css">
</head>
<body>
    <!--今後つけてい機能：いいね、通報、返信、会員登録、通知、LINEと連携、メールと連携、カテゴリ選択、画像載せ、掲示板をユーザが作れる-->
    <div id = "st-wrapper">    
        <header>
            <h1>勉強のあしあと 掲示板</h1>
            <div id = "sub_title">
                勉強の記録や、おすすめの息抜き方法、やる気の出る名言など<br>
                匿名で自由に書き込める掲示板です<br>
                モチベーションの維持に役立ちましたら幸いです
            </div>
        </header>
        <div id = "caution_container">
            <i class = "fas fa-exclamation-circle"></i>
            <div id = "caution_text">
                ご利用ありがとうございます！
                投稿内容が不適切であると判断された場合、管理者により書き込みが削除される場合がございます。
                あらかじめご了承ください。
            </div>
        </div>
        <div id = "form_container">
             <!--成功-->
            <?php if( empty($_POST['form_button']) && !empty($_SESSION['success_message']) ): ?>
                <p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <!--失敗-->
            <?php if( !empty($error_message) ): ?>
                <ul class="error_message">
                <?php foreach( $error_message as $value ): ?>
                    <li><?php echo $value; ?></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <!--ここからform-->
            <form action = "" method = "POST">
                <div>
                    <label for = "name">ニックネーム</label>
                    <input type = "text" id = "name" class = "form_parts" name="name">
                </div>
                <div>
                    <label for = "message">内容</label>
                    <textarea id = "message" class = "form_parts" name="message" cols="50" rows="5"></textarea>
                </div>
                <input type = "submit" name = "form_button" id = "form_button"　value = "送信">
            </form>
        </div>
        <div id = "log_wrapper">
            <?php if( !empty($message_array) ): ?>
            <?php foreach( $message_array as $value ): ?>
            <div class = "log_container">
                <div class = "log_name"><?php echo $value['name']; ?></div>
                <div class = "log_time"><?php echo date('　　　Y年m月d日 H:i', strtotime($value['post_date'])); ?></div>
                <div class = "log_message"><?php echo $value['message']; ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <footer><small>🄫2021 <a href = "shirono000.net">Miku Shiraishi</a></small></footer>  
</body>
</html>
