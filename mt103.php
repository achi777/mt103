<?php
function generateMT103($senderBank, $senderBIC, $senderName, $receiverBank, $receiverBIC, $receiverName, $amount, $currency, $valueDate, $reference, $chargeParty, $senderReference, $senderToReceiverInfo, $intermediaryBank, $intermediaryBIC, $intermediaryAccount) {
    $mt103 = "{1:F01{$senderBank}ZGCRPHMMAXXX}{2:I103{$senderBank}PH{$senderBIC}N}{3:{113:FTM}}{4:\n"
        . ":20:{$reference}\n"
        . ":23B:CRED\n"
        . ":32A:{$valueDate}{$amount},{$currency}\n"
        . ":50K:{$receiverName}\n"
        . ":52A:{$receiverBank}\n"
        . ":53A:{$senderBank}\n"
        . ":54A:{$senderBIC}\n"
        . ":71A:OUR\n"
        . ":71F:{$chargeParty}\n"
        . ":72:/PH/{$senderReference}\n"
        . ":72:/{$senderToReceiverInfo}\n";

    if (!empty($intermediaryBank) && !empty($intermediaryBIC) && !empty($intermediaryAccount)) {
        $mt103 .= ":56A:{$intermediaryBank}\n"
            .  ":56D:/{$intermediaryBIC}\n"
            .  ":57A:{$senderBank}\n"
            .  ":57D:/{$senderBIC}\n"
            .  ":58A:{$receiverBank}\n"
            .  ":58D:/{$receiverBIC}\n"
            .  ":59:/{$receiverName}\n"
            .  ":59:/{$receiverBIC}\n"
            .  ":59F:{$intermediaryAccount}\n"
            .  "-}";
    } else {
        $mt103 .= ":57A:{$senderBank}\n"
            .  ":57D:/{$senderBIC}\n"
            .  ":58A:{$receiverBank}\n"
            .  ":58D:/{$receiverBIC}\n"
            .  ":59:/{$receiverName}\n"
            .  ":59:/{$receiverBIC}\n"
            .  "-}";
    }

    return $mt103;
}

function parseMT103(string $mt103): array
{
    $parsed = [];

    // Parse tags
    preg_match_all('/\n([0-9]{2}[A-Z]?)(?:\s|\n)(.*?)\n/', $mt103."\n", $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $tag = $match[1];
        $value = trim($match[2]);

        switch ($tag) {
            case '20':
                $parsed['transaction_reference_number'] = $value;
                break;
            case '23B':
                $parsed['bank_operation_code'] = $value;
                break;
            case '32A':
                $currency = substr($value, 0, 3);
                $amount = substr($value, 3);
                $parsed['value_date'] = substr($valueDate, 0, 6);
                $parsed['currency_code'] = $currency;
                $parsed['amount'] = $amount;
                break;
            case '50K':
                $parsed['ordering_customer_name'] = $value;
                break;
            case '52A':
                $parsed['ordering_institution_bank'] = $value;
                break;
            case '53A':
                $parsed['sender_correspondent_bank'] = $value;
                break;
            case '54A':
                $parsed['receiver_correspondent_bank'] = $value;
                break;
            case '71A':
                $parsed['details_of_charges'] = $value;
                break;
            case '71F':
                $parsed['sender_to_receiver_info'] = $value;
                break;
            case '72':
                $parsed['sender_reference'] = $value;
                break;
            case '56A':
                $parsed['intermediary_institution_bank'] = $value;
                break;
            case '56D':
                $parsed['intermediary_institution_bic'] = $value;
                break;
            case '57A':
                $parsed['account_with_institution_bank'] = $value;
                break;
            case '57D':
                $parsed['account_with_institution_bic'] = $value;
                break;
            case '58A':
                $parsed['beneficiary_institution_bank'] = $value;
                break;
            case '58D':
                $parsed['beneficiary_institution_bic'] = $value;
                break;
            case '59':
                $parsed['beneficiary_customer_name_address'] = $value;
                break;
            case '59F':
                $parsed['beneficiary_customer_account'] = $value;
                break;
            default:
                // Ignore unsupported tags
                break;
        }
    }

    return $parsed;
}


// Example usage
$senderBank = "ABCDEF";
$senderBIC = "ABCDUS33";
$senderName = "John Doe";
$receiverBank = "GHIJKL";
$receiverBIC = "GHIJUS33";
$receiverName = "Jane Doe";
$amount = "1000";
$currency = "USD";
$valueDate = date('Ymd', strtotime('+2 days'));
$reference = "1234567890";
$chargeParty = "SHA";
$senderReference = "SR123456";
$senderToReceiverInfo = "Information for receiver";
$intermediaryBank = "XYZ";
$intermediaryBIC = "XYZBUS33";
$intermediaryAccount = "1234567890";
$mt103 = generateMT103($senderBank, $senderBIC, $senderName, $receiverBank, $receiverBIC, $receiverName, $amount, $currency, $valueDate, $reference, $chargeParty, $senderReference, $senderToReceiverInfo, $intermediaryBank, $intermediaryBIC, $intermediaryAccount);

echo $mt103."\r\n";

echo parseMT103($mt103);

?>