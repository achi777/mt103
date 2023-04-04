<?php

class SendSwift extends Save
{
    // Set up connection parameters


    function __construct()
    {
         //Load the MQ Client extension
        $host = "localhost";
        $port = "1414";
        $channel = "MY.CHANNEL";
        $queueManager = "MY.QUEUE.MANAGER";

        if (!extension_loaded('mqseries')) {
            dl('mqseries.' . PHP_SHLIB_SUFFIX);
        }
        // Connect to the queue manager
        $conn = mqseries_connx($queueManager, [
            MQSERIES_HOST_NAME => $host,
            MQSERIES_PORT => $port,
            MQSERIES_CHANNEL_NAME => $channel,
        ]);

        // Check if connection was successful
        if (!$conn) {
            echo "Failed to connect to queue manager: " . mqseries_strerror();
            exit(1);
        }

        // Open the queue for writing
        $queue = mqseries_open($queueManager, "MY.QUEUE.NAME", MQSERIES_MQOO_OUTPUT);

        // Check if queue was opened successfully
        if (!$queue) {
            echo "Failed to open queue: " . mqseries_strerror();
            mqseries_disc($conn);
            exit(1);
        }
    }

    public function send()
    {


    // Read the Swift file contents from disk
        $fileContents = file_get_contents("/path/to/swift/file");

    // Send the message to the queue
        $result = mqseries_put1($conn, $queue, $fileContents, [
            MQSERIES_MQMD => [
                MQSERIES_MQMD_FORMAT => MQSERIES_MQFMT_STRING,
                MQSERIES_MQMD_MSG_TYPE => MQSERIES_MQMT_DATAGRAM,
                MQSERIES_MQMD_EXPIRY => MQSERIES_MQEI_UNLIMITED,
            ],
        ]);

    // Check if message was sent successfully
        if ($result === false) {
            echo "Failed to send message to queue: " . mqseries_strerror();
            mqseries_close($queue);
            mqseries_disc($conn);
            exit(1);
        }

    // Close the queue and disconnect from the queue manager
        mqseries_close($queue);
        mqseries_disc($conn);

        echo "Message sent successfully!";
    }
}
?>