<?php
//ログをとる
ini_set('log_errors','on');
//ログの出力ファイル
ini_set('error_log','php.log');

//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//セッション準備＋セッションの有効期限を伸ばす
//セッションファイルの置き場を変更する(/var/tmp/以下に置くと30日は削除されない)
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ100分の1の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える(なりすましのセキュリティ対策)
session_regenerate_id();

//画面表示処理開始ログ吐き出し関数
function debugLogStart(){
  debug('debugLogスタート');
  debug('-----------------------------画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
  debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

//各種メッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード(再入力)があっていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','10文字以内で入力してください');
define('MSG07','250文字以内で入力してください');
define('MSG08','エラーが発生しました しばらく経ってからやり直してください');
define('MSG09','そのユーザーネームは既に登録されています');
define('MSG10','その Emailは既に登録されています');
define('MSG11','メールアドレスもしくはパスワードが違います');
define('MSG12','半角数字のみご利用いただけます');
define('MSG13','入力されたパスワードが間違っています');
define('MSG14','カテゴリーを選択してください');
define('SUC01','パスワードを変更しました');
define('SUC02','新規登録しました');
define('SUC03','編集完了しました');
define('SUC04','新規投稿しました');
define('SUC05','プロフィールを編集しました');

//エラーメッセージ格納用の配列
$err_msg = array();

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//バリデーション関数（Email形式チェック）
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数（ユーザーネーム重複チェック）
function validNameDup($user_name){
  global $err_msg;
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE user_name = :user_name AND delete_flg = 0';
    $data = array(':user_name' => $user_name);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    debug(array_shift($result));
    debug(print_r($result));
    if (!empty(array_shift($result))) {
      //結果がtrue（既にユーザーネームが登録済み）だったら
      debug('$result（ユーザーネーム重複チェック）の中身：'.print_r($result));
      $err_msg['user_name'] = MSG09;
    }
  } catch (Exception $e) {
    error_log('エラー発生:'.$e->getMessage());
    debug('ユーザーネーム重複チェックエラー');
    $err_msg['common'] = MSG08;
  }
}
//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty(array_shift($result))) {
      //結果がtrue（既にメールアドレスが登録済み）だったら
      debug('$result（email重複チェック）の中身：'.print_r($result));
      $err_msg['email'] = MSG10;
    }
  } catch (Exception $e) {
    error_log('エラー発生:'.$e->getMessage());
    debug('Email重複チェックエラー');
    $err_msg['common'] = MSG08;
  }
}
//バリデーション関数（パスワード一致チェック）
function validMatch($str1, $str2, $key){
  if ($str1 !== $str2) {
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  if (mb_strlen($str) < $min) {
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 250){
  if (mb_strlen($str) > $max) {
    global $err_msg;
    if ($max == 250) {
      $err_msg[$key] = MSG07;
    }elseif ($max == 10) {
      $err_msg[$key] = MSG06;
    }
  }
}
//バリデーション関数（半角チェック）
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//バリデーション関数（半角数字チェック）
function validNumber($str, $key){
  if (!preg_match("/^[0-9]+$/", $str)) {
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
function validSelect($str, $key){
  if(!preg_match("/^[1-10]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG14;
  }
}
//ユーザーデータの取得関数
function userData($user_id){
  debug('ユーザー情報を取得');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :user_id';
    $data = array(':user_id' => $user_id);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('クエリ成功（ユーザーデータ取得）');
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else {
      debug('クエリ失敗（ユーザーデータ取得）');
    }
  } catch (Exception $e) {
    error_log('ユーザーデータ取得エラー:'.$e->getMessage());
  }
}
//セッションのユーザーIDとGET送信された投稿IDから投稿情報を取得
function getPost($u_id,$p_id){
  debug('投稿情報を取得');
  debug('ユーザーID：'.$u_id);
  debug('投稿ID：'.$p_id);
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM posts WHERE user_id = :user_id AND post_id = :post_id AND delete_flg = 0';
    $data = array(':user_id' => $u_id, ':post_id' => $p_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('クエリ成功（投稿情報取得）');
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else {
      debug('クエリ失敗（投稿情報取得）');
      return false;
    }
  } catch (Exception $e) {
    error_log('投稿情報取得エラー：'.$e->getMessage());
  }
}
function getCategory(){
  debug('カテゴリーデータを取得');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM category';
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      //クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  if (isset($file['error']) && is_int($file['error'])) {
    try {
      //バリデーション
      //$file['error']の値を確認 配列内には「UPLOAD_ERR_OK」などの定数が入っている
      //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される 定数には値として0や1などの数値が入っている
      switch ($file['error']) {
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE://ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE://php.ini定義の最大サイズを超過した場合
        case UPLOAD_ERR_FORM_SIZE://フォーム定義の最大サイズを超過した場合
          throw new RuntimeException('ファイルのサイズが大きすぎます');
        default://そのほかの場合
          throw new RuntimeException('その他のエラーが発生しました');
      }
      //$file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
      //exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {//第三引数にtrueを設定することで厳密にチェックをしてくれる
        throw new RuntimeException('画像形式が未対応です');
      }

      //ファイルデータからSHA-1ハッシュをとってファイル名を決定し、ファイルを保存する
      //ハッシュ化せずアップロードされたファイル名そのままで保存すると、同じファイル名がアップロードされる可能性があり
      //DBにパスを保存した場合、どっちの画像のパスなのかわからなくなってしまう
      //imgae_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

      if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      //保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e) {

      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();

    }
  }
}
//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
//投稿を編集する際の入力情報保持（$strはカテゴリーID）
function getFormData($str, $flg = false){
  //編集でGET送信されたデータをもってくるのか、新規投稿もしくは編集でPOST送信されたデータを持ってくるのか判定
  if ($flg) {
    $method = $_GET;
  }else {
    $method = $_POST;
  }
  global $postData;//postEdit.phpでsessionのユーザーIDとGET送信された投稿IDをもとに取得した投稿情報
  if (!empty($postData)) {
  //投稿データがある場合
    if (!empty($err_msg[$str])) {
    //フォームのエラーがある場合
      if (!empty($method[$str])) {
        //POSTにデータがある場合
        return sanitize($method[$str]);//post送信されたデータ
      }else {
        return sanitize($postData[$str]);//DBから取得してきたデータ
      }
    }else {
      //POSTにデータがあり、DBの情報と違う場合（このフォームも変更していてエラーはないが、ほかのフォームで引っかかっている状態）
      if (!empty($method[$str]) && $method[$str] !== $postData[$str]) {
        return sanitize($method[$str]);
      }else {
        return sanitize($postData[$str]);
      }
    }
  }else { //$postDataにデータがない場合
    if (!empty($method[$str])) {
      return sanitize($method[$str]);
    }
  }
}
function getSessionFlash($key){
  if (!empty($_SESSION[$key])) {
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
function getPostList($currentMinNum = 1, $category, $sort, $u_id ,$f_user){
  debug('投稿情報を取得');
  try {
    //まずはカテゴリーを絞ってトータルの表示件数とページ数を取得
    $dbh = dbConnect();
    $sql = 'SELECT post_id FROM posts';
    if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
    if(!empty($u_id)) $sql .= ' WHERE user_id = '.$u_id;
    if(!empty($f_user)) $sql = 'SELECT post_id FROM favorite WHERE user_id = :f_user';
    $data = array();
    if(!empty($f_user)) $data = array(':f_user' => $f_user);

    $stmt = queryPost($dbh, $sql, $data);

    $rst['total'] = $stmt->rowCount();//総レコード数
    $rst['total_page'] = ceil($rst['total']/20);//総ページ数
    if (!$stmt) {
      return false;
    }

    $sql = 'SELECT * FROM posts';
    if(!empty($category)) $sql .= ' WHERE delete_flg = 0 AND category_id = '.$category;
    if(!empty($u_id)) $sql .= ' WHERE delete_flg = 0 AND user_id = '.$u_id;
    if(!empty($f_user)) $sql = 'SELECT * FROM posts INNER JOIN favorite ON posts.post_id = favorite.post_id WHERE favorite.user_id = :f_user';
    if (!empty($sort)) {
      switch ($sort) {
        case 1:
          $sql .= ' ORDER BY posts.post_id DESC';//昇順
          break;
        case 2:
          $sql .= ' ORDER BY posts.post_id ASC';//降順
          break;
      }
    }else{
      $sql .= ' ORDER BY posts.post_id DESC';//昇順
    }
    $sql .= ' LIMIT '. 20 .' OFFSET '.$currentMinNum;
    $data = array();
    if(!empty($f_user)) $data = array(':f_user' => $f_user);
    debug('SQL：'.$sql);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      //クエリ結果のデータを全レコード格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else {
      return false;
    }
  } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
  }
}
function getPostOne($p_id){
  debug('対象の投稿のデータを全て取得します');
  debug('投稿ID：'.$p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT p.post_id, p.category_id, p.detail, p.photo1, p.photo2, p.photo3, p.user_id, p.create_date, p.update_date, c.category_name FROM posts AS p LEFT JOIN category AS c ON p.category_id = c.category_id WHERE p.post_id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
    $data = array(':p_id' => $p_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      //クエリ結果のデータを1レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生'.$e->getMessage());
  }
}
function postUser($p_id){
  debug('対象の投稿をどのユーザーが投稿したかを取得します');
  debug('投稿ID：'.$p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT user_name FROM users RIGHT JOIN posts ON users.id = user_id WHERE post_id = :post_id AND posts.delete_flg = 0';
    $data = array(':post_id' => $p_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      //クエリ結果のデータを1レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生'.$e->getMessage());
  }
}
function getComment($p_id){
  debug('コメント情報を取得します');
  debug('投稿ID：'.$p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT comment, users.id, user_name, prof_pic, comments.create_date FROM comments LEFT JOIN users ON user_id = users.id WHERE post_id = :p_id AND comments.delete_flg = 0 AND users.delete_flg = 0 ORDER BY comments.create_date ASC';
    $data = array(':p_id' => $p_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetchAll();
    }else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function isLike($u_id, $p_id){
  debug('お気に入り情報があるか確認します');
  debug('ユーザーID：'.$u_id);
  debug('投稿ID：'.$p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM favorite WHERE post_id = :post_id AND user_id = :user_id';
    $data = array(':post_id' => $p_id, ':user_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt->rowCount()) {
      debug('お気に入りです');
      return true;
    }else {
      debug('気に入っていません');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function isEval($u_id, $p_id){
  debug('評価情報があるか確認します');
  debug('ユーザーID：'.$u_id);
  debug('投稿ID：'.$p_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM evaluation WHERE post_id = :post_id AND user_id = :user_id';
    $data = array(':post_id' => $p_id, ':user_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);

    $result = $stmt->fetchAll();
    debug('抽出結果($result)：'.print_r($result,true));

    if ($stmt->rowCount()) {
      debug('評価済みです');
      return $result[0]['eval'];
    }else {
      debug('まだ評価していません');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function getPosts($user_id){
  debug('対象のユーザーの投稿を抜き出します');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM posts WHERE user_id = :user_id AND delete_flg = 0 ORDER BY post_id DESC LIMIT 5';
    $data = array(':user_id' => $user_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('ユーザーの投稿を取得できました');
      return $stmt->fetchAll();
    }else {
      debug('投稿の抜き出しに失敗しました');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function favoPosts($user_id){
  debug('対象のユーザーがお気に入りにした投稿を全て抜き出します');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM posts RIGHT JOIN favorite ON posts.post_id = favorite.post_id WHERE favorite.user_id = :user_id AND posts.delete_flg = 0 ORDER BY favorite.post_id DESC LIMIT 5';
    $data = array(':user_id' => $user_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('お気に入りした投稿を取得できました');
      return $stmt->fetchAll();
    }else {
      debug('投稿の抜き出しに失敗しました');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function getTotalFavo($p_id){
  //対象の投稿のいいね数を取得します

  try {
    $dbh = dbConnect();
    $sql = 'SELECT COUNT(*) FROM favorite WHERE post_id = :post_id';
    $data = array(':post_id' => $p_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      return $total;
    }else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function getAveStar($p_id){

  try {
    $dbh = dbConnect();
    $sql = 'SELECT AVG(eval) FROM evaluation WHERE post_id = :post_id';
    $data = array(':post_id' => $p_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      $average = $stmt->fetch(PDO::FETCH_ASSOC);
      return $average;
    }else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function categoryRandom(){
  debug('トップページに表示する写真のカテゴリーをランダムに表示するよ');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM `category` ORDER BY RAND() LIMIT 3';
    $data = array();

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('ランダムなカテゴリーの取得に成功しました');
      $result = $stmt->fetchAll();
      return $result;
    }else {
      debug('ランダムなカテゴリーの取得に失敗しました');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function newPosts($num = 5){
  debug('指定した数の最新の投稿を取得');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM posts ORDER BY post_id DESC LIMIT 5';
    $data = array();

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('投稿の取得に成功しました');
      $result = $stmt->fetchAll();
      return $result;
    }else {
      debug('投稿の取得に失敗しました');
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function categoryPosts($c_id){
  debug('対象のカテゴリーの投稿を５件取得');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM posts WHERE category_id = :c_id ORDER BY post_id DESC LIMIT 5';
    $data = array(':c_id' => $c_id);

    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      debug('対象カテゴリーの投稿の取得に成功しました');
      $result = $stmt->fetchAll();
      return $result;
    }else {
      debug('対象カテゴリーの投稿の取得に失敗しました');
      return false;
    }
  } catch (Exception $e) {

  }

}
//DB接続関数
function dbConnect(){
  // ローカル用
  // //DB接続への準備
  // $dsn = 'mysql:dbname=ps_web;host=localhost;charset=utf8';
  // $user = 'root';
  // $password = 'root';
  // $options = array(
  //   //SQL実行失敗時にはエラーコードのみ設定
  //   PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
  //   //デフォルトフェッチモードを連想配列形式に設定
  //   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  //   //バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
  //   //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
  //   PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  // );
  // //PDOオブジェクト生成（DBへ接続）
  // $dbh = new PDO($dsn, $user, $password, $options);
  // return $dbh;
  // 本番用
  $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
  $db['dbname'] = ltrim($db['path'], '/');
  $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
  $user = $db['user'];
  $password = $db['pass'];
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
  );
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}
function queryPost($dbh, $sql, $data){
  //クエリ作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if (!$stmt->execute($data)) {
    debug('クエリに失敗しました。');
    $err_msg['common'] = MSG08;
    return 0;
  }
  return $stmt;
}













 ?>
