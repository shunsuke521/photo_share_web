<?php
//ログイン認証・自動ログアウト
//ログインしている場合
if (!empty($_SESSION['login_date'])) {
  debug('ログイン済みユーザーです');

  //現在日時が最終ログイン日時＋有効期限を超えているか確認
  if (($_SESSION['login_date'] + $_SESSION['login_limit']) < time()) {
    //超えていた場合
    debug('ログイン有効期限オーバーです');

    //セッションを削除（ログアウトする）
    session_destroy();
    //ログインページへ遷移
    header("Location:login.php");
  }else {
    //超えていなかった場合
    debug('ログイン有効期限以内です');
    //最終ログイン日時を現在日時に更新
    $_SESSION['login_date'] = time();

    //現在実行中のスクリプトファイル名がlogin.phpの場合
    //$_SERVER['PHP_SELF']はドメインからのパスを変えすため、今回だと「/web_service_output/index.php」が返ってくる
    //さらにbasename関数によってファイル名だけを取り出す
    if (basename($_SERVER['PHP_SELF']) === 'login.php' || basename($_SERVER['PHP_SELF']) === 'top.php' || basename($_SERVER['PHP_SELF']) === 'signup.php') {
    debug('メインページへ遷移');
    header("Location:index.php");
    }
  }
}else {
  //ログインされていなかった場合
  if (basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'top.php'&& basename($_SERVER['PHP_SELF']) !== 'signup.php') {
  debug('未ログインユーザーです');
  header("Location:top.php");
  }
}



 ?>
