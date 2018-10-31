<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('admins')->insert([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '842750985',
            'password' => '$2y$10$jc9c050SQrQFQ.eTijFZB.mJ2sjAr2L01vr/5MrMBOyVPWuhvsk.q',
            'image' => '',
            'remember_token' => 'QkKZHzmsFw6qQrT6dhALa9owGn1uI1k9ZWj2e4SVHaCfHHwrpbu7gBCQErfU',
            'created_at' => '2018-06-27 10:36:26',
            'updated_at' => '2018-06-27 10:36:26'
        ]);
    }
}
