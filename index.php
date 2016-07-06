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
        $paragraphs = $dom->getElementsByTagName('a');
        $pageurl;
        foreach ($paragraphs as $paragraph)
        {
            if(strcmp($paragraph->getAttribute('title'), "Historical Inflation Rates: 1914-2008") == 0)
            {
                echo '<p>FOUND TRUE VALUE<p>';
                echo $paragraph->getAttribute('href');
                echo $paragraph->nodeValue, PHP_EOL;
                
            }
        }
        ?>
    </body>
</html>
