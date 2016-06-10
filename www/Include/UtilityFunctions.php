<?php

/* Takes in a credit card in either the format
which is all numbers or the format which is all dashes
and verifies credit card number is in correct
format (either with or without dashes)
*/
function is_valid_credit_card($cc)
{
  
  if(credit_card_format($cc))
  {
    return true;
  }
  else
  {
    return false;
  }
}

/* Takes a valid credit card in either format
(either with or without dashes) and returns it in
the dashes format
*/
function credit_card_format($cc)
{
	if(strlen($cc) == 16)
	{
		$numbers_only = preg_replace("/[^\d]/", "", $cc);
    		$formatted_credit_card = preg_replace("/^1?(\d{4})(\d{4})(\d{4})(\d{4})$/", "$1-$2-$3-$4", $numbers_only);
	}
	else
	{
		return;
	}
	return $formatted_credit_card;
}


function credit_card_strip_input($string)
{
	$new_string = preg_replace("/[^0-9 - ]/", '', $string);
	return $new_string;
}

/** Returns true if the supplied username is less
than 15 characters and only contains A­Z
upper or lowercase letters, no numbers or anything else allowed
**/
function is_valid_username($username)
{
        //allows username to be at least 1 character but at most 14
	if (ereg ("^[a-zA-Z]{1,14}$", $username)) 
        {
    		return true;
	}
	else 
        {
	    	return false;
	}
}

/**Takes an array of characters and then removes them
if they match a character in the blacklist which is an
array of characters which are not allowed in the input
and returns the new string minus the bad input
**/
function strip_with_blacklist($string, $blacklist)
{

  //true if $string contains a character from $blacklist
  if(preg_match($blacklist, $string))
  {
      $string = preg_replace($blacklist, '', $string);
  }
  //returns $string without the characters in $blacklist
  return $string;
}


function strip_only_alphabet_nocase($string)
{
	$res = preg_replace("/[^a-zA-Z]/", "", $string);
	return $res;
}

function strip_number($string)
{
	$new_string = preg_replace("/[^0-9]/", '', $string);
        return $new_string;
}


function filter_input_comment($comment)
{
	$new_string = preg_replace("/[^a-zA-Z0-9.,!?]/", '', $comment);
        return $new_string;
}

function strip_project_name($string) {
	$new_string = preg_replace("/[^a-zA-Z0-9 ]/", '', $string);
	return $new_string;
}


?>
