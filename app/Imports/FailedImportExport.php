<?php

namespace App\Exports;
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
        return collect($this->failedRows);
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Gender', 'Error'];
    }
}

