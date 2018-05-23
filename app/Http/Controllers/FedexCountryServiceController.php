<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use PHPUnit\Util\Json;
use App\Helpers\FedexHelper;
/**
 * @SWG\Swagger(
 *     basePath="/api",
 *     host="fedexapi.corpulse.com",
 *     schemes={"http","https"},
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *          @SWG\Info(
 *              title="Fedex API Application ",
 *              version="1.0",
 *              description="FedexApO",
 *              @SWG\Contact(name="Ravi Teja",email="rteja7228@gmail.com"),
 *              @SWG\License(name="Unlicense")
 *          ),
 *          @SWG\Definition(
 *              definition="Timestamps",
 *              @SWG\Property(
 *                  property="created_at",
 *                  type="string",
 *                  format="date-time",
 *                  description="Creation date",
 *                  example="2017-03-01 00:00:00"
 *              ),
 *              @SWG\Property(
 *                  property="updated_at",
 *                  type="string",
 *                  format="date-time",
 *                  description="Last updated",
 *                  example="2017-03-01 00:00:00"
 *              )
 *          )
 * )
 */

define('COUNTRYWSDL', resource_path('wsdl/CountryService_v6.wsdl'));

class FedexCountryServiceController extends Controller
{

    private function getVersion ()
    {
        return array( "Version" => array(
            'ServiceId' => 'cnty',
            'Major' => '6',
            'Intermediate' => '0',
            'Minor' => '0'
        ));
    }

    private function buildRequest($input){
        $CountryServiceRequest['WebAuthenticationDetail'] = FedexHelper::getWebAuthenticationDetail()['ucred'];
        $CountryServiceRequest['ClientDetail'] = FedexHelper::getClientDetail()['ClientDetail'];
        $CountryServiceRequest['TransactionDetail'] = FedexHelper::getTransactionDetail()['TransactionDetail'];
        $CountryServiceRequest['Version'] = $this->getVersion()['Version'];
        $CountryServiceRequest['ShipDateTime'] = Carbon::now()->addDay()->format('Y-m-d\TH:i:s');
        $CountryServiceRequest['Address'] = $input->Address;
        $CountryServiceRequest['CarrierCode'] = $input->CarrierCode;
        $CountryServiceRequest['Residential'] = $input->Residential;

        return $CountryServiceRequest;

    }


    public function validatePostalCode(Request $request){



        $validateClient = FedexHelper::getSoapClient(COUNTRYWSDL);
        $FinalRequest  = $this->buildRequest($request);
        //dd($validateClient->__getFunctions());
        try {


            $postalResponse = $validateClient -> validatePostal($FinalRequest);
            //dd($postalResponse);


            if ($postalResponse -> HighestSeverity != 'FAILURE' && $postalResponse -> HighestSeverity != 'ERROR'){

                FedexHelper::printSuccess($validateClient, $postalResponse);
                return new JsonResponse(["status"=>200, "data" =>$postalResponse->PostalDetail],Response::HTTP_OK);

            }else{

                FedexHelper::printError($validateClient, $postalResponse);
                return new JsonResponse(["status"=>500,"data" =>$postalResponse],Response::HTTP_INTERNAL_SERVER_ERROR);

            }

        } catch (\SoapFault $exception) {

            FedexHelper::printFault($exception, $validateClient);
            return new JsonResponse(["status"=>500,"data" =>$exception],Response::HTTP_INTERNAL_SERVER_ERROR);

        }


    }
}
