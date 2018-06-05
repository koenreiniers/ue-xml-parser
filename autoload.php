<?php
use Symfony\Component\Debug\Debug;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

require __DIR__.'/vendor/autoload.php';

Debug::enable();

function parse_notation($input)
{
    $input = str_replace(', ', '&', $input);
    $input = substr($input, 1, -1);
    $result = [];
    parse_str($input, $result);
    return $result;
}

function decode_log($filename)
{
    $decoder = new XmlEncoder('Fitts_Study');
    return $decoder->decode(file_get_contents($filename), 'xml');
}