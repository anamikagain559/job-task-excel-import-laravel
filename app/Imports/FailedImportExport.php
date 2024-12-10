<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FailedImportExport implements FromCollection, WithHeadings
{
    protected $failedRows;

    public function __construct($failedRows)
    {
        $this->failedRows = $failedRows;
    }

    public function collection()
    {
        // Return a collection of failed rows
        return collect($this->failedRows);
    }

    public function headings(): array
    {
        // Define the header row for the export
        return ['Name', 'Email', 'Phone', 'Gender', 'Error'];
    }
}
