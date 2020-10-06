<?php
require('function.php');

debug('---------------------------------------');
debug('---プロフィール編集---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');

$userData = userData($_SESSION['user_id']);

debug('$userDataの中身：'.print_r($userData,true));

//post送信（「変更する」ボタン）されていた場合
if (!empty($_POST)) {
  debug('POST送信があります（プロフィール編集）');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数に代入
  $user_name = $_POST['user_name'];
  $email = $_POST['email'];
  $age = $_POST['age'];
  $prof_pic = $_FILES['prof_pic']['type'];
  $self_intro = $_POST['self_intro'];
  //画像をアップロードし、パスを格納
  $prof_pic = (!empty($_FILES['prof_pic']['name'])) ? uploadImg($_FILES['prof_pic'],'prof_pic') : '';
  //画像をPOSTしてない（登録してない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $prof_pic = (empty($prof_pic) && !empty($userData['prof_pic'])) ? $userData['prof_pic'] : $prof_pic;

  //既にDB上に登録されてある情報と入力が異なる場合にバリデーションを実施
  if ($userData['user_name'] !== $user_name) {
    validMaxLen($user_name, 'user_name');
    validNameDup($user_name);
  }

  if ($userData['email'] !== $email) {
    validEmail($email,'email');
    validMaxLen($email,'email');
    validEmailDup($email);
  }

  if ((int)$userData['age'] !== $age) {
    validMaxLen($age, 'age');
    validNumber($age, 'age');
  }

  if ($userData['self_intro'] !== $self_intro) {
    validMaxLen($self_intro, 'self_intro');
  }

  if (empty($err_msg)) {
    debug('バリデーションOKです');

    try {
      $dbh = dbConnect();
      $sql = 'UPDATE users SET user_name = :u_name, email = :email, age = :age, self_intro = :self_intro, prof_pic = :prof_pic WHERE id = :u_id';
      $data = array(':u_name' => $user_name, ':email' => $email, ':age' => $age, ':self_intro' => $self_intro, ':prof_pic' => $prof_pic, ':u_id' => $userData['id']);
      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {
        debug('クエリ成功（プロフィール編集）');
        debug('メインページへ遷移');
        $_SESSION['msg_success'] = SUC05;
        header("Location:index.php");
      }else {
        debug('クエリ失敗（プロフィール編集）');
        $err_msg['common'] = MSG08;
      }
    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
}
 ?>
<?php
$siteTitle = "ProfEdit";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>
<?php
require('sidebar.php');
 ?>

 <div class="input-form profedit">
   <h2 class="page-name">プロフィール編集</h2>
   <div class="common-error"><?php if(!empty($_POST['common'])){ echo $_POST['common'];} ?></div>
   <form method="post" enctype="multipart/form-data">
     <div class="form-group prof-pic">
       <p>プロフィール写真</p>
      <label><input type="file" name="prof_pic" class="file-input js-prof-pic">写真を選択する</label>
       <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
       <img src="<?php if(!empty($userData['prof_pic'])) echo $userData['prof_pic']; ?>" class="prev-img" alt="">
     </div>
     <div class="form-group">
       <p>ユーザーネーム</p><div class="vali"><?php if(!empty($err_msg['user_name'])) echo $err_msg['user_name']; ?></div>
       <input type="text" name="user_name" value="<?php if(!empty($_POST['user_name'])){ echo $_POST['user_name']; }elseif(!empty($userData['user_name'])){ echo $userData['user_name']; } ?>">
     </div>
     <div class="form-group">
       <p>メールアドレス</p><div class="vali"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></div>
       <input type="text" name="email" value="<?php if(!empty($_POST['email'])){ echo $_POST['email']; }elseif(!empty($userData['email'])){ echo $userData['email']; } ?>">
     </div>
     <div class="form-group">
       <p>年齢</p><div class="vali"><?php if(!empty($err_msg['age'])) echo $err_msg['age']; ?></div>
       <input type="text" name="age" value="<?php if(!empty($_POST['age'])){ echo $_POST['age']; }elseif(!empty($userData['age'])){ echo $userData['age']; } ?>">
     </div>
     <div class="form-group text-form">
       <p>自己紹介</p><div class="vali"><?php if(!empty($err_msg['self_intro'])) echo $err_msg['self_intro']; ?></div>
       <textarea name="self_intro" rows="8" cols="75"><?php if(!empty($_POST['self_intro'])){ echo $_POST['self_intro']; }elseif(!empty($userData['self_intro'])){ echo $userData['self_intro']; } ?></textarea>
     </div>
       <input class="" type="submit" value="変更する">
   </form>
 </div>

 <?php
 require('footer.php');
  ?>
