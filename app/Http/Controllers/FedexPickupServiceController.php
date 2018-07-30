<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\FedexHelper;

define('PICKUPWSDL', resource_path('wsdl/PickupService_v15.wsdl'));

class FedexPickupServiceController extends Controller
{
    private function getVersion ()
    {
        return array( "Version" => array(
            'ServiceId' => 'disp',
            'Major' => 15,
            'Intermediate' => 0,
            'Minor' => 0
        ));
    }

    private function buildRequest($input){
        $PickupServiceRequest['WebAuthenticationDetail'] = FedexHelper::getWebAuthenticationDetail()['ucred'];
        $PickupServiceRequest['ClientDetail'] = FedexHelper::getClientDetail()['ClientDetail'];
        $PickupServiceRequest['TransactionDetail'] = FedexHelper::getTransactionDetail()['TransactionDetail'];
        $PickupServiceRequest['Version'] = $this->getVersion()['Version'];
        $PickupServiceRequest['PickupAddress'] = $input->PickupAddress;
        $PickupServiceRequest['PickupRequestType'] = $input->PickupRequestType;
        $PickupServiceRequest['DispatchDate'] = $input->DispatchDate;
        $PickupServiceRequest['PackageReadyTime'] = $input->PackageReadyTime;
        $PickupServiceRequest['CustomerCloseTime'] = $input->CustomerCloseTime;
        $PickupServiceRequest['Carriers'] = $input->Carriers;

        return $PickupServiceRequest;

    }




    public function pickupServiceAvailability(Request $request){


    $validateClient = FedexHelper::getSoapClient(PICKUPWSDL);
    $FinalRequest  = $this->buildRequest($request);
    //print_r($FinalRequest);
    //dd("echo");
    dd($validateClient->__getFunctions());
    try {


        $postalResponse = $validateClient -> getPickupAvailability($FinalRequest);
        //dd($postalResponse);


        if ($postalResponse -> HighestSeverity != 'FAILURE' && $postalResponse -> HighestSeverity != 'ERROR'){

            FedexHelper::printSuccess($validateClient, $postalResponse);
            return new JsonResponse(["status"=>200, "data" =>$postalResponse],Response::HTTP_OK);

        }else{

            FedexHelper::printError($validateClient, $postalResponse);
            return new JsonResponse(["status"=>500,"data" =>$postalResponse],Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    } catch (\SoapFault $exception) {
        //dd($exception);

        FedexHelper::printFault($exception, $validateClient);
        return new JsonResponse(["status"=>500,"data" =>$exception],Response::HTTP_INTERNAL_SERVER_ERROR);

    }


}
    private function buildCreatePickupRequest($input){
        $PickupRequest['WebAuthenticationDetail'] = FedexHelper::getWebAuthenticationDetail()['ucred'];
        $PickupRequest['ClientDetail'] = FedexHelper::getClientDetail()['ClientDetail'];
        $PickupRequest['TransactionDetail'] = FedexHelper::getTransactionDetail()['TransactionDetail'];
        $PickupRequest['Version'] = $this->getVersion()['Version'];
        $PickupRequest['OriginDetail'] = $input->OriginDetail;
        $PickupRequest['PackageCount'] = $input->PackageCount;
        $PickupRequest['TotalWeight'] =  $input->TotalWeight;
        $PickupRequest['CarrierCode'] =  $input->CarrierCode;
        $PickupRequest['CourierRemarks'] = $input->CourierRemarks;

        return $PickupRequest;

    }
    public function createPickupService(Request $request){


        $validateClient = FedexHelper::getSoapClient(PICKUPWSDL);
        $FinalRequest  = $this->buildCreatePickupRequest($request);
        //dd(json_encode($FinalRequest));
        //dd("echo");
        //dd($validateClient->__getFunctions());
        try {


            $postalResponse = $validateClient -> createPickup($FinalRequest);

            //dd($postalResponse);


            if ($postalResponse -> HighestSeverity != 'FAILURE' && $postalResponse -> HighestSeverity != 'ERROR'){

                FedexHelper::printSuccess($validateClient, $postalResponse);
                return new JsonResponse(["status"=>200, "data" =>$postalResponse],Response::HTTP_OK);

            }else{

                FedexHelper::printError($validateClient, $postalResponse);
                return new JsonResponse(["status"=>500,"data" =>$postalResponse],Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } catch (\SoapFault $exception) {
            dd($exception);

            FedexHelper::printFault($exception, $validateClient);
            return new JsonResponse(["status"=>500,"data" =>$exception],Response::HTTP_INTERNAL_SERVER_ERROR);

        }


    }
    private function buildCancelPickupRequest($input){
        $CancelPickupRequest['WebAuthenticationDetail'] = FedexHelper::getWebAuthenticationDetail()['ucred'];
        $CancelPickupRequest['ClientDetail'] = FedexHelper::getClientDetail()['ClientDetail'];
        $CancelPickupRequest['TransactionDetail'] = FedexHelper::getTransactionDetail()['TransactionDetail'];
        $CancelPickupRequest['Version'] = $this->getVersion()['Version'];
        $CancelPickupRequest['CarrierCode'] = 'FDXE'; // valid values FDXE-Express, FDXG-Ground, etc
        $CancelPickupRequest['PickupConfirmationNumber'] = getProperty('pickupconfirmationnumber'); // Replace 'XXX' with your Pickup confirmation number
        $CancelPickupRequest['ScheduledDate'] = getProperty('pickupdate');
        $CancelPickupRequest['Location'] = getProperty('pickuplocationid'); // Replace 'XXX' with your Pickip Loaction Code/ID
        $CancelPickupRequest['CourierRemarks'] = 'Do not pickup.  This is a test';

        return $CancelPickupRequest;

    }
    public function cancelPickupService(Request $request){


        $validateClient = FedexHelper::getSoapClient(PICKUPWSDL);
        $FinalRequest  = $this->buildCancelPickupRequest($request);
        //print_r($FinalRequest);
        //dd("echo");
        //dd($validateClient->__getFunctions());
        try {


            $postalResponse = $validateClient -> cancelPickup($FinalRequest);
            //dd($postalResponse);


            if ($postalResponse -> HighestSeverity != 'FAILURE' && $postalResponse -> HighestSeverity != 'ERROR'){

                FedexHelper::printSuccess($validateClient, $postalResponse);
                return new JsonResponse(["status"=>200, "data" =>$postalResponse],Response::HTTP_OK);

            }else{

                FedexHelper::printError($validateClient, $postalResponse);
                return new JsonResponse(["status"=>500,"data" =>$postalResponse],Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } catch (\SoapFault $exception) {
            dd($exception);

            FedexHelper::printFault($exception, $validateClient);
            return new JsonResponse(["status"=>500,"data" =>$exception],Response::HTTP_INTERNAL_SERVER_ERROR);

        }


    }
}
