
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
        //echo '<p>FOUND HREF<p>';
        $pageurl = $anchor->getAttribute('href');
        //echo $anchor->nodeValue, PHP_EOL;
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
//$rows = $tables->item(0)->getElementsByTagName('tr');

//foreach ($rows as $row) 
//{
    //$cols = $row->getElementsByTagName('td');
    //echo $cols[2];
//}
$minyear = 0;
$maxyear = 0;
$yearAvgArray = [];

//echo '<p>This page contains data from JAN ' . $minyear . ' to DEC ' . $maxyear;
foreach($table->getElementsByTagName('tr') as $tr)
{

    $tds = $tr->getElementsByTagName('td'); // get the columns in this row
    if(($tds->length == 13) and (is_numeric($tds->item(12)->nodeValue)))
    {
        //echo $tds->item(0)->nodeValue;
        //get the current year from the th tag for the row
        $currentyear = $tr->getElementsByTagName('th')->item(0)->nodeValue;
        //echo '<p>Year?: ' . $tr->getElementsByTagName('th')->item(0)->nodeValue . '</p>';
        if(($minyear > $currentyear) or ($minyear == 0))
        {
            $minyear = $currentyear;
        }
        elseif(($maxyear < $currentyear) or ($minyear == 0))
        {
            $maxyear = $currentyear;
        }
        $yearAvgArray[$currentyear] = $tds->item(12)->nodeValue;
        //echo '<div>' . $tds->item(12)->nodeValue . '</div>';
        //echo PHP_EOL;
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
echo $description, PHP_EOL;
echo 'This page contains data from JAN ' . $minyear . ' to DEC ' . $maxyear;
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