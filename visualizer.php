<?php


use Symfony\Component\VarDumper\VarDumper;

require __DIR__.'/autoload.php';

$log = decode_log(__DIR__.'/input/sub-1.xml');

function normalize_condition($condition)
{
    $normalized = [
        'W' => $condition['@A'],
        'A' => $condition['@W'],
        'trials' => [],
    ];

    foreach($condition['Trial'] as $trial) {
        $normalized['trials'][] = normalize_trial($trial);
    }

    return $normalized;
}

function normalize_trial($trial)
{
    $thisCircle = parse_notation($trial['@thisCircle']);

    $normalized = [
        'thisCircle' => [
            'x' => (float)$thisCircle['X'],
            'y' => (float)$thisCircle['Y'],
            'radius' => (float)$thisCircle['Radius'],
        ],
        'movement' => [
            'moves' => [],
        ],
    ];

    if(isset($trial['Movement']['move'])) {
        foreach($trial['Movement']['move'] as $move) {

            $point = parse_notation($move['@point']);

            $normalizedMove['x'] = (float)$point['X'];
            $normalizedMove['y'] = (float)$point['Y'];
            $normalizedMove['t'] = (float)$point['Time'];

            $normalized['movement']['moves'][] = $normalizedMove;
        }

    }




    return $normalized;
}

function normalize_log($log)
{
    $normalized = [
        'conditions' => [],
    ];

    foreach($log['Condition'] as $condition) {


        $normalized['conditions'][] = normalize_condition($condition);
    }

    return $normalized;
}


$normalized = normalize_log($log);


$jsonLog = json_encode($normalized);




?>

<html>
<head>
    <title>UE Visualizer</title>
    <script
        src="https://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

    <style>
        #visualizer {
            width: 1920px;
            height: 1080px;
        }
    </style>
</head>
<body>

<select id="conditions-select"></select>


<canvas id="visualizer" width="1920" height="1080"></canvas>

<script>

    jQuery(document).ready(function($){

        var $visualizer = $('#visualizer');
        var $conditionsSelect = $('#conditions-select');

        var ctx = $visualizer[0].getContext('2d');


        var log = {
            conditions: [
                {
                    trials: [
                        {
                            thisCircle: {
                                x: 849,
                                y: 604,
                                radius: 4,
                            },
                            movement: {
                                moves: [
                                    {
                                        x: 959,
                                        y: 283,
                                        t: 66,
                                    },
                                    {
                                        x: 958,
                                        y: 283,
                                        t: 97,
                                    },
                                ],
                            },
                        }
                    ],
                },
            ],
        };

        log = <?=$jsonLog;?>;

        function drawCircle(circle)
        {
            ctx.beginPath();
            ctx.arc(circle.x,circle.y,circle.radius,0,2*Math.PI);
            ctx.stroke();
        }

        function drawMoves(moves)
        {

            ctx.beginPath();


            ctx.moveTo(moves[0].x, moves[0].y);

            for(var i = 1; i < moves.length; i++) {
                var move = moves[i];
                ctx.lineTo(move.x, move.y);
            }

            ctx.stroke();
            //ctx.lineTo(move.x, move.y);
        }

        function drawTrial(trial)
        {
            drawCircle(trial.thisCircle);

            drawMoves(trial.movement.moves);




        }

        function drawCondition(condition)
        {
            condition.trials.forEach(function(trial){

                drawTrial(trial);

            });
        }

        drawCondition(log.conditions[0]);

    });

</script>
</body>
</html>