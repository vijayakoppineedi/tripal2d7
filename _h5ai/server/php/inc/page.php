<?php 
header("Content-type: text/html;charset=utf-8"); ?>
<!DOCTYPE html>
<!--[if lt IE 10]>
<html class="no-js no-browser" lang="en">
<![endif]-->
<!--[if gt IE 9]>
<!-->
<html lang="en" class="no-js browser">
<!--<![endif]-->
 <head> 
  <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>index · powered by h5ai 0.26.1 (http://larsjung.de/h5ai/)</title>
	<meta name="description" content="index - powered by h5ai 0.26.1 (http://larsjung.de/h5ai/)">
	 <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="shortcut icon" href="<?php echo APP_HREF; ?>client/images/favicon/favicon-16-32.ico">
      <link rel="apple-touch-icon-precomposed" type="image/png" href="<?php echo APP_HREF; ?>client/images/favicon/favicon-152.png">
	  <link rel="stylesheet" href="<?php echo APP_HREF; ?>client/css/styles.css">
	  <script src="<?php echo APP_HREF; ?>client/js/scripts.js" data-module="main"></script>
 </head>
<body>
<div id="topbar" class="clearfix">
  <ul id="navbar"></ul>
</div>
<div id="bottombar" class="clearfix">
  <span class="left">
    <span class="noJsMsg">
	  ⚡ JavaScript disabled! ⚡
	</span>
	<span class="noBrowserMsg">
	  ⚡ Works best in <a href="http://browsehappy.com">modern browsers</a>! ⚡
	</span>
  </span>
  <span class="right">
    <a href="http://larsjung.de/h5ai/" title="h5ai 0.26.1 · a modern HTTP web server index">powered by h5ai 0.26.1</a>
  </span>
  <span class="center"></span>
</div>
<div id="sidebar">
  <div id="settings"></div>
</div>
<div id="fallback"><?php echo FALLBACK; ?></div>

</body>
</html>