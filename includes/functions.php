<?php

function url_exists($url) 
{
    $headers = @get_headers($url, 1);
    return $headers && strpos($headers[0], '200') !== false;
}

function redirect($page)
{
    header('Location: '.$page);
    die();
}

function select($name, $options, $selected = false)
{
 
    ?>

    <select name="<?=$name?>">

        <?php foreach($options as $value => $option): ?>
            <option value="<?=$value?>"><?=$option?></option>
        <?php endforeach; ?>

    </select>

    <?php

}

function format_date($date, $format = 'date')
{

    if(!is_numeric($date)) $date = strtotime($date);

    switch($format)
    {
        case 'datetime': return '';
        case 'mysql': return date('Y-m-j', $date);
        default: return date('F j, Y', $date);
    }

}

function difference_date($from, $to = false)
{

    if(!$to) $to = time();

    if(!is_numeric($from)) $from = strtotime($from);

    return $from - $to;

}

function leading_zeros($number, $length = 5)
{

    return sprintf('%0'.$length.'d', $number);

}

function number_to_string($number)
{

    $strings = array(
        'zero',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'elevel',
        'twelve'
    );

    return $strings[$number];
}