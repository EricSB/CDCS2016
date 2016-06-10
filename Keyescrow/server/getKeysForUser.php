<?php
header("Content-type: application/json");
include "Include/UtilityFunctions.php";

if(isset($_POST['username']) && isset($_POST['password'])){
	$username = strip_only_alphabet_nocase($_POST['username']);
	$password = $_POST['password'];

        $ldapServer = "ldap://ldap.saxophoneguerilla.com";

        $ldap_conn = ldap_connect($ldapServer);

        if(!ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3)){
                die("LDAP - Could not set LDAPv3\r\n");
        }
        else if(!ldap_start_tls($ldap_conn)) {
                die("LDAP - Could not start secure TLS connection");
        } else {

       //after bind you want to search for user entry with base dn of uid=$username,ou=users,dc=saxophoneguerilla,dc=com


       $basedn = "dc=saxophoneguerilla,dc=com";

	if($ldap_conn) {
       	    $ldap_bind = @ldap_bind($ldap_conn, "uid=" . $username . ",ou=users,dc=saxophoneguerilla,dc=com", $password);
	    if($ldap_bind) { //ldap bind successful

		$result = ldap_search($ldap_conn, "ou=users,dc=saxophoneguerilla,dc=com", "uid=" . $username);

		//once we get the result we get data entries of that result.
		$data = ldap_get_entries($ldap_conn, $result);

		//not sure on where the public key is stored.
		$pubkey = $data[0]["sshpublickey"][0];
		$privKey = $data[0]["sshprivatekey"][0];


		$keys = array("pubkey" => $pubkey, "privkey" => $privKey, "success" => "success");

		//send data as a json

        	echo json_encode($keys);


	    }
	    else {
		$keys = array("pubkey" => $pubkey, "privkey" => $privKey, "success" => "Could invalid login credentials");
		echo json_encode($keys);
	    }
	}
        else {
	  $keys = array("pubkey" => $pubkey, "privkey" => $privKey, "success" => "An internal error has occurred");
	  echo json_encode($keys);
	}
	}
}
else{
	$keys = array("pubkey" => $pubkey, "privkey" => $privKey, "success" => "Username or Password not set");
}
/*
if(isset($_POST['username']) && isset($_POST['password'])) {

	$username = $_POST["username"];
	$password = $_POST["password"];

	$ldapServer = "ldap.saxophoneguerilla.com";

	$ldap_connection = ldap_connect($ldapServer);

	$ldap_bind = @ldap_bind($ldap_connection, $username, $password);



}
*/
?>
