<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'code',
        'created_at',
        'updated_at'
    ];

    public static function getDropDownList($fieldName, $id=NULL){
        $str = "<option value=''>Select One</option>";
        $lists = self::orderBy($fieldName,'asc')->get();
        if($lists){
            foreach($lists as $list){
                if($id !=NULL && $id == $list->id){
                    $str .= "<option value='".$list->id."' selected>".$list->$fieldName."</option>";
                }else{
                    $str .= "<option value='".$list->id."'>".$list->$fieldName."</option>";
                }

            }
        }
        return $str;
    }
}
