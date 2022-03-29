<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FlightService
{

    //voo de ida
    CONST TYPE_FLIGHT_GOING = 1;

    //voo de volta
    CONST TYPE_FLIGHT_RETURN = 0;

    public function __construct()
    {
    }

    public function load()
    {
        $flights = Http::get("http://prova.123milhas.net/api/flights");

        if (!empty($flights)) {
            return json_decode($flights, true);
        }

        return [];
    }

    /**
     * handles flights to be able to group.
     *
     * @return \Illuminate\Http\Response
     */
    private function handleFlights()
    {
        $flights = $this->load();

        if (empty($flights)) {
            return [];
        }

        $data = [];

        foreach ($flights as $flight) {

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

        $handleFlights = $this->handleFlights();

        if (empty($handleFlights)) {
            return [];
        }

        $groups = [];

        foreach ($handleFlights as $type => $informations) {

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

        if (empty($formatFlights)) {
            return [];
        }

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

    public function groupsAvailable()
    {
        $groupFlights = $this->groupFlights();

        if (empty($groupFlights)) {
            return [];
        }

        $flightsAvailable = [];

        foreach ($groupFlights as $group) {
            $flightsAvailable[] = sprintf("%s (Valor Total R$ %s | idas[%s] | voltas[%s])", 
                $group['id'], $group['price'], $group['going'], $group['return']
            );
        }

        return $flightsAvailable;
    }

    public function lowestPrice()
    {
        $groupFlights = $this->groupFlights();

        if (empty($groupFlights)) {
            return [];
        }
        return current($groupFlights);
    }

    public function allInformations()
    {
        $flights = $this->load();
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