<?php
require('function.php');

debug('---------------------------------------');
debug('---マイページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

$user_id = $_GET['u_id'];

//ユーザー情報
$userData = userData($user_id);
debug('$userDataの中身：'.print_r($userData,true));

//ユーザーの投稿情報
$postsData = getPosts($user_id);
debug('$postsDataの中身：'.print_r($postsData,true));

//ユーザーがお気に入りした投稿
$favoPosts = favoPosts($user_id);
debug('$favoPostsの中身：'.print_r($favoPosts,true));


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

<div class="main-wrapper">
  <h2 class="page-name"><?php if($user_id == $_SESSION['user_id']){ echo 'マイ'; }else{ echo $userData['user_name'].'さんの'; } ?>ページ</h2>
  <div class="profile-wrapper">
    <div class="prof-pic">
      <img src="<?php if(!empty($userData['prof_pic'])){ echo $userData['prof_pic']; }else{ echo 'img/prof_def.png'; } ?>" style="<?php if(empty($userData['prof_pic'])) echo 'width: 250px; height: 250px; margin-top: 50px; margin-left: 25px;'; ?>">
    </div>
    <div class="user-data">
      <div class="personal-data">
        <p class="user-name">name：<span><?php echo $userData['user_name']; ?></span></p>
        <p class="user-age">age：<span><?php echo $userData['age'] ?></span></p><br>
      </div>
      <p class="user-detail">introduce：<span><?php echo $userData['self_intro']; ?></span></p>
    </div>
    <div class="dm-link">
      <a href="dirMsg.php"><!-- DMページへのリンクでメールアイコン付けたいけどうまくできない --></a>
    </div>
    <div class="prof-edit">
      <a href="profEdit.php">edit &gt;</a>
    </div>
  </div>
  <div class="personal-photos">
    <div class="new-posts">
      <h3 class="category-name"><?php echo $userData['user_name']; ?>さんの投稿<span>(最近の5件)</span></h3>
      <div class="photos">
        <?php if(!empty($postsData)){ ?>
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
        <?php }else{ ?>
          <p class="no-photos">まだ投稿がありません。</p>
        <?php } ?>
      </div>
      <div class="more-photos">
        <a href="morePhotos.php?p=1&u_id=<?php echo $user_id; ?>">more photos &gt;</a>
      </div>
    </div>
    <div class="new-posts">
      <h3 class="category-name"><?php echo $userData['user_name']; ?>さんのお気に入り<span>(最近の5件)</span></h3>
      <div class="photos">
        <?php if(!empty($favoPosts)){ ?>
        <?php for($i = 0; $i < 5; $i++): ?>
          <?php $star = getAveStar($favoPosts[$i]['post_id']); ?>
          <?php $favo = getTotalFavo($favoPosts[$i]['post_id']); ?>
        <a href="postDetail.php?p_id=<?php echo $favoPosts[$i]['post_id']; ?>" class="js-img-hover">
          <img src="<?php echo sanitize($favoPosts[$i]['photo1']); ?>">
          <div class="white-back">
            <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
            <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
          </div>
        </a>
        <?php endfor; ?>
        <?php }else{ ?>
          <p class="no-photos">お気に入りの投稿はありません。</p>
        <?php } ?>
      </div>
      <div class="more-photos">
        <a href="morePhotos.php?p=1&f_user=<?php echo $user_id; ?>">more photos &gt;</a>
      </div>
    </div>
  </div>
</div>






 <?php
 require('footer.php');
  ?>
