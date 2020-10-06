<body id="js-modal">
  <header>
    <a href="index.php"><h1>Photo Share Web</h1></a> <!-- /PHPのif文でtopページだったらfont-size:80px;にしたい -->
    <div class="header-right">
      <ul>
        <?php if(empty($_SESSION['user_id'])){ ?>
        <li><a href="login.php">ログイン</a></li>
        <li><a href="signup.php" class="signup">新規登録</a></li>
        <?php }else{ ?>
        <li><i class="fa fa-bars" id="js-side-button"></i></li>
        <?php } ?>
      </ul>
    </div>
  </header>
  <main id="js-main">
