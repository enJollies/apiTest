<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Collection;

class SectionService {

    public function generateXmlArray(Collection $collection, $titleField) {
        $xmlArray = [];
        $itemsArray = $collection->toArray();

        foreach($itemsArray as $item) {
            $xmlArray[$item[$titleField]] = $item;
        }
        return $xmlArray;
    }

    public function generateXmlItem($model, $titleField) {

        $xmlItem[$model[$titleField]] = $model;

        return $xmlItem;
    }
}
