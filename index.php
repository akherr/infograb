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
        $found;
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
        $rows = $dom->getElementByTagName('tr');
        
        foreach ($rows as $row) 
        {
            $cols = $row->getElementsByTagName('td');
            echo $cols[2];
        }
        
        
        echo '<p>Description:</p>' . $description;
        echo '<p>URL:</p>' . $pageurl;
        ?>
    </body>
</html>
