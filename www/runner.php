<?php

require_once "Include/auth.php";
require_once "Include/UtilityFunctions.php";

//if(isset($_POST['project']) && isset($_POST['runner'])) {
//
//	//Check CSRF Token to fix a really fun way to get root :) CSRF + evil gitlab project + runner + evil bashrc file
//        if(!isset($_POST['csrfToken']) || strcmp($_POST['csrfToken'], $_SESSION[csrfToken]) !== 0)
//        {
//            header('Location: /runner.php');
//            exit;
//        }
//
//        $project = strip_only_alphabet_nocase($_POST['project']);
//        $runner = strip_only_alphabet_nocase($_POST['runner']);
//
//	if(isset($_POST['button']))
//	{
//		$action = strip_only_alphabet_nocase($_POST['button']);
//        }
//	else
//	{
//            header('Location: /runner.php');
//	    exit;
//        }
//
//        if($action != '') {
//
//            if($action == 'stdin' && isset($_POST['stdin']))
//	    {
//                $stdin = strip_only_alphabet_nocase($_POST['stdin']);
//            }
//
//            $result = do_runner($action, $project, $runner, strip_only_alphabet_nocase($_SESSION['username']), $stdin);
//	}
//}
?>
<!DOCTYPE html> <html lang="en">
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
    <link href="/dist/css/signin2.css" rel="stylesheet">
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
            <li><a href="/comments.php?l=10">Comments</a></li>
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
            <li><a href="viewacct.php?u=<?php echo $user;?>">My Account</a></li>
            <li class="active"><a href="runner.php">Code Runners</a></li>
            <li><a href="viewallacct.php">Manage Users</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php
            }else{
            ?>
            <li><a href="viewacct.php?u=<?php echo $user;?>">My Account</a></li>
            <li class="active"><a href="runner.php">Code Runners</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php
            }
            ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
 <div class="row">
        <div style="text-align: center;" class="col-md-12">
          <h2>DON'T Run Some Code</h2>
        </div>
      </div>
      <hr>
      <div class="row">
	<center><h3 style="color:red;">NOT IMPLEMENTED BECAUSE OF LACK OF TIME/AND HIGH RISK</h3></center>
	<br />
	<center><h3 style="color:red;">Potential for some nasty nested command injection vulnerabilities</h3></center>
	<br />
	<center><h3 style="color:red;">Please use crconsole utility on the shell server it is easier to use anyway :)</h3></center>
	<!--
       <form class="form-signin" method="post" action="/runner.php">
        <div class="col-md-6">
        <div class="aaa">
        <label for="input1" class="sr-only">Project</label>
        <input name="project" type="text" id="input1" class="form-control" placeholder="Project" value="<?php echo $project;?>" required>
        <label for="input2" class="sr-only">Runner</label>
        <input name="runner" type="text" id="input2" class="form-control" placeholder="Runner" value="<?php echo $runner;?>" required>
        <textarea id="input3" name="stdin" class="form-control" rows="5" placeholder="Stdin(optional)"></textarea>
        <input type="hidden" name="csrfToken" value="<?php echo $_SESSION['csrfToken'] ?>" >
	</div>
       </div>
        <div class="col-md-6">
        <div class="aaa">
        <button name="button" value="deploy" class="btn btn-lg btn-primary btn-block" type="submit">Deploy</button>
        <button name="button" value="clean" class="btn btn-lg btn-primary btn-block" type="submit">Clean</button>
        <button name="button" value="build" class="btn btn-lg btn-primary btn-block" type="submit">Build</button>
        <button name="button" value="run" class="btn btn-lg btn-primary btn-block" type="submit">Run</button>
        <button name="button" value="stdin" class="btn btn-lg btn-primary btn-block" type="submit">Set Stdin</button>
        <button name="button" value="stdout" class="btn btn-lg btn-primary btn-block" type="submit">Print Stdout</button>
        </div>
       </div>
      </form>
      </div>

      <div class="row">
      <div style="text-align: center;" class="col-md-12">
      <h2>Output</h2>
      <div class="col-md-2">
      </div>
      <div class="col-md-8 jumbotron">
      <p>
      <?php
      //if($fnt != ''){
      //	  echo htmlspecialchars($result, ENT_COMPAT, 'UTF-8');
      //}
      ?>
      </p>-->
      </div>
      <div class="col-md-2">
      </div>
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
