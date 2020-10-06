<?php
require('function.php');

debug('---------------------------------------');
debug('---ログアウト---');
debug('---------------------------------------');
debugLogStart();

debug('ログアウトします');
//セッションを削除
session_destroy();
debug('トップページへ遷移します');
header("Location:top.php");



 ?>
