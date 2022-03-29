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
     * Return all flights.
     *
     * @OA\Get(
     *     path="/",
     *     @OA\Response(response="200", description="Retorna todos os voos")
     * )
     * 
     * @return \Illuminate\Http\Response
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
     * Available groups apresentation
     *
     * @OA\Get(
     *     path="/groups-available",
     *     @OA\Response(response="200", description="Retorna o grupo de voos disponiveis para visualização")
     * )
     * 
     * @return \Illuminate\Http\Response
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
     * Lowest Price Flight
     *
     * @OA\Get(
     *     path="/lowest-price",
     *     @OA\Response(response="200", description="Retorna o grupo com o menor preço")
     * )
     * 
     * @return \Illuminate\Http\Response
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
     * All Informations Flight
     * 
     * @OA\Get(
     *     path="/informations",
     *     @OA\Response(response="200", description="Retorna todas as informações de voos, 
     *     grupos de voos, grupo com o menor preço e o identificador do grupo com o menor preço")
     * )
     * 
     * @return \Illuminate\Http\Response
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
