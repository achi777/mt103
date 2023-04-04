<?php

class MT202Generator {
    private string $senderBank;
    private string $senderBIC;
    private string $senderName;
    private string $receiverBank;
    private string $receiverBIC;
    private string $receiverName;
    private string $amount;
    private string $currency;
    private string $valueDate;
    private string $reference;
    private string $chargeParty;
    private string $senderReference;
    private string $senderToReceiverInfo;
    private string $intermediaryBank;
    private string $intermediaryBIC;

    public function __construct(string $senderBank, string $senderBIC, string $senderName, string $receiverBank, string $receiverBIC, string $receiverName, string $amount, string $currency, string $valueDate, string $reference, string $chargeParty, string $senderReference, string $senderToReceiverInfo, string $intermediaryBank = '', string $intermediaryBIC = '') {
        $this->senderBank = $senderBank;
        $this->senderBIC = $senderBIC;
        $this->senderName = $senderName;
        $this->receiverBank = $receiverBank;
        $this->receiverBIC = $receiverBIC;
        $this->receiverName = $receiverName;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->valueDate = $valueDate;
        $this->reference = $reference;
        $this->chargeParty = $chargeParty;
        $this->senderReference = $senderReference;
        $this->senderToReceiverInfo = $senderToReceiverInfo;
        $this->intermediaryBank = $intermediaryBank;
        $this->intermediaryBIC = $intermediaryBIC;
    }

    public function generate(): string {
        $mt202 = "{1:F01{$this->senderBank}ZGCRPHMMAXXX}{2:I202{$this->senderBank}PH{$this->senderBIC}N}{4:\n"
            . ":20:{$this->reference}\n"
            . ":21:{1:F01{$this->senderBank}ZGCRPHMMAXXX}{4:{$this->reference}}\n"
            . ":32A:{$this->valueDate}{$this->currency}{$this->amount},\n"
            . ":52A:{$this->receiverBank}\n"
            . ":56A:{$this->intermediaryBank}\n"
            . ":57A:{$this->senderBank}\n"
            . ":58A:{$this->receiverBank}\n"
            . ":72:/PH/{$this->senderReference}\n"
            . ":71A:OUR\n"
            . ":71F:{$this->chargeParty}\n"
            . ":77B:/{$this->senderToReceiverInfo}\n";

        if (!empty($this->intermediaryBIC)) {
            $mt202 .= ":56D:/{$this->intermediaryBIC}\n";
        }

        $mt202 .= "-}";

        return $mt202;
    }
}



// Example usage
$senderBank = "ABC Bank";
$senderBIC = "ABC1234";
$senderName = "John Doe";
$receiverBank = "XYZ Bank";
$receiverBIC = "XYZ5678";
$receiverName = "Jane Doe";
$amount = "1000.00";
$currency = "USD";
$valueDate = "20220405";
$reference = "12345";
$chargeParty = "BEN";
$senderReference = "67890";
$senderToReceiverInfo = "Invoice #123";
$intermediaryBank = "DEF Bank";
$intermediaryBIC = "DEF5678";
$intermediaryAccount = "123456789";

$obj = new Mt202Generator($senderBank,
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
    $intermediaryAccount);
$mt202 = $obj->generate();

echo $mt202."\r\n";




?>
