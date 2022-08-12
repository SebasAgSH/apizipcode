<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Zip_code;

class ZipCodeController extends Controller
{
    public function searchZipCode($zipCode)
    {
        return "hola";
        //Convert txt file to array 
        $array = $this->convertTxtFile();
        //Filter array with zip code parameter
        $filter_data = $this->filterArray($array, $zipCode);
        //Format to respose
        $fomart_response = $this->applyFormat($filter_data);
        //Return response
        return  $fomart_response;
    }
    private function convertTxtFile()
    {
        $lines = file('CPdescarga.txt', FILE_IGNORE_NEW_LINES);
        $filecont = str_replace("|", ",", $lines);
        foreach ($filecont as $key => $line) {
            if (!empty($line)) {
                $array[$key] = array_filter(explode(",", $line));
            }
        }
        return $array;
    }
    private function filterArray($array, $zipCode)
    {
        $filter_data = array_filter($array, function ($ar) use ($zipCode) {
            if ($ar[0] == $zipCode) {
                return $ar;
            }
        });
        return  $filter_data;
    }
    private function applyFormat($array)
    {
        foreach ($array as $key => $a) {
            $new_array = [
                'zip_code' => $a[0],
                'locality' => !empty($a[3]) ? $this->removeSpecialCharacters($a[3]) : '',
                'federal_entity' => [
                    'key' => ltrim($a[7],"0"),
                    'name' => $this->removeSpecialCharacters($a[4]),
                    'code' => !empty($a[9]) ? $a[9] : null,
                ],
                'settlements' => [
                    [
                        'key' => !empty($a[12]) ? $a[12] : '',
                        'name' => !empty($a[1]) ? $this->removeSpecialCharacters($a[1]) : '',
                        'zone_type' => !empty($a[13]) ? $this->removeSpecialCharacters($a[13]) : '',
                        'settlement_type' => [
                            'name' => !empty($a[2]) ? $this->removeSpecialCharacters($a[2]) : '',
                        ]
                    ]
                        ],
                        'municipality' => [
                            'key' => !empty($a[10]) ? $a[10] : '',
                            'name' => !empty($a[10]) ? $this->removeSpecialCharacters($a[5]) : '',
                        ]
            ];
        }
        return  $new_array;
    }
    public function removeSpecialCharacters($string)
    {
        $new_string = utf8_encode($string);

        $new_string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $new_string
        );
        $new_string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $new_string
        );
        $new_string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $new_string
        );
        $new_string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $new_string
        );
        $new_string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $new_string
        );
        $new_string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $new_string
        );

        return $new_string;
    }
}
