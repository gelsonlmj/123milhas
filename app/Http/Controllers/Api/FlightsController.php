<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use App\Services\FlightService;

/**
 * @OA\Info(title="Api de voos", version="0.1")
 */

class FlightsController extends Controller
{

    private $flightService;

    public function __construct()
    {
        $this->flightService = new FlightService();
    }

    /**
     * Retorna todos os voos.
     *
     * @OA\Get(
     *   path="/",
     *   tags={"Flights"},
     *   summary="Lista de voos",
     *   @OA\Response(
     *    response=200,
     *    description="Sucesso",
     *      @OA\JsonContent(
     *         @OA\Property(property="data", type="object", example={
     *                {
     *                      {"id":1,"cia":"GOL","fare":"1AF","flightNumber":"G3-1701","origin":"CNF","destination":"BSB","departureDate":"29/01/2021","arrivalDate":"29/01/2021","departureTime":"07:40","arrivalTime":"09:00","classService":3,"price":50,"tax":36,"outbound":1,"inbound":0,"duration":"1:20"},
     *                      {"id":2,"cia":"AZUL","fare":"1AF","flightNumber":"AD-1111","origin":"CNF","destination":"BSB","departureDate":"29/01/2021","arrivalDate":"29/01/2021","departureTime":"07:40","arrivalTime":"09:00","classService":3,"price":50,"tax":36,"outbound":1,"inbound":0,"duration":"1:20"}
     *                }
     *          }),
     *         @OA\Property(property="mensagem", type="string", example="Operação realizada com sucesso."),
     *         @OA\Property(property="sucesso", type="bool", example="true")
     *      )
     *   ),
     *   @OA\Response(
     *    response=400,
     *    description="Exemplos de possíveis erros",
     *      @OA\JsonContent(
     *         @OA\Property(property="mensagem", type="string", example="Erro ao realizar operação."),
     *         @OA\Property(property="sucesso", type="bool", example="false")
     *      )
     *   ),
     * )
     * 
     */
    public function flights()
    {
        try {
            return $this->responseDataSuccess($this->flightService->load());
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * Retorna o grupo de voos disponiveis
     * 
     * @OA\Get(
     *   path="/groups-available",
     *   tags={"Flights"},
     *   summary="Lista os grupos de voos disponíveis",
     *   @OA\Response(
     *    response=200,
     *    description="Sucesso",
     *      @OA\JsonContent(
     *         @OA\Property(property="data", type="object", example={
     *                {
     *                  "1AFG1 (Valor Total R$ 200 | idas[1,2,3] | voltas[9,10])",
     *                  "1AFG2 (Valor Total R$ 250 | idas[1,2,3,4,5,6] | voltas[9,10,11])",
     *                  "1AFG4 (Valor Total R$ 270 | idas[7] | voltas[9,10])",
     *                  "1AFG3 (Valor Total R$ 300 | idas[4,5,6,8] | voltas[9,10,11])"
     *                }
     *          }),
     *         @OA\Property(property="mensagem", type="string", example="Operação realizada com sucesso."),
     *         @OA\Property(property="sucesso", type="bool", example="true")
     *      )
     *   ),
     *   @OA\Response(
     *    response=400,
     *    description="Exemplos de possíveis erros",
     *      @OA\JsonContent(
     *         @OA\Property(property="mensagem", type="string", example="Erro ao realizar operação."),
     *         @OA\Property(property="sucesso", type="bool", example="false")
     *      )
     *   ),
     * )
     * 
     */
    public function groupsAvailable()
    {
        try {
            return $this->responseDataSuccess($this->flightService->groupsAvailable());
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * Retorna o grupo com o menor preço
     *
     * @OA\Get(
     *   path="/lowest-price",
     *   tags={"Flights"},
     *   summary="Retorna o grupo de voo de menor preço",
     *   @OA\Response(
     *    response=200,
     *    description="Sucesso",
     *      @OA\JsonContent(
     *         @OA\Property(property="data", type="object", example={
     *                {
     *                  "id":"1AFG1",
     *                  "type":"1AF",
     *                  "going":"1,2,3",
     *                  "return":"9,10",
     *                  "price":200
     *                }
     *      }),
     *         @OA\Property(property="mensagem", type="string", example="Operação realizada com sucesso."),
     *         @OA\Property(property="sucesso", type="bool", example="true")
     *      )
     *   ),
     *   @OA\Response(
     *    response=400,
     *    description="Exemplos de possíveis erros",
     *      @OA\JsonContent(
     *         @OA\Property(property="mensagem", type="string", example="Erro ao realizar operação."),
     *         @OA\Property(property="sucesso", type="bool", example="false")
     *      )
     *   ),
     * )
     * 
     */
    public function lowestPrice()
    {
        try {
            return $this->responseDataSuccess($this->flightService->lowestPrice());
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * Retorna todas as informações de voos
     * 
     * @OA\Get(
     *   path="/informations",
     *   tags={"Flights"},
     *   summary="Retorna o grupo de voo de menor preço",
     *   @OA\Response(
     *    response=200,
     *    description="Sucesso",
     *      @OA\JsonContent(
     *         @OA\Property(property="data", type="object", example={
     *                {
     *                  "fligths":{
     *                      {"id":1,"cia":"GOL","fare":"1AF","flightNumber":"G3-1701","origin":"CNF","destination":"BSB","departureDate":"29/01/2021","arrivalDate":"29/01/2021","departureTime":"07:40","arrivalTime":"09:00","classService":3,"price":50,"tax":36,"outbound":1,"inbound":0,"duration":"1:20"},
     *                      {"id":2,"cia":"AZUL","fare":"1AF","flightNumber":"AD-1111","origin":"CNF","destination":"BSB","departureDate":"29/01/2021","arrivalDate":"29/01/2021","departureTime":"07:40","arrivalTime":"09:00","classService":3,"price":50,"tax":36,"outbound":1,"inbound":0,"duration":"1:20"}
     *                  },
     *                  "totalGroups":8,
     *                  "totalFlights":15,
     *                  "cheapestPrice":200,
     *                  "cheapestGroup":"1AFG1"
     *                }
     *      }),
     *         @OA\Property(property="mensagem", type="string", example="Operação realizada com sucesso."),
     *         @OA\Property(property="sucesso", type="bool", example="true")
     *      )
     *   ),
     *   @OA\Response(
     *    response=400,
     *    description="Exemplos de possíveis erros",
     *      @OA\JsonContent(
     *         @OA\Property(property="mensagem", type="string", example="Erro ao realizar operação."),
     *         @OA\Property(property="sucesso", type="bool", example="false")
     *      )
     *   ),
     * )
     * 
     */
    public function informations()
    {
        try {
            return $this->responseDataSuccess($this->flightService->allInformations());
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

}
