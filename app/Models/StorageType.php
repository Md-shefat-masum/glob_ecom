<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageType extends Model
{
    use HasFactory;
    public static function getDropDownList($fieldName, $id=NULL){
        $str = "<option value=''>Select One</option>";
        $lists = self::orderBy($fieldName,'asc')->where('status', 1)->get();
        if($lists){
            foreach($lists as $list){
                if($id !=NULL && $id == $list->id){
                    $str .= "<option value='".$list->id."' selected>".$list->$fieldName."/".$list->rom."</option>";
                }else{
                    $str .= "<option value='".$list->id."'>".$list->$fieldName."/".$list->rom."</option>";
                }

            }
        }
        return $str;
    }
}
