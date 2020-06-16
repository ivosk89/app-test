<?php

namespace Controllers;

use Models\CityModel;
use Models\CountryModel;
use Models\RegionModel;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{

    public function index()
    {
        $countries = CountryModel::getAll();
        $regions = RegionModel::getByCountry(reset($countries));
        $cities = CityModel::getByRegion(reset($regions));

        return \App::_()->render('front/index.twig', [
            'countries' => $countries,
            'regions' => $regions,
            'cities' => $cities,
        ]);
    }

    public function ajaxChangeCountry(Request $request)
    {
        if ($request->getMethod('POST') && $request->get('countryId') && $request->get('change') == 1) {
            $regions = RegionModel::getByCountry($request->get('countryId'));
            $result = ['regions' => [], 'cities' => []];
            foreach ($regions as $n => $region) {
                $result['regions'][$n]['id'] = $region->getId();
                $result['regions'][$n]['name'] = $region->getName();
            }

            foreach (CityModel::getByRegion(reset($regions)) as $n => $city) {
                $result['cities'][$n]['id'] = $city->getId();
                $result['cities'][$n]['name'] = $city->getName();
            }

            return new JsonResponse([
                'regions' => $result['regions'],
                'cities' => $result['cities']
            ]);
        }

        return new JsonResponse();
    }

    public function ajaxChangeRegion(Request $request)
    {
        if ($request->getMethod('POST') && $request->get('regionId') && $request->get('change') == 1) {
            $result = ['cities' => []];
            foreach (CityModel::getByRegion($request->get('regionId')) as $n => $city) {
                $result['cities'][$n]['id'] = $city->getId();
                $result['cities'][$n]['name'] = $city->getName();
            }

            return new JsonResponse([
                'cities' => $result['cities']
            ]);
        }

        return new JsonResponse();
    }

}