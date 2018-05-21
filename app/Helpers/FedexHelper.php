<?php
/**
 * Created by PhpStorm.
 * User: raviteja
 * Date: 5/19/18
 * Time: 11:28 AM
 */

namespace App\Helpers;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

define('Newline',"<br />");

class FedexHelper
{
    /**
     *  Print SOAP request and response
     */

    public  static function getSoapClient($WSDLPATH){
        return new \SoapClient($WSDLPATH,array('trace' => 1));

    }

    public static function getWebAuthenticationDetail(){
        $webAuth = array(
            'pcred' => array('ParentCredential' => array(
                'Key' => Config::get('fedex.fedexparentapiKey'),
                'Password' => Config::get('fedex.fedexparentapiPassword')
            )),
            'ucred'=>array('UserCredential' => array(
                'Key' => Config::get('fedex.fedexapiKey'),
                'Password' => Config::get('fedex.fedexapiPassword')
            ))
        );
        return $webAuth;
    }

    public  static  function getClientDetail(){
        return array('ClientDetail' => array(
            'AccountNumber' => Config::get('fedex.fedexaccountNumber'),
            'MeterNumber' => Config::get('fedex.fedexmeterNumber')
        ));
    }

    public static function getTransactionDetail(){
        return array('TransactionDetail' => array('CustomerTransactionId' => rand(100000, 999999)));
    }

    public static function printSuccess($client, $response) {
        self::printReply($client, $response);
    }

    public static function printReply($client, $response){
        $highestSeverity=$response->HighestSeverity;
        if($highestSeverity=="SUCCESS") {

            Log::info("The transaction was successful");
        }
        if($highestSeverity=="WARNING"){
            Log::warning("The transaction returned a Warning");

        }
        if($highestSeverity=="ERROR"){
            Log::error("The transaction returned an Error.");
            }
        if($highestSeverity=="FAILURE"){
            Log::error("The transaction returned a Failure.");
        }

        self::printNotifications($response -> Notifications);
        self::printRequestResponse($client, $response);
    }

    public static function printRequestResponse($client){
        Log::info('REQUEST:' . "\n". htmlspecialchars($client->__getLastRequest())."");
        Log::info('RESPONSE:' . "\n". htmlspecialchars($client->__getLastResponse())."");
    }

    /**
     *  Print SOAP Fault
     */
    public static function printFault($exception, $client) {
        Log::error("Code:" . $exception->faultcode);
        Log::error("String:". $exception->faultstring);
        Log::error('REQUEST:' . htmlspecialchars($client->__getLastRequest()));
    }

    /**
     * SOAP request/response logging to a file
     */
//    public static function writeToLog($client){
//
//        /**
//         * __DIR__ refers to the directory path of the library file.
//         * This location is not relative based on Include/Require.
//         */
//        if (!$logfile = fopen(__DIR__.'/fedextransactions.log', "a"))
//        {
//            error_func("Cannot open " . __DIR__.'/fedextransactions.log' . " file.\n", 0);
//            exit(1);
//        }
//        fwrite($logfile, sprintf("\r%s:- %s",date("D M j G:i:s T Y"), $client->__getLastRequest(). "\r\n" . $client->__getLastResponse()."\r\n\r\n"));
//
//    }

