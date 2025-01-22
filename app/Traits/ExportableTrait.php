<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait ExportableTrait
{
    /**
     * Function for creating a CSV of a collection, returns the CSV data as string
     * @param Collection $collection
     * @return false|string
     */
    public function toCsv(Collection $collection)
    {
        // create a file pointer connected to the output stream
        $output = fopen('php://temp/maxmemory:' . (64 * 1024 * 1024), 'r+');
        // loop over the rows, outputting them
        foreach ($collection as $row) {
            fputcsv($output, $row->toArray());
        }
        rewind($output);
        return stream_get_contents($output);
    }
}
