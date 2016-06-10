<?php

/*
* Desc: Taken from (see pages 34-36)
* https://media.blackhat.com/bh-us-12/Briefings/Argyros/BH_US_12_Argyros_PRNG_WP.pdf
*
* generates secure random data using most secure method available in PHP.
*
* args: len (number of bytes of random data you want)
* ret: random data
*/
function secure_random_string($len = 20)
{
    // if a secure randomness generator exists and we don't have a buggy PHP version use it.
    if (function_exists('openssl_random_pseudo_bytes') &&
       (version_compare(PHP_VERSION, '5.3.4') >= 0 || substr(PHP_OS, 0, 3) !== 'WIN'))
    {
    $str = bin2hex(openssl_random_pseudo_bytes(($len/2)+1, $strong));
    if ($strong == true)
        return substr($str, 0, $len);
    }
 
    //collect any entropy available in the system along with a number
    //of time measurements or operating system randomness.
    $str = '';
    $bits_per_round = 2;
    $msec_per_round = 400;
    $hash_len = 20; // SHA-1 Hash length
    $total = ceil($len/2); // total bytes of entropy to collect
 
    do
    {
        $bytes = ($total > $hash_len)? $hash_len : $total;
        $total -= $bytes;
        //collect any entropy available from the PHP system and filesystem
        $entropy = rand() . uniqid(mt_rand(), true);
        $entropy .= implode('', @fstat(fopen( __FILE__, 'r')));
        $entropy .= memory_get_usage();
        if(@is_readable('/dev/urandom') && ($handle = @fopen('/dev/urandom', 'rb')))
        {
            $entropy .= @fread($handle, $bytes);
            @fclose($handle);
        }
    else
    {
        // Measure the time that the operations will take on average
        for ($i = 0; $i < 3; $i ++)
        {
            $c1 = microtime() * 1000000;   
            $var = sha1(mt_rand());
            for ($j = 0; $j < 50; $j++)
            {
                $var = sha1($var);
            }
            $c2 = microtime() * 1000000;
        $entropy .= $c1 . $c2;
        }
        if ($c1 > $c2) $c2 += 1000000;  
 
        // Based on the above measurement determine the total rounds
        // in order to bound the total running time.
        $rounds = (int)(($msec_per_round / ($c2-$c1))*50);
 
        // Take the additional measurements. On average we can expect
        // at least $bits_per_round bits of entropy from each measurement.
        $iter = $bytes*(int)(ceil(8 / $bits_per_round));
        for ($i = 0; $i < $iter; $i ++)
        {
            $c1 = microtime();
            $var = sha1(mt_rand());
            for ($j = 0; $j < $rounds; $j++)
            {
                $var = sha1($var);
            }
            $c2 = microtime();
            $entropy .= $c1 . $c2;
        }
    }
    // We assume sha1 is a deterministic extractor for the $entropy variable.
    $str .= sha1($entropy);
    } while ($len > strlen($str));
 
    return substr($str, 0, $len);
}
 
//Generate random CSRF token for user session
function genCSRFToken() {
  return secure_random_string(32);
}
?>
