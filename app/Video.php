<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded =[];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function getRandomVideo($category_name = null)
    {
        if($category_name) {
            $category = Category::where('name', $category_name)->first();
            $video = self::where('category_id', $category->id)->get()->random();
        } else {
            $video = self::all()->random();
        }

        return $video;
    }

    public static function findVideo($name)
    {
        $video = self::where('name', 'like', '%' . mb_strtolower($name) . '%')->get();
        if(count($video) > 0){
            return $video->random();
        } else {
            return false;
        }

    }
}
