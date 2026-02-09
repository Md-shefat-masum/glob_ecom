<?php
namespace App\Models;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscribedUsersExcel implements FromCollection, WithHeadings

{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return SubscribedUsers::select("email", "created_at")->orderBy('id', 'desc')->get();
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["EMAIL", "Created AT"];
    }

}
