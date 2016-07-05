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
        
        echo '<html><p>I Am Here Now</p></html>';
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $paragraphs = $dom->getElementsByTagName('p');
        foreach ($paragraphs as $paragraph)
        {
            echo $paragraph->nodeValue, PHP_EOL;
        }
        echo $content;
        echo $source;
        // put your code here
        ?>
    </body>
</html>
