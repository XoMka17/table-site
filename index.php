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

$number_columns = array();
$float_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'float' ) {
        $float_columns[] = $key;
        $number_columns[] = $key;
    }
}

$integer_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'integer' ) {
        $integer_columns[] = $key;
        $number_columns[] = $key;
    }
}

$dropdown_columns = array();
foreach ($head_table as $key => $head_column) {
    if( $head_column['type_of'] == 'dropdown' ) {
        $dropdown_columns[] = $key;
    }
}

$dropdown_array = array();
if( isset($dropdown_columns[0])) {
    foreach ($spreadsheet_data as $line) {
        if(!in_array($line[$dropdown_columns[0]],$dropdown_array)) {
            $dropdown_array[] = $line[$dropdown_columns[0]];
        }
    }
}

?>

<html>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="image_src" href="https://airlinelist.co/assets/social.jpg">
    <link rel="icon" href="https://airlinelist.co/assets/icon.png"/>

    <meta property="og:title" content=""/>
    <meta property="og:url" content="https://airlinelist.co"/>
    <meta property="og:description" content="The best airlines, airplanes and airports compared"/>
    <meta property="og:image" content="https://airlinelist.co/assets/social.jpg"/>
    <meta property="og:site_name" content="Airline List" />
    <meta property="og:type" content="website"/>
    <style>
        html,body {
            padding:0;
            margin:0;
        }
        body {
            text-align:center;
            /*margin-top:28px;*/
        }
        body,input,button,h1,p.slogan {
            color:#333;
        }
        .header {
            width:100%;
            position:absolute;
            top:0;
            left:0;
            z-index:-1;
            pointer-events:none;
            height:600px;
        }
        .header .bg {
            position:absolute;
            top:0;
            left:0;
            /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#ffffff+0,ffffff+100&0+0,1+100 */
            background: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            pointer-events:none;
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
            z-index:-1;
            width:100%;
            height:100%;
        }
        .header .img {
            position:absolute;
            top:0;
            left:0;
            z-index:-2;
            pointer-events:none;
            width:100%;
            height:100%;
            object-fit:cover;
            background-size:cover;
            background-position:center center;
        }
        body, input {
            font-family:sans-serif;
            font-family:"Varela Round",sans-serif;
            color:#222;
            background:#fbfbfb;
        }
        .container {
            border-radius:5px;
            background:#fbfbfb;
            max-width:1000px;
            padding:0;
            display:block;
            overflow:hidden;
            margin:0 auto;
            text-align:center;
        }
        h2,h3 {
            text-align:left;
        }
        .filters {
            z-index:1;
            position:relative;
            display:block;
        }
        .filters .group {
            padding:14px;
            display:block;
        }
        .filters .divider {
            background:#ededed;
            height:1px;
        }
        .filters button, .filters select{
            background:#fff;
            outline:none;
            padding:7px;
            padding-left:14px;
            padding-right:14px;
            vertical-align:middle;
            min-height:39px;
            border-radius:5px;
            cursor:pointer;
            font-weight:bold;
            border:1px solid #ededed;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            margin:3.5px;
            display:inline-block;
            font-size:13px;
        }
        .filters button:hover {
            background:#f5f5f5;
        }
        .filters button.active {
            background:#00a6e3;
            border-color:#00a6e3;
            color:#fff;
        }
        .filters select option{
            padding:7px;
            font-weight:bold;
        }
        table {
            table-layout:fixed;
            min-width:100%;
            width:100%;
            background:#fbfbfb;
            /*margin-top:56px;*/
        }
        table td.td-img {
            padding:0;
            margin:0;
        }
        table td.td-maker {
            max-width:100px;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        table td img {
            width:30px;
            height:30px;
            object-fit:cover;
        }
        table {
            border-collapse:collapse;
        }
        table tr:nth-child(odd) td {
            /*background-color:#efefef;*/
        }
        table th,
        table td {
            padding:7px;
            border-top:1px solid #ededed;
            font-size:13px;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        table td {
            background:#fff;
            text-align:right;
        }
        table td.score {
            text-align:center;
        }
        table td.name,
        table td.country,
        table td.alliance {
            text-align:left;
        }
        table td:active,
        table td:focus {
            /*outline:2px solid #4285f4;*/
            box-shadow:inset 2px 2px 0 #4285f4, inset -2px -2px 0 #4285f4;
        }
        table thead {
            z-index: 9;
        }
        table thead th {
            border-bottom:1px solid #ededed;
            cursor:pointer;
            font-weight:bold;
            background-color:#fbfbfb;
        }
        table thead th:hover {
            opacity:0.75;
        }
        table thead th:active {
            opacity:0.5;
        }
        table tbody td.logo {
            padding:0;
            width:50px;
        }
        table tbody td.logo img {
            width:50px;
            height:50px;
            margin:0;
            border:none;
            outline:none;
            background:#fff;
        }
        .by {
            text-decoration:none;
            right:0;
            bottom:0;
            background-color:rgb(255, 71, 66);
            color:#fff;
            position:fixed;
            font-weight:500;
            z-index:8;
            border-top-left-radius: 5px;
            padding: 0.5em;
            border-top:1px solid #efefef;
            font-weight:800;
            border-left:1px solid #efefef;
            background:#fff;
            color:#6f6f6f;
        }
        .by:hover {
            background:#efefef !important;
        }
        .by img {
            border-radius:2em;
            width:1.5em;
            vertical-align:middle;
        }
        .by p {
            margin:0;
            vertical-align:middle;
            display:inline;
            margin-left:0.5em;
            font-weight:400;
        }
        .disclaimer {
            font-size:10px;
            opacity:0.25;
            text-transform:uppercase;
            display:block;
            margin:0 auto;
            margin-top:52px;
            margin-bottom:52px;
            max-width:1000px;
        }

        td.score-100 {
            background-color:#57BB8A;
        }
        td.score-90 {
            background-color:#57BB8A;
        }
        td.score-80 {
            background-color:#99C57C;
        }
        td.score-70 {
            background-color:#99C57C;
        }
        td.score-60 {
            background-color:#DDD06E;
        }
        td.score-50 {
            background-color:#FFD566;
        }
        td.score-40 {
            background-color:#FAC568;
        }
        td.score-30 {
            background-color:#F6B36A;
        }
        td.score-20 {
            background-color:#EFA06D;
        }
        td.score-10 {
            background-color:#EB8E70;
        }
        td.score-0 {
            background-color:#E67C73;
        }

        @media (min-width:800px) {
            .filters {
                position:fixed;
                top:0;
                left:0;
                width:300px;
                height:100%;
                border-right:1px solid #ededed;
                overflow-y:scroll;
                background:#fbfbfb;
            }
            body {
                padding-left:300px;
            }
            .top-bar {

                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:5em;
                border-bottom:1px solid #ededed;
                background:#fff;
                overflow:hidden;
                z-index:2;
            }
            table {
                margin-top:0;
            }
        }
        @media (max-width:800px) {
            .filters {
                padding-top: 60px;
            }
        }
    </style>
    <script>
        function windowSize() {
            var e = window, a = 'inner';
            if (!('innerWidth' in window )) {
                a = 'client';
                e = document.documentElement || document.body;
            }
            return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
        }
        var windowHeight=windowSize().height;
        setTimeout(lazyload,100);
        function lazyload() {
            $('.lazyload').each(function() {
                objectTop=$(this).offset().top;
                if(objectTop.length==0) {
                    return;
                }
                scrollTop=$(window).scrollTop();
                edge=scrollTop+windowHeight+1000;
                if(objectTop==scrollTop) {
                    // bug, wrong offset returned by objects, is same as scroll pos
                    // probably means image isn't initiated by browser yet
                    return;
                }
                if(
                    objectTop
                    <
                    edge
                ) {
                    url=$(this).data('src');
                    console.log(url);
                    $(this).prop('src',url);
                    $(this).removeClass('lazyload');
                    $(this).addClass('lazyloaded');
                }
            })
        }
        lazyload();
        var lazyloadItemTimeout=setTimeout(function() {},0);

        $(function() {
            resizeTable();

            $(window).scroll(function() {
                clearTimeout(lazyloadItemTimeout);
                lazyloadItemTimeout=setTimeout(function() {
                    lazyload()
                },250);
            });
            // $('body').on('click','table thead th',function() {
            //     window.location.href='/?by='+$(this).data('sort')+'&asc=1';
            // });
            $('.filters button').bind('click',function(){
                if($(this).data('mutually-exclusive')=='yes') {
                    if($(this).hasClass('active')) {
                        $(this).removeClass('active');
                    }
                    else {
                        $('.filters button[data-key="'+$(this).data('key')+'"]').removeClass('active');
                        $(this).addClass('active');
                    }
                }
                else {
                    $(this).toggleClass('active');
                }
                makeFilters();
            });
        });

        function makeFilters() {
            filters=[];
            $('.filters .active').each(function() {
                filters.push({
                    key:$(this).data('key'),
                    operator:$(this).data('operator'),
                    value:$(this).data('value')}
                );
                console.log(filters);
            });

            filters.push({
                    country:$('#dropdown-list').val()
                }
            );

            $.ajax({
                url: '<?php echo $ini['controller_url']; ?>',
                type:'POST',
                dataType:'html',
                data:{
                    filters:JSON.stringify(filters)
                },
            }).done(function(reply) {
                // done
                console.log('done');
                $('table tbody').html(reply);
                resizeTable();
                lazyload();
            });
        }
        function resizeTable() {
            var i=1;
            $('table thead th').each(function() {
                if($(this).hasClass('logo')) {
                    $(this).css('max-width','50px');
                    $(this).css('min-width','50px');
                    $(this).css('width','50px');
                    $('table tr td:nth-child('+i+')').css('min-width','50px');
                    $('table tr td:nth-child('+i+')').css('width','50px');
                    $('table tr td:nth-child('+i+')').css('max-width','50px');
                    $('table tr td:nth-child('+i+')').css('overflow','hidden');
                    i++;
                    return;
                }
                $(this).css('min-width',$(this).width()+'px');
                $(this).css('width',$(this).width()+'px');
                $(this).css('max-width',$(this).width()+'px');
                $(this).css('overflow','hidden');
                $('table tr td:nth-child('+i+')').css('min-width',$(this).width()+'px');
                $('table tr td:nth-child('+i+')').css('width',$(this).width()+'px');
                $('table tr td:nth-child('+i+')').css('max-width',$(this).width()+'px');
                $('table tr td:nth-child('+i+')').css('overflow','hidden');
                i++;
            });
            $('table thead').css('width','100%');
            $('table thead').css('top','0');
            $('table thead').css('position','fixed');
            $('table').css('margin-top',$('table thead').height()-1);
            // $('table thead').css('top','5em');
        }
    </script>

    <title>
        Hotel List
    </title>

    <div class="container">
        <div class="filters">
            <?php
            if($dropdown_array) {
                echo '<div class="group">'
                    . 'Choose ' . $head_table[$dropdown_columns[0]]['title']
                    . '<select id="dropdown-list" form="data">';
                        foreach ($dropdown_array as $dropdown_item) {
                            echo '<option value="' . $dropdown_item . '">' . $dropdown_item . '</option>';
                        }
                    echo '</select>
                </div>
                <div class="divider"></div>';
            }
            ?>

            <?php
            foreach ($head_table as $key => $head_column) {
                if($head_column['type_of'] == 'integer') {
                    echo '<div class="group">'
                        . $head_column['title'] . ':';

                    $max_value = 1;

                    foreach($spreadsheet_data as $line ) {
                        if($max_value < $line[$key])   {
                            $max_value = $line[$key];
                        }
                    }

                    $middle_data = (int)($max_value * 0.8);
                    $max_range = ($middle_data + 1) . '-' . $max_value;
                    $min_range = '1-' . $middle_data;

                    $data_key = $head_column['name'];
                    echo '<button data-key="' . $data_key . '" data-operator="range" data-value="' . $max_range . '">Max</button>';
                    echo '<button data-key="' . $data_key . '" data-operator="range" data-value="' . $min_range . '">Min</button>';

                    echo '</div><div class="divider"></div>';
                }

                if($head_column['type_of'] == 'float') {
                    echo '<div class="group">'
                    . $head_column['title'] . ':';

                    $data_key = $head_column['name'];
                    $step = 10;
                    $max_value = 90;

                    if($data_key == 'new-min-points') {
                        $step = 5;
                        $max_value = 95;
                    }

                    for($range = 5; $range <= $max_value; $range += $step) {
                        $data_value = $range . '-' . ($range + $step);

                        if($range == 5 && $step != 5) {
                            $data_value = $range . '-' . ($range + 5);
                            $range -= 5;
                        }

                        echo '<button data-key="' . $data_key . '" data-operator="range" data-value="' . $data_value . '">'
                            . $data_value
                            . '</button>';
                    }
                    echo '</div><div class="divider"></div>';
                }
            }
            ?>
        </div>
    </div>

    <div style="overflow-x:scroll;position:relative;">
        <table cellpadding="1" id="hotels">
            <thead>
            <?php
            $i = 0;
            foreach($head_table as $head_column ) {
                if($head_column['type_of'] != 'link') {
                    echo '<th onclick="sortTable(' . $i  . ')">' . $head_column['title'] . '</th>';
                    $i++;
                }
            }
            ?>
            </thead>
            <tbody>
            <?php
                if(isset($dropdown_columns[0])) {
                    echo '<tr><td style="text-align: center; font-size: 18px;"><br>Please, choose ' . $head_table[$dropdown_columns[0]]['title'] .'<br></td></tr>';
                }
                else {
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
            ?>
            </tbody>
        </table>
    </div>

    <script>
        $("#dropdown-list").change(function(){
            filters=[];
            $('.filters .active').each(function() {
                filters.push({
                    key:$(this).data('key'),
                    operator:$(this).data('operator'),
                    value:$(this).data('value')}
                );
                console.log(filters);
            });

            filters.push({
                    country:$('#dropdown-list').val()
                }
            );


            $.ajax({
                url: '<?php echo $ini['controller_url']; ?>',
                type:'POST',
                dataType:'html',
                data:{
                    filters:JSON.stringify(filters)
                },
            }).done(function(reply) {
                // done
                console.log('done');
                $('table tbody').html(reply);
                resizeTable();
                lazyload();
            });
        });
    </script>
    <script>
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("hotels");
            switching = true;
            // Set the sorting direction to ascending:
            dir = "asc";
            /* Make a loop that will continue until
            no switching has been done: */
            while (switching) {
                // Start by saying: no switching is done:
                switching = false;
                rows = table.rows;
                /* Loop through all table rows (except the
                first, which contains table headers): */
                for (i = 1; i < (rows.length - 1); i++) {
                    // Start by saying there should be no switching:
                    shouldSwitch = false;
                    /* Get the two elements you want to compare,
                    one from current row and one from the next: */

                    if(<?php
                        foreach ($link_columns as $key => $link_column) {
                            if($key != 0) {
                                echo ' || ';
                            }
                            echo 'n == ' . ($link_column - ($key+1));
                        }
                        ?>) {
                        x = rows[i].getElementsByTagName("TD")[n];
                        x = x.getElementsByTagName("A")[0];
                        y = rows[i + 1].getElementsByTagName("TD")[n];
                        y = y.getElementsByTagName("A")[0];
                    }
                    else {
                        x = rows[i].getElementsByTagName("TD")[n];
                        y = rows[i + 1].getElementsByTagName("TD")[n];
                    }

                    /* Check if the two rows should switch place,
                    based on the direction, asc or desc: */
                    if (dir == "asc") {

                        if(<?php
                            foreach ($number_columns as $key => $number_column) {
                                if($key != 0) {
                                    echo ' || ';
                                }
                                echo 'n == ' . $number_column;
                            }
                            ?>) {
                            if (Number(x.innerHTML) > Number(y.innerHTML)) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                        else {
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        }
                    } else if (dir == "desc") {
                        if(<?php
                            foreach ($number_columns as $key => $number_column) {
                                if($key != 0) {
                                    echo ' || ';
                                }
                                echo 'n == ' . $number_column;
                            }
                            ?>) {
                            if (Number(x.innerHTML) < Number(y.innerHTML)) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                        else {
                            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                }
                if (shouldSwitch) {
                    /* If a switch has been marked, make the switch
                    and mark that a switch has been done: */
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    // Each time a switch is done, increase this count by 1:
                    switchcount ++;
                } else {
                    /* If no switching has been done AND the direction is "asc",
                    set the direction to "desc" and run the while loop again. */
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
</html>