    /**
     * This section provides a convenient place to setup many commonly used variables
     * needed for the php sample code to function.
     */
    public static function getProperty($var){

        if($var == 'key') Return 'XXX';
        if($var == 'password') Return 'XXX';
        if($var == 'parentkey') Return 'XXX';
        if($var == 'parentpassword') Return 'XXX';
        if($var == 'shipaccount') Return 'XXX';
        if($var == 'billaccount') Return 'XXX';
        if($var == 'dutyaccount') Return 'XXX';
        if($var == 'freightaccount') Return 'XXX';
        if($var == 'trackaccount') Return 'XXX';
        if($var == 'dutiesaccount') Return 'XXX';
        if($var == 'importeraccount') Return 'XXX';
        if($var == 'brokeraccount') Return 'XXX';
        if($var == 'distributionaccount') Return 'XXX';
        if($var == 'locationid') Return 'PLBA';
        if($var == 'printlabels') Return true;
        if($var == 'printdocuments') Return true;
        if($var == 'packagecount') Return '4';
        if($var == 'validateaccount') Return 'XXX';
        if($var == 'meter') Return 'XXX';

        if($var == 'shiptimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));

        if($var == 'spodshipdate') Return '2016-04-13';
        if($var == 'serviceshipdate') Return '2013-04-26';
        if($var == 'shipdate') Return '2016-04-21';

        if($var == 'readydate') Return '2014-12-15T08:44:07';
        //if($var == 'closedate') Return date("Y-m-d");
        if($var == 'closedate') Return '2016-04-18';
        if($var == 'pickupdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
        if($var == 'pickuptimestamp') Return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
        if($var == 'pickuplocationid') Return 'SQLA';
        if($var == 'pickupconfirmationnumber') Return '1';

        if($var == 'dispatchdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
        if($var == 'dispatchlocationid') Return 'NQAA';
        if($var == 'dispatchconfirmationnumber') Return '4';

        if($var == 'tag_readytimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
        if($var == 'tag_latesttimestamp') Return mktime(20, 0, 0, date("m"), date("d")+1, date("Y"));

        if($var == 'expirationdate') Return date("Y-m-d", mktime(8, 0, 0, date("m"), date("d")+15, date("Y")));
        if($var == 'begindate') Return '2014-10-16';
        if($var == 'enddate') Return '2014-10-16';

        if($var == 'trackingnumber') Return 'XXX';

        if($var == 'hubid') Return '5531';

        if($var == 'jobid') Return 'XXX';

        if($var == 'searchlocationphonenumber') Return '5555555555';
        if($var == 'customerreference') Return '39589';

        if($var == 'shipper') Return array(
            'Contact' => array(
                'PersonName' => 'Sender Name',
                'CompanyName' => 'Sender Company Name',
                'PhoneNumber' => '1234567890'
            ),
            'Address' => array(
                'StreetLines' => array('Addres \r  s Line 1'),
                'City' => 'Collierville',
                'StateOrProvinceCode' => 'TN',
                'PostalCode' => '38017',
                'CountryCode' => 'US',
                'Residential' => 1
            )
        );
        if($var == 'recipient') Return array(
            'Contact' => array(
                'PersonName' => 'Recipient Name',
                'CompanyName' => 'Recipient Company Name',
                'PhoneNumber' => '1234567890'
            ),
            'Address' => array(
                'StreetLines' => array('Address Line 1'),
                'City' => 'Herndon',
                'StateOrProvinceCode' => 'VA',
                'PostalCode' => '20171',
                'CountryCode' => 'US',
                'Residential' => 1
            )
        );

        if($var == 'address1') Return array(
            'StreetLines' => array('10 Fed Ex Pkwy'),
            'City' => 'Memphis',
            'StateOrProvinceCode' => 'TN',
            'PostalCode' => '38115',
            'CountryCode' => 'US'
        );
        if($var == 'address2') Return array(
            'StreetLines' => array('13450 Farmcrest Ct'),
            'City' => 'Herndon',
            'StateOrProvinceCode' => 'VA',
            'PostalCode' => '20171',
            'CountryCode' => 'US'
        );
        if($var == 'searchlocationsaddress') Return array(
            'StreetLines'=> array('240 Central Park S'),
            'City'=>'Austin',
            'StateOrProvinceCode'=>'TX',
            'PostalCode'=>'78701',
            'CountryCode'=>'US'
        );

        if($var == 'shippingchargespayment') Return array(
            'PaymentType' => 'SENDER',
            'Payor' => array(
                'ResponsibleParty' => array(
                    'AccountNumber' => getProperty('billaccount'),
                    'Contact' => null,
                    'Address' => array('CountryCode' => 'US')
                )
            )
        );
        if($var == 'freightbilling') Return array(
            'Contact'=>array(
                'ContactId' => 'freight1',
                'PersonName' => 'Big Shipper',
                'Title' => 'Manager',
                'CompanyName' => 'Freight Shipper Co',
                'PhoneNumber' => '1234567890'
            ),
            'Address'=>array(
                'StreetLines'=>array(
                    '1202 Chalet Ln',
                    'Do Not Delete - Test Account'
                ),
                'City' =>'Harrison',
                'StateOrProvinceCode' => 'AR',
                'PostalCode' => '72601-6353',
                'CountryCode' => 'US'
            )
        );
    }

    public static function setEndpoint($var){
        if($var == 'changeEndpoint') Return false;
        if($var == 'endpoint') Return 'XXX';
    }

    public static function printNotifications($notes){
        foreach($notes as $noteKey => $note){
            if(is_string($note)){
                Log::info($noteKey . ': ' . $note . Newline);
            }
            else{
                self::printNotifications($note);
            }
        }
        //echo Newline;
    }

    public static function printError($client, $response){
        self::printReply($client, $response);
    }

    public static function trackDetails($details, $spacer){
        foreach($details as $key => $value){
            if(is_array($value) || is_object($value)){
                $newSpacer = $spacer. '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<tr><td>'. $spacer . $key.'</td><td>&nbsp;</td></tr>';
                self::trackDetails($value, $newSpacer);
            }elseif(empty($value)){
                echo '<tr><td>'.$spacer. $key .'</td><td>'.$value.'</td></tr>';
            }else{
                echo '<tr><td>'.$spacer. $key .'</td><td>'.$value.'</td></tr>';
            }
        }
    }

    public static function printString($spacer, $key, $value){
        if(is_bool($value)){
            if($value)$value='true';
            else $value='false';
        }
        echo '<tr><td>'.$spacer. $key .'</td><td>'.$value.'</td></tr>';
    }

    public static function printPostalDetails($details, $spacer){
        foreach($details as $key => $value){
            if(is_array($value) || is_object($value)){
                $newSpacer = $spacer. '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<tr><td>'. $spacer . $key.'</td><td>&nbsp;</td></tr>';
                self::printPostalDetails($value, $newSpacer);
            }elseif(empty($value)){
                self::printString($spacer, $key, $value);
            }else{
                self::printString($spacer, $key, $value);
            }
        }
    }
}