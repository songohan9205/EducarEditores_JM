<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class nuevos_empleados extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('empleado')->insert([
            [
                'cargo_id'         => 5,
                'documento'        => '1234567890',
                'primer_nombre'    => 'Armando',
                'segundo_nombre'   => '',
                'primer_apellido'  => 'Rodríguez',
                'segundo_apellido' => 'Cortéz',
                'saldo'            => 0,
                'created_at'       => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'       => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'cargo_id'         => 1,
                'documento'        => '87451250',
                'primer_nombre'    => 'Luisa',
                'segundo_nombre'   => 'Valentina',
                'primer_apellido'  => 'Acosta',
                'segundo_apellido' => 'Rocha',
                'saldo'            => 0,
                'created_at'       => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'       => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'cargo_id'         => 3,
                'documento'        => '79874414',
                'primer_nombre'    => 'Javier',
                'segundo_nombre'   => 'Alberto',
                'primer_apellido'  => 'Mendoza',
                'segundo_apellido' => 'Pérez',
                'saldo'            => 0,
                'created_at'       => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'       => Carbon::now()->format('Y-m-d H:i:s')
            ],            
        ]);
    }
}
