<?php 
    /*
    |--------------------------------------------------------------------------
    | Default Vars
    |--------------------------------------------------------------------------
    |
    | variabel ini dibuat untuk kebutuhan aplikasi
    | variable data yang sudah di define disini akan menjadi standar data yang digunakan di aplikasi
    | kamu bisa menambahkan data-data lain untuk kebutuhan aplikasi
    |
    | 
    |
    */

return [
    'distribution_type' =>['CROSS SELLING','AGENCY','BROKER ASURANSI','CO INSURANCE','BANCASSURANCE','DIRECT MARKETING'],
    'line_bussines' =>['DWIGUNA','JANGKAWARSA','EKAWARSA','KECELAKAAN'],
    'reason_reject' => ['Decline, Usia masuk diluar ketentuan polis',
                         'Decline, Usia masuk ditambah masa asuransi diluar ketentuan polis',
                         'Decline, Masa asuransi diluar ketentuan polis',
                         'Decline, Mengalami obesitas grade I / II / III ; menderita penyakit .....',
                         'Decline, Masa asuransi telah berakhir (mature)',
                         'Postpone, Masa asuransi belum dimulai',
                         'Postpone, Mohon konfirmasinya mengenai Tgl. Lahir / Tgl. Mulai / Tgl. Akhir',
                         'Postpone, Mohon melengkapi SPK / Medis A B C D E',
                         'Postpone, Sampai dengan hasil MCU Medis A B C D E diterima',
                         'Postpone, sesuai UA akumulasi dengan No. Peserta .... UA ...., mohon melengkapi SPK / Medis A B C D E',
                         'Postpone, Sampai dengan persetujuan Extra Mortalita diterima karena ...',
                         'Postpone, Mohon konfirmasinya mengenai perbedaan data peserta excel dengan SPK',
                         'Postpone, Mohon melengkapi jawaban deklarasi no .... pada SPK',
                         'Peserta telah diakseptasi dengan No. Peserta ....',
                         'Postpone, double data',
                         'Postpone, menunggu jawaban pertanyaan tambahan penyakit ....',
                         'Postpone, mohon melengkapi TB dan BB pada SPK',
                         'Postpone, sampai dengan 40 hari setelah melahirkan',
                         'Postpone, mohon melengkapi kelengkapan dokumen Medis A B C D E yaitu ....',
                         'Decline, nilai Uang Asuransi diluar ketentuan Polis',
                         'Postpone, mohon konfirmasinya mengenai perbedaan rate',
                         'Postpone, mohon melengkapi Tanda Tangan peserta pada SPK'
                         ]
];