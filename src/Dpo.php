<?php

namespace PaymeTech\Dpo;


class Dpo
{

    public function __construct() {}



    public function createToken($company_token, $reference, $amount, $currency, $service_type, $service_description, $redirect_url, $endpoint, $back_url)
    {


        $xmlData = "<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>" . $company_token . "</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>" . $amount . "</PaymentAmount><PaymentCurrency>" . $currency . "</PaymentCurrency><CompanyRef>" . $reference . "</CompanyRef><RedirectURL>" . $redirect_url . "</RedirectURL><BackURL>" . $back_url . "</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL></Transaction><Services><Service><ServiceType>" . $service_type . "</ServiceType><ServiceDescription>" . $service_description . "</ServiceDescription><ServiceDate>" . now()->format("Y/m/d H:i") . "</ServiceDate></Service></Services></API3G>";


        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }

    public function online_checkout($company_token, $reference, $amount, $currency, $service_type, $service_description, $redirect_url, $payment_url, $endpoint, $back_url)
    {


        $token_response = json_decode(json_encode(simplexml_load_string($this->createToken($company_token, $reference, $amount, $currency, $service_type, $service_description, $redirect_url, $endpoint, $back_url))), true);

        $token = $token_response["TransToken"];
        return $payment_url . "" . $token;
    }

    public function verify_token($transaction_token, $endpoint, $company_token)
    {

        $xmlData = "<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>" . $company_token . "</CompanyToken><Request>verifyToken</Request><TransactionToken>" . $transaction_token . "</TransactionToken></API3G>";



        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);

        curl_close($ch);
        $token_verification = json_decode(json_encode(simplexml_load_string($result)), true);
        return $token_verification;
    }
}
