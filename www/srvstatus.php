<?php

require_once "Include/UtilityFunctions.php";
require_once "Include/auth_not_required.php";

//not checking host because we do not care if this connection is MITM or account hacked attacker can register account and have shell access on here anyway would be bad outside CDC context tho..
$runner1 = shell_exec("/usr/bin/sshpass  -p 'cdc' /usr/bin/ssh  -oStrictHostKeyChecking=no status@192.168.1.22 \"ps aux --sort -rss\" | /usr/bin/awk '{print $1\",\"$2\",\"$3\",\"$4\" \"$11$12$13}'");
$runner2 = shell_exec("/usr/bin/sshpass  -p 'cdc' /usr/bin/ssh  -oStrictHostKeyChecking=no status@192.168.1.23 \"ps aux --sort -rss\" | /usr/bin/awk '{print $1\",\"$2\",\"$3\",\"$4\",\"$11$12$13}'");

//Escape output of command
$encoding='UTF-8';
$runner1 = htmlspecialchars($runner1, ENT_QUOTES | ENT_HTML401,$encoding);
$runner2 = htmlspecialchars($runner2, ENT_QUOTES | ENT_HTML401,$encoding);
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
            <li class="active"><a href="/srvstatus.php?p=runscript">Server Status</a></li>
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
          <h2>Server Status</h2>
          <p><a class="btn btn-default" href="/srvstatus.php?p=runscript" role="button">Refresh</a></p>
        </div>
        <div class="col-md-6">
          <h2>Runner 1</h2>
                <table class="table">
                <tbody>
            <?php
            $arr = explode("\n", $runner1);
            foreach($arr as $a){
                echo "<tr>";
                $arr2 = explode(",", $a);
                foreach($arr2 as $aa){ ?>
                    <td><?php echo $aa;?></td>
                <?php }
                echo "<tr>";
                }
                ?>
        </tbody>
        </table>
        </div>
        <div class="col-md-6">
          <h2>Runner 2</h2>
                <table class="table">
                <tbody>
            <?php
            $arr = explode("\n", $runner2);
            foreach($arr as $a){
                echo "<tr>";
                $arr2 = explode(",", $a);
                foreach($arr2 as $aa){ ?>
                    <td><?php echo $aa;?></td>
                <?php }
                echo "<tr>";
                }
                ?>
        </tbody>
        </table>
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
