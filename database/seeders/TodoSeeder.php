<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Todo;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         *  Using Factory class
         */
        Todo::factory()
            ->count(30)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['user_id'=>User::pluck('id')->random()]
            ))
            ->create();
        
        /**
         *  Without using Factory class
         */
        // $todos = ['BUY','PLAY','READ','VISIT'];
        // $items = ['Milk','EUV','1984','My GF'];
        // 
        // for ($x=0; $x < count($todos); $x++ ) {
            // DB::table('todos')->insert([
                // 'user_id'=> 1,
                // 'content'=>'I want to '.$todos[$x].' '.$items[$x],
                // 'created_at'=>Carbon::now('Asia/Jakarta')->format('Y-m-d h:i:s'),
            // ]);
            // sleep(2);
        // }                
    }
}
