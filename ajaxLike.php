<?php
require('function.php');

debug('---------------------------------------');
debug('---likeAjax---');
debug('---------------------------------------');
debugLogStart();

//postがあり、ユーザーIDがある場合
if (isset($_POST['postId']) && isset($_SESSION['user_id'])) { //postIdはfooter.phpのjqueryで設定した値
  debug('post送信があります');
  debug('postの中身：'.print_r($_POST,true));
  $p_id = $_POST['postId'];
  debug('投稿ID：'.$p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM favorite WHERE post_id = :post_id AND user_id = :user_id';
    $data = array(':post_id' => $p_id, ':user_id' => $_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);

    if (!empty($resultCount)) {
      //レコードを削除する
      debug('お気に入りから削除するよ');
      $sql = 'DELETE FROM favorite WHERE post_id = :post_id AND user_id = :user_id';
      $data = array(':post_id' => $p_id, ':user_id' => $_SESSION['user_id']);

      $stmt = queryPost($dbh, $sql, $data);

    }else {
      //レコードを挿入する
      debug('お気に入り登録するよ');
      $sql = 'INSERT INTO favorite(post_id, user_id, create_date) VALUES (:post_id, :user_id, :create_date)';
      $data = array(':post_id' => $p_id, ':user_id' => $_SESSION['user_id'], ':create_date' => date('Y-m-d H:i:s'));

      $stmt = queryPost($dbh, $sql, $data);
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

 ?>
