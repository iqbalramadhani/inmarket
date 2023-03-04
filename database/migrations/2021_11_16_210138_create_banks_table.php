<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
        });
        \DB::table('banks')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Bank BRI',
                    'code' => '002',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'Bank Mandiri',
                    'code' => '008',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'Bank BNI',
                    'code' => '009',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'Bank Danamon',
                    'code' => '011',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'Bank Permata',
                    'code' => '013',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            5 =>
                array (
                    'id' => 6,
                    'name' => 'Bank Permata Syariah',
                    'code' => '013',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            6 =>
                array (
                    'id' => 7,
                    'name' => 'Bank BCA',
                    'code' => '014',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            7 =>
                array (
                    'id' => 8,
                    'name' => 'BII Maybank',
                    'code' => '016',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            8 =>
                array (
                    'id' => 9,
                    'name' => 'Maybank Syariah',
                    'code' => '016',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            9 =>
                array (
                    'id' => 10,
                    'name' => 'Bank Panin',
                    'code' => '019',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            10 =>
                array (
                    'id' => 11,
                    'name' => 'CIMB Niaga',
                    'code' => '022',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            11 =>
                array (
                    'id' => 12,
                    'name' => 'Bank UOB INDONESIA',
                    'code' => '023',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            12 =>
                array (
                    'id' => 13,
                    'name' => 'Bank OCBC NISP',
                    'code' => '028',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            13 =>
                array (
                    'id' => 14,
                    'name' => 'CITIBANK',
                    'code' => '031',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            14 =>
                array (
                    'id' => 15,
                    'name' => 'Bank Windu Kentjana International',
                    'code' => '036',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            15 =>
                array (
                    'id' => 16,
                    'name' => 'Bank ARTHA GRAHA',
                    'code' => '037',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            16 =>
                array (
                    'id' => 17,
                    'name' => 'Bank TOKYO MITSUBISHI UFJ',
                    'code' => '042',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            17 =>
                array (
                    'id' => 18,
                    'name' => 'Bank DBS',
                    'code' => '046',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            18 =>
                array (
                    'id' => 19,
                    'name' => 'Standard Chartered',
                    'code' => '050',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            19 =>
                array (
                    'id' => 20,
                    'name' => 'Bank CAPITAL',
                    'code' => '054',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            20 =>
                array (
                    'id' => 21,
                    'name' => 'ANZ Indonesia',
                    'code' => '061',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            21 =>
                array (
                    'id' => 22,
                    'name' => 'Bank OF CHINA',
                    'code' => '069',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            22 =>
                array (
                    'id' => 23,
                    'name' => 'Bank Bumi Arta',
                    'code' => '076',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            23 =>
                array (
                    'id' => 24,
                    'name' => 'Bank HSBC',
                    'code' => '087',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            24 =>
                array (
                    'id' => 25,
                    'name' => 'Bank Antardaerah',
                    'code' => '088',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            25 =>
                array (
                    'id' => 26,
                    'name' => 'Bank Rabobank',
                    'code' => '089',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            26 =>
                array (
                    'id' => 27,
                    'name' => 'Bank JTRUST INDONESIA',
                    'code' => '095',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            27 =>
                array (
                    'id' => 28,
                    'name' => 'Bank MAYAPADA',
                    'code' => '097',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            28 =>
                array (
                    'id' => 29,
                    'name' => 'Bank Jawa Barat',
                    'code' => '110',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            29 =>
                array (
                    'id' => 30,
                    'name' => 'Bank DKI',
                    'code' => '111',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            30 =>
                array (
                    'id' => 31,
                    'name' => 'Bank BPD DIY',
                    'code' => '112',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            31 =>
                array (
                    'id' => 32,
                    'name' => 'Bank JATENG',
                    'code' => '113',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            32 =>
                array (
                    'id' => 33,
                    'name' => 'Bank Jatim',
                    'code' => '114',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            33 =>
                array (
                    'id' => 34,
                    'name' => 'Bank Jambi',
                    'code' => '115',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            34 =>
                array (
                    'id' => 35,
                    'name' => 'Bank Jambi Syariah',
                    'code' => '115',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            35 =>
                array (
                    'id' => 36,
                    'name' => 'Bank Aceh',
                    'code' => '116',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            36 =>
                array (
                    'id' => 37,
                    'name' => 'Bank Aceh Syariah',
                    'code' => '116',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            37 =>
                array (
                    'id' => 38,
                    'name' => 'Bank SUMUT',
                    'code' => '117',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            38 =>
                array (
                    'id' => 39,
                    'name' => 'Bank NAGARI',
                    'code' => '118',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            39 =>
                array (
                    'id' => 40,
                    'name' => 'Bank Riau',
                    'code' => '119',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            40 =>
                array (
                    'id' => 41,
                    'name' => 'Bank Riau Syariah',
                    'code' => '119',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            41 =>
                array (
                    'id' => 42,
                    'name' => 'Bank SUMSEL BABEL',
                    'code' => '120',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            42 =>
                array (
                    'id' => 43,
                    'name' => 'Bank SUMSEL BABEL Syariah',
                    'code' => '120',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            43 =>
                array (
                    'id' => 44,
                    'name' => 'Bank Lampung',
                    'code' => '121',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            44 =>
                array (
                    'id' => 45,
                    'name' => 'Bank KALSEL',
                    'code' => '122',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            45 =>
                array (
                    'id' => 46,
                    'name' => 'Bank KALBAR',
                    'code' => '123',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            46 =>
                array (
                    'id' => 47,
                    'name' => 'Bank BPD Kaltim',
                    'code' => '124',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            47 =>
                array (
                    'id' => 48,
                    'name' => 'Bank BPD Kalteng',
                    'code' => '125',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            48 =>
                array (
                    'id' => 49,
                    'name' => 'Bank SULSELBAR',
                    'code' => '126',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            49 =>
                array (
                    'id' => 50,
                    'name' => 'Bank Sulut',
                    'code' => '127',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            50 =>
                array (
                    'id' => 51,
                    'name' => 'Bank NTB',
                    'code' => '128',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            51 =>
                array (
                    'id' => 52,
                    'name' => 'Bank NTB Syariah',
                    'code' => '128',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            52 =>
                array (
                    'id' => 53,
                    'name' => 'Bank BPD Bali',
                    'code' => '129',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            53 =>
                array (
                    'id' => 54,
                    'name' => 'Bank NTT',
                    'code' => '130',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            54 =>
                array (
                    'id' => 55,
                    'name' => 'Bank Maluku',
                    'code' => '131',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            55 =>
                array (
                    'id' => 56,
                    'name' => 'Bank BPD Papua',
                    'code' => '132',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            56 =>
                array (
                    'id' => 57,
                    'name' => 'Bank SULTENG',
                    'code' => '134',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            57 =>
                array (
                    'id' => 58,
                    'name' => 'Bank Sultra',
                    'code' => '135',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            58 =>
                array (
                    'id' => 59,
                    'name' => 'Bank BANTEN',
                    'code' => '137',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            59 =>
                array (
                    'id' => 60,
                    'name' => 'Bank Nusantara Parahyangan',
                    'code' => '145',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            60 =>
                array (
                    'id' => 61,
                    'name' => 'Bank Of India Indonesia',
                    'code' => '146',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            61 =>
                array (
                    'id' => 62,
                    'name' => 'Bank Muamalat',
                    'code' => '147',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            62 =>
                array (
                    'id' => 63,
                    'name' => 'Bank Mestika',
                    'code' => '151',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            63 =>
                array (
                    'id' => 64,
                    'name' => 'Bank SHINHAN',
                    'code' => '152',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            64 =>
                array (
                    'id' => 65,
                    'name' => 'Bank Sinarmas',
                    'code' => '153',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            65 =>
                array (
                    'id' => 66,
                    'name' => 'Bank Maspion',
                    'code' => '157',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            66 =>
                array (
                    'id' => 67,
                    'name' => 'Bank Ganesha',
                    'code' => '161',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            67 =>
                array (
                    'id' => 68,
                    'name' => 'Bank ICBC',
                    'code' => '164',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            68 =>
                array (
                    'id' => 69,
                    'name' => 'Bank QNB indonesia',
                    'code' => '167',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            69 =>
                array (
                    'id' => 70,
                    'name' => 'Bank BTN',
                    'code' => '200',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            70 =>
                array (
                    'id' => 71,
                    'name' => 'Bank Woori Saudara',
                    'code' => '212',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            71 =>
                array (
                    'id' => 72,
                    'name' => 'Bank BTPN',
                    'code' => '213',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            72 =>
                array (
                    'id' => 73,
                    'name' => 'Bank Victoria Syariah',
                    'code' => '405',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            73 =>
                array (
                    'id' => 74,
                    'name' => 'Bank Jabar Banten Syariah',
                    'code' => '425',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            74 =>
                array (
                    'id' => 75,
                    'name' => 'Bank Mega',
                    'code' => '426',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            75 =>
                array (
                    'id' => 76,
                    'name' => 'Bank Bukopin',
                    'code' => '441',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            76 =>
                array (
                    'id' => 77,
                    'name' => 'Bank Syariah Indonesia',
                    'code' => '451',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            77 =>
                array (
                    'id' => 78,
                    'name' => 'Bank Jasa Jakarta',
                    'code' => '472',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            78 =>
                array (
                    'id' => 79,
                    'name' => 'Bank KEB HANA',
                    'code' => '484',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            79 =>
                array (
                    'id' => 80,
                    'name' => 'Bank MNC INTERNATIONAL',
                    'code' => '485',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            80 =>
                array (
                    'id' => 81,
                    'name' => 'Bank YUDHA BHAKTI/ Bank Neo Commerce',
                    'code' => '490',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            81 =>
                array (
                    'id' => 82,
                    'name' => 'Bank Rakyat Indonesia AGRONIAGA',
                    'code' => '494',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            82 =>
                array (
                    'id' => 83,
                    'name' => 'Bank SBI Indonesia (Indomonex)',
                    'code' => '498',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            83 =>
                array (
                    'id' => 84,
                    'name' => 'Bank Royal',
                    'code' => '501',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            84 =>
                array (
                    'id' => 85,
                    'name' => 'Bank National Nobu',
                    'code' => '503',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            85 =>
                array (
                    'id' => 86,
                    'name' => 'Bank MEGA SYARIAH',
                    'code' => '506',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            86 =>
                array (
                    'id' => 87,
                    'name' => 'Bank INA',
                    'code' => '513',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            87 =>
                array (
                    'id' => 88,
                    'name' => 'Bank PANIN SYARIAH',
                    'code' => '517',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            88 =>
                array (
                    'id' => 89,
                    'name' => 'PRIMA MASTER BANK',
                    'code' => '520',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            89 =>
                array (
                    'id' => 90,
                    'name' => 'Bank SYARIAH BUKOPIN',
                    'code' => '521',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            90 =>
                array (
                    'id' => 91,
                    'name' => 'Bank Sahabat Sampoerna',
                    'code' => '523',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            91 =>
                array (
                    'id' => 92,
                    'name' => 'Bank DINAR',
                    'code' => '526',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            92 =>
                array (
                    'id' => 93,
                    'name' => 'Bank KESEJAHTERAAN EKONOMI',
                    'code' => '535',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            93 =>
                array (
                    'id' => 94,
                    'name' => 'Bank BCA SYARIAH',
                    'code' => '536',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            94 =>
                array (
                    'id' => 95,
                    'name' => 'Bank ARTOS/ Bank Jago',
                    'code' => '542',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            95 =>
                array (
                    'id' => 96,
                    'name' => 'Bank BTPN SYARIAH',
                    'code' => '547',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            96 =>
                array (
                    'id' => 97,
                    'name' => 'Bank MULTIARTA SENTOSA',
                    'code' => '548',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            97 =>
                array (
                    'id' => 98,
                    'name' => 'Bank Mayora',
                    'code' => '553',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            98 =>
                array (
                    'id' => 99,
                    'name' => 'Bank INDEX',
                    'code' => '555',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            99 =>
                array (
                    'id' => 100,
                    'name' => 'CNB',
                    'code' => '559',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            100 =>
                array (
                    'id' => 101,
                    'name' => 'Bank MANTAP',
                    'code' => '564',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            101 =>
                array (
                    'id' => 102,
                    'name' => 'Bank VICTORIA INTL',
                    'code' => '566',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            102 =>
                array (
                    'id' => 103,
                    'name' => 'HARDA',
                    'code' => '567',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            103 =>
                array (
                    'id' => 104,
                    'name' => 'BPR KS',
                    'code' => '688',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            104 =>
                array (
                    'id' => 105,
                    'name' => 'IBK',
                    'code' => '945',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            105 =>
                array (
                    'id' => 106,
                    'name' => 'Bank CTBC Indonesia',
                    'code' => '949',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            106 =>
                array (
                    'id' => 107,
                    'name' => 'Bank COMMONWEALTH',
                    'code' => '950',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            107 =>
                array (
                    'id' => 108,
                    'name' => 'OVO',
                    'code' => 'ovo',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            108 =>
                array (
                    'id' => 109,
                    'name' => 'LinkAja',
                    'code' => 'linkaja',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            109 =>
                array (
                    'id' => 110,
                    'name' => 'Dana',
                    'code' => 'dana',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            110 =>
                array (
                    'id' => 111,
                    'name' => 'Gopay',
                    'code' => 'gopay',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
        ));
    }

    public function down()
    {
        Schema::dropIfExists('banks');
    }
}