<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Collection;

class Service {

    public function generateResponce($params) {

        $type = $params['request']->header('Accept');
        if(!$params['isCollection']) {

            switch($type) {
                case 'application/xml':

                    $xmlData = $this->generateXmlData($params['result'], $params['isCollection']);
                    return response()->xml($xmlData);

                default:
                    return new $params['resource']($params['result']);
            }
        }
        else {

            switch($type) {
                case 'application/xml':

                    $xmlData = $this->generateXmlData($params['result'], $params['isCollection']);
                    return response()->xml($xmlData);

                default:
                    return $params['resource']::collection($params['result']);
            }
        }
    }

    private function generateXmlData($result, $isCollection = false) {
        $xmlData = [];

        if($isCollection) {

            foreach($result->toArray() as $item) {
                $xmlData["item{$item['id']}"] = $item;
            }
        }
        else {
            $xmlData["item{$result->id}"] = $result->toArray();
        }

        return $xmlData;
    }

}
