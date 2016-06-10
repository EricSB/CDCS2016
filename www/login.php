<?php

require_once "Include/UtilityFunctions.php";
require_once "Include/auth_not_required.php";
require_once "Include/config.php";
require_once "Include/csrf.php";

$login_error = false;
$login_error_msg = '';

if($logged_in){
    header('Location: /');
} else{

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(isset($_POST['username']) && isset($_POST['password'])) {

            $ldapUsername  = "uid=" . strip_only_alphabet_nocase($_POST['username']). ",ou=users,dc=saxophoneguerilla,dc=com";
            $ldapPassword = $_POST['password'];

            $ds = ldap_connect($ldap_uri);

            if(!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)){
                die("LDAP - Could not set LDAPv3\r\n");
            }
            else if(!ldap_start_tls($ds)) {
                die("LDAP - Could not start secure TLS connection");
            } else {

                // now we need to bind to the ldap server
                $bth = ldap_bind($ds, $ldapUsername, $ldapPassword);

                if($bth)
                {
	                //LOGIN SUCCESS

	                //Regenerate session ID to prevent session fixation attacks
	                session_regenerate_id();

                    //Session lasts 25 minutes then you need to login again
	                $lifetime= 60 * 25;
                    session_set_cookie_params($lifetime);

	                //Check if user is supposed to be administrator or customer

                    //Store user information in session

                    $_SESSION['csrfToken'] = genCSRFToken();
 	                $_SESSION['username'] = strip_only_alphabet_nocase($_POST['username']);

	                //look in LDAP to check if they exist
	                $sr = ldap_search($ds, "ou=users,dc=saxophoneguerilla,dc=com", "(uid=" . strip_only_alphabet_nocase($_POST['username']) . ")");


                    $entry = ldap_get_entries($ds, $sr);
                    $gidNumber = $entry[0]["gidnumber"][0];

	                if(strcmp($gidNumber, "13337") == 0)
                    {
                        $_SESSION['role'] = "admin";
                    }
                    else
                    {
                        $_SESSION['role'] = "customer";
                    }

                    ldap_close($bth);

                    header('Location: /');
	             	exit();


                }
                else
                {
	                //LOGIN FAILED
                    $login_error = true;
                    $login_error_msg = "Invalid username or password";

                }
            }
        }
    }
    
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
    <link href="/dist/css/signin.css" rel="stylesheet">
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
            <li class="active"><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <?php
            }
            ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <form class="form-signin" method="post" action"/login.php">
       <h2 class="form-signin-heading">Into the Cluster...</h2>
        <label for="inputUsername" class="sr-only">Username</label>
        <input name="username" type="text" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->
    <?php
    if($login_error){
    ?>
    <div class="alert alert-danger container" style="text-align: center;">
        <p>Error: <?php echo $login_error_msg;?></p>
    </div>
    <?php
    }
    ?>

      <hr>

      <footer>
        <p>&copy; 2015 CDC Inc. <a href="/privacy.txt">privacy policy</a></p>
      </footer>
    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/dist/js/jquery.min.js"></script>
    <script src="/dist/js/bootstrap.min.js"></script>
  </body>
</html>
<?php
}
?>
