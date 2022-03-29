<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(title="Api de voos", version="0.1")
 */

class FlightsController extends Controller
{

    private Array $flights;

    //voo de ida
    CONST TYPE_FLIGHT_GOING = 1;

    //voo de volta
    CONST TYPE_FLIGHT_RETURN = 0;

    public function __construct()
    {
        $this->flights = $this->loadFlights();
    }

    /**
     * Load all flights 123milhas.
     * 
     * @return \Illuminate\Http\Response
     */
    private function loadFlights()
    {
        $flights = Http::get("http://prova.123milhas.net/api/flights");

        if (!empty($flights)) {
            return json_decode($flights, true);
        }

        return [];
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
        return $this->flights;
    }

    /**
     * handles flights to be able to group.
     *
     * @return \Illuminate\Http\Response
     */
    private function handleFlights()
    {
        if (empty($this->flights)) {
            return [];
        }

        $data = [];

        foreach ($this->flights as $flight) {

            $going = (int) ($flight['outbound'] && !$flight['inbound']);

            $data[$flight['fare']][$going][] = [
                'id' => $flight['id'],
                'price' => $flight['price'],
            ];

        }

        return $data;
    }

    /**
     * After grouped, format the flights so you can create flight group.
     *
     * @return \Illuminate\Http\Response
     */
    private function formatFlights()
    {
        $groups = [];

        foreach ($this->handleFlights() as $type => $informations) {

            $groups[$type] = [];

            foreach ($informations[self::TYPE_FLIGHT_GOING] as $going) {

                foreach ($informations[self::TYPE_FLIGHT_RETURN] as $return) {

                    $value = $going['price'] + $return['price'];

                    if (!array_key_exists($value, $groups[$type])) {
                        $groups[$type][$value] = [
                            'going' => [$going['id']],
                            'return' => [$return['id']]
                        ];
                        continue;
                    }

                    if (!in_array($going['id'], $groups[$type][$value]['going'])) {
                        $groups[$type][$value]['going'][] = $going['id'];
                    }

                    if (!in_array($return['id'], $groups[$type][$value]['return'])) {
                        $groups[$type][$value]['return'][] = $return['id'];
                    }

                }

            }

        }

        return $groups;
    }

    /**
     * Agrouped flights by price and order by lowest price
     *
     * @return Array
     */
    private function groupFlights()
    {
        $this->handleFlights();
        $formatFlights = $this->formatFlights();

        $key = 1;

        $groupData = [];
        $prices = [];

        foreach ($formatFlights as $type => $group) {

            foreach ($group as $price => $direcao) {

                sort($direcao['going']);
                sort($direcao['return']);

                $going = implode(',', $direcao['going']);
                $return = implode(',', $direcao['return']);

                $groupData[$key] = [
                    'id' => $type.'G'.$key,
                    'type' => $type,
                    'going' => $going,
                    'return' => $return,
                    'price' => $price,
                ];

                $prices[$key] = $price;

                $key++;

            }

        }

        array_multisort($prices, SORT_ASC, $groupData);

        return $groupData;
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
        $flightsAvailable = [];

        foreach ($this->groupFlights() as $group) {
            $flightsAvailable[] = sprintf("%s (Valor Total R$ %s | idas[%s] | voltas[%s])", 
                $group['id'], $group['price'], $group['going'], $group['return']
            );
        }

        return $flightsAvailable;
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
        $groupFlights = $this->groupFlights();
        return current($groupFlights);
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
    public function informationsFlight()
    {
        $flights = $this->flights;
        $groupFlights = $this->groupFlights();
        $lowestPriceFlight = $this->lowestPrice();

        return [
            'fligths' => $flights,
            'groups' => $groupFlights,
            'totalGroups' => count($groupFlights),
            'totalFlights' => count($flights),
            'cheapestPrice' => $lowestPriceFlight['price'],
            'cheapestGroup' => $lowestPriceFlight['id']
        ];
    }

}
