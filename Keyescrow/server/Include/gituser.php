<?php

/*
 * Returns the link for the requested API function.
 */
function gitlab_api_link($api) {
    return 'https://git.team6.isucdc.com/api/v3/' . $api;
}

/*
 * Create a Gitlab user.
 */
function gituser_create($username, $password) {
    if (is_null($username) || is_null($password)) {
        die("Must give a username and password.");
    }

    $params = array(
        'private_token' => '',
    );

    $url = gitlab_api_link('users') . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_POST => TRUE,
        CURLOPT_CAPATH => '/etc/ssl/certs/',
        CURLOPT_POSTFIELDS => array(
            'name' => $username,
            'username' => $username,
            'password' => $password,
            'email' => $username . '@team6.isucdc.com',
            'confirm' => 'false',
        ),
        CURLOPT_RETURNTRANSFER => TRUE,
    );
  
    curl_setopt_array($ch, $options);


    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    //print_r($result);
    //print_r($error);
}

/*
 * Change the password of a Gitlab user.
 */
function gituser_change_pass($username, $new_password) {
    if (is_null($username) || is_null($new_password)) {
        die("Must give a username and a new password.");
    }

    $params = array(
        'private_token' => '',
        'username' => $username
    );

    $url = gitlab_api_link('users') . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
  
    $obj = json_decode($result);

  
    $uid = $obj[0]->id;
  
    // Now that we have the id, submit a post request to change the password
    unset($params['username']);
    $params['password'] = $new_password;
    $url = gitlab_api_link('users/' . $uid) . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PUT => true,
    );
    curl_setopt_array($ch, $options);
  
    $result = curl_exec($ch);
    $error = curl_error($ch);

    //print_r($result);
    //print_r($error);

    curl_close($ch);
}

/*
 * Delete a Gitlab user.
 */
function gituser_delete($username) {
    if (is_null($username)) {
        die("Must give a username");
    }

    $params = array(
        'private_token' => '',
        'username' => $username
    );

    $url = gitlab_api_link('users') . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);
    $error = curl_error($ch);
  
    $obj = json_decode($result);
    curl_close($ch);
  
    $uid = $obj[0]->id;
  
    // Now that we have the id, submit a post request to delete the user
    unset($params['username']);
    $url = gitlab_api_link('users/' . $uid) . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
    );
    curl_setopt_array($ch, $options);
  
    $result = curl_exec($ch);
    $error = curl_error($ch);

    //print_r($result);
    //print_r($error);

    curl_close($ch);
}


/*
 * Get the user token.
 */
function gituser_get_token($username, $password) {
    $api = "https://git.team6.isucdc.com/api/v3/";
    $params = array(
        'private_token' => '',
    );
    $url = $api . 'session' . '?' . http_build_query($params);
    
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => TRUE,
        CURLOPT_SSLCERT => 'iseage-root-ca.pem',
        CURLOPT_CAPATH => '/etc/ssl/certs/',
        CURLOPT_POSTFIELDS => array(
            'login' => $username,
            'password' => $password,
        ),
        CURLOPT_RETURNTRANSFER => TRUE,
    );

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

/*
 * Change the the SSH key of a user.
 */
function gituser_change_pubkey($username, $pubkey) {

    // First find user id.
    $params = array(
        'private_token' => '', // admin user
        'username' => $username
    );
    $url = gitlab_api_link('users') . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result);

    $uid = $obj[0]->id;

    var_dump($uid);

    // Now that we have the user id, let's see how many keys they have
    $params = array(
        'private_token' => '',
        'uid' => $uid,
    );
    $url = gitlab_api_link('users/' . $uid . '/keys') . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);
    curl_close($ch);

    $obj = json_decode($result);

    var_dump($obj);
    
    $num_keys = count($obj);

    var_dump($num_keys);

    if ($num_keys > 0) {
        // Delete all keys before adding the new one.
        $key_id = $obj[0]->id;
        $params = array(
            'private_token' => '',
            'uid' => $uid,
            'id' => $key_id,
        );
        $url = gitlab_api_link('user/keys/' . $key_id) . '?' . http_build_query($params);
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    // Now we can add the new key
    $params = array(
        'private_token' => '',
    );
    $url = gitlab_api_link('users/' . $uid . '/keys') . '?' . http_build_query($params);
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'id' => $uid,
            'title' => 'Shell Server',
            'key' => $pubkey,
        ),
    );

    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);

    var_dump($result);
    curl_close($ch);
}


?>
