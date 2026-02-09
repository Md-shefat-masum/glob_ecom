<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use ZipArchive;
use File;

class BackupController extends Controller
{
    public function downloadDBBackup(){

        $tables = array();
        $allTables = DB::select('SHOW TABLES');
        foreach ($allTables as $table) {
            foreach ($table as $key => $value)
                $tables[] = $value;
        }

        $mysqlHostName      = env('DB_HOST');
        $mysqlUserName      = env('DB_USERNAME');
        $mysqlPassword      = env('DB_PASSWORD');
        $DbName             = env('DB_DATABASE');

        $connect = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword",array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $get_all_table_query = "SHOW TABLES";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $result = $statement->fetchAll();

        $output = '';
        foreach($tables as $table)
        {
            $show_table_query = "SHOW CREATE TABLE " . $table . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();

            foreach($show_table_result as $show_table_row)
            {
                $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
            }

            $select_query = "SELECT * FROM " . $table . "";
            $statement = $connect->prepare($select_query);
            $statement->execute();
            $total_row = $statement->rowCount();

            for($count=0; $count<$total_row; $count++)
            {
                $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                $table_column_array = array_keys($single_result);
                $table_value_array = array_values($single_result);
                $output .= "\nINSERT INTO $table (";
                $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                $output .= "'" . implode("','", $table_value_array) . "');\n";
            }
        }

        $file_name = 'database_backup.sql';
        $file_handle = fopen(public_path($file_name), 'w+');
        fwrite($file_handle, $output);
        fclose($file_handle);
        return response()->download(public_path($file_name));
        // header('Content-Description: File Transfer');
        // header('Content-Type: application/octet-stream');
        // header('Content-Disposition: attachment; filename=' . basename($file_name));
        // header('Content-Transfer-Encoding: binary');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate');
        // header('Pragma: public');
        // header('Content-Length: ' . filesize($file_name));
        // ob_clean();
        // flush();
        // readfile($file_name);
        // unlink($file_name);
    }

    public function downloadProductFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'ProductImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('productImages'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // return response()->download(public_path($fileName));
        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Product Images Found', 'Success');
            return back();
        }
    }

    public function downloadUserFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'UserImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('userProfileImages'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // return response()->download(public_path($fileName));
        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No User Images Found', 'Success');
            return back();
        }
    }

    public function downloadBannerFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'BannerImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('banner'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // return response()->download(public_path($fileName));
        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Banner Images Found', 'Success');
            return back();
        }
    }

    public function downloadCategoryFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'CategoryImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('category_images'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // return response()->download(public_path($fileName));
        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Category Images Found', 'Success');
            return back();
        }
    }

    public function downloadSubcategoryFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'SubcategoryImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('subcategory_images'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        // return response()->download(public_path($fileName));
        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Subcategory Images Found', 'Success');
            return back();
        }
    }

    public function downloadTicketFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'TicketFilesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('support_ticket_attachments'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Ticket Attachments Found', 'Success');
            return back();
        }
    }

    public function downloadBlogFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'BlogFilesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('blogImages'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Blog Images Found', 'Success');
            return back();
        }
    }

    public function downloadOtherFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'OtherImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('images'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Other Images Found', 'Success');
            return back();
        }
    }

    public function downloadFlagFilesBackup(){
        $zip = new ZipArchive;
        $fileName = 'FlagImagesBackup.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(public_path('flag_icons'));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

        if(file_exists(public_path($fileName))){
            return response()->download(public_path($fileName));
        } else {
            Toastr::error('No Flag Icons Found', 'Success');
            return back();
        }

        // $storagePath = storage_path('app');
        // $zipFileName = 'storage.zip';
        // $zipFilePath = storage_path($zipFileName);

        // // Create a new zip archive
        // $zip = new ZipArchive;
        // if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        //     // Add all files and directories in the storage folder to the zip archive
        //     $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storagePath));
        //     foreach ($files as $file) {
        //         if (!$file->isDir()) {
        //             $filePath = $file->getRealPath();
        //             $relativePath = 'storage/' . substr($filePath, strlen($storagePath) + 1);
        //             $zip->addFile($filePath, $relativePath);
        //         }
        //     }

        //     // Close the zip archive
        //     $zip->close();

        //     // Set appropriate headers for the download
        //     $headers = [
        //         'Content-Type' => 'application/octet-stream',
        //         'Content-Disposition' => 'attachment; filename=' . $zipFileName,
        //     ];

        //     // Return the zip file as a response
        //     return response()->download($zipFilePath, $zipFileName, $headers);
        // }
        // return back()->with('error', 'Failed to create the zip archive.');
    }
}
