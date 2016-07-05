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
        foreach ($paragraphs as $paragraph)
        {
            if(strcmp($paragraph->nodeValue, "Historical US Inflation Rates"))
            {
                echo '<p>FOUND TRUE VALUE<p>';
            }
            echo $paragraph->nodeValue, PHP_EOL;
            
            echo $paragraph->getAttribute('href');
            echo '<html>\r\n</html>';
        }
        // put your code here
        ?>
    </body>
</html>
