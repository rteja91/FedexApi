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
        //dd($validateClient->__getFunctions());
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
            dd($exception);

            FedexHelper::printFault($exception, $validateClient);
            return new JsonResponse(["status"=>500,"data" =>$exception],Response::HTTP_INTERNAL_SERVER_ERROR);

        }


    }
}
