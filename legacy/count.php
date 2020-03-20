<?php
/**
 * A very simple stats counter for all kind of stats about a development folder
 * 
 * @author Joel Lord
 * @copyright Engrenage (www.engrenage.biz)
 * 
 * For more information: joel@engrenage.biz
 Sourced from here http://stackoverflow.com/questions/790956/count-lines-in-a-php-project
 
 */
function convert_number_to_words($number) {
    
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
}
$fileCounter = array();
$linesofcode = countLines('.', $fileCounter);
$linesofcodewords = convert_number_to_words($linesofcode);
/*foreach($fileCounter['gen'] as $key=>$val) {
    echo ucfirst($key).":".$val."<br>";
}
foreach($fileCounter as $key=>$val) {
    if(!is_array($val)) echo strtoupper($key).":".$val." file(s)<br>";
}*/
function countLines($dir, &$fileCounter) {
    $_allowedFileTypes = "(html|htm|phtml|php|js|css|ini)";
    $lineCounter = 0;
    $dirHandle = opendir($dir);
    $path = realpath($dir);
    $nextLineIsComment = false;

    if($dirHandle) {
        while(false !== ($file = readdir($dirHandle))) {
            if(is_dir($path."/".$file) && ($file !== '.' && $file !== '..')) {
                $lineCounter += countLines($path."/".$file, $fileCounter);
            } elseif($file !== '.' && $file !== '..') {
                //Check if we have a valid file 
                $ext = _findExtension($file);
                if(preg_match("/".$_allowedFileTypes."$/i", $ext)) {
                    $realFile = realpath($path)."/".$file;
                    $fileHandle = fopen($realFile, 'r');
                    $fileArray = file($realFile);
                    //Check content of file:
                    for($i=0; $i<count($fileArray); $i++) {
                        if($nextLineIsComment) {
                            $fileCounter['gen']['commentedLines']++;
                            //Look for the end of the comment block
                            if(strpos($fileArray[$i], '*/')) {
                                $nextLineIsComment = false;
                            }
                        } else {
                            //Look for a function
                            if(strpos($fileArray[$i], 'function')) {
                                $fileCounter['gen']['functions']++;
                            }
                            //Look for a commented line
                            if(strpos($fileArray[$i], '//')) {
                                $fileCounter['gen']['commentedLines']++;
                            }
                            //Look for a class
                            if(substr(trim($fileArray[$i]), 0, 5) == 'class') {
                                $fileCounter['gen']['classes']++;
                            }
                            //Look for a comment block
                            if(strpos($fileArray[$i], '/*')) {
                                $nextLineIsComment = true;
                                $fileCounter['gen']['commentedLines']++;
                                $fileCounter['gen']['commentBlocks']++;
                            }
                            //Look for a blank line
                            if(trim($fileArray[$i]) == '') {
                                $fileCounter['gen']['blankLines']++;
                            }
                        }

                    }
                    $lineCounter += count($fileArray);
                }
                //Add to the files counter
                $fileCounter['gen']['totalFiles']++;
                $fileCounter[strtolower($ext)]++;
            }
        }
    } else echo 'Could not enter folder';

    return $lineCounter;
}

function _findExtension($filename) {
    $filename = strtolower($filename) ; 
    $exts = split("[/\\.]", $filename) ; 
    $n = count($exts)-1; 
    $exts = $exts[$n]; 
    return $exts;  
}
?>