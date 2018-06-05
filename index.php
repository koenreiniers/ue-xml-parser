<?php


use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\VarDumper\VarDumper;



require __DIR__.'/autoload.php';





function parse_file($filename)
{
    $inputDir = __DIR__.'/input';

    $study = decode_log($inputDir.'/'.$filename);



    $results = [];



    foreach($study['Condition'] as $condition) {

        $result = [
            'MT' => $condition['@MTe'],
            'A' => $condition['@A'],
            'W' => $condition['@W'],
            'Subject' => 2,
            'conditionGroup' => '2',
            'ID' => $condition['@ID'],
            'maxAcceleration' => 0,
            'maxVelocity' => 0,
            'overshoots' => 0,
            'movementError' => 0,
            'travel' => 0,
        ];

        $numberOfTrials = count($condition['Trial']);

        foreach($condition['Trial'] as $trial) {



            $movement = $trial['Movement'];

            $maxAcceleration = parse_notation($movement['@maxAcceleration']);
            $result['maxAcceleration'] = max((float)$maxAcceleration['X'], (float)$maxAcceleration['Y'], $result['maxAcceleration']);
            
            $maxVelocity = parse_notation($movement['@maxVelocity']);
            $result['maxVelocity'] = max((float)$maxVelocity['X'], (float)$maxVelocity['Y'], $result['maxVelocity']);

            $result['overshoots'] += $trial['@overshoots'] / $numberOfTrials;
            $result['movementError'] += $movement['@movementError'] / $numberOfTrials;
            $result['travel'] += $movement['@travel'] / $numberOfTrials;



        }

        $results[] = $result;
    }

    return $results;
}

function dump(array $results, $filename = null)
{
    $outputDir = __DIR__.'/output';
    $filename = $filename ?: time().'.csv';
    $encoder = new CsvEncoder();

    $output = $encoder->encode($results, 'csv');

    file_put_contents($outputDir.'/'.$filename, $output);
}

$results = parse_file('sub-4.xml');

dump($results);




VarDumper::dump($results);die;
