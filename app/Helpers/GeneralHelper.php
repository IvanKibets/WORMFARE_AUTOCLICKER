<?php
use App\Models\User;
use App\Models\UserOtp;


function toFixed($number, $decimals) {
    return number_format($number, $decimals, '.', "");
}

function inRange($x, $min, $max) {
    return (($x-$min)*($x-$max) <= 0);
}

function terbilang($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
        $temp = terbilang($nilai - 10). " Belas";
    } else if ($nilai < 100) {
        $temp = terbilang($nilai/10)." Puluh". terbilang($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " Seratus" . terbilang($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = terbilang($nilai/100) . " Ratus" . terbilang($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " Seribu" . terbilang($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = terbilang($nilai/1000) . " Ribu" . terbilang($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = terbilang($nilai/1000000) . " Juta" . terbilang($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = terbilang($nilai/1000000000) . " Milyar" . terbilang(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = terbilang($nilai/1000000000000) . " Trilyun" . terbilang(fmod($nilai,1000000000000));
    }     
    return $temp;
}
function numberToRomawi($number)
{
    $solution = '';
    $lookup = [
        1000 => 'M',
        900 => 'CM',
        500 => 'D',
        400 => 'CD',
        100 => 'C',
        90 => 'XC',
        50 => 'L',
        40 => 'XL',
        10 => 'X',
        9 => 'IX',
        5 => 'V',
        4 => 'IV',
        1 => 'I',
    ];

    foreach($lookup as $limit => $glyph){
        while ($number >= $limit) {
            $solution .= $glyph;
            $number -= $limit;
        }
    }

    return $solution;
}

function countDay($now,$end)
{
    $now = strtotime($now); // or your date as well
    $your_date = strtotime($end);
    $datediff = $now - $your_date;

    return round($datediff / (60 * 60 * 24));
} 

function hitung_masa($start,$end){
    $birthDate = new \DateTime($start);
	$today = new \DateTime($end);
	if ($birthDate > $today) { 
	    return 0;
    }
    $tahun = $today->diff($birthDate)->y;

    return $tahun;
}

function hitung_masa_bulan($start,$end,$masa_asuransi=1){
    $birthDate = \DateTime::createFromFormat('Y-m-d', $start );
	$today = \DateTime::createFromFormat('Y-m-d', $end );

    $tahun = $today->diff($birthDate)->y;

    $bulan = $today->diff($birthDate)->m;
    if($tahun>0) $bulan += $tahun*12;
    
    $hari = $today->diff($birthDate)->d;

    if($masa_asuransi==2) $today->modify('+1 day');

    if($hari >0)
        return $bulan + 1;
    else
        return $bulan;
}

function hitung_umur($tanggal_lahir,$pembulatan=1,$today=''){
    $birthDate = new \DateTime($tanggal_lahir);
    
    if($today)
	    $today = new \DateTime($today);
    else
	    $today = new \DateTime("today");

	if ($birthDate > $today) { 
	    return 0;
    }
    $tahun = $today->diff($birthDate)->y;

    if($pembulatan==1) { // Nears birthday
        if($today->diff($birthDate)->m > 6)
            $tahun++;
        elseif($today->diff($birthDate)->m == 6 and $today->diff($birthDate)->d>0)
            $tahun++;
    }
    if($pembulatan==2 and $today->diff($birthDate)->m > 12) $tahun++; // Actual Birthday
    
    if($pembulatan==3) return $today->diff($birthDate)->y .' Tahun '. $today->diff($birthDate)->m .' Bulan '. $today->diff($birthDate)->d.' Hari';
    if($pembulatan==4) return $today->diff($birthDate)->days;

    return $tahun;
}

function gl_number($item)
{
    return $item->general_ledger_number .($item->revisi!=0 ? " - R{$item->revisi}" : "");
}

function general_ledger_number()
{
    $count = \App\Models\GeneralLedger::count()+1;
    
    return 'GL-'.str_pad($count,6, '0', STR_PAD_LEFT).'/'.date('m').'/'.date('Y');
}

function get_coa($code,$field="id")
{
    $coa = \App\Models\Coa::where('code', $code)->first();
    return $coa?$coa->$field : '';
}

/**
 * Replace first string
 */
function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}
function genereate_otp()
{
    $user = User::where(['user_access_id'=>\Auth::user()->user_access_id,'is_supervisor'=>1])->first();
    if($user){
        $otp = mt_rand(100000,999999);
        $data = new UserOtp();
        $data->user_id = $user->id;
        $data->otp = $otp;
        $data->expired = date('Y-m-d H:i:s',strtotime("+8 hour"));
        $data->status = 0;
        $data->request_user_id = \Auth::user()->id;
        $data->save();

        return ['otp'=>$data->otp,'expired'=>date('d M Y H:i',strtotime($data->expired))];
    }
    return;
}
function get_supervisor($field='telepon')
{
    $user = User::where(['user_access_id'=>\Auth::user()->user_access_id,'is_supervisor'=>1])->first();
    if($user) return $user->$field;
}

function send_wa($param)
{
    $message = strip_tags($param['message']) ."\n\n_Do not reply to this message, as it is generated by system._";
    $message = $message;
    $number = str_replace_first('0','62', $param['phone']);
    $number = str_replace('-', '', $number);
    
    $curl = curl_init(); 
    $token = "HioVXgQTselUx6alx9GmtfcJgpySCDnH3FCZh2tARb0C7vRtQon5shmOwx0KmGl1";
    $data = [
        'phone' => $number,
        'message' => $message,
    ];

    curl_setopt($curl, CURLOPT_HTTPHEADER,
        array(
            "Authorization: ". $token,
        )
    );
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_URL, "https://console.wablas.com/api/send-message");
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($curl);
    curl_close($curl);
}

function flag($item)
{
    $flag = " <span class=\"badge badge-info\" title=\"".($item->type==1?'Konven':'Syariah').".\">".($item->type==1?'K':'S')."</span>";

    return $flag;
}

function no_voucher($item)
{
    $flag = " <span class=\"badge badge-info\" title=\"".($item->type==1?'Konven':'Syariah').".\">".($item->type==1?'K':'S')."</span>";

    return $item->no_voucher.$flag;
}

function status_account_balance($status)
{
    switch($status){
        case 1:
            return 'Inhouse Transfer';
        break;
        case 2:
            return "Open Balance";
        break;
        case 3:
            return "Investasi";
        break;
        case 4:
            return "Premium Receivable";
        break;
        case 5:
            return "Reinsurance Premium";
        break;
        case 6:
            return "Claim Payable";
        break;
        case 7:
            return "Refund";
        break;
        case 8:
            return "Cancelation";
        break;
        case 9:
            return "Endorsement Reas";
        break;
    }
}

function calculate_aging($date,$end_date)
{
    $start_date = new \DateTime($date);
    $today = new \DateTime($end_date);
    if ($start_date > $today) { 
        return 0;
    }

    $date1=date_create($date);
    $date2=date_create($end_date);
    $diff=date_diff($date1,$date2);
    return $diff->format("%R%a days");
}

function sum_journal_cashflow_by_group($month,$year,$group)
{
    $sum = \App\Models\Journal::join('code_cashflows','code_cashflows.id','=','journals.code_cashflow_id')->where('group',$group)->whereYear('date_journal',$year)->whereMonth('date_journal',$month)->sum('saldo');
    return $sum?$sum:0;
}
function sum_journal_cashflow($year,$month,$cashflow)
{
    $sum = \App\Models\Journal::whereYear('date_journal',$year)->whereMonth('date_journal',$month)->where('code_cashflow_id',$cashflow)->sum('saldo');
    
    return ($sum ? $sum : 0);
}
function month()
{
    $month = [1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"];

    return $month;
}
function replace_idr($nominal)
{
    if($nominal == "") return 0;

    $nominal = str_replace('Rp. ', '', $nominal);
    $nominal = str_replace(' ', '', $nominal);
    $nominal = str_replace('.', '', $nominal);
    $nominal = str_replace(',', '', $nominal);
    $nominal = str_replace('-', '', $nominal);
    $nominal = str_replace('(', '', $nominal);
    $nominal = str_replace(')', '', $nominal);

    return (int)$nominal;
}
function get_group_cashflow($key="")
{
    $data = [1=>'Operation Activities',2=>'Investment Activities',3=>'Financing Activities'];
    if($key!="") return @$data[$key];
    
    return $data;
}

function generate_no_voucher_konven_underwriting($coa_id)
{
    $coa = \App\Models\Coa::find($coa_id);
    $count = \App\Models\KonvenUnderwriting::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->count()+1;
    
    if($coa) return $coa->code_voucher.'-'.str_pad($count,3, '0', STR_PAD_LEFT).'/'.date('m').'/'.date('Y');

    return '';
}

function generate_no_voucer_journal($type="AP"){

    $count = \App\Models\Finance\Journal::count();
    
    return "{$type}-".str_pad($count,3, '0', STR_PAD_LEFT).'/'.date('m').'/'.date('Y');
}

function generate_no_voucher($coa_id="",$count="")
{
    $coa = \App\Models\Coa::find($coa_id);
    if(empty($count)) 
        $count = \App\Models\Journal::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->count()+1;
    if($coa) 
        return $coa->code_voucher.'-'.str_pad($count,3, '0', STR_PAD_LEFT).'/'.date('m').'/'.date('Y');

    return '';
}
function format_idr($number,$decimal=0)
{
    if(empty($number)) return 0;
    // $number = (int) $number;
    return number_format($number,$decimal);
}
function get_setting($key)
{
    $setting = \App\Models\Settings::where('key',$key)->first();

    if($setting)
    {
        if($key=='logo' || $key=='favicon' ){
            return  asset("storage/{$setting->value}");
        }

        return $setting->value;
    }
}
function update_setting($key,$value)
{
    $setting = \App\Models\Settings::where('key',$key)->first();
    if($setting){
        $setting->value = $value;
        $setting->save();
    }else{
        $setting = new \App\Models\Settings();
        $setting->key = $key;
        $setting->value = $value;
        $setting->save();
    }
}