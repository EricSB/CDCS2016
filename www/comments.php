<?php
require_once "Include/db_helper.php";
require_once "Include/config.php";
require_once "Include/UtilityFunctions.php";
require_once "Include/csrf.php";
require_once "Include/auth_not_required.php";

$limit = 10;

if(isset($_GET['l'])) {
    $limit = $_GET['l'];
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['comment'])) {
	db_put_comment($_POST['comment']);
    }
}

$comments = db_get_comments($limit);


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/img/favicon.ico">

    <title>Cluster Deployment Company</title>

    <!-- Bootstrap core CSS -->
    <link href="/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/dist/css/custom.css" rel="stylesheet">
  </head>
  <body>

    <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">Cluster Deployment Company</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="/srvstatus.php?p=runscript">Server Status</a></li>
            <li class="active"><a href="/comments.php?l=10">Comments</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <?php
            if(!$logged_in){
            ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <?php
            }elseif($admin){
            ?>
            <li><a href="viewacct.php?u=<?php echo strip_only_alphabet_nocase($user);?>">My Account</a></li>
            <li><a href="runner.php">Code Runners</a></li>
            <li><a href="viewallacct.php">Manage Users</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php
            }else{
            ?>
            <li><a href="viewacct.php?u=<?php echo strip_only_alphabet_nocase($user);?>">My Account</a></li>
            <li><a href="runner.php">Code Runners</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php
            }
            ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>


        <div style="text-align: center; " class="col-md-12">
          <h2>Comments</h2>
          <h4>tell us how you really feel</h4>
        </div>
        <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-6 jumbotron">
        <?php
        while ($row = mysql_fetch_assoc($comments)) {
            $str = '';
            foreach($row as $r){
                $str = $str.' '.$r;
            }

	    $encoding='UTF-8';
            echo '<p style="padding-left: 10px;">' . htmlspecialchars($str, ENT_QUOTES | ENT_HTML401,$encoding) . '</p>';
        }
        ?>
       </div>
        <div class="col-md-3">
        </div>
        </div>

        <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-6">
          <form class="form-signin" method="post" action="/comments.php">
        <label for="input1" class="sr-only">Comment</label>
	<textarea id="input1" name="comment" class="form-control" rows="5" placeholder="Comment"></textarea>
        <button name="button" value="deploy" class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
      </form>
       </div>
        <div class="col-md-3">
        </div>
        </div>
      <hr>

      <footer>
        <p>&copy; 2016 CDC Inc. <a href="/privacy.txt">privacy policy</a></p>
      </footer>
    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/dist/js/jquery.min.js"></script>
    <script src="/dist/js/bootstrap.min.js"></script>
  </body>
</html>
