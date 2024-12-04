<?php
//Your Settings can be read here: settings::read('myArray/settingName') = $settingValue;
//Your Settings can be saved here: settings::set('myArray/settingName',$settingValue,$overwrite = true/false);
class triangle_demo{
    public static function command($line):void{
        $milliseconds = time::millistamp();

        $file = "packages/triangle_demo/triangles/t$line.txt";

        if(!is_file($file)){
            echo "Triangle not found!\nTry: 5, 8, 10, 15, 20, 25\n";
            return;
        }

        $numbers = file($file);

        if(is_bool($numbers)){
            echo "File not found!\n";
            goto end;
        }

        $rows = intval($numbers[0]);
        array_shift($numbers);

        //Parse numbers
        $i = 0;
        foreach($numbers as $numberRow){
            $numberRow = trim($numberRow);
            $numbers[$i] = explode(" ", $numberRow);
            $ii = 0;
            foreach($numbers[$i] as $numberString){
                $numbers[$i][$ii] = intval($numberString);
                $ii++;
            }
            $i++;
        }
        //

        $moveSeed = 0;
        $lastSum = 0;
        $laststring = "";
        while($laststring !== str_repeat("1",$rows-1)){
            $laststring = str_pad(decbin($moveSeed),$rows-1,"0",STR_PAD_LEFT);
            $moves = str_split($laststring);
            array_unshift($moves,0);
            $sum = self::getSum($moves,$numbers);

            if($sum > $lastSum){
                $lastSum = $sum;
                $bestMoves = $moves;
            }

            self::showTriangle($numbers,$moves,$sum,$lastSum,true);

            $moveSeed++;
            //break;
        }

        self::showTriangle($numbers,$bestMoves,$lastSum,$lastSum,false);

        //

        end:

        $timeTaken = time::millistamp() - $milliseconds;
        echo "\nTime taken: " . $timeTaken/1000 . "s\n";
    }//Run when base command is class name, $line is anything after base command (string). e.g. > [base command] [$line]
    //public static function init():void{}//Run at startup

    private static function getSum($moves,$numbers){
        $sum = 0;
        $row = 0;
        $column = 0;
        foreach($moves as $move){
            //echo "ADDING " . $numbers[$row][$column] . "\n";
            $sum += $numbers[$row][$column];
            $row ++;
            $column += $move;
            if(!isset($numbers[$row])){
                break;
            }
        }
        return $sum;
    }
    private static function showTriangle($numbers,$moves,$sum,$lastSum,$clear = true){
        array_unshift($moves,0);
        $line = "";
        $triangleWidth = count(end($numbers));
        $space = $triangleWidth-1;
        $i = 0;
        $col = 0;
        foreach($numbers as $numberArray){
            $line .= str_repeat(" ",$space);
            $col += $moves[$i];
            $col2 = 0;
            foreach($numberArray as $number){
                if($col === $col2){
                    $colour = "33";
                    if(!$clear){
                        $colour = "32";
                    }
                    $line .= "\033[" . $colour . "m" . $number . "\033[0m" . " ";
                }
                else{
                    $line .= $number . " ";
                }
                $col2++;
            }
            $line .= "\n";
            $space--;
            $i++;
        }
        $line .= "\nSum is: " . str_pad($sum,3,"0",STR_PAD_LEFT);
        $line .= "\nBest sum: " . str_pad($lastSum,3,"0",STR_PAD_LEFT);
        echo $line;

        if($clear){
            echo chr(27) . "[0G"; // Set cursor to first column
            echo chr(27) . "[" . $i+2 ."A"; // Set cursor up x lines
        }
    }
}