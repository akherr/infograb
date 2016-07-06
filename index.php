
<?php
//Austin Herr - July 2016 - DC Energy Code Screening

$url = 'http://www.usinflationcalculator.com/inflation/';
$curl = curl_init();

//set option for curl to be a URL
curl_setopt($curl, CURLOPT_URL, $url); 
//set the curl to return as string
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
//execute and store in $content
$content = curl_exec($curl);
//close the curl
curl_close($curl);
//libxml_use_internal_errors(true);
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
        $pageurl = $anchor->getAttribute('href');
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

$minyear = 0;
$maxyear = 0;
$yearAvgArray = [];

//echo '<p>This page contains data from JAN ' . $minyear . ' to DEC ' . $maxyear;
foreach($table->getElementsByTagName('tr') as $tr)
{

    // get the columns in this row
    $tds = $tr->getElementsByTagName('td'); 
    if(($tds->length == 13) and (is_numeric($tds->item(12)->nodeValue)))
    {
        //get the current year from the th tag for the row
        $currentyear = $tr->getElementsByTagName('th')->item(0)->nodeValue;
        if(($minyear > $currentyear) or ($minyear == 0))
        {
            $minyear = $currentyear;
        }
        elseif(($maxyear < $currentyear) or ($minyear == 0))
        {
            $maxyear = $currentyear;
        }
        $yearAvgArray[$currentyear] = $tds->item(12)->nodeValue;
    }
}
echo $description, PHP_EOL;
echo 'This page contains data from JAN ' . $minyear . ' to DEC ' . $maxyear . PHP_EOL;
echo '#------------------#' . PHP_EOL;
echo '| year | inflation |' . PHP_EOL;
echo '#------------------#' . PHP_EOL;
foreach ($yearAvgArray as $key => $value) 
{
    $valuesize = strlen($value);
    echo '| ' . $key . ' |   ' . $value . str_repeat(' ', 8-$valuesize) . '|' . PHP_EOL;
}
echo '#------------------#' . PHP_EOL;
//echo '<p> Size Of Array: ' . sizeof($yearAvgArray) . '</p>';
//echo '<p>This page contains data from JAN ' . $minyear . 'to DEC ' . $maxyear;
//echo '<p> Min Year: ' . $minyear . '</p>';
//echo '<p> Max Year: ' . $maxyear . '</p>';
//echo '<p>Description:</p>' . $description;
//echo '<p>URL:</p>' . $pageurl;
?>