<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\FedexHelper;

define('AVSWSDL', resource_path('wsdl/AddressValidationService_v4.wsdl'));

class FedexAddressValidationController extends Controller
{
    private function getVersion ()
    {
        return array( "Version" => array(
            'ServiceId' => 'aval',
            'Major' => '4',
            'Intermediate' => '0',
            'Minor' => '0'
        ));
    }

    private function buildRequest($input){
        $AddressServiceRequest['WebAuthenticationDetail'] = FedexHelper::getWebAuthenticationDetail()['ucred'];
        $AddressServiceRequest['ClientDetail'] = FedexHelper::getClientDetail()['ClientDetail'];
        $AddressServiceRequest['TransactionDetail'] = FedexHelper::getTransactionDetail()['TransactionDetail'];
        $AddressServiceRequest['Version'] = $this->getVersion()['Version'];
        $AddressServiceRequest['InEffectAsOfTimestamp'] = date('c');
        $AddressServiceRequest['AddressesToValidate'] = $input->AddressesToValidate;

        return $AddressServiceRequest;

    }


    public function validatePostalAddress(Request $request){
        //dd($request);

        $TEST = array(
            0 => array(
                'ClientReferenceId' => 'ClientReferenceId1',
                'Address' => array(
                    'StreetLines' => array('100 Nickerson RD'),
                    'PostalCode' => '01752',
                    'City' => 'Marlborough',
                    'StateOrProvinceCode' => 'MA',
                    'CountryCode' => 'US'
                )
            ),
            1 => array(
                'ClientReferenceId' => 'ClientReferenceId2',
                'Address' => array(
                    'StreetLines' => array('167 PROSPECT HIGHWAY'),
                    'City' => 'New SOUTH WALES',
                    'PostalCode' => '2147',
                    'CountryCode' => 'AU'
                )
            ),
            2 => array(
                'ClientReferenceId' => 'ClientReferenceId3',
                'Address' => array(
                    'StreetLines' => array('3 WATCHMOOR POINT', 'WATCHMOOR ROAD'),
                    'PostalCode' => 'GU153AQ',
                    'City' => 'CAMBERLEY',
                    'CountryCode' => 'GB'
                )
            )
        );

        //dd(json_encode($request->AddressesToValidate));



        $validateClient = FedexHelper::getSoapClient(AVSWSDL);
        $FinalRequest  = $this->buildRequest($request);
        //dd($FinalRequest);
        //dd($validateClient->__getFunctions());
        try {


            $postalResponse = $validateClient -> addressValidation($FinalRequest);
            //dd($postalResponse);


            if ($postalResponse -> HighestSeverity != 'FAILURE' && $postalResponse -> HighestSeverity != 'ERROR'){

                FedexHelper::printSuccess($validateClient, $postalResponse);
                return new JsonResponse(["status"=>200, "data" =>$postalResponse->AddressResults],Response::HTTP_OK);

            }else{

                FedexHelper::printError($validateClient, $postalResponse);
                return new JsonResponse(["status"=>500,"data" =>$postalResponse],Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } catch (SoapFault $exception) {

            FedexHelper::printFault($exception, $validateClient);
            return new JsonResponse(["status"=>500,"data" =>$exception],Response::HTTP_INTERNAL_SERVER_ERROR);

        }


    }

}
