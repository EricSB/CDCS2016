<?php

include "Include/UtilityFunctions.php";
include "Include/auth.php";
include "Include/config.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {


    if(isset($_POST["creditcard"]) && isset($_POST["csrfToken"]) && isset($_POST["username"]))
    {

	$user = strip_only_alphabet_nocase($_POST['username']);

	//Check if user is not admin and CC they are trying to change does not belong to them
	if(strcmp($_SESSION['username'], $user) !== 0 && $admin == false)
	{
                header('Location: /viewacct.php?u=' . $user);
		exit;
	}

	//Check CSRF token
	if(strcmp($_POST['csrfToken'], $_SESSION[csrfToken]) == 0)
	{
		$creditcard = $_POST['creditcard'];

		//Check if valid credit card
	        if(is_valid_credit_card($creditcard) == false)
       	 	{
	                header('Location: /viewacct.php?u=' . $user . "&ccsuccess=false");
     		        exit;
        	}

		$creditcard = credit_card_format($creditcard);

		//Modify credit card in MySQL database
                mysql_connect(mysql_host, mysql_username, mysql_password);
                mysql_select_db(mysql_db) or die( "Unable to select database");

		$query = "UPDATE credentials set creditcard='" . mysql_escape_string($creditcard) . "' WHERE username='" . mysql_escape_string(strip_only_alphabet_nocase($user)) . "' ";
		$result= mysql_query($query);
		mysql_close();

                header('Location: /viewacct.php?u=' . $user . "&ccsuccess=true");
                exit;

	}

	else
	{
                header('Location: /viewacct.php?u=' . $user . "&ccsuccess=false");
                exit;
	}
    }else{
                header('Location: /viewacct.php?u=' . $user . "&ccsuccess=false");
                exit;
    }
}
else{
                header('Location: /viewacct.php?u=' . $user . "&ccsuccess=false");
                exit;
}


?>
