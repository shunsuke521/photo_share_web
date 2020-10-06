<?php
require('function.php');

debug('---------------------------------------');
debug('---写真投稿or編集ページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

//GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('$p_id：'.$p_id);
//DBから投稿データを取得
$postData = (!empty($p_id)) ? getPost($_SESSION['user_id'], $p_id) : '';
//新規投稿画面か編集画面か判定用のフラグ
$edit_flg = (empty($postData)) ? false : true; //falseだったら投稿情報が入っていないので新規投稿
//DBからカテゴリーデータを取得
$categoryData = getCategory();
debug('商品ID：'.$p_id);
debug('商品情報($postData)：'.print_r($postData,true));
debug('カテゴリーデータ($categoryData)：'.print_r($categoryData,true));

//GETパラメータ改竄チェック
//URLをいじくってGETパラメータを改竄した場合、メインページへ遷移
if (!empty($p_id) && empty($postData)) {
  //GETパラメータを得られたが投稿の情報が得られていない場合
  debug('GETパラメータの商品IDが違います');
  header("Location:index.php");
}

//post送信時
if (!empty($_POST)) {
  debug('POST送信があります（投稿or編集）');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //送信された情報の代入
  $detail = $_POST['detail'];
  $category = $_POST['category_id'];
  //画像がアップロードされていた場合はパスを格納
  $photo1 = (!empty($_FILES['photo1']['name'])) ? uploadImg($_FILES['photo1'],'photo1') : '';
  $photo2 = (!empty($_FILES['photo2']['name'])) ? uploadImg($_FILES['photo2'],'photo2') : '';
  $photo3 = (!empty($_FILES['photo3']['name'])) ? uploadImg($_FILES['photo3'],'photo3') : '';
  //画像がアップロードされていない場合もしくは既にDBに登録されている場合はDBのパスを入れる（jsで画像を表示しているだけでありPOST送信には反映されないので）
  $photo1 = (empty($photo1) && !empty($postData['photo1'])) ? $postData['photo1'] : $photo1;
  $photo2 = (empty($photo1) && !empty($postData['photo2'])) ? $postData['photo2'] : $photo2;
  $photo3 = (empty($photo1) && !empty($postData['photo3'])) ? $postData['photo3'] : $photo3;

  //更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if (empty($postData)) { //新規投稿の場合

    validRequired($detail,'detail');
    validMaxLen($detail,'detail');
    validSelect($category, 'category_id');

  }else { //編集の場合

    if ($postData['detail'] !== $detail) {
      validRequired($detail, 'detail');
      validMaxLen($detail, 'detail');
    }
    if ($postData['category_id'] !== $category) {
      validSelect($category, 'category_id');
      debug($err_msg['category_id']);
    }
  }

  if (empty($err_msg)) {
    debug('バリデーションOK（新規投稿or編集）');

    try {
      $dbh = dbConnect();
      //新規投稿の場合と編集の場合とでsql文を分ける
      if ($edit_flg) {
        //編集の場合
        debug('編集');
        $sql = 'UPDATE posts SET category_id = :category, detail = :detail, photo1 = :photo1, photo2 = :photo2, photo3 = :photo3 WHERE user_id = :u_id AND post_id = :p_id';
        $data = array(':category' => $category, ':detail' => $detail, ':photo1' => $photo1, ':photo2' => $photo2, ':photo3' => $photo3, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
      }else {
        //新規投稿の場合
        debug('新規投稿');
        $sql = 'INSERT INTO posts(user_id, category_id, detail, photo1, photo2, photo3, create_date) VALUES (:u_id, :category, :detail, :photo1, :photo2, :photo3, :date)';
        $data = array(':u_id' => $_SESSION['user_id'], ':category' => $category, ':detail' => $detail, ':photo1' => $photo1, ':photo2' => $photo2, ':photo3' => $photo3, ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：'.$sql);
      debug('流し込みデータ：'.print_r($data,true));

      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        if ($edit_flg) {
        $_SESSION['msg_success'] = SUC03;
      }else {
        $_SESSION['msg_success'] = SUC04;
      }
        debug('メインページへ遷移');
        header("Location:index.php");
      }

    } catch (Exception $e) {

    }

  }

}



 ?>
<?php
$siteTitle = ($edit_flg) ? 'Edit_post' : 'New_post';
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>
 <?php
require('sidebar.php');
  ?>

 <form action="" method="post" enctype="multipart/form-data" class="main-wrapper">
   <h2 class="page-name"><?php echo ($edit_flg) ? '編集': '新規投稿'; ?></h2>
   <div class="post-photo">
     <div class="images-wrapper">
      <div class="image">
        <p>ドラッグ＆ドロップ</p>
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728"> <!-- 最大サイズを指定する場合はこのinputタグが必要 -->
        <input type="file" name="photo1" class="input-file">
        <img src="<?php if(!empty($p_id)) echo $postData['photo1']; ?>" class="prev-img" alt="">
      </div>
      <div class="image">
        <p>ドラッグ＆ドロップ</p>
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        <input type="file" name="photo2" class="input-file">
        <img src="<?php if(!empty($p_id)) echo $postData['photo2']; ?>" class="prev-img" alt="">
      </div>
      <div class="image">
        <p>ドラッグ＆ドロップ</p>
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        <input type="file" name="photo3" class="input-file">
        <img src="<?php if(!empty($p_id)) echo $postData['photo3']; ?>" class="prev-img" alt="">
      </div>
     </div>
     <div class="photo-detail">
       <p>詳細</p>
       <div class="vali"><?php if(!empty($err_msg['detail'])) echo $err_msg['detail']; ?></div>
       <textarea class="js-count" name="detail"><?php if(!empty($p_id)) echo $postData['detail']; ?></textarea>
     </div>
     <div class="category-wrapper">
      <div class="category-select">
       <p>カテゴリー</p>
       <div class="vali"><?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id']; ?></div>
       <div class="select-wrapper">
         <select class="category-id" name="category_id">
           <option value="0" <?php if(getFormData('category_id') == 0){ echo 'selected'; } ?>>選択してください</option>
           <?php foreach($categoryData as $key => $val){ ?>
           <option value="<?php echo $val['category_id'] ?>" <?php if(getFormData('category_id') == $val['category_id']){ echo 'selected';}  ?> ><?php echo $val['category_name']; ?></option>
           <?php } ?>
         </select>
       </div>
      </div>
      <p class="count"><span class="js-count-view"><?php if(!empty($postData['detail'])){ echo mb_strlen($postData['detail']); }else{ echo 0;} ?></span>/250文字</p>
     </div>
   </div>
   <div class="photo-submit">
     <input class="" type="submit" value="投稿する">
   </div>
 </form>

<?php
require('footer.php');
 ?>
