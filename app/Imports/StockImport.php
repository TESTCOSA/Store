<?php

namespace App\Imports;

use App\Models\Stock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StockImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    public function collection(Collection $collection)
    {

        foreach ($collection as $row) {

            Stock::create([
                'item_id' => $row[0],
                'quantity' => $row[1],
                'available_quantity' => $row[2],
                'warehouse_id' => $row[3],

            ]);
        }
    }
}
