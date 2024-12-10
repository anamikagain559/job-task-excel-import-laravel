<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class FailedImportExport implements FromCollection
{
    protected $failedRows;

    public function __construct($failedRows)
    {
        $this->failedRows = $failedRows;
    }

    public function collection()
    {
        return collect($this->failedRows);  // Return failed rows as a collection
    }
}
