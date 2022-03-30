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

    /** 
     * Retorna os voos disponibilizados pela 123milhas
     * 
    */
    public function load()
    {
        $flights = Http::get("http://prova.123milhas.net/api/flights");

        if (!empty($flights)) {
            return json_decode($flights, true);
        }

        return [];
    }

    /**
     * organiza os voos disponibilizados
     *
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
     * Depois de organizar os voos, vamos formatar os dados para poder criar os grupos de voos
     *
     */
    private function formatFlights()
    {

        $handleFlights = $this->handleFlights();

        if (empty($handleFlights)) {
            return [];
        }

        $groups = [];

        foreach ($handleFlights as $type => $informations) {
            $groups = array_merge($groups, $this->bundleFlightPackages($type, $informations));
        }

        return $groups;
    }

    /** 
     * Combina os voos de ida com o voos de voltas da mesma tarifa, para obtermos o valor desse pacote de voo
     * 
    */
    private function bundleFlightPackages($type, $data) 
    {

        $groups[$type] = [];

        foreach ($data[self::TYPE_FLIGHT_GOING] as $going) {

            foreach ($data[self::TYPE_FLIGHT_RETURN] as $return) {

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

        return $groups;

    }

    /**
     * Cria os grupos de voos através do preço e ordena os grupos em ordem crescente de preço
     *
     * @return Array
     */
    private function groupFlights()
    {

        $formatFlights = $this->formatFlights();

        if (empty($formatFlights)) {
            return [];
        }

        $groupData = [];

        foreach ($formatFlights as $type => $group) {
            $groupData = array_merge($groupData, $this->aggroupFlights($type, $group));
        }

        return $groupData;
    }

    /** 
     * Agrupa os voos para poder gerar os grupos de voos
     * 
    */
    private function aggroupFlights($type, $group)
    {
        $prices = [];
        $groupData = [];

        foreach ($group as $price => $direcao) {

            sort($direcao['going']);
            sort($direcao['return']);

            $going = implode(',', $direcao['going']);
            $return = implode(',', $direcao['return']);

            $id = $type.'G'.$price;

            $groupData[$id] = [
                'id' => $id,
                'type' => $type,
                'going' => $going,
                'return' => $return,
                'price' => $price,
            ];

            $prices[$id] = $price;

        }

        array_multisort($prices, SORT_ASC, $groupData);
        return $groupData;

    }

    /** 
     * Apresenta os grupos de voos disponíveis
     * 
    */
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

    /** 
     * Retorno o grupo de voo com o menor preço
     * 
    */
    public function lowestPrice()
    {
        $groupFlights = $this->groupFlights();

        if (empty($groupFlights)) {
            return [];
        }
        return current($groupFlights);
    }

    /** 
     * Retorna todas as informações de voos, grupos de voos e o grupo de voo com o menor preço
    */
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