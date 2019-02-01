<?php
/**
 * Created by PhpStorm.
 * User: Назар
 * Date: 03.01.2019
 * Time: 13:58
 */

/*
 * In: $spreadsheet_url - url(.csv) table from Google spreadsheets
 * Out: array(table)
 */
function readTable($spreadsheet_url) {
    $spreadsheet_data = array();
    if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

    if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $spreadsheet_data[] = $data;
        }
        fclose($handle);
    }
    else
        die("Problem reading csv");

    return $spreadsheet_data;
}

$ini_file = file_get_contents('ini.json');
$ini = json_decode($ini_file, true);

$spreadsheet_data = readTable($ini['table_url']);

$type_of_columns = array_shift($spreadsheet_data);
$name_columns = array_shift($spreadsheet_data);
$title_table = array_shift($spreadsheet_data);

$head_table = array();
for($i = 0; $i < count($type_of_columns); $i++) {
    $head_table[$i]['type_of'] = $type_of_columns[$i];
    $head_table[$i]['name'] = $name_columns[$i];
    $head_table[$i]['title'] = $title_table[$i];
}

$link_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'link' ) {
        $link_columns[] = $key;
    }
}

$string_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'string' ) {
        $string_columns[] = $key;
    }
}

$float_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'float' ) {
        $float_columns[] = $key;
    }
}

$integer_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'integer' ) {
        $integer_columns[] = $key;
    }
}

$dropdown_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'dropdown' ) {
        $dropdown_columns[] = $key;
    }
}

$json_array = array();
$country = '';
if(!isset($dropdown_columns[0])) {
    $country = 1;
}
else if(isset($_POST['filters'])) {
    $json_array = json_decode($_POST['filters'], true);

    foreach ($json_array as $json) {
        if($json['country'])
            $country = $json['country'];
    }
}

if($country && $json_array) {
    $json_array = json_decode($_POST['filters'], true);

    if( isset($dropdown_columns[0]) ) {
        foreach ($head_table as $key1 => $head_column) {
            if( $head_column['type_of'] == 'dropdown' ) {
                $key = $key1;
                break;
            }
        }

        $sliced_array = array();
        foreach ($spreadsheet_data as $line) {
            if(stristr($line[$key],$country)) {
                $sliced_array[] = $line;
            }
        }
        $spreadsheet_data = $sliced_array;
    }


    foreach ($json_array as $json ) {

        $key = $json['key'];
        $operator = $json['operator'];
        $value = $json['value'];

        foreach ($head_table as $key1 => $head_column) {
            if($head_column['name'] == $key) {
                $key = $key1;
                break;
            }
        }

        $type_of = '';
        if(in_array($key,$string_columns)) {
            $type_of = 'string';
        }
        else if(in_array($key,$float_columns)) {
            $type_of = 'float';
        }
        else if(in_array($key,$integer_columns)) {
            $type_of = 'integer';
        }

        // filtering data
        if( $type_of == 'string' && $operator) {

            $sliced_array = array();
            if($operator == 'equal') {
                foreach ($spreadsheet_data as $line) {
                    if(stristr($line[$key],$value)) {
                        $sliced_array[] = $line;
                    }
                }
            }
            $spreadsheet_data = $sliced_array;
        }
        else if(($type_of == 'float' || $type_of == 'integer') && $operator && $value > 0) {

            $sliced_array = array();
            if($operator == 'range') {
                preg_match('|([1234567890]*)-|',$value,$val_less);
                preg_match('|-([1234567890]*)|',$value,$val_more);

                foreach ($spreadsheet_data as $line) {
                    if((float)$line[$key] >= (float)$val_less[1] && (float)$line[$key] <= (float)$val_more[1]) {
                        $sliced_array[] = $line;
                    }
                }
            }
            else if($operator == 'greater-than') {
                preg_match('|([1234567890]*)-|',$value,$val);
                foreach ($spreadsheet_data as $line) {
                    if((float)$line[$key] > (float)$val[1]) {
                        $sliced_array[] = $line;
                    }
                }
            }
            else if($operator == 'equal') {
                preg_match('|([1234567890]*)-|',$value,$val);
                foreach ($spreadsheet_data as $line) {
                    if((float)$line[$key] == (float)$val[1]) {
                        $sliced_array[] = $line;
                    }
                }
            }
            else if($operator == 'less-than') {
                preg_match('|([1234567890]*)-|',$value,$val);
                foreach ($spreadsheet_data as $line) {
                    if((float)$line[$key] < (float)$val[1]) {
                        $sliced_array[] = $line;
                    }
                }
            }
            $spreadsheet_data = $sliced_array;
        }
        if(!$spreadsheet_data) {
            break;
        }
    }
    unset($sliced_array);

    // print data
    foreach($spreadsheet_data as $line ) {
        echo '<tr>';
        foreach ($line as $key => $cell) {
            if(in_array($key,$link_columns)) {
                continue;
            }

            $class = 'score';
            if(in_array($key,$float_columns)) {
                preg_match('|([0123456789]*)|',$cell,$cl);
                while($cl[0]%10 != 0) {
                    if($cl[0]%10 >= 5) {
                        $cl[0]++;
                    }
                    else {
                        $cl[0]--;
                    }
                }
                $class .= ' score-' . $cl[0];
                $cell = str_replace(',','.',$cell);
                $cell = number_format((float)$cell, 2, '.', '');

            }

            echo  '<td class="' . $class . '">';

            if(in_array($key + 1,$link_columns)) {
                echo '<a href="' . $line[$key+1] . '">' . $cell . '</a>';
            }
            else
                echo $cell;

            echo '</td>';
        }
        echo '</tr>';
    }
}
else {
    echo '<tr><td style="text-align: center; font-size: 18px;"><br>Please, choose ' . $head_table[$dropdown_columns[0]]['title'] .'<br></td></tr>';
}