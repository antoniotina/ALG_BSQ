<?php
$array = [];
$handle = fopen($argv[1], "r");
$vernr = (int)fgets($handle, 4096);
while (($buffer = fgets($handle, 4096)) !== false) {
    array_push($array, str_split(trim($buffer)));
}
$hornr = count($array[0]);
fclose($handle);
$table = new Table($array, $hornr, $vernr);
$biggestSquare = $table->tableIterator();
$finalTable = $table->tableReplaceDotToCross($biggestSquare);
$finalTable->tableOutput();

class Table
{
    public array $table;
    public int $vernr;
    public int $hornr;
    const Dot = '.';
    const Circle = 'o';
    const Cross = 'X';

    function __construct(array $array, int $hornr, int $vernr)
    {
        $this->table = $array;
        $this->hornr = $hornr;
        $this->vernr = $vernr;
    }

    public function tableIterator(): Square
    {
        $biggestSquare = new Square(0, 0, 0);
        for ($y = 0; checkIfWithinBounds($y, $biggestSquare->size, $this->vernr); $y++) {
            for ($x = 0; checkIfWithinBounds($x, $biggestSquare->size, $this->hornr); $x++) {
                $biggestSquare = $this->iterateSquare($x, $y, $biggestSquare, $biggestSquare->size + 1);
            }
        }
        return $biggestSquare;
    }

    public function iterateSquare(int $x, int $y, Square $square, int $size): Square
    {
        if ($x + $size > $this->hornr || $y + $size > $this->vernr) {
            return $square;
        }
        for ($yfor = $y; $yfor < $y + $size; $yfor++) {
            for ($xfor = $x; $xfor < $x + $size; $xfor++) {
                if ($this->table[$yfor][$xfor] == $this::Circle) {
                    return $square;
                }
            }
        }
        if ($size > $square->size) {
            $square = new Square($x, $y, $size);
        }
        return $this->iterateSquare($x, $y, $square, $size + 1);
    }

    public function tableReplaceDotToCross(Square $square): Table
    {
        $table = clone ($this);
        for ($y = $square->y; $y < $square->y + $square->size; $y++) {
            for ($x = $square->x; $x < $square->x + $square->size; $x++) {
                $table->table[$y][$x] = $this::Cross;
            }
        }
        return $table;
    }

    public function tableOutput()
    {
        for ($y = 0; $y < $this->vernr; $y++) {
            echo implode($this->table[$y]) . "\n";
        }
    }
}
class Square
{
    public int $x;
    public int $y;
    public int $size;

    function __construct(int $x, int $y, int $size)
    {
        $this->x = $x;
        $this->y = $y;
        $this->size = $size;
    }
}

function checkIfWithinBounds(int $coordinate, int $interval, int $limit)
{
    return $coordinate < $limit && $coordinate + $interval < $limit;
}
