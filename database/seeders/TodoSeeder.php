<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todos = ['BUY','PLAY','READ','VISIT'];
        $items = ['Milk','EUV','1984','My GF'];
        
        for ($x=0; $x < count($todos); $x++ ) {
            DB::table('todos')->insert([
                'user_id'=> 1,
                'content'=>'I want to '.$todos[$x].' '.$items[$x],
                'created_at'=>Carbon::now('Asia/Jakarta')->format('Y-m-d h:i:s'),
            ]);
            sleep(2);
        }
    }
}
