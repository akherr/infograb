<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Info Grab Results</title>
    </head>
    <body>
        <?php
        $url = 'http://www.usinflationcalculator.com/inflation/';
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $content = curl_exec($curl);
        $source = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $anchors = $dom->getElementsByTagName('a');
        $paragraphs = $dom->getElementsByTagName('p');
        $pageurl;
        $description;
        foreach ($anchors as $anchor)
        {
            if(strcmp($anchor->getAttribute('title'), "Historical Inflation Rates: 1914-2008") == 0)
            {
                echo '<p>FOUND HREF<p>';
                $pageurl = $anchor->getAttribute('href');
                echo $anchor->nodeValue, PHP_EOL;
            }
        }
        $found = false;
        foreach($paragraphs as $paragraph)
        {
            if($found)
            {
                $description = $paragraph->nodeValue;
                $found = false;
            }
            if(strcmp($paragraph->nodeValue,"Historical Inflation Rates: 1914 to Current") == 0)
            {
                echo'<p>FOUND PARAGRAPH</p>';
                $found = true;
            }
        }
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $pageurl);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        $content = curl_exec($curl);
        $source = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $table = $dom->getElementsByTagName('table')->item(0);
        //$rows = $tables->item(0)->getElementsByTagName('tr');
        
        //foreach ($rows as $row) 
        //{
            //$cols = $row->getElementsByTagName('td');
            //echo $cols[2];
        //}
        foreach($table->getElementsByTagName('tr') as $tr)
        {
            $tds = $tr->getElementsByTagName('td'); // get the columns in this row
            echo '<p>Year?: ' . $tr->getElementsByTagName('th')->item(0)->nodeValue . '</p>';
            if($tds->length == 13)
            {
                //echo $tds->item(0)->nodeValue;
                echo '<div>' . $tds->item(1)->nodeValue . '</div>';
                echo '<p>WAT</p>';
                echo PHP_EOL;
                //// check if B and D are found in column 2 and 4
                //if(trim($tds->item(1)->nodeValue) == 'B' && trim($tds->item(3)->nodeValue) == 'D')
                //{
                    // found B and D in the second and fourth columns
                    // echo out each column value
                    //echo $tds->item(0)->nodeValue; // A
                    //echo $tds->item(1)->nodeValue; // B
                    //echo $tds->item(2)->nodeValue; // C
                    //echo $tds->item(3)->nodeValue; // D
                    //break; // don't check any further rows
                //}
            }
        }
        
        echo '<p>Description:</p>' . $description;
        echo '<p>URL:</p>' . $pageurl;
        ?>
    </body>
</html>
