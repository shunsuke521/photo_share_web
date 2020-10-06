<?php
require('function.php');

debug('---------------------------------------');
debug('---evaluationAjax---');
debug('---------------------------------------');
debugLogStart();

//postがあり、ユーザーIDがある場合
if (isset($_POST['postId']) && isset($_SESSION['user_id'])) { //postIdはfooter.phpのjqueryで設定した値
  debug('post送信があります');
  $p_id = $_POST['postId'];
  debug('投稿ID：'.$p_id);
  $eval = $_POST['postEval'];
  debug('$evalの中身：'.$eval);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM evaluation WHERE post_id = :post_id AND user_id = :user_id';
    $data = array(':post_id' => $p_id, ':user_id' => $_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);

    if (!empty($resultCount)) {
      //レコードを削除する
      debug('評価を削除するよ');
      $sql = 'DELETE FROM evaluation WHERE post_id = :post_id AND user_id = :user_id';
      $data = array(':post_id' => $p_id, ':user_id' => $_SESSION['user_id']);

      $stmt = queryPost($dbh, $sql, $data);
    }else {
      //レコードを挿入する
      debug('評価するよ');
      $sql = 'INSERT INTO evaluation(post_id, user_id, eval, create_date) VALUES (:post_id, :user_id, :eval, :create_date)';
      $data = array(':post_id' => $p_id, ':user_id' => $_SESSION['user_id'], ':eval' => $eval, ':create_date' => date('Y-m-d H:i:s'));

      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

 ?>
