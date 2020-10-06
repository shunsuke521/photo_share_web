<?php
require('function.php');

debug('---------------------------------------');
debug('---メインページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

//現在のページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
//カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
//ソート
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
//パラメータに不正な値が入っているかチェック
if (!is_int((int)$currentPageNum)) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}

$categoryRand = categoryRandom();
debug('$categoryRandの中身：'.print_r($categoryRand,true));


//最新の５件
$postsData = newPosts(5);
debug('$postsDataの中身：'.print_r($postsData,true));
//ランダムなカテゴリーの情報
$category_data1 = $categoryRand[0];
debug('$category_data1の中身：'.print_r($category_data1,true));
$category_data2 = $categoryRand[1];
debug('$category_data2の中身：'.print_r($category_data2,true));
$category_data3 = $categoryRand[2];
debug('$category_data3の中身：'.print_r($category_data3,true));
//ランダムなカテゴリーの投稿５件
$categoryPosts1 = categoryPosts($category_data1['category_id']);
debug('$categoryPosts1の中身：'.print_r($categoryPosts1,true));
$categoryPosts2 = categoryPosts($category_data2['category_id']);
debug('$categoryPosts2の中身：'.print_r($categoryPosts2,true));
$categoryPosts3 = categoryPosts($category_data3['category_id']);
debug('$categoryPosts3の中身：'.print_r($categoryPosts3,true));


//表示件数
$listSpan = 20;
//現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);

 ?>
<?php
$siteTitle = "Home";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>
<?php
require('sidebar.php');
 ?>

<div class="js-modal-wrapper modal">
  <i class="js-button fa fa-times"></i>
  <p class="js-modal"><?php echo getSessionFlash('msg_success') ?></p>
</div>
<div class="js-modal-back modal-back"></div>
<div class="main-wrapper">
  <h2 class="page-name">投稿一覧</h2>
  <div class="new-posts">
    <h3 class="category-name">新着投稿</h3>
    <div class="photos">
      <?php for($i = 0; $i < 5; $i++): ?>
        <?php $star = getAveStar($postsData[$i]['post_id']); ?>
        <?php $favo = getTotalFavo($postsData[$i]['post_id']); ?>
      <a href="postDetail.php?p_id=<?php echo $postsData[$i]['post_id']; ?>" class="js-img-hover">
        <img src="<?php echo sanitize($postsData[$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div class="more-photos">
      <a href="morePhotos.php?p=1">more photos &gt;</a>
    </div>
  </div>
  <div class="new-posts">
    <h3 class="category-name"><?php echo $category_data1['category_name']; ?>に関する写真</h3>
    <div class="photos">
      <?php for($i = 0; $i < 5; $i++): ?>
        <?php $star = getAveStar($categoryPosts1[$i]['post_id']); ?>
        <?php $favo = getTotalFavo($categoryPosts1[$i]['post_id']); ?>
      <a href="postDetail.php?p_id=<?php echo $categoryPosts1[$i]['post_id']; ?>" class="js-img-hover">
        <img src="<?php echo sanitize($categoryPosts1[$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div class="more-photos">
      <a href="morePhotos.php?p=1&c_id=<?php echo $category_data1['category_id']; ?>">more photos &gt;</a>
    </div>
  </div>
  <div class="new-posts">
    <h3 class="category-name"><?php echo $category_data2['category_name']; ?>に関する写真</h3>
    <div class="photos">
      <?php for($i = 0; $i < 5; $i++): ?>
        <?php $star = getAveStar($categoryPosts2[$i]['post_id']); ?>
        <?php $favo = getTotalFavo($categoryPosts2[$i]['post_id']); ?>
      <a href="postDetail.php?p_id=<?php echo $categoryPosts2[$i]['post_id']; ?>" class="js-img-hover">
        <img src="<?php echo sanitize($categoryPosts2[$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div class="more-photos">
      <a href="morePhotos.php?p=1&c_id=<?php echo $category_data2['category_id']; ?>">more photos &gt;</a>
    </div>
  </div>
  <div class="new-posts">
    <h3 class="category-name"><?php echo $category_data3['category_name']; ?>に関する写真</h3>
    <div class="photos">
      <?php for($i = 0; $i < 5; $i++): ?>
        <?php $star = getAveStar($categoryPosts3[$i]['post_id']); ?>
        <?php $favo = getTotalFavo($categoryPosts3[$i]['post_id']); ?>
      <a href="postDetail.php?p_id=<?php echo $categoryPosts3[$i]['post_id']; ?>" class="js-img-hover">
        <img src="<?php echo sanitize($categoryPosts3[$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div class="more-photos">
      <a href="morePhotos.php?p=1&c_id=<?php echo $category_data3['category_id']; ?>">more photos &gt;</a>
    </div>
  </div>
</div>





 <?php
 require('footer.php');
  ?>
