<?php

function reactUserObject()
{
    $api_token = \Illuminate\Support\Facades\Crypt::decryptString(request()->bearerToken());

    return \App\Models\User::where('api_token', $api_token)
        ->whereNotNull('api_token')
        ->where('status', true)
        ->first();
}

function NewGuid()
{
    $s = strtoupper(md5(uniqid(rand(), true)));
    $guidText =
        substr($s, 0, 8) . '-' .
        substr($s, 8, 4) . '-' .
        substr($s, 12, 4) . '-' .
        substr($s, 16, 4) . '-' .
        substr($s, 20);
    return $guidText;
}

function cleanString($text)
{
    $utf8 = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        '/‬/'    =>   '',
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

function zero_add_cellphone($number)
{
    return str_pad($number, 10, '0', STR_PAD_LEFT);
}

function zero_area($number)
{
    return preg_replace('/^0?/', '595', $number);
}

function digito_verificador($ci, $baseMax = 11)
{
    $resultado = 0;
    $index = 0;
    $r = 0;
    for ($rucIndex = strlen($ci) - 1; $rucIndex >= 0; $rucIndex--) {
        $resultado += (int) substr($ci, $rucIndex, 1) * ($index + 2);
        $r = $resultado % $baseMax;
        $index++;
    };
    $verificador = $r > 1 ? $baseMax - $r : 0;

    return $verificador;
}

// function shapeSpace_system_load($coreCount = 2, $interval = 1)
// {
//     $rs = sys_getloadavg();
//     $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
//     $load = $rs[$interval];
//     return round(($load * 100) / $coreCount,2);
// }

// function shapeSpace_disk_usage() {

//     $disktotal = disk_total_space ('/');
//     $diskfree  = disk_free_space  ('/');
//     $diskuse   = round (100 - (($diskfree / $disktotal) * 100)) .'%';

//     return $diskuse;

// }

// function shapeSpace_server_memory_usage() {

//     $free = shell_exec('free');
//     $free = (string)trim($free);
//     $free_arr = explode("\n", $free);
//     $mem = explode(" ", $free_arr[1]);
//     $mem = array_filter($mem);
//     $mem = array_merge($mem);
//     $memory_usage = $mem[2] / $mem[1] * 100;

//     return $memory_usage;

// }

function numberToWords($num)
{
    $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
    return $formatter->format($num);
}

function cached_asset($path, $bustQuery = true)
{
    // Get the full path to the asset.
    $realPath = public_path($path);

    if (!file_exists($realPath)) {
        throw new LogicException("File not found at [{$realPath}]");
    }

    // Get the last updated timestamp of the file.
    $timestamp = filemtime($realPath);

    if (!$bustQuery) {
        // Get the extension of the file.
        $extension = pathinfo($realPath, PATHINFO_EXTENSION);

        // Strip the extension off of the path.
        $stripped = substr($path, 0, - (strlen($extension) + 1));

        // Put the timestamp between the filename and the extension.
        $path = implode('.', array($stripped, $timestamp, $extension));
    } else {
        // Append the timestamp to the path as a query string.
        $path  .= '?' . $timestamp;
    }

    return asset($path);
}

function activeRoutes(array $routes, $output = 'active')
{
    return in_array(request()->route()->getName(), $routes) ? $output : '';
}

if (!function_exists('posix_getpid'))
{
    function posix_getpid()
    {
        return getmypid();
    }
}

function check_date($date)
{
    $output = true;

    $date_time  = explode(' ', $date);
    $date       = explode('/', $date_time[0]);
    $time       = isset($date_time[1]) ? explode(':', $date_time[1]) : null;
    $count_date = 0;

    foreach ($date as $key => $value)
    {
        $length = str_split($value);
        $counte_values = 0;
        foreach ($length as $key2 => $value2)
        {
            $is_numeric = is_numeric($value2);
            $counte_values++;
            if (!$is_numeric)
            {
                $output = false;
                break;
            }
        }
        $count_date++;

        if ($count_date == 3 && $counte_values != 4)
        {
            $output = false;
            break;
        }
    }

    if ($count_date < 3)
    {
        $output = false;
    }

    if ($time)
    {
        foreach ($time as $key => $value)
        {
            $length = str_split($value);

            foreach ($length as $key2 => $value2)
            {
                $is_numeric = is_numeric($value2);

                if (!$is_numeric)
                {
                    $output = false;
                    break;
                }
            }
        }

        if (!isset($time[2]))
        {
            $output = false;
        }
    }

    return $output;
}

function date_interval_periods($from_date, $interval, $until_date)
{
    $results = [];
    $from_date = \Carbon\Carbon::parse($from_date);
    $until_date = \Carbon\Carbon::parse($until_date);

    while ($from_date <= $until_date) {
        $results[] = $from_date;
        $from_date = \Carbon\Carbon::parse($from_date)->add($interval);
    }

    return $results;
}

function math_operator($value1, $value2, $operator)
{
    switch ($operator) {
        case '==':
            return $value1 == $value2;
        case '>':
            return $value1 > $value2;
        case '>=':
            return $value1 >= $value2;
    }
}

function numberToRoman($num)  
{ 
    // Be sure to convert the given parameter into an integer
    $n = intval($num);
    $result = ''; 
 
    // Declare a lookup array that we will use to traverse the number: 
    $lookup = array(
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
    ); 
 
    foreach ($lookup as $roman => $value)  
    {
        // Look for number of matches
        $matches = intval($n / $value); 
 
        // Concatenate characters
        $result .= str_repeat($roman, $matches); 
 
        // Substract that from the number 
        $n = $n % $value; 
    } 

    return $result; 
}

function TicketAgentBusinessDepartmentsHelper($user = null)
{
    $business_departments = [];
    if ($user)
    {
        if($user->can('tickets-tic.index'))
        {
            array_push($business_departments,1);
        }
        
        if($user->can('tickets-general-services.index'))
        {
            array_push($business_departments,16);
        }
        if($user->can('tickets-marketing.index'))
        {
            array_push($business_departments,13);
        }
        if($user->can('tickets-business-intelligence.index'))
        {
            array_push($business_departments,23);
        }
        if($user->can('tickets-process.index'))
        {
            array_push($business_departments,30);
        }
    }
    else
    {
        if(auth()->user()->can('tickets-tic.index'))
        {
            array_push($business_departments,1);
        }
        
        if(auth()->user()->can('tickets-general-services.index'))
        {
            array_push($business_departments,16);
        }
        if(auth()->user()->can('tickets-marketing.index'))
        {
            array_push($business_departments,13);
        }
        if(auth()->user()->can('tickets-business-intelligence.index'))
        {
            array_push($business_departments,23);
        }
        if(auth()->user()->can('tickets-process.index'))
        {
            array_push($business_departments,30);
        }
    }
    return $business_departments;
}

function sum_array($array)
{
    $total_amount = 0;
    foreach ($array as $key => $value)
    {
        if ($value)
        {
            $total_amount += cleartStringNumber($value);
        }
    }
    return $total_amount;
}

function cleartStringNumber($value)
{
    return str_replace(',', '.',str_replace('.', '', $value));
}

function custom_array_keys($keys,$array)
{
    foreach ($keys as $key) 
    {
        unset($array[$key]);
    }
    return $array;
}

function get_customKeys($keys,$array)
{
    foreach ($array as $key => $arr) 
    {
        if(!in_array($key,$keys))
        {
            unset($array[$key]);
        }
    }
    return $array;
}


