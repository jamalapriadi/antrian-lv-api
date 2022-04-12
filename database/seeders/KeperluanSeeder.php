<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keperluan;
use App\Models\Receptionist;

class KeperluanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Keperluan::create(['nama'=>'Posbakum (Pos Bantuan Hukum)','created_at'=>date('Y-m-d H:i:s'),'no_urut'=>1]);
        Keperluan::create(['nama'=>'Informasi','created_at'=>date('Y-m-d H:i:s'),'no_urut'=>2]);
        Keperluan::create(['nama'=>'Pendaftaran','created_at'=>date('Y-m-d H:i:s'),'no_urut'=>3]);
        Keperluan::create(['nama'=>'Pengambilan Produk','created_at'=>date('Y-m-d H:i:s'),'no_urut'=>4]);
        Keperluan::create(['nama'=>'Pengaduan','created_at'=>date('Y-m-d H:i:s'),'no_urut'=>5]);

        Receptionist::create(['nama'=>'Loket 1']);
        Receptionist::create(['nama'=>'Loket 2']);
        Receptionist::create(['nama'=>'Loket 3']);
    }
}
