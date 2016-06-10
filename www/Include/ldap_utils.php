<?php

include_once "Include/config.php";
include_once "Include/UtilityFunctions.php";
include_once "Include/gituser.php";

function ldap_register($username, $password1, $password2, $creditcard)
{
	if(is_valid_username($username) == false)
	{
		return 3;
	}

	if(strcmp($password1, $password2) !== 0)
	{
		return 2;
	}
	if(is_valid_credit_card($creditcard) == false)
	{
		return 5;
	}

	if(strcmp(strip_only_alphabet_nocase($username), "root") == 0) //LMFAO
	{
		return 3;
	}

    if(strcmp(strip_only_alphabet_nocase($username), "cdc") == 0)
    {
        return 3;
    }

    if(strcmp(strip_only_alphabet_nocase($username), "sshd") == 0)
    {
        return 3;
    }

	$user = strip_only_alphabet_nocase($username);

	$pass = $password1;

	$cc = credit_card_strip_input($creditcard);

    $ds = ldap_connect("ldap://ldap.saxophoneguerilla.com");

    if(!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)){
        die("LDAP - Could not set LDAPv3\r\n");
    }
    else if(!ldap_start_tls($ds)) {
        die("LDAP - Could not start secure TLS connection");
    } else {

        // now we need to bind to the ldap server as administrator
        $bind = ldap_bind($ds, "cn=admin,dc=saxophoneguerilla,dc=com", ldapAdminPassword) or die("Could not bind to LDAP server for user registration");

		//look in mysql check if the user already exists
        mysql_connect(mysql_host, mysql_username, mysql_password);
        mysql_select_db(mysql_db) or die( "Unable to select database");

		$query="SELECT username from credentials where username = '" . mysql_escape_string($user) . "'"; //redundant but you can't be retroactively paranoid..
		$result = mysql_query($query);

		if (mysql_num_rows($result) >= 1)
		{
			return 1;
		}

		//look in LDAP to check if they exist
		$sr = ldap_search($ds, "dc=saxophoneguerilla,dc=com", "uid=" . strip_only_alphabet_nocase($user) . ",ou=users");
		$info = ldap_get_entries($ds, $sr);

		if($info["count"] > 0)
		{
            return 5;
		}



        //if they do not already exist insert user and credit card into database
		$query= "INSERT INTO credentials (username, creditcard) VALUES ('" . mysql_escape_string($user) . "','" . mysql_escape_string($cc) . "')";
		$result= mysql_query($query);

		//get new users uid number
        $query="SELECT id from credentials where username = '" . mysql_escape_string($user) . "'"; 
        $result= mysql_query($query);

		$uidNumber = mysql_fetch_row($result);
		$uidNumber = $uidNumber[0];

        if($bind)
        {

	        $url = 'https://keyescrow.team6.isucdc.com/generateKeys.php';
       		$ch = curl_init($url);

	        $options = array(
        	    CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => true
	        );

	        curl_setopt_array($ch, $options);
	        $result = curl_exec($ch);

	        $keys = json_decode($result, true);

            $info  = array();
            $info["objectClass"] = array();
            $info["objectClass"][] = "top";
            $info["objectClass"][] = "account";
            $info["objectClass"][] = "posixAccount";
            $info["objectClass"][] = "shadowAccount";
            $info["objectClass"][] = "CDCUserInfo";
            $info["uid"]=$user;
            $info["homeDirectory"] = "/home/users/".$user;
            $info["cn"] = $user;
            $info["userPassword"] = '{MD5}' . base64_encode(pack('H*',md5($pass)));
            $info["uidnumber"]="12555";
            $info["gidNumber"]="500";
            $info["loginShell"]="/bin/bash";
            $info["sshPrivateKey"] = $keys["privkey"];
            $info["sshPublicKey"] = $keys["pubkey"];

	    gituser_create($user, $pass);
            gituser_change_pubkey($user, $keys["pubkey"]);

            //Add user to directory
            $r = ldap_add($ds, "uid=" . strip_only_alphabet_nocase($user) . ",ou=confirm,dc=saxophoneguerilla,dc=com", $info);

            mysql_close();
            ldap_close($ds);

        }
    }
}
?>
