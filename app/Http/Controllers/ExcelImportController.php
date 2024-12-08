<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\UserExcel;
use App\Exports\FailedImportExport;

class ExcelImportController extends Controller
{
    public function showImportForm()
    {
        $importedData = UserExcel::all(); 
        //dd($importedData);
        return view('import', compact('importedData'));
    }

    public function import(Request $request)
    {
        // Validate the file to ensure it's an Excel file
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);
    
        // Get the uploaded file
        $file = $request->file('file');
    
        // Initialize the import process
        $import = new UsersImport();
    
        // Import the file
        Excel::import($import, $file);
    
        // Get the failed rows (if any)
        $failedRows = $import->getFailedRows();
    
        if (count($failedRows) > 0) {
            // Save failed rows to a new Excel file
            $failedFilePath = 'failed-import-' . time() . '.xlsx';
            Excel::store(new FailedImportExport($failedRows), $failedFilePath, 'public');
    
            // Return the success message and the failed file path to the view
            return back()->with('success', 'Import completed with some errors.')
                         ->with('failed_file', $failedFilePath);
        }else{
   // If no failed rows, return success message and fetch the imported data
   $importedData = UserExcel::all(); // Adjust this to fetch the data after import
        
   return back()->with('success', 'Import completed successfully.')
                ->with('importedData', $importedData);
        }

           
      
    }

    public function exportFailedRows($failedRows)
    {
        // Define the file name and path to save the failed rows
        $fileName = 'failed_imports.xlsx';
        $filePath = storage_path('app/public/' . $fileName);

        // Store the failed rows in the Excel file
        Excel::store(new FailedImportExport($failedRows), $filePath);

        // Return the file as a downloadable response
        return response()->download($filePath);
    }
}
