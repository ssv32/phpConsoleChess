<?php
global $argv;
$isRunConsole = isset($argv);

if($isRunConsole){ // пока запускать только из консоли

    global $br;
    global $plane;
    global $typePlayerRun; // какой сейчас игрок хди 0 белые или 1 чёрные
    global $planStart;
    
    $br = "\n";// если запуск из консоли
    
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

    //$plane = $planStart; // текущая доска

    $letters = array('A','B', 'C', 'D', 'I', 'F', 'G', 'H');
    $keyLetters = array_flip($letters);

    $saveGameFile = 'chessSave';
    
    /**
     * showHelpFigure - показать подсказку по фигурам
     */
    function showHelpFigure(){
        echo 'ячейка xyz - x - это игрок (1 или 2), y - это фигура (ниже фигуры), z - это доска (0 - белая, 1 - чёрная)';
        echo '8 - пешка, 1 - ладья, 2 - конь, 3 - офицер, 5 - ферзь, 7 - кароль';
    }

    /**
     * showHelpGame - показать подсказку по действиям в игре
     */
    function showHelpGame(){
        echo $br.'ход - run go <игрок (1 или 2)> <фигура> <куда ходить, ряд> <куда ходить, буква> <откуда ходить, ряд> <откуда ход, буква>';
        echo $br.'сброс игры - clear';
    }

    /**
     * clearGame - сбросить игру
     */
    function clearGame($saveGameFile){
        // file_put_contents($saveGameFile, '');
        global $planStart;
        saveGame($planStart, 0, $saveGameFile );
    }

    /**
     * findGame - проверит существует ли уже игра
     */
    function findGame($saveGameFile){
        return file_exists($saveGameFile);
    }

    /**
     * loadGame - загрузить игру
     */
    function loadGame($saveGameFile){
        global $plane;
        global $typePlayerRun;
        $arrGame = file_get_contents($saveGameFile);
        $arrGame = json_decode($arrGame);
        
        if (!empty($arrGame->plane) && isset($arrGame->typePlayerRun)){
            $plane = $arrGame->plane;
            $typePlayerRun = $arrGame->typePlayerRun;
        }else{
            echo $br."  ! File save game have errors, create new game".$br;
            clearGame($saveGameFile);
            loadGame($saveGameFile);
        }
    }
    
    /**
     * saveGame - сохранить текущее состояние игры
     * @param array $plane - текущее состояние доски
     * @param int $typePlayerRun - какой игрок сейчас ходит
     * @param string $saveGameFile - файл где сохранять
     */
    function saveGame($plane, $typePlayerRun, $saveGameFile ){
        file_put_contents($saveGameFile, json_encode(array('plane' => $plane, 'typePlayerRun' => $typePlayerRun )));
    }
    
    /**
     * showLetters - вывести ряд с буквами
     * @param array $letters
     */
    function showLetters($letters){
            global $br;
        echo $br;
        echo '   ';
        foreach ($letters as $value) {

            echo '  '.$value.' ';
        }
        echo '    ';
    }

    /**
     * showPlane - вывести поле
     * @global string $br
     * @param array $ar - массив описывающий текущее состояние доски
     * @param array $letters - массив с буквами
     */
    function showPlane($ar = array(), $letters){
        global $br;
        global $typePlayerRun;
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
        echo $br."=================================".$br;
        echo (($typePlayerRun == 0)? 'this move whites' : 'this move black').$br.$br;
    };

    /**
     * checkBlackCellByString - проверить чёрная ли ячейка
     * @param type $str
     * @return type
     */
    function checkBlackCellByString($str){
        return preg_match('/[0-9][0-9]1/', $str);
    }	

    /**
     * checkColorCell - проверить цвет ячейки
     * @param type $plane
     * @param type $stepN
     * @param type $stepLetter
     * @return typeва
     */
    function checkColorCell($plane, $stepN, $stepLetter){
        
        print_r($plane);
        
        echo ' x='.$stepN.' y='.$stepLetter.' '.$plane[$stepN][$stepLetter].'   ====';
        
        
        return checkBlackCellByString( $plane[$stepN][$stepLetter] );
    }	

    /**
     * goStep - сделать ход 
     * @param array $plane
     * @param array $keyLetters 
     * @param int $player - игрок (1 или 2)
     * @param int $figure - фигура
     * @param int $stepN куда - ходить, ряд
     * @param string $stepLetter - куда ходить, буква 
     * @param int $stepNOld - откуда ходить, ряд
     * @param string $stepLetterOld - откуда ход, буква
     * @return string
     */
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

    if (findGame($saveGameFile)){
        echo $br.'Open game (player 1 and 2), loading, show this game';
        loadGame($saveGameFile);
    } else{
        clearGame($saveGameFile);
        echo $br.'Create new game - player 1 and player 2, show this game';
        loadGame($saveGameFile);     
    }
    
    if (isset($argv)){
        if (!empty($argv[1]) && !empty($argv[2]) && ($argv[1] == 'run') && !empty($argv[2]) ){
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
                    $typePlayerRun = $argv[3];
                    saveGame($plane, $typePlayerRun, $saveGameFile );
                }else{
                        echo '   ! error bad data';
                }
                echo $br.'finish go step'.$br;
            }	
            if($argv[2] == 'clear'){
                clearGame($saveGameFile);
                loadGame($saveGameFile);
            }
        }	
    }	

    showPlane($plane, $letters);
    
} else {
    echo '! this script is run in console';
}
?>
