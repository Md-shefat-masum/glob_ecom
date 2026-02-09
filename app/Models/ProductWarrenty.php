<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWarrenty extends Model
{
    use HasFactory;
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
