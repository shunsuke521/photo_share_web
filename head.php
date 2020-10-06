<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title><?php echo $siteTitle; ?> | Photo Share Web</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Odibee+Sans&display=swap" rel="stylesheet">
    <style>
      <?php
      if (basename($_SERVER['PHP_SELF']) === 'top.php') {
       ?>
       header{
         position: fixed;
         overflow: hidden;
         width: 100%;
         z-index: 3;
         background: transparent !important;
         border: none !important;
         top: 0;
       }
       main{
         padding: 0 !important;
       }
      <?php
      }
       ?>
    </style>
  </head>
