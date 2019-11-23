<?php

namespace App;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use SpatialTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city', 'zip_code', 'street', 'house_number', 'coordinates'
    ];

    private $spatialFields = ['coordinates'];

    public $timestamps = false;

    public function toArray()
    {
        $array =  parent::toArray();
        $array['lat'] = $this->coordinates->getLat();
        $array['lng'] = $this->coordinates->getLng();

        return $array;
    }
}
