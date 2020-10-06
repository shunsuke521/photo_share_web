<?php
require('function.php');

debug('---------------------------------------');
debug('---カテゴリー別一覧ページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

//現在のページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは1ページ目
//カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
//ソート
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
//ユーザーID
$u_id = (!empty($_GET['u_id'])) ? $_GET['u_id'] : '';
//お気に入りユーザー
$f_user = (!empty($_GET['f_user'])) ? $_GET['f_user'] : '';
debug('$sortの中身：'.$sort);
//ページのGETパラメータに不正な値が入っているかチェック
if (!is_int((int)$currentPageNum)) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}

//ユーザーデータ
$userData = (!empty($u_id)) ? userData($u_id) : '';
$userData = (!empty($f_user)) ? userData($f_user) : $userData;
debug('$userData：'.print_r($userData,true));

//表示件数
$listSpan = 20;
//現在の表示投稿の先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);
//DBから投稿データを取得
$postsData = getPostList($currentMinNum, $category, $sort ,$u_id, $f_user);
debug('$postsDataの中身：'.print_r($postsData,true));
//DBからカテゴリーデータを取得
$categoryData = getCategory();
debug('$categoryDataの中身：'.print_r($categoryData,true));
//対象のカテゴリー名を取得
$categoryName = (!empty($category)) ? $categoryData[$category - 1]['category_name'] : '';
debug('$categoryNameの中身：'.$categoryName);
debug('現在のページ：'.$currentPageNum);
$fabo = getTotalFavo(88);
debug(array_shift($fabo));

 ?>
