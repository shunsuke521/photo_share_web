
    </main>
    <footer id="footer">
      <div class="footer-link">
        <ul>
          <?php if(!empty($_SESSION)){ ?>
          <li class="main-page-link"><a href="index.php">メインページ</a></li>
          <?php }else{ ?>
          <li><a href="login.php">ログイン</a></li>
          <li><a href="signup.php">新規登録</a></li>
          <?php } ?>
        </ul>
      </div>
      <p class="copyright">@2020 Hosha</p>
    </footer>
    <script src="js/vendor/jquery-2.2.2.min.js"></script>
    <script>
      $(function(){
        //サイトの上下幅が画面の上下幅より小さくても、フッターを画面下に固定する
        var $ftr = $('#footer');
        if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
          $ftr.attr({'style': 'position:fixed; top:'+ (window.innerHeight - $ftr.outerHeight()) + 'px;'});
        }

        //トップページ
        $('.js-box img').addClass('move');
        $(window).scroll(function(){
          $('.js-box').each(function(){
            var imgPos = $(this).offset().top,
                scroll = $(window).scrollTop(),
                windowHeight = $(window).height();
                if(scroll > imgPos - windowHeight + windowHeight/3){
                  $(this).find('img').removeClass('move');
                }else {
                  $(this).find('img').addClass('move');
                }
          });
        });

        $('.js-box p').addClass('move');
        $(window).scroll(function(){
          $('.js-box').each(function(){
            var imgPos = $(this).offset().top,
                scroll = $(window).scrollTop(),
                windowHeight = $(window).height();
                if(scroll > imgPos - windowHeight + windowHeight/3){
                  $(this).find('p').removeClass('move');
                }else {
                  $(this).find('p').addClass('move');
                }
          });
        });

        //サイドバー
        var $sidebutton = $('#js-side-button'),
            $sidebar = $('#js-sidebar'),
            $main = $('#js-main'),
            duration = 300;

        $sidebutton.on('click', function(){
          $sidebar.toggleClass('open');
          if ($sidebar.hasClass('open')) {
              $sidebar.stop(true).animate({
              top:'122px'
            }, duration);
          }else {
            $sidebar.stop(true).animate({
              top:'-360px'
            }, duration);
          };
        });

        $main.on('click', function(){
          if ($sidebar.hasClass('open')) {
            $sidebar.stop(true).animate({
              top:'-360px'
            }, duration);
          }
        });

        //モーダル
        var $jsModal = $('.js-modal');
        var $jsModalWrapper = $('.js-modal-wrapper');
        var $jsModalBack = $('.js-modal-back');
        var $jsButton = $('.js-button');
        var msg = $jsModal.text();

        if (msg.replace(/^[\s ]+|[\s ]+$/g, "").length) {
          $jsModalWrapper.css(
            'display','block'
          );
          $jsModalBack.css(
            'display','block'
          );
        }

        $jsButton.on('click', function(){
          $jsModalWrapper.css(
            'display','none'
          );
          $jsModalBack.css(
            'display','none'
          );
        });

        $jsModalBack.on('click', function(){
          $jsModalWrapper.css(
            'display','none'
          );
          $jsModalBack.css(
            'display','none'
          );
        });

        //画像ライブプレビュー
        var $dropArea = $('.image');
        var $dropProf = $('.prof-pic-block');
        var $dropImg = $('.js-prof-pic');
        var $fileInput = $('.input-file');

        $dropArea.on('dragover', function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border', '3px #9a1b27 dashed');
        });
        $dropArea.on('dragleave', function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border', 'none');
        });

        $dropImg.on('dragover', function(e){
          e.stopPropagation();
          e.preventDefault();
          $dropProf.css('border', '3px #9a1b27 dashed');
        });
        $dropImg.on('dragleave', function(e){
          e.stopPropagation();
          e.preventDefault();
          $dropProf.css('border', 'none');
        });

        $fileInput.on('change', function(e){
          $dropArea.css('border', 'none');
          var file = this.files[0],                   //files配列にファイルが入っている
              $img = $(this).siblings('.prev-img'),   //jQueryのsiblingsメソッドで兄弟のimgを取得
              fileReader = new FileReader();          //ファイルを読み込むFileReaderオブジェクト

          //読み込みが完了した際のイベントハンドラ imgのsrcにデータをセット
          fileReader.onload = function(event){
            //読み込んだデータをimgに設定
            $img.attr('src', event.target.result).show();
          };
          console.log($img.attr('src'));

          //画像読み込み
          fileReader.readAsDataURL(file);

        });


        var $inputFile = $('.file-input');

        $inputFile.on('change', function(e){
          $dropArea.css('border', 'none');
          var file = this.files[0],                   //files配列にファイルが入っている
              $img = $('.prev-img'),
              fileReader = new FileReader();          //ファイルを読み込むFileReaderオブジェクト

          //読み込みが完了した際のイベントハンドラ imgのsrcにデータをセット
          fileReader.onload = function(event){
            //読み込んだデータをimgに設定
            $img.attr('src', event.target.result).show();
          };
          console.log($img.attr('src'));

          //画像読み込み
          fileReader.readAsDataURL(file);

        });

        //テキストエリアカウント
        var $countUp = $('.js-count'),
            $countView = $('.js-count-view');
        $countUp.on('keyup', function(e){
          $countView.html($(this).val().length);
        });

        var $star1 = $('#star1'),
            $star2 = $('#star2'),
            $star3 = $('#star3'),
            $star4 = $('#star4'),
            $star5 = $('#star5'),
            $stars = $('#star1'),
            $stars2 = $('#star1, #star2'),
            $stars3 = $('#star1, #star2, #star3'),
            $stars4 = $('#star1, #star2, #star3, #star4'),
            $stars5 = $('#star1, #star2, #star3, #star4, #star5'),
            $heart = $('#heart');

        $star1.on('mouseover', function(){
          $(this).css('color', '#ffbe00');
        });
        $star1.on('mouseout', function(){
          $(this).css('color', '');
        });

        $star2.on('mouseover', function(){
          $stars2.css('color', '#ffbe00');
        });
        $star2.on('mouseout', function(){
          $stars2.css('color', '');
        });

        $star3.on('mouseover', function(){
          $stars3.css('color', '#ffbe00');
        });
        $star3.on('mouseout', function(){
          $stars3.css('color', '');
        });

        $star4.on('mouseover', function(){
          $stars4.css('color', '#ffbe00');
        });
        $star4.on('mouseout', function(){
          $stars4.css('color', '');
        });

        $star5.on('mouseover', function(){
          $stars5.css('color', '#ffbe00');
        });
        $star5.on('mouseout', function(){
          $stars5.css('color', '');
        });

        $star1.on('click', function(){
          if($star1.hasClass('star-hv')){
            $stars5.removeClass('star-hv');
          }else {
            $(this).addClass('star-hv');
          }
        });

        $star2.on('click', function(){
          if($star1.hasClass('star-hv')){
            $stars5.removeClass('star-hv');
          }else {
            $stars2.addClass('star-hv');
          }
        });

        $star3.on('click', function(){
          if($star1.hasClass('star-hv')){
            $stars5.removeClass('star-hv');
          }else {
            $stars3.addClass('star-hv');
          }
        });

        $star4.on('click', function(){
          if($star1.hasClass('star-hv')){
            $stars5.removeClass('star-hv');
          }else {
            $stars4.addClass('star-hv');
          }
        });

        $star5.on('click', function(){
          if($star1.hasClass('star-hv')){
            $stars5.removeClass('star-hv');
          }else {
            $stars5.addClass('star-hv');
          }
        });

        $heart.on('mouseover', function(){
          $(this).css('color', '#e054e8');
        });
        $heart.on('mouseout', function(){
          $(this).css('color', '');
        });

        $heart.on('click', function(){
          if($(this).hasClass('heart-hv')){
            $(this).removeClass('heart-hv');
          }else {
            $(this).addClass('heart-hv');
          }
        });

        //画像切り替え
        var $switchImgSubs = $('.js-switch-img-sub'), //3つのimgタグに全て同じクラス名をつけているのでidではなくclass
            $switchImgMain = $('#js-switch-img-main');
        $switchImgSubs.on('click', function(e){
          console.log('hahaha');
          $switchImgMain.attr('src', $(this).attr('src'));
        });

        //コメント表示
        var $comment = $('#js-comment'),
            $commentDisplay = $('#js-comment-display');

        $comment.on('click', function(){
          if ($commentDisplay.hasClass('active')) {
            $commentDisplay.removeClass('active');
            $commentDisplay.slideUp(500);
            $comment.text('コメントを見る');
          }else {
            $commentDisplay.addClass('active');
            $commentDisplay.slideDown(500);
            $comment.text('コメントを閉じる');
          }
        });

        //お気に入り登録・削除
        var $favorite,
            favoritePostId;
        $favorite = $('#heart') || null; //nullはnull値という値で、変数の中身が空であることを明示するために使う値
        favoritePostId = $favorite.data('postid') || null;
        //数値の0はfalseと判定されてしまうが、post_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
        if (favoritePostId !== undefined && favoritePostId !== null) {
          $favorite.on('click', function(){
            $.ajax({
              type:"POST",
              url:"ajaxLike.php",
              data:{ postId : favoritePostId}
            }).done(function(data){
              console.log('Ajax Success');
            }).fail(function(msg){
              console.log('Ajax Error');
            });
          });
        }

        //投稿の評価・削除
        var $evaluation,
            evaluationPostId,
            postEvaluation;
        $evaluation = $('.star') || null;
        evaluationPostId = $evaluation.data('postid') || null;

        if (evaluationPostId !== undefined && favoritePostId !== null) {
          $evaluation.on('click', function(){
            var $this = $(this);

            if ($this.attr('id') === 'star1') {
              postEvaluation = 1;
            }else if ($this.attr('id') === 'star2') {
              postEvaluation = 2;
            }else if ($this.attr('id') === 'star3') {
              postEvaluation = 3;
            }else if ($this.attr('id') === 'star4') {
              postEvaluation = 4;
            }else if ($this.attr('id') === 'star5') {
              postEvaluation = 5;
            }

            $.ajax({
              type:"POST",
              url:"ajaxEval.php",
              data:{
                postId : evaluationPostId,
                postEval : postEvaluation
              }
            }).done(function(data){
              console.log('Ajax Success');
            }).fail(function(msg){
              console.log('Ajax Error');
            });
          });
        }

        //画像hover時の星とハート表示
        var $imgHover = $('.js-img-hover');

        $imgHover.on('mouseover', function(e){
          console.log('hahaha');
          $(this).find('img').stop(true).animate({
            opacity:0.6
          }, 500);
          $(this).find('.white-back').stop(true).animate({
            opacity:1
          }, 500);
        });

        $imgHover.on('mouseout', function(e){
          $(this).find('img').stop(true).animate({
            opacity:1
          }, 500);
          $(this).find('.white-back').stop(true).animate({
            opacity:0
          }, 500);
        });
      });
    </script>
  </body>
</html>
