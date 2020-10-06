<?php
require('function.php');

debug('---------------------------------------');
debug('---投稿詳細ページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
//DBから投稿データを取得
$viewData = getPostOne($p_id);
debug('$viewDataの中身：'.print_r($viewData,true));
//投稿者の名前を取得
$postUser = postUser($p_id);
debug('$postUserの中身：'.print_r($postUser,true));

//投稿の評価
$starCount = isEval($_SESSION['user_id'], $viewData['post_id']);
debug('投稿の評価：'.$starCount);

//表示する写真の取得
function photoData($key){
  debug('表示する写真を取得します');
  global $viewData;
  debug('$viewData[$key]の中身：'.$viewData[$key]);

  if (!empty($viewData[$key])) {
    return $viewData[$key];
  }else {
    return 'img/sample.png';
  }
}

$photo1 = photoData('photo1');
debug('$photo1の中身：'.$photo1);
$photo2 = photoData('photo2');
debug('$photo2の中身：'.$photo2);
$photo3 = photoData('photo3');
debug('$photo3の中身：'.$photo3);

//パラメータに不正な値が入っているかチェック
if (empty($viewData)) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}

//post送信されていた場合

 ?>
<?php
debug('---------------------------------------');
debug('---コメント処理---');
debug('---------------------------------------');

$myUserInfo = '';
$postInfo = '';
$commentData = '';

$commentData = getComment($p_id);
debug('取得したコメントデータ：'.print_r($commentData,true));

if(!empty($_POST)){
  debug('post送信があります（コメント）');

  $comment = (isset($_POST['comment'])) ? $_POST['comment'] : '';
  validMaxLen($comment, 'comment', 200);
  validRequired($comment, 'comment');

  if (empty($err_msg)) {
    debug('バリデーションOK');

    try {
      $dbh = dbConnect();
      $sql = 'INSERT INTO comments(user_id, post_id, comment, create_date) VALUES (:user_id, :post_id, :comment, :create_date)';
      $data = array(':user_id' => $_SESSION['user_id'], ':post_id' => $p_id, ':comment' => $_POST['comment'], ':create_date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        $_POST = array(); //postをクリア
        header("Location: " . $_SERVER['PHP_SELF'] .'?p_id='.$p_id);//自分自身に遷移する
      }
    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
}

?>
 <?php
 $siteTitle = "PostDetail";
 require('head.php');
 //↑これの順番が逆だと読み込んでくれない
  ?>
 <?php
 require('header.php');
  ?>
 <?php
 require('sidebar.php');
  ?>

 <div class="main-wrapper">
  <h2 class="page-name"><a class="post-user-name" href="myPage.php?u_id=<?php echo sanitize($viewData['user_id']); ?>"><?php echo sanitize($postUser['user_name']); ?></a>さんの投稿</h2>
  <div class="photo-wrapper">
    <div class="main-photo">
      <img src="<?php echo sanitize($photo1); ?>" id="js-switch-img-main">
    </div>
    <div class="sub-photo">
      <div class="sub">
        <img src="<?php echo sanitize($photo1); ?>" class="js-switch-img-sub">
      </div>
      <div class="sub">
        <img src="<?php echo sanitize($photo2); ?>" class="js-switch-img-sub">
      </div>
      <div class="sub">
        <img src="<?php echo sanitize($photo3); ?>" class="js-switch-img-sub">
      </div>
    </div>
  </div>
  <div class="photo-descript">
    <div class="descript-text">
      <?php echo sanitize($viewData['detail']); ?>
    </div>
    <div class="photo-data">
      <p class="post-person">【投稿者】　<?php echo sanitize($postUser['user_name']); ?></p>
      <p class="post-date">【投稿日時】　<?php echo date('Y年m月d日', strtotime($viewData['create_date'])); ?></p>
      <p class="post-category">【カテゴリー】 <?php echo sanitize($viewData['category_name']); ?></p>
    </div>
    <div class="icon-zone">
      <i id="star1" class="fa fa-star star <?php if($starCount != 0 ){ echo 'star-hv'; } ?>" data-postid="<?php echo sanitize($viewData['post_id']); ?>"></i>
      <i id="star2" class="fa fa-star star <?php if($starCount != 0 && $starCount > 1){ echo 'star-hv'; } ?>" data-postid="<?php echo sanitize($viewData['post_id']); ?>"></i>
      <i id="star3" class="fa fa-star star <?php if($starCount != 0 && $starCount > 2){ echo 'star-hv'; } ?>" data-postid="<?php echo sanitize($viewData['post_id']); ?>"></i>
      <i id="star4" class="fa fa-star star <?php if($starCount != 0 && $starCount > 3){ echo 'star-hv'; } ?>" data-postid="<?php echo sanitize($viewData['post_id']); ?>"></i>
      <i id="star5" class="fa fa-star star <?php if($starCount == 5){ echo 'star-hv'; } ?>" data-postid="<?php echo sanitize($viewData['post_id']); ?>"></i>
      <i id="heart" class="fa fa-heart <?php if(isLike($_SESSION['user_id'], $viewData['post_id'])){ echo 'heart-hv'; } ?>" data-postid="<?php echo sanitize($viewData['post_id']); ?>"></i>
    </div>
    <?php if($viewData['user_id'] == $_SESSION['user_id']){ ?>
    <div class="edit-post">
      <a href="postEdit.php?p_id=<?php echo $p_id; ?>">編集する</a>
    </div>
    <?php } ?>
  </div>
  <div class="comment-wrapper">
    <form method="post" class="comment-post">
      <textarea class="js-count" name="comment"></textarea>
      <p class="count"><span class="js-count-view">0</span>/250文字</p>
      <input type="submit" value="コメントを送る">
    </form>
    <p class="all-comment" id="js-comment">コメントを見る</p>
    <div class="comment-display" id="js-comment-display">
        <?php if (!empty($commentData)){ ?>
          <?php foreach ($commentData as $key => $val){ ?>
          <div class="comment-box">
              <div class="comment-user-img" style="<?php if(empty($val['prof_pic'])) echo 'background:#e4dace;'; ?>">
                <img src="<?php if(!empty($val['prof_pic'])){ echo $val['prof_pic']; }else{ echo 'img/prof_def.png'; } ?>" style="<?php if(empty($val['prof_pic'])) echo 'width: 50px; height: 50px; margin-top: 10px; margin-left: 5px;'; ?>">
              </div>
              <div class="comment-right">
                <p><span><a class="comment-user-name" href="myPage.php?u_id=<?php echo $val['id']; ?>"><?php echo $val['user_name']; ?></a></span><span class="comment-create-date"><?php echo $val['create_date']; ?></span> </p>
                <p class="user-comment"><?php echo $val['comment']; ?></p>
              </div>
          </div>
        <?php } ?>
        <?php }else { ?>
          <h3>まだコメントがありません</h3>
        <?php } ?>
    </div>
  </div>
 </div>

  <?php
  require('footer.php');
   ?>
