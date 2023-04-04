<?php
class MT760Generator {
    public static function generateMessage($senderBIC, $receiverBIC, $transactionReference, $guaranteeAmount, $expiryDate, $beneficiaryName, $applicantName, $issuingBankName, $issuingBankAddress, $issuingBankCountry, $issuingBankBIC, $advisingBankName, $advisingBankAddress, $advisingBankCountry, $advisingBankBIC, $applicableRules, $senderReference, $description, $currencyCode) {

        // Define the message header
        $header = "MT760 1\r\n";

        // Define the sender and receiver information
        $sender = "{1:" . $senderBIC . "}\r\n";
        $receiver = "{2:" . $receiverBIC . "}\r\n";

        // Define the transaction reference
        $transaction = "{20:" . $transactionReference . "}\r\n";

        // Define the guarantee amount and currency code
        $amount = "{32B:" . $currencyCode . $guaranteeAmount . "}\r\n";

        // Define the expiry date
        $expiry = "{31C:" . $expiryDate . "}\r\n";

        // Define the beneficiary and applicant names
        $beneficiary = "{59:" . $beneficiaryName . "}\r\n";
        $applicant = "{50:" . $applicantName . "}\r\n";

        // Define the issuing bank information
        $issuingBank = "{41A:" . $issuingBankName . "\r\n" . $issuingBankAddress . "\r\n" . $issuingBankCountry . "\r\n" . $issuingBankBIC . "}\r\n";

        // Define the advising bank information
        $advisingBank = "{42C:" . $advisingBankName . "\r\n" . $advisingBankAddress . "\r\n" . $advisingBankCountry . "\r\n" . $advisingBankBIC . "}\r\n";

        // Define the applicable rules
        $rules = "{77D:" . $applicableRules . "}\r\n";

        // Define the sender's reference
        $senderRef = "{20:REF" . $senderReference . "}\r\n";

        // Define the description of the guarantee
        $description = "{72:" . $description . "}\r\n";

        // Construct the message
        $message = $header . $sender . $receiver . $transaction . $amount . $expiry . $beneficiary . $applicant . $issuingBank . $advisingBank . $rules . $senderRef . $description . "-}";

        return $message;
    }
}

// Instantiate the MT760Generator class
$mt760Generator = new MT760Generator();

// Define the required parameters
$senderBIC = "ABCDUS33XXX";
$receiverBIC = "WXYZUS44XXX";
$transactionReference = "1234567890";
$guaranteeAmount = "1000000";
$expiryDate = "20230430";
$beneficiaryName = "John Doe";
$applicantName = "Jane Smith";
$issuingBankName = "ABC Bank";
$issuingBankAddress = "123 Main Street";
$issuingBankCountry = "USA";
$issuingBankBIC = "ABC123";
$advisingBankName = "XYZ Bank";
$advisingBankAddress = "456 Park Avenue";
$advisingBankCountry = "USA";
$advisingBankBIC = "XYZ456";
$applicableRules = "URDG758";
$senderReference = "9876543210";
$description = "Bank guarantee for invoice #12345";
$currencyCode = "USD";

// Generate the MT760 message
$message = $mt760Generator->generateMessage($senderBIC, $receiverBIC, $transactionReference, $guaranteeAmount, $expiryDate, $beneficiaryName, $applicantName, $issuingBankName, $issuingBankAddress, $issuingBankCountry, $issuingBankBIC, $advisingBankName, $advisingBankAddress, $advisingBankCountry, $advisingBankBIC, $applicableRules, $senderReference, $description, $currencyCode);

// Output the generated message
echo $message;

?>