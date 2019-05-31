<?php

/**
 * Class Mpesa
 * @package Safaricom\Mpesa
 */
class Mpesa
{

    /**
     * @var null $consumer_key
     */
    private $consumer_key = null;

    /**
     * @var null $consumer_secret
     */
    private $consumer_secret = null;

    /**
     * @var null $application_status
     */
    private $application_status = null;

    /**
     * @var null $apiUrl
     */
    private $apiUrl = null;

    /**
     * @var null $tokenUrl
     */
    private $tokenUrl = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('mpesa', true);

        $mpesa_config = $this->CI->config->item('mpesa');

        if (!isset($mpesa_config['consumer_key']) || !isset($mpesa_config['consumer_secret']) || !isset($mpesa_config['application_status'])) {
            die("please ensure that your config file has values for all variables");
        }

        $this->consumer_key = $mpesa_config['consumer_key'];
        $this->consumer_secret = $mpesa_config['consumer_secret'];
        $this->application_status = $mpesa_config['application_status'];

        if ($this->application_status == "live") {
            // live api url
            $this->apiUrl = $mpesa_config['live_url'];
            // live token url
            $this->tokenUrl = $mpesa_config['live_token_url'];
        } elseif ($this->application_status == "sandbox") {
            //sandbpx api url
            $this->apiUrl = $mpesa_config['sandbox_url'];
            // live token url
            $this->tokenUrl = $mpesa_config['sandbox_token_url'];
        } else {
            die("please ensure that your config file has the right values for all variables");
        }
    }

    /**
     * use this function to generate a token
     * @return mixed
     */
    private function generateToken()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->tokenUrl);
        $credentials = base64_encode($this->consumer_key . ':' . $this->consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);

        return json_decode($curl_response)->access_token;
    }

    public function registerUrl($shortCode, $responseType, $validateUrl, $confirmUrl)
    {
        $url = $this->apiUrl . 'c2b/v1/registerurl';
        $data = array(
            'ShortCode' => $shortCode,
            'ResponseType' => $responseType, //Completed or Cancelled
            'ConfirmationURL' => $confirmUrl,
            'ValidationURL' => $validateUrl,
        );
        return $this->_curl_request($url, $data);
    }

    /**
     * Use this function to initiate a reversal request
     * @param $Initiator | The name of Initiator to initiating  the request
     * @param $SecurityCredential | Password of API operator(encrypted using the public key certificate).
     *         Get details from MPESA portal
     * @param $TransactionID | Unique Id received with every transaction response.
     * @param $Amount | Amount
     * @param $ReceiverParty | Organization /MSISDN sending the transaction
     * @param $RecieverIdentifierType | Type of organization receiving the transaction
     *         (1 => MSISDN, 2 => Till Number, 4 => Shortcode(Paybill))
     * @param $ResultURL | The path that stores information of transaction
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $Occasion |     Optional Parameter
     * @return mixed|string
     */
    public function reversal($Initiator, $SecurityCredential, $TransactionID, $Amount, $ReceiverParty, $RecieverIdentifierType, $ResultURL, $QueueTimeOutURL, $Remarks)
    {
        $url = $this->apiUrl . 'reversal/v1/request';
        $data = array(
            'CommandID' => "TransactionReversal",
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'TransactionID' => $TransactionID,
            'Amount' => $Amount,
            'ReceiverParty' => $ReceiverParty,
            'RecieverIdentifierType' => $RecieverIdentifierType,
            'ResultURL' => $ResultURL,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'Remarks' => $Remarks,
            'Occasion' => $Occasion,
        );
        return $this->_curl_request($url, $data);
    }

    /**
     * @param $InitiatorName |     This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request
     * @param $CommandID | Unique command for each transaction type
     *          values: SalaryPayment, BusinessPayment, PromotionPayment
     * @param $Amount | The amount being transacted
     * @param $PartyA | Organization’s shortcode initiating the transaction.
     * @param $PartyB | Phone number receiving the transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The timeout end-point that receives a timeout response.
     * @param $ResultURL | The end-point that receives the response of the transaction
     * @param $Occasion |     Optional
     * @return string
     */
    public function b2c($InitiatorName, $SecurityCredential, $CommandID, $Amount, $PartyA, $PartyB, $Remarks, $QueueTimeOutURL, $ResultURL, $Occasion)
    {

        $url = $this->apiUrl . 'b2c/v1/paymentrequest';
        $data = array(
            'InitiatorName' => $InitiatorName,
            'SecurityCredential' => $SecurityCredential,
            'CommandID' => $CommandID,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'ResultURL' => $ResultURL,
            'Occasion' => $Occasion,
        );

        return $this->_curl_request($url, $data);
    }
    /**
     * Use this function to initiate a C2B transaction
     * @param $ShortCode | 6 digit M-Pesa Till Number or PayBill number
     * @param $CommandID | Unique command for each transaction type.
     *            values: CustomerPayBillOnline,CustomerBuyGoodsOnline
     * @param $Amount | The amount being transacted.
     * @param $Msisdn | MSISDN (phone number) sending the transaction, start with country code without the plus(+) sign. e.g 254XXXXXXXXX
     * @param $BillRefNumber |  Bill Reference Number (Optional).
     * @return mixed|string
     */
    public function c2b($ShortCode, $CommandID, $Amount, $Msisdn, $BillRefNumber)
    {
        $url = $this->apiUrl . 'c2b/v1/simulate';
        $data = array(
            'ShortCode' => $ShortCode,
            'CommandID' => $CommandID,
            'Amount' => $Amount,
            'Msisdn' => $Msisdn,
            'BillRefNumber' => $BillRefNumber,
        );
        return $this->_curl_request($url, $data);
    }

    /**
     * Use this to initiate a balance inquiry request
     * @param $Initiator |     This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request
     * @param $PartyA | Type of organization receiving the transaction
     * @param $IdentifierType |Type of organization receiving the transaction
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $ResultURL |     The path that stores information of transaction
     * @return mixed|string
     */
    public function accountBalance($Initiator, $SecurityCredential, $PartyA, $IdentifierType, $Remarks, $QueueTimeOutURL, $ResultURL)
    {
        $CommandID = "AccountBalance";
        $url = $this->apiUrl . 'accountbalance/v1/query';
        $data = array(
            'CommandID' => $CommandID,
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'PartyA' => $PartyA,
            'IdentifierType' => $IdentifierType,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'ResultURL' => $ResultURL,
        );

        return $this->_curl_request($url, $data);
    }

    /**
     * Use this function to make a transaction status request
     * @param $Initiator | The name of Initiator to initiating the request.
     * @param $SecurityCredential |     Encrypted password for the initiator to autheticate the transaction request.
     * @param $CommandID | Unique command for each transaction type, possible values are: TransactionStatusQuery.
     * @param $TransactionID | Organization Receiving the funds.
     * @param $PartyA | Organization/MSISDN sending the transaction
     * @param $IdentifierType | Type of organization receiving the transaction
     * @param $ResultURL | The path that stores information of transaction
     * @param $QueueTimeOutURL | The path that stores information of time out transaction
     * @param $Remarks |     Comments that are sent along with the transaction
     * @param $Occasion |     Optional Parameter
     * @return mixed|string
     */
    public function transactionStatus($Initiator, $SecurityCredential, $CommandID, $TransactionID, $PartyA, $IdentifierType, $ResultURL, $QueueTimeOutURL, $Remarks, $Occasion)
    {
        $url = $this->apiUrl . 'transactionstatus/v1/query';
        $data = array(
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'CommandID' => $CommandID,
            'TransactionID' => $TransactionID,
            'PartyA' => $PartyA,
            'IdentifierType' => $IdentifierType,
            'ResultURL' => $ResultURL,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'Remarks' => $Remarks,
            'Occasion' => $Occasion,
        );

        return $this->_curl_request($url, $data);
    }

    /**
     * Use this function to initiate a B2B request
     * @param $Initiator | This is the credential/username used to authenticate the transaction request.
     * @param $SecurityCredential | Encrypted password for the initiator to autheticate the transaction request.
     * @param $Amount | Base64 encoded string of the B2B short code and password, which is encrypted using M-Pesa public key and validates the transaction on M-Pesa Core system.
     * @param $PartyA | Organization’s short code initiating the transaction.
     * @param $PartyB | Organization’s short code receiving the funds being transacted.
     * @param $Remarks | Comments that are sent along with the transaction.
     * @param $QueueTimeOutURL | The path that stores information of time out transactions.it should be properly validated to make sure that it contains the port, URI and domain name or publicly available IP.
     * @param $ResultURL | The path that receives results from M-Pesa it should be properly validated to make sure that it contains the port, URI and domain name or publicly available IP.
     * @param $AccountReference | Account Reference mandatory for “BusinessPaybill” CommandID.
     * @param $commandID | Unique command for each transaction type, possible values are: BusinessPayBill, MerchantToMerchantTransfer, MerchantTransferFromMerchantToWorking, MerchantServicesMMFAccountTransfer, AgencyFloatAdvance
     * @param $SenderIdentifierType | Type of organization sending the transaction.
     * @param $RecieverIdentifierType | Type of organization receiving the funds being transacted.

     * @return mixed|string
     */
    public function b2b($Initiator, $SecurityCredential, $Amount, $PartyA, $PartyB, $Remarks, $QueueTimeOutURL, $ResultURL, $AccountReference, $commandID, $SenderIdentifierType, $RecieverIdentifierType)
    {
        $url = $this->apiUrl . 'b2b/v1/paymentrequest';
        $data = array(
            'Initiator' => $Initiator,
            'SecurityCredential' => $SecurityCredential,
            'CommandID' => $commandID,
            'SenderIdentifierType' => $SenderIdentifierType,
            'RecieverIdentifierType' => $RecieverIdentifierType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'AccountReference' => $AccountReference,
            'Remarks' => $Remarks,
            'QueueTimeOutURL' => $QueueTimeOutURL,
            'ResultURL' => $ResultURL,
        );

        return $this->_curl_request($url, $data);
    }

    /**
     * Use this function to initiate an STKPush Simulation
     * @param $BusinessShortCode | The organization shortcode used to receive the transaction.
     * @param $LipaNaMpesaPasskey | The password for encrypting the request. This is generated by base64 encoding BusinessShortcode, Passkey and Timestamp.
     * @param $TransactionType | The transaction type to be used for this request. Only CustomerPayBillOnline is supported.
     * @param $Amount | The amount to be transacted.
     * @param $PartyA | The MSISDN sending the funds.
     * @param $PartyB | The organization shortcode receiving the funds
     * @param $PhoneNumber | The MSISDN sending the funds.
     * @param $CallBackURL | The url to where responses from M-Pesa will be sent to.
     * @param $AccountReference | Used with M-Pesa PayBills.
     * @param $TransactionDesc | A description of the transaction.
     * @param $Remark | Remarks
     * @return mixed|string
     */
    public function STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remark)
    {
        $url = $this->apiUrl . 'stkpush/v1/processrequest';

        $timestamp = date("Ymdhis");
        $password = base64_encode($BusinessShortCode . $LipaNaMpesaPasskey . $timestamp);

        $data = array(
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionType,
            'Remark' => $Remark,
        );
        return $this->_curl_request($url, $data);
    }

    /**
     * Use this function to initiate an STKPush Status Query request.
     * @param $checkoutRequestID | Checkout RequestID
     * @param $businessShortCode | Business Short Code
     *  @param $LipaNaMpesaPasskey | The password linked to businessShortCode
     * @param $timestamp | Timestamp
     * @return mixed|string
     */
    public function STKPushQuery($checkoutRequestID, $businessShortCode, $LipaNaMpesaPasskey)
    {
        $timestamp = date("Ymdhis");
        $password = base64_encode($businessShortCode . $LipaNaMpesaPasskey . $timestamp);

        $url = $this->apiUrl . 'stkpushquery/v1/query';
        $data = array(
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID,
        );
        return $this->_curl_request($url, $data);
    }

    /**
     *Use this function to confirm all transactions in callback routes
     */
    public function finishTransaction()
    {
        $resultArray = [
            "ResultDesc" => "Confirmation Service request accepted successfully",
            "ResultCode" => "0",
        ];
        header('Content-Type: application/json');

        echo json_encode($resultArray);
    }

    private function _curl_request($url, $curl_post_data)
    {
        $data_string = json_encode($curl_post_data);
        $token = $this->generateToken();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $curl_response = curl_exec($curl);

        return json_decode($curl_response);
    }
}
