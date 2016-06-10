<?php

include_once "Include/gituser.php";

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST["data"]))
{
	$username = $_POST["username"];
	$password = $_POST["password"];

	$data = json_decode($_POST["data"], true);

	$pubKey = $data["publicKey"];
	$privKey = $data["privateKey"];

	$ldapServer = "ldap://ldap.saxophoneguerilla.com";

        $ldap_conn = ldap_connect($ldapServer);

        if(!ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3)){
                die("LDAP - Could not set LDAPv3\r\n");
        }
        else if(!ldap_start_tls($ldap_conn)) {
                die("LDAP - Could not start secure TLS connection");
        } else {
		if($ldap_conn) {
			$ldap_bind = @ldap_bind($ldap_conn, "uid=" . $username . ",ou=users,dc=saxophoneguerilla,dc=com", $password);
			if($ldap_bind) {
				$dn = "uid=" . $username . ",ou=users,dc=saxophoneguerilla,dc=com";
				$userdata["sshprivatekey"][0] = $privKey;
				$userdata["sshpublickey"][0] = $pubKey;
				gituser_change_pubkey($username, $pubKey);
				ldap_modify($ldap_conn, $dn, $userdata);
				echo json_encode(array("result" => ldap_error($ldap_conn)));
			}
			else {
				echo json_encode(array("result" => "Login failed: Invalid credentials"));
			}
		} else  {
			echo json_encode(array("result" => "Login failed: Connection unsuccessful"));
		}
	}

}
?>
