<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Exports\FailedImportExport;  // Make sure this import is present
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\UserExcel;

class ExcelImportController extends Controller
{
    public function showImportForm()
    {
        $importedData = UserExcel::all();
        return view('import', compact('importedData'));
    }

    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,csv',
    //     ]);

    //     $file = $request->file('file');

    //     $import = new UsersImport();
    
    //     Excel::import($import, $file);
    //     $failedRows = $import->getFailedRows();
    //     $failed_row=count($failedRows);

    //     if (count($failedRows) > 0) {
    //         $failedFilePath = 'failed-import-' . time() . '.xlsx';
    //         Excel::store(new FailedImportExport($failedRows), $failedFilePath, 'public');
    
    //         return back()->with('success', 'Import completed with some errors.')
    //                      ->with('failed_file', $failedFilePath);
    //     } else {
    //         $importedData = UserExcel::all();
    //         return back()->with('success', 'Import completed successfully.')
    //                      ->with('importedData', $importedData);
    //     }
    // }
    public function import(Request $request)
    {
        // Validate the file to ensure it's an Excel file
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);
    
        // Clear old CSV or Excel files before starting the new import
        $this->clearOldCsvFiles();
    
        // Get the uploaded file
        $file = $request->file('file');
    
        // Initialize the import process
        $import = new UsersImport();
         $import->resetFailure();
        // Import the file
        Excel::import($import, $file);
        $failedRows = $import->getFailedRows();
        $totalRows = 0;
  




        $dataArray = Excel::toArray($import, $file);
        foreach ($dataArray as $sheet) {
             $totalRows += count($sheet); // Add count of each sheet's rows
        }
        $totalFaileds=count($failedRows);
        $successRows=$totalRows -$totalFaileds;
        //dd($successRows);
        if ($totalFaileds > 0) {
            $failedFilePath = 'failed-import-' . time() . '.xlsx';
            Excel::store(new FailedImportExport($failedRows), $failedFilePath, 'public');
    
            return back()
            ->with([
                'success' => 'Import completed with some errors.',
                'failed_file' => $failedFilePath,
                'successRows' => $successRows,
                'totalFaileds' => $totalFaileds,
            ]);
        
        }
        else {
          
            $importedData = UserExcel::all();
            return back()->with('success', 'Import completed successfully.')
                         ->with('importedData', $importedData);
        }
    }
    
    private function generateCsv($data, $fileName)
    {
        $directory = storage_path('app/failed_imports/');
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . $fileName;

        $file = fopen($filePath, 'w');
        fputcsv($file, ['Name', 'Email', 'Phone', 'Gender', 'Error']);

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return $filePath;
    }

    public function downloadFailedFile(Request $request)
    {
        $fileName = $request->query('file');
        $filePath = storage_path('app/failed_imports/' . $fileName);

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function clearOldCsvFiles()
    {
        $directory = storage_path('app/failed_imports/');
    
        if (file_exists($directory)) {
            $files = glob($directory . '*.csv');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}

// namespace App\Http\Controllers;

// use App\Imports\UsersImport;
// use App\Exports\FailedImportExport;
// use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Http\Request;
// use App\Models\UserExcel;
// use Illuminate\Support\Facades\Storage;

// class ExcelImportController extends Controller
// {
//     public function showImportForm()
//     {
//         $importedData = UserExcel::all();
//         return view('import', compact('importedData'));
//     }

//     public function import(Request $request)
//     {
//         // Validate the file to ensure it's an Excel file
//         $request->validate([
//             'file' => 'required|mimes:xlsx,csv',
//         ]);

//         // Clear old CSV files before starting the new import
//         $this->clearOldCsvFiles();

//         // Get the uploaded file
//         $file = $request->file('file');

//         // Initialize the import process
//         $import = new UsersImport();

//         // Import the file
//         Excel::import($import, $file);

//         // Get the failed rows (if any)
//         $failedRows = $import->getFailedRows();

//         if (count($failedRows) > 0) {
//             // Generate a unique file name for the failed rows
//             $failedFileName = 'failed-import-' . time() . '.csv';

//             // Save the failed rows to a new CSV file
//             $filePath = $this->generateCsv($failedRows, $failedFileName);
//             if (!file_exists($filePath)) {
//                 throw new \Exception("CSV file was not created at {$filePath}");
//             }

//             return back()->with('success', 'Import completed with some errors.')
//                          ->with('failed_file', $failedFileName);
//         } else {
//             $importedData = UserExcel::all();
//             return back()->with('success', 'Import completed successfully.')
//                          ->with('importedData', $importedData);
//         }
//     }

//     private function generateCsv($data, $fileName)
//     {
//         $directory = storage_path('app/failed_imports/');

//         // Create the directory if it doesn't exist
//         if (!file_exists($directory)) {
//             mkdir($directory, 0755, true);
//         }

//         $filePath = $directory . $fileName;

//         // Open the file for writing
//         $file = fopen($filePath, 'w');
//         if ($file === false) {
//             throw new \Exception("Could not create CSV file at {$filePath}");
//         }

//         // Add the header row
//         fputcsv($file, ['Name', 'Email', 'Phone', 'Gender', 'Error']);

//         // Add the data rows
//         foreach ($data as $row) {
//             fputcsv($file, $row);
//         }

//         // Close the file
//         fclose($file);

//         return $filePath;
//     }

//     public function downloadFailedFile(Request $request)
//     {
//         $fileName = $request->query('file');
//         $filePath = storage_path('app/failed_imports/' . $fileName);

//         // Ensure the file exists
//         if (!file_exists($filePath)) {
//             return response()->json(['message' => 'File not found'], 404);
//         }

//         return response()->download($filePath);
//     }

//     private function clearOldCsvFiles()
//     {
//         $directory = storage_path('app/failed_imports/');

//         if (file_exists($directory)) {
//             $files = glob($directory . '*.csv');
//             foreach ($files as $file) {
//                 if (is_file($file)) {
//                     unlink($file);
//                 }
//             }
//         }
//     }
// }
