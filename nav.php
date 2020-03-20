<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
		<meta charset="iso-8859-1">
		<title>Charter</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-title" content="Charter">
		<link rel="apple-touch-icon-precomposed" sizes="57x57"    href="touchicons/57.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72"    href="touchicons/72.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114"  href="touchicons/114.png">
		<link rel="apple-touch-icon-precomposed" sizes="144x144"  href="touchicons/144.png">
		<link rel="apple-touch-icon" href="touchicons/114.png">
		<meta name="application-name" content="Charter">
		<meta name="msapplication-TileImage" content="touchicons/144.png">
		<meta name="msapplication-TileColor" content="#FFFFFF">
		<meta name="mobile-web-app-capable" content="yes">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
		
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="css/styles.css" rel="stylesheet">
	</head>
	<body>
<nav class="navbar navbar-fixed-top header">
 	<div class="col-md-12">
        <div class="navbar-header">
          
          <a href="index.php" class="navbar-brand">Charter</a>
          <button type="button" data-toggle="modal" data-target="#logout" class="navbar-toggle">Logout</button>
      
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse1">
          <form class="navbar-form pull-left">
              <!--<div class="input-group" style="max-width:470px;">
                <input type="text" class="form-control" placeholder="Search" name="srch-term" id="srch-term">
                <div class="input-group-btn">
                  <button class="btn btn-default btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                </div>
              </div>-->
          </form>
          <ul class="nav navbar-nav navbar-right">
             <li><a href="//www.facebook.com" target="_ext"><?php echo $forename . ' ' . $surname; ?></a></li>
             <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i></a>
                <ul class="dropdown-menu">
                  <li><a href="#" data-toggle="modal" data-target="#logout">Logout</a></li>
			  </ul>
             </li>
           </ul>
        </div>	
     </div>	
</nav>
<div class="navbar navbar-default" id="subnav">
    <div class="col-md-12">
        <div class="navbar-header">
          
          <!--<a href="#" style="margin-left:15px;" class="navbar-btn btn btn-default btn-plus dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-home" style="color:#dd1111;"></i> Home <small><i class="glyphicon glyphicon-chevron-down"></i></small></a>
          <ul class="nav dropdown-menu">
              <li><a href="#"><i class="glyphicon glyphicon-user" style="color:#1111dd;"></i> Profile</a></li>
              <li><a href="#"><i class="glyphicon glyphicon-dashboard" style="color:#0000aa;"></i> Dashboard</a></li>
              <li><a href="#"><i class="glyphicon glyphicon-inbox" style="color:#11dd11;"></i> Pages</a></li>
              <li class="nav-divider"></li>
              <li><a href="#"><i class="glyphicon glyphicon-cog" style="color:#dd1111;"></i> Settings</a></li>
              <li><a href="#"><i class="glyphicon glyphicon-plus"></i> More..</a></li>
          </ul>-->
          
          
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse2">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          </button>
      
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse2">
          <ul class="nav navbar-nav navbar-right">
             <li 
			 <?php
			 if(!isset($_GET['appendix']) and !isset($_GET['signatories'])) echo 'class="active"'; 
			 echo '><a href="index.php">Charter</a></li>
             <li ';
			 if(isset($_GET['appendix'])) echo 'class="active"'; echo '><a href="index.php?appendix">Appendix</a></li>
             <li ';
			 if(isset($_GET['signatories'])) echo 'class="active"'; echo '><a href="index.php?signatories">Signatories</a></li>';
			 ?>
           </ul>
        </div>	
     </div>	
</div>

<!--main-->
<div class="container" id="main">
<?php
if (!isset($littlepage)) {
	echo '<div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">' . (isset($title) ? '<h4>' . $title . '</h4>' : null) . '</div><div class="panel-body">';
	$extrafoot = true;
}
if (isset($extrafoot)) {
	if ($extrafoot) $foot = '</div></div></div></div>';
	else $foot = '';
} else $foot = '';
$foot .= '
</div>
	<!-- script references -->
		<script src="js/scripts.js"></script>
		<div class="modal fade" id="logout" tabindex="-1" role="dialog" aria-labelledby="logoutLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<a href="https://www.youtube.com/watch?v=btEpF334Rtc"><button type="button" class="close" aria-hidden="true">&times;</button></a>
				<h4 style="border: 0; border-bottom-width: 0px; padding-bottom: 0px;" class="modal-title" id="logoutLabel">Logout</h4>
			</div>
			<div class="modal-body">
				<script>
				$(document).ready(function(){
				' . "
				$('#logout').on('shown.bs.modal', function (e) {
					$('#logoutiframe').attr('src', 'logout.php'); 
					$('#logoutmusiciframe').attr('src', 'https://www.youtube.com/embed/btEpF334Rtc');
				})
				" . '   
				});
				</script> 
				<iframe id="logoutiframe" frameborder="0" style="width: 100%; height: 40px;"><a href="logout.php">Confirm Logout</a></iframe>
				<br/><iframe width="420" id="logoutmusiciframe" height="315" frameborder="0" allowfullscreen></iframe>
			</div>
			<div class="modal-footer">
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
	</body>
</html>';
?>