<?php

// namespace App\Imports;

// use App\Models\UserExcel;  // Import the UserExcel model
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
// use Maatwebsite\Excel\Concerns\SkipsOnFailure;
// use Maatwebsite\Excel\Concerns\WithValidation;
// use Maatwebsite\Excel\Validators\Failure;

// class UsersImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnFailure, WithValidation
// {
//     protected $failedRows = [];  // To store failed rows

//     /**
//      * Map each row to the model.
//      */
//     public function model(array $row)
//     {
//         // Sanitize the phone number (e.g., remove scientific notation issues)
//         $phone = is_numeric($row['phone']) ? number_format((float) $row['phone'], 0, '', '') : $row['phone'];

//         return new UserExcel([
//             'name'   => $row['name'],
//             'email'  => $row['email'],
//             'phone'  => $phone,
//             'gender' => $row['gender'],
//         ]);
//     }

//     /**
//      * Validation rules for each row.
//      */
//     public function rules(): array
//     {
//         return [
//             'name'   => 'required|string',
//             'email'  => 'required|email|unique:user_excels,email',  // Ensure email is unique in the `user_excels` table
//             'phone'  => 'required',  // Ensure phone is numeric
//             'gender' => 'required|in:M,F',  // Allow only "M" or "F" for gender
//         ];
//     }

//     /**
//      * Track failed rows during validation.
//      */
//     public function onFailure(Failure ...$failures)
//     {
//         foreach ($failures as $failure) {
//             $this->failedRows[] = [
//                 'name'   => $failure->values()['name'] ?? 'N/A',
//                 'email'  => $failure->values()['email'] ?? 'N/A',
//                 'phone'  => $failure->values()['phone'] ?? 'N/A',
//                 'gender' => $failure->values()['gender'] ?? 'N/A',
//                 'error'  => implode(', ', $failure->errors()),  // Add error details
//             ];
//         }
//     }

//     /**
//      * Retrieve failed rows with errors for reporting or download.
//      */
//     public function getFailedRows(): array
//     {
//         return $this->failedRows;
//     }

    
// }



namespace App\Imports;

use App\Models\UserExcel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Row;
class UsersImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnFailure, WithValidation,OnEachRow
{
    protected $failedRows = []; // Array to store failed rows

    /**
     * Map each row to the model.
     */
    private $totalRows = 0;
   
    public function model(array $row)
    {
        // Sanitize the phone number to avoid issues with formatting (e.g., scientific notation)
        $phone = is_numeric($row['phone']) ? number_format((float) $row['phone'], 0, '', '') : $row['phone'];

        $this->totalRows++; 
        return new UserExcel([
            'name'   => $row['name'],
            'email'  => $row['email'],
            'phone'  => $phone,
            'gender' => $row['gender'],
        ]);
    }

    /**
     * Define validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:user_excels,email', // Ensure email is unique
            'phone'  => 'required|numeric', // Ensure phone is numeric
            'gender' => 'required|in:M,F', // Gender must be either "M" or "F"
        ];
    }

    public function resetFailure(){
      $this->failedRows = [];
    }
    /**
     * Track failed rows during validation and store the details for reporting.
     */
    public function onFailure(Failure ...$failures)
    {
        $errors ="";
        foreach ($failures as $failure) {
            $record = $failure->values();
            $errors.=$failure->errors()[0]."\n";
           
                  
        }
        //dd($errors);
    
        $record['errors'] = $errors;
        $this->failedRows[$failure->row()] =$record;
    }

    /**
     * Retrieve the failed rows for reporting or download.
     */
    public function getFailedRows(): array
    {
        return $this->failedRows;
    }
    public function onRow(Row $row)
    {
       
        $this->totalRows++; 
    }
    public function getTotalRecords()
    {
       return $this->totalRows; 
    }
}
