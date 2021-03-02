<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $this->web_title; ?></title>
  <base href="<?= $basePath; ?>">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Styles -->
  <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
  <!--link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"-->
  <link href="assets/css/icon.css" rel="stylesheet">    
  <link href="assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
  <link href="assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">

  <!-- Theme Styles -->
  <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
   <!-- added by husnanw -->
  <link href="assets/css/dropzone.min.css" rel="stylesheet" type="text/css"/>
  <link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
  <!-- ### -->
  <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
  <link href="assets/plugins/select2/css/select2.css" rel="stylesheet"> 
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <!--link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"-->
  <link rel="stylesheet" href="assets/css/fonts.css">

  <!-- Script -->
  <!-- Javascripts -->
  <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
  <script src="assets/plugins/materialize/js/materialize.min.js"></script>
  <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
  <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
  <script src="assets/plugins/select2/js/select2.min.js"></script>
  <script src="assets/js/alpha.min.js"></script>
  <script src="assets/js/moment.js"></script>
</head>
    <?php require_once $viewPath; ?>
</html>
