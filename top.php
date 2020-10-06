<?php
require('function.php');

debug('---------------------------------------');
debug('---トップページ---');
debug('---------------------------------------');
debugLogStart();

require('auth.php');
 ?>
<?php
$siteTitle = "Top";
require('head.php');
//↑これの順番が逆だと読み込んでくれない
 ?>
<?php
require('header.php');
 ?>

  <div class="image-wrapper">
    <div class="catch-phrase">
      撮影の腕に自信がある人も！そうでない人も！<br>
      自慢の一枚を世界中のみんなと共有しよう！！
    </div>
  </div>
  <div class="middle-wrapper">
    <div class="middle-left js-box">
      <p>どんな写真でもOK！<br>
      どんどん投稿しよう！</p>
    </div>
    <div class="middle-right js-box">
      <img src="img/top_img6.png">
    </div>
  </div>
  <div class="middle-wrapper2">
    <div class="middle2-left js-box">
      <img src="img/top_img3.png">
    </div>
    <div class="middle2-right js-box">
      <p>いろんな人からコメントをもらえる！<br>
      アドバイスをもらってもっと腕が上達するかも！</p>
    </div>
  </div>
  <div class="bottom-wrapper">
    <div class="bottom-middle js-box">
      <p>今日からあなたも写真家の仲間入り</p>
      <a href="signup.php" class="signup">新規登録はこちら</a>
    </div>
  </div>




<?php
require('footer.php');
 ?>
