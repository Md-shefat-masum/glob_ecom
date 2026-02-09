<?php
namespace App\Models;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExcel implements FromCollection, WithHeadings

{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return User::select("name", "email", "phone", "address", "created_at")->where('user_type', 3)->orderBy('id', 'desc')->get();
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["NAME", "EMAIL", "PHONE", "ADDRESS", "Created AT"];
    }

}
