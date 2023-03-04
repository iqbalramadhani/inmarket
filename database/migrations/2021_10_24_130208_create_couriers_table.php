<?php

use App\Models\Courier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouriersTable extends Migration
{
    public function up()
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $data = [
            [
                'code' => 'pos',
                'name' => 'POS Indonesia (POS)',
            ],
            [
                'code' => 'tiki',
                'name' => 'Citra Van Titipan Kilat (TIKI)'
            ],
            [
                'code' => 'jne',
                'name' => 'Jalur Nugraha Ekakurir (JNE)'
            ],
            [
                'code' => 'pcp',
                'name' => 'PCP'
            ],
            [
                'code' => 'esl',
                'name' => 'ESL'
            ],
            [
                'code' => 'rpx',
                'name' => 'RPX Holding (RPX)'
            ],
            [
                'code' => 'pandu',
                'name' => 'Pandu Logistics (PANDU)'
            ],
            [
                'code' => 'wahana',
                'name' => 'Wahana Prestasi Logistik (WAHANA)'
            ],
            [
                'code' => 'jnt',
                'name' => 'J&T Express (J&T)'
            ],
            [
                'code' => 'pahala',
                'name' => 'Pahala Kencana Express (PAHALA)'
            ],
            [
                'code' => 'cahaya',
                'name' => 'CAHAYA'
            ],
            [
                'code' => 'sap',
                'name' => 'SAP Express (SAP)'
            ],
            [
                'code' => 'indah',
                'name' => 'INDAH'
            ],
            [
                'code' => 'dse',
                'name' => '21 Express (DSE)'
            ],
            [
                'code' => 'slis',
                'name' => 'Solusi Ekspres (SLIS)'
            ],
            [
                'code' => 'first',
                'name' => 'First Logistics (FIRST)'
            ],
            [
                'code' => 'ncs',
                'name' => 'Nusantara Card Semesta (NCS)'
            ],
            [
                'code' => 'star',
                'name' => 'Star Cargo (STAR)'
            ],
            [
                'code' => 'anteraja',
                'name' => 'Anteraja'
            ]
        ];
        Courier::insert($data);
    }

    public function down()
    {
        Schema::dropIfExists('couriers');
    }
}