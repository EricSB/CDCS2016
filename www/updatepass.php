<?php

include "Include/UtilityFunctions.php";
include "Include/auth.php";
include "Include/config.php";
include "Include/gituser.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //errors -
    //m - missing fields
    //p - passwords don't match
    //i - incorrect password
    //o - something else
    //f - false/no error

    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if(!(isset($_POST['username']) && isset($_POST['oldpassword']) && isset($_POST['password']) && isset($_POST['password2']) && isset($_POST['csrfToken']))) {
        // Fail: Not all fields are filled
        header('Location: /viewacct.php?u=' . $user . '&error=m');
	//header('Location: /viewacct.php?u=' . $user);
        exit;
    }
    else if (!(strcmp($_POST['csrfToken'], $_SESSION['csrfToken']) == 0)) {
        // Fail: Cerf tokens don't match
	header('Location: /viewacct.php?u=' . $user . '&error=o');
	//header('Location: /viewacct.php?u=' . $user);
        exit;
    }
    else if (!(strcmp($password, $password2) == 0)) {
        // Fail: Passwords don't match
        header('Location: /viewacct.php?u=' . $user . '&error=p');
	//header('Location: /viewacct.php?u=' . $user);
        exit;
    }


    // Attempt LDAP Connection
    $user = strip_only_alphabet_nocase($_SESSION['username']);
    $ldapUsername  = "uid=" . strip_only_alphabet_nocase($user). ",ou=users,dc=saxophoneguerilla,dc=com";
    $ldapPassword = $_POST['oldpassword'];
    $ds = ldap_connect("ldap://ldap.saxophoneguerilla.com");
    if(!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)) {
        die("LDAP - Could not set LDAPv3\r\n");
    }
    else if(!ldap_start_tls($ds)) {
        die("LDAP - Could not start secure TLS connection");
    }

    // Attempt to Bind
    $bth = ldap_bind($ds, $ldapUsername, $ldapPassword);
    if (!($bth)) {
	header('Location: /viewacct.php?u=' . $user . '&error=i');
	exit;
        //die("LDAP - Could not start secure TLS connection");
    }

    //look in LDAP to check if they exist
    $sr = ldap_search($ds, "ou=users,dc=saxophoneguerilla,dc=com", "(uid=" . strip_only_alphabet_nocase($user) . ")");
    $entry = ldap_get_entries($ds, $sr);

    $userdata=array();
    $userdata["userpassword"][0] = '{MD5}' . base64_encode(pack('H*',md5($password)));
    ldap_modify($ds, $ldapUsername, $userdata);

    // Update the Gitlab user's password.
    gituser_change_pass($user, $password);

    header('Location: /viewacct.php?u=' . $user . "&pwsuccess=true");
    exit;
}

?>