<?php
$siteTitle = "Posts";
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
<div class="main-wrapper posts-list">
  <h2 class="page-name">投稿一覧</h2>
  <div class="new-posts">
    <h3 class="category-name"><?php if(!empty($categoryName)){ echo $categoryName; }elseif(!empty($u_id)){ echo $userData['user_name'].'さん'; }elseif(!empty($f_user)){ echo $userData['user_name'].'さんお気に入り'; }else{ echo '新着'; } ?>の投稿一覧</h3>
    <form method="get" class="select-box">
      <div class="small-box category-box">
        <h3>カテゴリー</h3>
        <div class="select-wrapper">
          <select class="" name="c_id">
            <option value="0" <?php if(getFormData('c_id',true) == 0){echo 'selected'; } ?> >選択してください</option>
            <?php foreach($categoryData as $key => $val){ ?>
            <option value="<?php echo $val['category_id']; ?>" <?php if(getFormData('c_id', true) == $val['category_id']){echo 'selected'; } ?>><?php echo $val['category_name']; ?></option>
          <?php } ?>
          </select>
        </div>
      </div>
      <div class="small-box sort-box">
        <h3>表示順</h3>
        <div class="select-wrapper">
          <select class="" name="sort">
            <option value="0" <?php if(getFormData('sort',true) == 0){echo 'selected'; } ?>>選択してください</option>
            <option value="1" <?php if(getFormData('sort',true) == 1){echo 'selected'; } ?>>新しい順</option>
            <option value="2" <?php if(getFormData('sort',true) == 2){echo 'selected'; } ?>>古い順</option>
          </select>
        </div>
      </div>
      <input type="hidden" name="p" value="1">
      <input type="submit" value="検索">
    </form>
    <div class="photos">
      <?php if(!empty($postsData['data'])){ ?>
      <?php for($i = 0; $i < 5; $i++): ?>
        <?php $star = (!empty($postsData['data'][$i]['post_id'])) ? getAveStar($postsData['data'][$i]['post_id']) : ''; ?>
        <?php $favo = (!empty($postsData['data'][$i]['post_id'])) ? getTotalFavo($postsData['data'][$i]['post_id']) : ''; ?>
      <a href="postDetail.php?p_id=<?php if(!empty($postsData['data'][$i]['post_id'])) echo $postsData['data'][$i]['post_id']; ?>" class="js-img-hover" style="display:<?php if(empty($postsData['data'][$i]['post_id'])) echo 'none'; ?>">
        <img src="<?php if(!empty($postsData['data'][$i])) echo sanitize($postsData['data'][$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
      <?php }else{ ?>
        <p class="no-photos">投稿がありません。</p>
      <?php } ?>
    </div>
    <div class="photos" style="display:<?php if(empty($postsData['data'][5])) echo 'none'; ?>">
      <?php for($i = 5; $i < 10; $i++): ?>
        <?php $star = (!empty($postsData['data'][$i]['post_id'])) ? getAveStar($postsData['data'][$i]['post_id']) : ''; ?>
        <?php $favo = (!empty($postsData['data'][$i]['post_id'])) ? getTotalFavo($postsData['data'][$i]['post_id']) : ''; ?>
      <a href="postDetail.php?p_id=<?php if(!empty($postsData['data'][$i]['post_id'])) echo $postsData['data'][$i]['post_id']; ?>" class="js-img-hover" style="display:<?php if(empty($postsData['data'][$i]['post_id'])) echo 'none'; ?>">
        <img src="<?php if(!empty($postsData['data'][$i])) echo sanitize($postsData['data'][$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div class="photos" style="display:<?php if(empty($postsData['data'][10])) echo 'none'; ?>">
      <?php for($i = 10; $i < 15; $i++): ?>
        <?php $star = (!empty($postsData['data'][$i]['post_id'])) ? getAveStar($postsData['data'][$i]['post_id']) : ''; ?>
        <?php $favo = (!empty($postsData['data'][$i]['post_id'])) ? getTotalFavo($postsData['data'][$i]['post_id']) : ''; ?>
      <a href="postDetail.php?p_id=<?php if(!empty($postsData['data'][$i]['post_id'])) echo $postsData['data'][$i]['post_id']; ?>" class="js-img-hover" style="display:<?php if(empty($postsData['data'][$i]['post_id'])) echo 'none'; ?>">
        <img src="<?php if(!empty($postsData['data'][$i])) echo sanitize($postsData['data'][$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
    <div class="photos" style="display:<?php if(empty($postsData['data'][15])) echo 'none'; ?>">
      <?php for($i = 15; $i < 20; $i++): ?>
        <?php $star = (!empty($postsData['data'][$i]['post_id'])) ? getAveStar($postsData['data'][$i]['post_id']) : ''; ?>
        <?php $favo = (!empty($postsData['data'][$i]['post_id'])) ? getTotalFavo($postsData['data'][$i]['post_id']) : ''; ?>
      <a href="postDetail.php?p_id=<?php if(!empty($postsData['data'][$i]['post_id'])) echo $postsData['data'][$i]['post_id']; ?>" class="js-img-hover" style="display:<?php if(empty($postsData['data'][$i]['post_id'])) echo 'none'; ?>">
        <img src="<?php if(!empty($postsData['data'][$i])) echo sanitize($postsData['data'][$i]['photo1']); ?>">
        <div class="white-back">
          <span class="icon-on-img star-on-img"><i class="fa fa-star"></i><?php echo round(array_shift($star),1); ?></span>
          <span class="icon-on-img heart-on-img"><i class="fa fa-heart"></i>×<?php echo array_shift($favo); ?></span>
        </div>
      </a>
      <?php endfor; ?>
    </div>
  </div>
  <div class="paging">
    <ul>
      <!-- ↓ページネーションの最小値と最大値を決定するためのforeach文↓ -->
      <?php
        //ページネーションの表示数
        $pageColNum = 5;
        //総ページ数
        $totalPageNum = $postsData['total_page'];

        //現在のページが総ページ数と同じかつ総ページ数が表示項目数(5)以上　⇨　左に4つリンクを出す
        if ($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum) {
          debug('1番目を適用');
          $minPageNum = $currentPageNum - 4;
          $maxPageNum = $currentPageNum;
          //現在のページが総ページ数の1ページ前かつ総ページ数が5以上　⇨　左に3、右に1出す
        }elseif ($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
          debug('2番目を適用');
          $minPageNum = $currentPageNum - 3;
          $maxPageNum = $currentPageNum + 1;
          //現在のページが2かつ総ページ数が5以上 ⇨　左に1、右に3出す
        }elseif ($currentPageNum == 2 && $totalPageNum >= $pageColNum) {
          debug('3番目を適用');
          $minPageNum = $currentPageNum - 1;
          $maxPageNum = $currentPageNum + 3;
          //現ページが1かつ総ページ数が5以上　⇨　左には何も出さず、右に4出す
        }elseif ($currentPageNum == 1 && $totalPageNum >= $pageColNum) {
          debug('4番目を適用');
          $minPageNum = $currentPageNum;
          $maxPageNum = 5;
          //総ページ数が5より少ない　⇨　総ページ数をループのmax、ループのminを1に設定
        }elseif ($totalPageNum < $pageColNum) {
          debug('5番目を適用');
          $minPageNum = 1;
          $maxPageNum = $totalPageNum;
          //それ以外
        }else {
          debug('その他を適用');
          $minPageNum = $currentPageNum - 2;
          $maxPageNum = $currentPageNum + 2;
        }
        debug('$minPageNum：'.$minPageNum);
        debug('$maxPageNum：'.$maxPageNum);
       ?>
       <?php if($currentPageNum != 1): ?>
      <li><a href="?c_id=<?php echo $category; ?>&sort=<?php echo $sort; ?>&u_id=<?php echo $u_id; ?>&f_user=<?php echo $f_user; ?>&p=1">&lt;&lt;</a></li>
       <?php endif; ?>
       <?php if($currentPageNum != 1): ?>
      <li><a href="?c_id=<?php echo $category; ?>&sort=<?php echo $sort; ?>&u_id=<?php echo $u_id; ?>&f_user=<?php echo $f_user; ?>&p=<?php echo $currentPageNum-1; ?>">&lt;</a></li>
       <?php endif; ?>
       <?php for($i = $minPageNum; $i <= $maxPageNum; $i++): ?>
      <li class="<?php if($currentPageNum == $i) echo 'current-page'; ?>"><a href="?c_id=<?php echo $category; ?>&sort=<?php echo $sort; ?>&u_id=<?php echo $u_id; ?>&f_user=<?php echo $f_user; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
       <?php endfor; ?>
       <?php if($currentPageNum != $maxPageNum): ?>
      <li><a href="?c_id=<?php echo $category; ?>&sort=<?php echo $sort; ?>&u_id=<?php echo $u_id; ?>&f_user=<?php echo $f_user; ?>&p=<?php echo $currentPageNum+1; ?>">&gt;</a></li>
       <?php endif; ?>
       <?php if($currentPageNum != $maxPageNum): ?>
      <li><a href="?c_id=<?php echo $category; ?>&sort=<?php echo $sort; ?>&u_id=<?php echo $u_id; ?>&f_user=<?php echo $f_user; ?>&p=<?php echo $postsData['total_page']; ?>">&gt;&gt;</a></li>
       <?php endif; ?>
    </ul>
  </div>
</div>





 <?php
 require('footer.php');
  ?>
