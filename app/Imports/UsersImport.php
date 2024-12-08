<?php

namespace App\Imports;

use App\Models\UserExcel;  // Import the UserExcel model
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;  // Import the trait

class UsersImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use SkipsFailures;  // Use the SkipsFailures trait

    protected $failedRows = [];

    public function model(array $row)
    {
        // If required fields are missing, log the failure
        if (empty($row['name']) || empty($row['email']) || empty($row['phone']) || empty($row['gender'])) {
            // This will allow failure tracking
            return null;
        }
    
        // Here you can add more validations for email format, phone format etc.
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            // If email is invalid, log the failure
            return null;
        }
    
        // You can handle phone number formatting if needed (scientific notation issue)
        $phone = str_replace(['.', ',', ' '], '', $row['phone']); // Sanitizing the phone number
    
        return new UserExcel([
            'name'   => $row['name'],
            'email'  => $row['email'],
            'phone'  => $phone,
            'gender' => $row['gender'],
        ]);
    }
    // This method is used to get the failed rows
    public function getFailedRows()
    {
        return $this->failedRows;
    }

    public function onFailure(\Maatwebsite\Excel\Concerns\Failure $failure)
    {
        foreach ($failure->errors() as $error) {
            $this->failedRows[] = [
                'name'  => $failure->values()['name'],
                'email' => $failure->values()['email'],
                'phone' => $failure->values()['phone'],
                'gender'=> $failure->values()['gender'],
                'error' => implode(', ', $error),  // Capture all error messages
            ];
        }
    }
    
}
