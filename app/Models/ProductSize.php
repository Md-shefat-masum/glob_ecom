<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    protected $guarded = [];
    public static function getDropDownList($fieldName, $id=NULL){
        $str = "<option value=''>Select Size</option>";
        $lists = self::orderBy('serial', 'asc')->get();
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
