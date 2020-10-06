<?php
require('function.php');

debug('---------------------------------------');
debug('---退会ページ---');
debug('---------------------------------------');
debugLogStart();

//post送信されていた場合
if (!empty($_POST)) {
  debug('POST送信があります（退会ページ）');

  try {
    $dbh = dbConnect();
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :user_id';
    $sql2 = 'UPDATE posts SET delete_flg = 1 WHERE user_id = :user_id';
    $sql3 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :user_id';
    $data = array(':user_id' => $_SESSION['user_id']);
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);
    $stmt3 = queryPost($dbh, $sql3, $data);

    //クエリ実行成功の場合
    if ($stmt1 && $stmt2 && $stmt3) {
      //セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('退会完了画面へ遷移');
      header("Location:withdraw.php");
    }else {
      debug('クエリが失敗しました（退会）');
      $err_msg['common'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG08;
  }

}
 ?>
<?php
$siteTitle = "Withdraw";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>
 <?php
require('sidebar.php');
  ?>

<?php if(!empty($_SESSION)){ ?>
  <div class="withdraw-wrapper">
    <div class="input-form">
      <form class="withdraw" action="" method="post">
          <input class="" name="withdraw" type="submit" value="退会する">
      </form>
    </div>
      <a href="index.php">&lt; メインページへ戻る</a>
  </div>
<?php }else{ ?>
    <div class="after-withdraw">
      <h3>退会処理が完了しました。</h3>
      <p>ご利用いただきありがとうございました。</p>
      <a href="top.php">トップページへ戻る</a>
    </div>
<?php } ?>


<?php
require('footer.php');
 ?>
