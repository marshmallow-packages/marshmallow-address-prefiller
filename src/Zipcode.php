<?php

namespace Marshmallow\Zipcode;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Zipcode
{
    protected $street = 'street';

    protected $city = 'city';

    protected $province = 'province';

    protected $country = 'country';

    protected $latitude = 'latitude';

    protected $longitude = 'longitude';

    public function get(string $zip_code, string $house_number)
    {
        $response_array = $this->callGeoDataApi($zip_code, $house_number);
        $this->validateApiResponse($response_array);

        $address_information = $this->getAddressInformation($response_array);

        return response()->json([
            $this->street => $address_information['straatnaam'],
            $this->city => $address_information['woonplaatsnaam'],
            $this->province => $address_information['provincienaam'],
            $this->country => 'NL',
            $this->latitude => $this->getLatitudeFromResponse($address_information),
            $this->longitude => $this->getLongitudeFromResponse($address_information),
        ]);
    }

    protected function parsePointLocation($address_information, $key)
    {
        $location = Str::of($address_information['centroide_ll'])
            ->replace('POINT(', '')
            ->replace(')', '')
            ->explode(' ');

        return $location[$key];
    }

    protected function getLatitudeFromResponse(array $address_information)
    {
        return $this->parsePointLocation($address_information, 0);
    }

    protected function getLongitudeFromResponse(array $address_information)
    {
        return $this->parsePointLocation($address_information, 1);
    }

    protected function getAddressInformation(array $response_array)
    {
        return $response_array['response']['docs'][0];
    }

    protected function validateApiResponse(array $response_array)
    {
        if (!isset($response_array['response']['docs'][0])) {
            throw new Exception(__('No address was found.'), 1);
        }
    }

    protected function callGeoDataApi(string $zip_code, string $house_number)
    {
        $zip_code = str_replace(' ', '', $zip_code);
        return Http::get(
            "https://geodata.nationaalgeoregister.nl/locatieserver/free?fq=postcode:{$zip_code}&fq=huisnummer:{$house_number}"
        )->json();
    }

    public function street($field)
    {
        $this->street = $field;
        return $this;
    }

    public function city($field)
    {
        $this->city = $field;
        return $this;
    }

    public function province($field)
    {
        $this->province = $field;
        return $this;
    }

    public function country($field)
    {
        $this->country = $field;
        return $this;
    }

    public function latitude($field)
    {
        $this->latitude = $field;
        return $this;
    }

    public function longitude($field)
    {
        $this->longitude = $field;
        return $this;
    }
}
