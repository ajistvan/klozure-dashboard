<?php    
function check_token($token) {
    if (isset($token)){
        $mygtoken = json_decode($token, TRUE);
        $check_url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$mygtoken['access_token'];
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" 
            )
        );
        $context = stream_context_create($opts);
        $token_check= file_get_contents($check_url, FALSE, $context); //get the xml data for the worksheet
    }
    else {$token_check = 'No Token';}
    
    if (strlen($token_check) >= 100){return TRUE;}
    else {return FALSE;}
}

function get_sheet_data($sheetauth) {
    
    //decode JSON token
    $mygtoken = json_decode($sheetauth, TRUE);
    
    // build url
    $gshtkey = '1uxcO2KCUJ77As0vnzD55A1PcO6NOb6B_289ycMiOvyM'; //key from the klozure "Daily Digits" Google Spreadsheet
    $gwkshtid = 'od6'; //worksheet id for the worksheet containing the final caclulated data
    $url= 'https://spreadsheets.google.com/feeds/cells/'.$gshtkey.'/'.$gwkshtid.'/private/values?token_type=bearer&access_token='.$mygtoken['access_token'];
    
    //Create Fetch Context
    $opts = array(
        'http'=>array(
            'method'=>"GET",
            'header'=>"Accept-language: en\r\n" 
        )
    );
    $context = stream_context_create($opts);
    $google_sheet= file_get_contents($url, FALSE, $context); //get the xml data for the worksheet

    //next we parse the data into an array
    //for each cell with an entry an array element will be created with the format [row, col, value]
    $cells_xml = new SimpleXMLElement($google_sheet);
    $i = 0;
    $google_cells = array();
    foreach ($cells_xml->entry as $tempentry){
        $rownum = (string) $tempentry->children('gs', true)->attributes()->row;
        $colnum = (string) $tempentry->children('gs', true)->attributes()->col;
        $tempvalue = (string) $tempentry->children('gs', true);
        $google_cells[$i] = array('row' => $rownum, 'col' => $colnum, 'value' => $tempvalue);
        $i++;
    }
    return $google_cells;
}

function build_dash_widget_array($cell_array){
    $dash_widget_data = array();
    $ci = 0;
    $w = 0;
    $ci_end = count($cell_array)-1;
    while ($ci <= $ci_end){
        if ($cell_array[$ci]['row'] == 1 && $cell_array[$ci]['col'] == 1){$dash_widget_data['page-title'] = $cell_array[$ci]['value'];}
        if ($cell_array[$ci]['col'] == 1){
            if ($cell_array[$ci]['col'] == 1 && $cell_array[$ci]['value'] == 'SectionTitle'){
                $w++;
                $m = 0;
                $cin = $ci + 1;
                $dash_widget_data[$w]['widget-title'] = $cell_array[($cin)]['value'];
                $cit = $ci + 2;
                $dash_widget_data[$w]['widget-type'] = $cell_array[($cit)]['value'];
            }
            elseif ($cell_array[$ci]['col'] == 1 && $cell_array[$ci]['row'] != 1 && $cell_array[$ci]['value'] != 'SectionTitle'){
                $cin = $ci + 1;
                $cit = $ci + 2;
                $citr= $ci + 3;
                $cirg= $ci + 4;
                $dash_widget_data[$w]['metrics'][$m]['metric-title'] = $cell_array[$ci]['value'];
                $dash_widget_data[$w]['metrics'][$m]['metric-value'] = $cell_array[$cin]['value'];
                if ($cell_array[$cit]['row'] == $cell_array[$ci]['row']) {$dash_widget_data[$w]['metrics'][$m]['metric-target'] = $cell_array[$cit]['value'];}
                if ($cell_array[$citr]['row'] == $cell_array[$ci]['row']) {$dash_widget_data[$w]['metrics'][$m]['metric-trend'] = $cell_array[$citr]['value'];}
                if ($cell_array[$cirg]['row'] == $cell_array[$ci]['row']) {$dash_widget_data[$w]['metrics'][$m]['metric-redgreen'] = $cell_array[$cirg]['value'];}
                $m++;
            }
        }
        $ci++;
    }
    //echo json_encode($dash_widget_data); 
    //echo "<br>";
    //echo json_encode($cell_array); 
    return $dash_widget_data;
}

function show_widgets($dash_widget_data){
    foreach ($dash_widget_data as $widgetarray){
        if (isset($widgetarray['widget-title']) && $widgetarray['widget-type'] == 'standard'){
            echo "<div class='widget' id='".$widgetarray['widget-title']."'>";
            echo "<h2>".$widgetarray['widget-title']."</h2>";
            echo "<table>";
            foreach($widgetarray['metrics'] as $metricarray){
                echo "<tr><td>".$metricarray['metric-title']."</td><td>".$metricarray['metric-value']."</td></tr>";
            }
            echo "</table></div>";
        }
        if (isset($widgetarray['widget-title']) && $widgetarray['widget-type'] == 'targetandtrend'){
            echo "<div class='widget ".$widgetarray['widget-type']."' id='".$widgetarray['widget-title']."'>";
            echo "<h2>".$widgetarray['widget-title']."</h2>";
            echo "<table>";
            foreach($widgetarray['metrics'] as $metricarray){
                echo "<tr><td class=".$metricarray['metric-redgreen'].">".$metricarray['metric-title']."</td><td>".$metricarray['metric-value']."</td><td>".$metricarray['metric-target']."</td><td>".$metricarray['metric-trend']."</td></tr>";
            }
            echo "</table></div>";
        }
    }
}