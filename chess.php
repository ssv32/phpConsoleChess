<?php
global $br;
global $plane;
global $argv;

if(isset($argv)){
	$br = "\n";// если запуск из консоли
}else{
	$br = "</br>"; // запуск из браузера
}	

//print_r($argv);

$planStart = array( // стартовая доска
    0 => array('110','121','130','171','150','131','120','111'), 
    1 => array('181','180','181','180','181','180','181','180'),
    2 => array('000','001','000','001','000','001','000','001'),
    3 => array('001','000','001','000','001','000','001','000'),
    4 => array('000','001','000','001','000','001','000','001'),
    5 => array('001','000','001','000','001','000','001','000'),
    6 => array('280','281','280','281','280','281','280','281'),
    7 => array('211','220','231','200','201','230','221','210'),
);

$plane = $planStart; // текущая доска

$letters = array('A','B', 'C', 'D', 'I', 'F', 'G', 'H');
$keyLetters = array_flip($letters);

$saveGameFile = 'chessSave';

function showHelpFigure(){
    echo 'ячейка xyz - x - это игрок (1 или 2), y - это фигура (ниже фигуры), z - это доска (0 - белая, 1 - чёрная)';
    echo '8 - пешка, 1 - ладья, 2 - конь, 3 - офицер, 5 - ферзь, 7 - кароль';
}

function showHelpGame(){
    echo $br.'ход - run go <игрок (1 или 2)> <фигура> <куда ходить, ряд> <куда ходить, буква> <откуда ходить, ряд> <откуда ход, буква>';
	echo $br.'сброс игры - clear';
}

function clearGame($saveGameFile){
    file_put_contents($saveGameFile, '');
}

function findGame($saveGameFile){
    return file_exists($saveGameFile);
}

function loadGame($saveGameFile){
    
}

function showLetters($letters){
	global $br;
    echo $br;
    echo '   ';
    foreach ($letters as $value) {
        
        echo '  '.$value.' ';
    }
    echo '    ';
}

function showPlane($ar = array(), $letters){
	global $br;
    echo $br."============plan=================";
    showLetters($letters);
    foreach ($ar as $key => $value) {
        echo $br;
        echo ' '.($key+1).' ';
        foreach($value as $key2 => $val2){
            echo '|'.$val2;
        }
        echo '| '.($key+1).' ';
        //echo '|';
    }
    showLetters($letters);
    echo $br."=================================";
};

function checkBlackCellByString($str){
	return preg_match('/[0-9][0-9]1/', $str);
}	

function checkColorCell($plane, $stepN, $stepLetter){	
	return checkBlackCellByString( $plane[$stepN][$stepLetter] );
}	

function goStep($plane, $keyLetters, $player, $figure, $stepN, $stepLetter, $stepNOld, $stepLetterOld){ // сделать ход
	$stepN--;
	$stepNOld--;
	$plane[$stepN][$keyLetters[$stepLetter]] = $player.$figure.checkColorCell($plane, $stepN,$keyLetters[$stepLetter]);
	$plane[$stepNOld][$keyLetters[$stepLetterOld]] = '00'.checkColorCell($plane, $stepNOld, $keyLetters[$stepLetterOld]);
	return $plane;
}

// тест хода
//$plane = goStep($plane, $keyLetters, 1, 8, 4, 'B', 2,'A' );

echo '---===chess is run===---'.$br;
echo 'You run action if console - chess.php run ... '.$br;
echo 'actions: HelpFigure, HelpGame'.$br;

if (isset($argv)){
	if ( ($argv[1] == 'run') && !empty($argv[2]) ){
		if ($argv[2] == 'HelpFigure'){
			echo $br.'run HelpFigure();'.$br;
			showHelpFigure();
			echo $br.'finish HelpFigure();'.$br;
		}	
		if ($argv[2] == 'HelpGame'){
			echo $br.'run showHelpGame();'.$br;
			showHelpGame();
			echo $br.'finish showHelpGame();'.$br;
		}	
		if($argv[2] == 'go'){
			echo $br.'run go step'.$br;
			if(!empty($argv[3]) && !empty($argv[4]) && !empty($argv[5]) && !empty($argv[6]) && !empty($argv[7]) && !empty($argv[8])  ){
				// $argv[3] - игрок
				// $argv[4] - фигура
				// $argv[5] куда ходить, ряд
				// $argv[6] куда ходить, буква 
				// $argv[7] откуда ходить, ряд
				// $argv[8] откуда ход, буква				
				$plane = goStep($plane, $keyLetters, $argv[3], $argv[4], $argv[5], $argv[6], $argv[7], $argv[8] );
			}else{
				echo '   ! error bad data';
			}
			echo $br.'finish go step'.$br;
		}	
		if($argv[2] == 'clear'){
			clearGame($saveGameFile);
		}
	}	
}	

if (findGame($saveGameFile)){
    echo $br.'Open game (player 1 and 2), loading, show this game';
	showPlane($plane, $letters);
} else{
	clearGame($saveGameFile);
    echo $br.'Create new game - player 1 and player 2, show this game';
	showPlane($plane, $letters);
}

?>
