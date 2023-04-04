<?php
interface Mt103GeneratorInterface {
    public function generate(string $senderBank, string $senderBIC, string $senderName, string $receiverBank, string $receiverBIC, string $receiverName, float $amount, string $currency, string $valueDate, string $reference, string $chargeParty, string $senderReference, string $senderToReceiverInfo, string $intermediaryBank = null, string $intermediaryBIC = null, string $intermediaryAccount = null): string;
}

class Mt103Generator implements Mt103GeneratorInterface {
    public function generate(string $senderBank, string $senderBIC, string $senderName, string $receiverBank, string $receiverBIC, string $receiverName, float $amount, string $currency, string $valueDate, string $reference, string $chargeParty, string $senderReference, string $senderToReceiverInfo, string $intermediaryBank = null, string $intermediaryBIC = null, string $intermediaryAccount = null): string {
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

        if (!is_null($intermediaryBank) && !is_null($intermediaryBIC) && !is_null($intermediaryAccount)) {
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
}

interface Mt103ParserInterface {
    public function parse(string $mt103): array;
}

class Mt103Parser implements Mt103ParserInterface {
    public function parse(string $mt103): array {
        // Create an array to hold the parsed data
        $data = array();

        // Split the MT103 message into lines
        $lines = explode("\n", $mt103);

        // Iterate through the lines and parse each tag
        foreach ($lines as $line) {
            // Ignore empty lines and the closing tag
            if (empty($line) || $line == "-}") {
                continue;
            }

            // Split the line into the tag name and value
            $tagName = substr($line, 1, 2);
            $tagValue = substr($line, 4);

            // Parse the tag value based on the tag name
            switch ($tagName) {
                case "20":
                    $data["reference"] = $tagValue;
                    break;
                case "23":
                    $data["transactionType"] = $tagValue;
                    break;
                case "32":
                    $valueDate = substr($tagValue, 0, 6);
                    $amount = substr($tagValue, 6);
                    $data["valueDate"] = $valueDate;
                    $data["amount"] = $amount;
                    break;
                case "50":
                    $data["receiverName"] = $tagValue;
                    break;
                case "52":
                    $data["receiverBank"] = $tagValue;
                    break;
                case "53":
                    $data["senderBank"] = $tagValue;
                    break;
                case "54":
                    $data["senderBIC"] = $tagValue;
                    break;
                case "56":
                    $intermediaryBank = substr($tagValue, 0, 12);
                    $intermediaryBIC = substr($tagValue, 13, 11);
                    $data["intermediaryBank"] = $intermediaryBank;
                    $data["intermediaryBIC"] = $intermediaryBIC;
                    break;
                case "57":
                    $data["accountWithBank"] = $tagValue;
                    break;
                case "58":
                    $data["beneficiaryBank"] = $tagValue;
                    break;
                case "59":
                    if (isset($data["beneficiaryName"])) {
                        $data["beneficiaryAccount"] = $tagValue;
                    } else {
                        $data["beneficiaryName"] = $tagValue;
                    }
                    break;
                case "71":
                    if ($tagValue == "OUR") {
                        $data["chargeParty"] = "sender";
                    } elseif ($tagValue == "SHA") {
                        $data["chargeParty"] = "shared";
                    } elseif ($tagValue == "BEN") {
                        $data["chargeParty"] = "beneficiary";
                    }
                    break;
                case "71F":
                    $data["chargePartyDetails"] = $tagValue;
                    break;
                case "72":
                    if (substr($tagValue, 0, 4) == "/PH/") {
                        $data["senderReference"] = substr($tagValue, 4);
                    } else {
                        $data["senderToReceiverInfo"] = substr($tagValue, 1);
                    }
                    break;
            }
        }

        return $data;
    }
}



$generator = new Mt103Generator();

$senderBank = "Sender Bank";
$senderBIC = "Sender BIC";
$senderName = "Sender Name";
$receiverBank = "Receiver Bank";
$receiverBIC = "Receiver BIC";
$receiverName = "Receiver Name";
$amount = "1000";
$currency = "USD";
$valueDate = "20230403";
$reference = "1234567890";
$chargeParty = "SHA";
$senderReference = "Sender Reference";
$senderToReceiverInfo = "Sender to Receiver Info";
$intermediaryBank = "Intermediary Bank";
$intermediaryBIC = "Intermediary BIC";
$intermediaryAccount = "Intermediary Account";

$mt103 = $generator->generate(
    $senderBank,
    $senderBIC,
    $senderName,
    $receiverBank,
    $receiverBIC,
    $receiverName,
    $amount,
    $currency,
    $valueDate,
    $reference,
    $chargeParty,
    $senderReference,
    $senderToReceiverInfo,
    $intermediaryBank,
    $intermediaryBIC,
    $intermediaryAccount
);

echo $mt103."\r\n";
echo "Parsed\r\n";

$parser = new Mt103Parser();
$fields = $parser->parse($mt103);
print_r($fields);
