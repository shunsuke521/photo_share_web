<?php
require('function.php');

debug('---------------------------------------');
debug('---ユーザー登録ページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');


//post送信されていた場合
if (!empty($_POST)) {
  debug('post送信があります');

  //変数にユーザー情報を代入
  $user_name = $_POST['user_name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $re_pass = $_POST['re_pass'];

  //未入力チェック
  validRequired($user_name,'user_name');
  validRequired($email,'email');
  validRequired($pass,'pass');
  validRequired($re_pass,'re_pass');

  //この段階でエラーメッセージの配列が空であれば全て入力はされている
  if (empty($err_msg)) {

    //ユーザーネーム重複チェック
    validNameDup($user_name);
    //ユーザーネームの最大文字数チェック
    validMaxLen($user_name, 'user_name', 10);

    //emailの形式チェック
    validEmail($email,'email');
    //emailの最大文字数チェック
    validMaxLen($email,'email');
    //emailの重複チェック
    validEmailDup($email);

    //パスワードの半角英数字チェック
    validHalf($pass,'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass,'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass,'pass');

    if (empty($err_msg)) {
      //パスワードとパスワード再入力が合っているかチェック
      validMatch($pass, $re_pass, 're_pass');

      if (empty($err_msg)) {
        //全てのバリデーションを通してエラーメッセージが空であれば
        //usersテーブルに入力した情報を登録
        //例外処理
        try {
          $dbh = dbConnect();
          $sql = 'INSERT INTO users (user_name, email, password, create_date) VALUES(:user_name, :email, :password, :create_date)';
          $data = array(':user_name' => $user_name, ':email' => $email, ':password' => password_hash($pass, PASSWORD_DEFAULT), ':create_date' => date('Y-m-d H:i:s'));
          $stmt = queryPost($dbh, $sql, $data);

          //クエリ成功の場合
          if ($stmt) {
            //ログイン有効期限（ユーザー登録時はチェックボックスを設けていないのでデフォルト1時間）
            $sesLimit = 60*60;
            //最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身(ユーザー登録後)：'.print_r($_SESSION,true));

            $_SESSION['msg_success'] = SUC02;
            header("Location:index.php");
          }else {
            error_log('クエリに失敗しました');
            $err_msg['common'] = MSG08;
          }

        } catch (Exception $e) {
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG08;
        }

      }
    }
  }
}


 ?>
<?php
$siteTitle = "Signup";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>

  <div class="input-form">
    <h2 class="page-name">ユーザー登録</h2>
    <div class="common-error"><?php if(!empty($_POST['common'])) echo $_POST['common']; ?></div>
    <form class="" action="" method="post">
      <div class="form-group">
        <div class="vali"><?php if(!empty($err_msg['user_name'])) echo $err_msg['user_name']; ?></div>
        <input type="text" name="user_name" placeholder="ユーザーネーム" value="<?php if(!empty($_POST['user_name'])) echo $_POST['user_name']; ?>">
      </div>
      <div class="form-group">
        <div class="vali"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></div>
        <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
      </div>
      <div class="form-group">
        <div class="vali"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></div>
        <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
      </div>
      <div class="form-group">
        <div class="vali"><?php if(!empty($err_msg['re_pass'])) echo $err_msg['re_pass']; ?></div>
        <input type="password" name="re_pass" placeholder="パスワード(確認)" value="">
      </div>
        <input class="" type="submit" value="登録する">
    </form>
  </div>

<?php
require('footer.php');
 ?>
