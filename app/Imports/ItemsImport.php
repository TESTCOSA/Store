<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // Iterate over each row in the collection
        foreach ($collection as $row) {
            // Create a new Item instance and save it to the database
            Item::create([
                'category_id' => $row[0],  // Assuming category_id is the first column in the file
                'name' => $row[1],          // Assuming name is the second column
                'size' => $row[2],          // Assuming size is the third column
                'serial_number' => $row[3], // Assuming serial_number is the fourth column
                'test_tag' => $row[4],      // Assuming test_tag is the fifth column
                'make' => $row[5],          // Assuming make is the sixth column
                'model' => $row[6],         // Assuming model is the seventh column
                'status' => $row[7],        // Assuming status is the eighth column
                'sequence' => $row[8],      // Assuming sequence is the ninth column
                'low_stock' => $row[9],     // Assuming low_stock is the tenth column
                'file' => $row[10],         // Assuming file is the eleventh column
            ]);
        }
    }
}
