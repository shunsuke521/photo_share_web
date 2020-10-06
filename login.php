<?php
require('function.php');

debug('---------------------------------------');
debug('---ログインページ---');
debug('---------------------------------------');
debugLogStart();

//ログイン認証
require('auth.php');

//post送信されていた場合
if (!empty($_POST)) {
  debug('POST送信があります（ログイン）');

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;//if文のショートハンド（略記法）という書き方

  //各種バリデーション
  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if (empty($err_msg)) {
    //その他バリデーション
    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    if (empty($err_msg)) {
      debug('バリデーションOK（ログインpost送信）');

      try {
        $dbh = dbConnect();
        $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        debug('クエリ結果（ログイン）の中身：'.print_r($result,true));

        //パスワード照合  //passwoord_verifyはhash化したパスワードと入力されたパスワードの照合を行う関数
        if (!empty($result) && password_verify($pass, array_shift($result))) {
          //クエリ結果の値（passwoordとid）が空でなく、抽出結果と入力したパスワードが一致した場合
          debug('パスワードが一致しました');

          //ログイン有効期限(デフォルトを1時間とする)
          $sesLimit = 60*60;
          //最終ログイン日時を現在日時に
          $_SESSION['login_date'] = time();//time関数は1970年1月1日00：00：00を0として、1秒経過するごとに1ずつ増加させた値が入る

          //ログイン保持にチェックがある場合
          if ($pass_save) {
            debug('ログイン保持にチェックがあります');
            //ログイン有効期限を30日にセット
            $_SESSION['login_limit'] = $sesLimit*24*30;
          }else {
            debug('ログイン保持にチェックはありません。');
            $_SESSION['login_limit'] = $sesLimit;
          }
          //ユーザーIDを格納($resultの中身があることはif文の条件式で確認済み)
          $_SESSION['user_id'] = $result['id'];

          debug('セッション変数の中身：'.print_r($_SESSION,true));
          debug('メインページへ遷移');
          header("Location:index.php");
        }else {
          debug('パスワードが一致しません');
          $err_msg['common'] = MSG11;
        }
      } catch (Exception $e) {
        error_log('エラー発生（ログイン時）：'.$e->getMessage());
        $err_msg['common'] = MSG08;
      }

    }
  }
debug('画面表示処理終了-----------------------------');
}



 ?>
<?php
$siteTitle = "Login";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>

  <div class="input-form">
    <h2 class="page-name">ログイン</h2>
    <form class="" action="" method="post">
      <div class="form-group">
        <div class="vali"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
        <div class="vali"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></div>
        <input type="text" name="email" placeholder="メールアドレス" value="">
      </div>
      <div class="form-group">
        <div class="vali"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></div>
        <input type="password" name="pass" placeholder="パスワード" value="">
        <input type="checkbox" name="pass_save"> 次回から自動でログイン
      </div>
        <input class="" type="submit" value="ログイン">
    </form>
  </div>





<?php
require('footer.php');
 ?>
