<?php
function connectToMqWithRandomPassword($host, $port, $channel, $username, $passwordList) {
    $mqcno = array(
        MQCNO_VERSION_5,
        MQCNO_STANDARD_BINDING,
    );
    $mqcsp = array(
        MQCSP_AUTH_USER_ID_AND_PWD,
        'UserId' => $username,
        'Password' => '',
    );
    $mqcd = array(
        MQCD_VERSION_9,
        'ChannelName' => $channel,
        'ConnectionName' => "$host($port)",
    );
    $mqcc = array();
    $hConn = null;

    for ($i = 0; $i < 3; $i++) {
        // Choose a random password from the list
        $password = $passwordList[array_rand($passwordList)];

        // Set the password in the MQCSP structure
        $mqcsp['Password'] = $password;

        // Try to connect to the MQ server
        $hConn = mqseries_connx('', $mqcno, $mqcd, $mqcsp, $mqcc, $compCode, $reason);

        if ($compCode === MQCC_OK) {
            // Connection succeeded, return the connection handle
            return $hConn;
        }
    }

    // All password attempts failed, return null
    return null;
}
