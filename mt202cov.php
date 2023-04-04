<?php
class MT202COVGenerator {

    private $sender;
    private $receiver;
    private $amount;
    private $currency;
    private $paymentReference;
    private $senderToReceiverInformation;

    public function __construct($sender, $receiver, $amount, $currency, $paymentReference, $senderToReceiverInformation) {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->paymentReference = $paymentReference;
        $this->senderToReceiverInformation = $senderToReceiverInformation;
    }

    public function generate() {
        $message = "{1:F01" . $this->sender .
            "\n{2:I202" . $this->receiver .
            "\n{3:{108:" . $this->paymentReference .
            "\n{32B:" . $this->currency . $this->amount .
            "\n{50A:" . $this->sender .
            "\n{59A:" . $this->receiver .
            "\n{70:" . $this->senderToReceiverInformation .
            "\n-}";
        return $message;
    }

}
$generator = new MT202COVGenerator("ABCDUS33XXX", "XYZAUS33XXX", "100000.00", "USD", "123456", "Invoice Payment");
$message = $generator->generate();
echo $message;

?>