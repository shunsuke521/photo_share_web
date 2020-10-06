<?php
require('function.php');

debug('---------------------------------------');
debug('---パスワード変更ページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

//post送信されていた場合
if (!empty($_POST)) {
  debug('POST送信があります（パスワード変更）');

  //変数にユーザー情報を代入
  $old_pass = $_POST['old_pass'];
  $new_pass = $_POST['new_pass'];
  $re_pass = $_POST['re_pass'];

  //全ての項目が入力されているかチェック
  validRequired($old_pass, 'old_pass');
  validRequired($new_pass, 'new_pass');
  validRequired($re_pass, 're_pass');

  //入力された現在のパスワードが正しいか確認
  if (empty($err_msg)) {
    debug('全ての欄が入力されています');

    try {
      $dbh = dbConnect();
      $sql = 'SELECT password FROM users WHERE id = :u_id AND delete_flg = 0';
      $data = array(':u_id' => $_SESSION['user_id']);
      $stmt = queryPost($dbh, $sql, $data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果（パスワード変更）の中身：'.print_r($result,true));

      if (!empty($result) && password_verify($old_pass, array_shift($result))) {
        //クエリ結果の値が空でなく、抽出結果と入力したパスワードが一致した場合
        debug('パスワードが一致しました');
        debug('次の処理に移ります');
      }else {
        //入力されたパスワードが一致しなかった場合
        debug('入力された古いパスワードが一致しませんでした');
        $err_msg['old_pass'] = MSG13;
      }
    } catch (Exception $e) {
      error_log('エラー発生（パスワード変更時）：'.$e->getMessage());
      $err_msg['common'] = MSG08;
    }

  }else {
    debug('入力されていない欄があります');
  }

  //パスワード更新の処理
  //新しいパスワードのバリデーション
  debug('新しいパスワードと確認用パスワードのバリデーションをします');
  validMaxLen($new_pass, 'new_pass');
  validMinLen($new_pass, 'new_pass');
  validHalf($new_pass, 'new_pass');

  if (empty($err_msg)) {
      //新しいパスワードと確認用のパスワードが合っているか確認
      validMatch($new_pass, $re_pass, 're_pass');

      if (empty($err_msg)) {
        //新しいパスワードと確認用が一致しているので新しいパスワードに更新
        try {
          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :re_pass WHERE id = :u_id AND delete_flg = 0';
          $data = array(':re_pass' => password_hash($re_pass, PASSWORD_DEFAULT), ':u_id' => $_SESSION['user_id']);
          $stmt = queryPost($dbh, $sql, $data);

          if ($stmt) {
            debug('パスワードの更新に成功しました');
            $_SESSION['msg_success'] = SUC01;
            header("Location:index.php");
          }else {
            error_log('クエリに失敗しました（パスワード更新）');
            $err_msg['common'] = MSG08;
          }
        } catch (Exception $e) {
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG08;
        }

      }else {
        debug('入力された新しいパスワードのバリデーションを通れませんでした');
      }
  }
}



 ?>
<?php
$siteTitle = "PassChange";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>
<?php
require('sidebar.php');
 ?>

 <div class="input-form">
   <h2 class="page-name">パスワード変更</h2>
   <form class="" action="" method="post">
     <div class="form-group">
       <div class="vali"><?php if(!empty($err_msg['old_pass'])) echo $err_msg['old_pass']; ?></div>
       <input type="password" name="old_pass" placeholder="現在のパスワード" value="">
     </div>
     <div class="form-group">
       <div class="vali"><?php if(!empty($err_msg['new_pass'])) echo $err_msg['new_pass']; ?></div>
       <input type="password" name="new_pass" placeholder="新しいパスワード" value="">
     </div>
     <div class="form-group">
       <div class="vali"><?php if(!empty($err_msg['re_pass'])) echo $err_msg['re_pass']; ?></div>
       <input type="password" name="re_pass" placeholder="新しいパスワード(確認)" value="">
     </div>
       <input class="" type="submit" value="変更する">
   </form>
 </div>





<?php
require('footer.php');
 ?>
