#!/usr/bin/env php

<?php

//TODO: Set before deploying

error_reporting(0);

if(strcmp($argv[1], "generate") == 0)

{

        $url = 'https://keyescrow.team6.isucdc.com/generateKeys.php';

        $ch = curl_init($url);

        $options = array(

            CURLOPT_URL => $url,

            CURLOPT_CAINFO => '/etc/iseage-root-ca.pem',

            CURLOPT_RETURNTRANSFER => true

        );

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        $keys = json_decode($result, true);

        if(!is_null($keys["pubkey"]) && !is_null($keys["privkey"])){

                $pubkey = fopen("public.key", "w");

                $privkey = fopen("private.key", "w");

                fwrite($pubkey, $keys["pubkey"]);

                fwrite($privkey, $keys["privkey"]);

                shell_exec("chmod 700 ~/*.key");

                fclose($pubkey);

                fclose($privkey);

        }

}

else if(strcmp($argv[1], "get") == 0)
{
    if(strcmp($argv[2], "-t") == 0)
    {
        $dir = $argv[3];
    }
    else
    {
        $dir = "";
    }
    echo "Username:";
    $username = trim(fgets(STDIN), "\n");
    echo "Password:";
    $stty = 'stty -g';
    system("stty -echo"); 
    $password = trim(fgets(STDIN), "\n");
    system("stty echo");
    echo "\n";
    $url = 'https://keyescrow.team6.isucdc.com/getKeysForUser.php';
    $ch = curl_init($url);
    $options = array(
    CURLOPT_POST => TRUE,

            CURLOPT_URL => $url,

            CURLOPT_CAINFO => '/etc/iseage-root-ca.pem',

        CURLOPT_POSTFIELDS => array(

        "username" => $username,

        "password" => $password,

        ),

        CURLOPT_RETURNTRANSFER => TRUE,

    );

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    $error = curl_error($ch);

    curl_close($ch);

    if (!is_null($result)){

        $keys = json_decode($result, true);

        if ($keys != NULL) {

        $success = $keys["success"];

        if ($success == "success") {

                $pubkey = fopen($dir . "public.key", "w");

                $privkey = fopen($dir . "private.key", "w");

        

                fwrite($pubkey,  $keys["pubkey"]);

                fwrite($privkey, $keys["privkey"]);

                shell_exec("chmod 700 " . $dir . "*.key");

                fclose($pubkey);

                fclose($privkey);

        }

        else{

            echo $success . "\n";

        }

         }

    }

}

else if(strcmp($argv[1], "set") == 0)

{

        if(strcmp($argv[2], "-p") == 0)

        {

                $pubKeyPath = $argv[3];

        }

        else

        {

        usage();

        }

    if(strcmp($argv[4], "-i") == 0)

    {

        $privKeyPath = $argv[5];

    }

    else

    {

        usage();

    }

    

    shell_exec("cp " . $privKeyPath . " ~/private.key");

        shell_exec("cp " . $pubKeyPath . " ~/public.key");

    shell_exec("chmod 700 ~/*.key");

}

else if(strcmp($argv[1], "dispatch") == 0)

{

        echo "Username:";

        $username = trim(fgets(STDIN), "\n");

        echo "Password:";

        $stty = 'stty -g';

        system("stty -echo");

        $password = trim(fgets(STDIN), "\n");

        system("stty echo");

        echo "\n";

    shell_exec("cd ~/");

    $pubkey =  file_get_contents("public.key");

    $privkey =  file_get_contents("private.key");

        $data = array("publicKey" => $pubkey, "privateKey" => $privkey);

        $data_string = json_encode($data);

    //sets parameters for the http query

    $params = array(

            'username' => $username,

            'password' => $password,

        'data' => $data_string,

        );

    $url = 'https://keyescrow.team6.isucdc.com/setKeysForUser.php';
        $ch = curl_init($url);
        $options = array(
            CURLOPT_POST => TRUE,
            CURLOPT_URL => $url,
            CURLOPT_CAINFO => '/etc/iseage-root-ca.pem',
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => TRUE,
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
    $data_string = json_decode($result);
    echo $data_string;
}
else
{
    usage();
}
function usage()
{
    echo "Usage For Keyescrow Client Utility\n";
    echo "get [-t dir] - Get a key for the current user and place it in the directory specified by -t otherwise print the key to stdout\n";
    echo "set -p PUBKEY -y PRIVKEY - set the current user's key to the files specified\n";
    echo "generate - Generate a key for the current user\n";
    echo "dispatch - Distributes the key to be used for authentication on all servers\n";
    echo "\n";
    exit(-1);
}

?>


