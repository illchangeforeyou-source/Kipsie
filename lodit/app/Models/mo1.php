<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class mo1 extends Model
{
   public function tampil($table)
{
    if ($table === 'dok') {
        return $this->bbll('dok', 'notsuspicious', 'dok.id', 'notsuspicious.ID');
    }

    return DB::table($table)->get();
}

      function ya($table,$where){
   	return DB::table($table)->where($where)->first();
	}
 
    function tambah($table,$data){
        return DB::table($table)->insert($data);
    }

    function edit($table,$data,$where){
        return DB::table($table)->where($where)->update($data);
    }

    function hapus($table,$where){
        return DB::table($table)->where($where)->delete();
    }
    public function bbll($dok, $notsuspicious, $id, $ID) {
    return DB::table($dok)
             ->join($notsuspicious, $id, '=', $ID)
             ->get();
}
    public function insert($table, $data)
{
    return DB::table($table)->insert($data);
}


}


