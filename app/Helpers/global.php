<?php

use Illuminate\Support\Facades\DB;

/**
 * @param mixed $value
 * @return string
 * @throws \Exception
 */
function format_rupiah($value)
{
    return 'Rp ' . number_format((float) $value);
}

function time_elapsed_string($datetime, $full = false)
{
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'tahun',
        'm' => 'bulan',
        'w' => 'minggu',
        'd' => 'hari',
        'h' => 'jam',
        'i' => 'menit',
        's' => 'detik',
    ];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' lalu' : 'baru saja';
}

function get_user($id)
{
    return \App\Models\User::find($id)->name ?? '-';
}

function batasString($text, $jumlah)
{
    return strlen($text) > $jumlah ? substr(strip_tags(html_entity_decode($text)), 0, $jumlah) . ' .....' : substr(strip_tags(html_entity_decode($text)), 0, $jumlah);
}

function hitungPromo($price, $typePromo, $nominalPromo)
{
    if (isset($nominalPromo)) {
        if ($typePromo == 0) {
            $returnPrice = $price - $nominalPromo;
        } else {
            $returnPrice = $price - (($price * $nominalPromo) / 100);
        }
    } else {
        $returnPrice = $price;
    }

    return $returnPrice;
}

function hitungNominalPromo($price, $typePromo, $nominalPromo)
{
    if (isset($nominalPromo)) {
        if ($typePromo == 0) {
            $returnPrice = $nominalPromo;
        } else {
            $returnPrice = ($price * $nominalPromo) / 100;
        }
    } else {
        $returnPrice = 0;
    }

    return $returnPrice;
}

function hitungPresentasePromo($price, $typePromo, $nominalPromo)
{
    if (isset($nominalPromo)) {
        if ($typePromo == 0) {
            $returnPrice = ($nominalPromo / $price) * 100;
        } else {
            $returnPrice = $nominalPromo;
        }
    } else {
        $returnPrice = 0;
    }

    return $returnPrice != 0 ? round($returnPrice, 2) : 0;
}

function perbandinganPromoWithPrice($price, $promo_id)
{
    $dataPromo = DB::table('promo')->select('promo_type', 'discount_amount')->where('id', $promo_id)->where('promo_type', '0')->first();
    if (isset($dataPromo)) {
        if ($price < $dataPromo->discount_amount) {
            $return = false;
        } else {
            $return = true;
        }
    } else {
        $return = true;
    }

    return $return;
}

function convert_format_tanggal($value)
{
    return (!$value) ? '' : date('d/m/Y', strtotime($value));
}

if (!function_exists('put_permanent_env')) {
    function put_permanent_env($key, $value)
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('=' . env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path),
        ));
    }
}
if (!function_exists('getUserById')) {
    function getUserById($id)
    {
        return \App\Models\User::find($id) ?? '-';
    }
}

if (!function_exists('format_number')) {
    function format_number($value)
    {
        return number_format($value, 0, ",", ".");
    }
}

if (!function_exists('can')) {
    function can($permisssion)
    {
        return  auth()->user()->can($permisssion);
    }
}
