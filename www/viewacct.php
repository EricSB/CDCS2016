<?php

require_once "Include/config.php";
require_once "Include/auth.php";
require_once "Include/db_helper.php";
require_once "Include/UtilityFunctions.php";

$public_key = '';
$private_key = '';

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
//---error stuff
    if(isset($_GET['error'])) {
        $p_error = strip_only_alphabet_nocase($_GET['error']);
    } else {
	$p_error = 'f';

	if (isset($_GET['pwsuccess'])) {
	   $success = strip_only_alphabet_nocase($_GET['pwsuccess']);
 	}
    }
    if(isset($_GET['ccsuccess'])) {
	$cc_success = strip_only_alphabet_nocase($_GET['ccsuccess']);
    } else {
	$cc_success = "na";
    }
//----

    if(!isset($_GET['u']))
    {
	$user = strip_only_alphabet_nocase($_SESSION['username']);
    }
    else
    {
        $user = strip_only_alphabet_nocase($_GET['u']);

    	if(strcmp($user, $_SESSION['username']) !== 0)
    	{
		if($admin == false)
		{
      			header('Location: /viewacct.php');
	       	       	exit();
		}
        }
    }

        $ds = ldap_connect($ldap_uri);

        if(!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)){
                die("LDAP - Could not set LDAPv3\r\n");
        }
        else if(!ldap_start_tls($ds)) {
                die("LDAP - Could not start secure TLS connection");
        } else {

                // now we need to bind to the ldap server as administrator
                $bth = ldap_bind($ds, "cn=admin,dc=saxophoneguerilla,dc=com", ldapAdminPassword) or die("Could not bind to LDAP server for user registration");

                if($bth)
                {
                        $sr = ldap_search($ds, "ou=users,dc=saxophoneguerilla,dc=com", "(uid=" . strip_only_alphabet_nocase($user) . ")");
                        $entry = ldap_get_entries($ds, $sr);
                        $gidNumber = $entry[0]["gidnumber"][0];

		        $privkey = $entry[0]["sshprivatekey"][0];
		        $pubkey = $entry[0]["sshpublickey"][0];
                        $gidNumber = $entry[0]["gidnumber"][0];

                        if(strcmp($gidNumber, "13337") == 0)
                        {
				$group = "admin";
                        }
                        else
                        {
				$group = "customer";
                        }
		}
		else
		{
			die("something went wrong....");
		}

	}

    $public_key = $pubkey;

    if(strcmp($user, $_SESSION['username']) == 0)
    {
	 $private_key = $privkey;
    }
    else
    {
        $private_key = "MD5 Hash of the users private key: " . md5($privkey);
    }

    $encoding='UTF-8';
    $public_key = htmlspecialchars($public_key, ENT_QUOTES | ENT_HTML401,$encoding);
    $private_key = htmlspecialchars($private_key, ENT_QUOTES | ENT_HTML401,$encoding);

    mysql_connect(mysql_host, mysql_username, mysql_password);
    mysql_select_db(mysql_db) or die( "Unable to select database");
    $query=  "select creditcard from credentials where username = '" . mysql_escape_string($user) . "'"; //redundant but you can't be retroactively paranoid..
    $result=mysql_query($query);
    mysql_close();

    if (mysql_num_rows($result) >= 1)
    {
	$row = mysql_fetch_row($result);
	$cc = credit_card_strip_input($row[0]);
    }
    else
    {
	$cc = "Invalid User";
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
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <?php
            }elseif($admin){
            ?>
            <li class="active"><a href="viewacct.php?u=<?php echo htmlspecialchars(strip_only_alphabet_nocase($user), ENT_QUOTES | ENT_HTML401,$encoding); ?>" >My Account</a></li>
            <li><a href="runner.php">Code Runners</a></li>
            <li><a href="viewallacct.php">Manage Users</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php
            }else{
            ?>
            <li class="active"><a href="viewacct.php?u=<?php echo $user;?>">My Account</a></li>
            <li><a href="runner.php">Code Runners</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php
            }
            ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>


    <div style="text-align: center; margin-top: 15px;" class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-12">
          <h2>Account Management: <?php echo htmlspecialchars(strip_only_alphabet_nocase($user), ENT_QUOTES | ENT_HTML401,$encoding);?></h2>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-6">
          <h2>Account Info</h2>
          <table class="table">
        <tbody>
        <tr>
        <td>Credit Card</td> <td><?php echo $cc;?></td>
        </tr>
        <tr>
        <td>Group</td> <td><?php echo $group;?></td>
        </tr>
        </tbody>
        </table>
          <h3>Public Key</h3>
          <textarea class="form-control" rows="3"><?php echo $public_key;?></textarea>
          <h3>Private Key</h3>
          <textarea class="form-control" rows="20"><?php echo $private_key;?></textarea>
        </div>
        <div class="col-md-6">
          <h2>Edit Account</h2>
<?php if($p_error !== 'f') { ?>
	<div class="alert alert-danger">
<?php
                if($p_error == 'p') {
                        echo "error - your passwords don't match";
                } else if ($p_error == 'm') {
                        echo 'error - please fill in all fields';
                } else if ($p_error == 'i') {
                        echo 'error - incorrect password';
                } else {
                        echo 'something went wrong';
                }
?> </div> <?php
  	 } else if ($success == 'true') {
?>
		<div class="alert alert-success">
			<strong> Password Successfully Changed.</strong>
		</div>
<?php
	}
?>
       <form class="form-signin" method="post" action="/updatepass.php">
       <h3 class="form-signin-heading">Change Password</h3>
	<label for="inputPassword" class="sr-only">Old Password</label>
        <input name="oldpassword" type="password" id="inputPassword" class="form-control" placeholder="Old Password" required>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <label for="inputPassword2" class="sr-only">Repeat Password</label>
        <input name="password2" type="password" id="inputPassword2" class="form-control" placeholder="Repeat Password" required>
	<input type="hidden" name="csrfToken" value="<?php echo $_SESSION['csrfToken'] ?>" >
	<input type="hidden" name="username" value="<?php echo $user ?>" >
        <button class="btn btn-lg btn-primary btn-block" type="submit">Update Password</button>
      </form>
       <form class="form-signin" method="post" action="/updatecc.php">
       <h3 class="form-signin-heading">Change Credit Card</h3>
        <label for="inputcc" class="sr-only">Credit Card</label>
        <input name="creditcard" type="text" id="inputcc" class="form-control" placeholder="Credit Card" required>
        <input type="hidden" name="csrfToken" value="<?php echo $_SESSION['csrfToken'] ?>" >
        <input type="hidden" name="username" value="<?php echo $user ?>" >
	<button class="btn btn-lg btn-primary btn-block" type="submit">Update Credit Card</button>
      </form>

<?php
	if($cc_success == "true") {
?>
		<div class="alert alert-success">
			<strong>Your credit card number was successfully changed.</strong>
		</div>
<?php
	} else if($cc_success == "false") {
?>
		<div class="alert alert-danger">
			<strong>Something went wrong.</strong>
		</div>
<?php
	}
?>
	</div>
      </div>

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
