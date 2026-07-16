<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrJobCategory extends Model
{
    use SoftDeletes;
	
	
	protected $guarded = [];  
	
	protected $primaryKey = 'id';
    protected $table = 'hr_job_category';



    public static $jcRule = [
        'category_name' => 'required',
    ];

 
    public static $jcMessage = [
        'category_name.required' => 'Category is required',
    ];
}
