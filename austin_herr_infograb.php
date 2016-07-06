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
//catch xml errors
libxml_use_internal_errors(true);
//instantiate new DOMDocument
$dom = new DOMDocument();
//load the retrieved html into $content
$dom->loadHTML($content);
//grab all of the anchor tags for processing
$anchors = $dom->getElementsByTagName('a');
//grab all of the paragraph tags for processing
$paragraphs = $dom->getElementsByTagName('p');
//variable for the next page's URL
$pageurl;
//variable for the description
$description;
foreach ($anchors as $anchor)
{
    //does this anchor tag equal what we need?
    if(strcmp($anchor->getAttribute('title'), "Historical Inflation Rates: 1914-2008") == 0)
    {
        //store the url in $pageurl
        $pageurl = $anchor->getAttribute('href');
    }
}
//boolean for conditional within loop
$found = false;
foreach($paragraphs as $paragraph)
{
    //is found true?
    if($found)
    {
        //set the $description to the paragraph
        $description = $paragraph->nodeValue;
        $found = false;
    }
    //for the purposes of this screening, I decided to search for the paragraph that started
    //   "Historical Inflation Rates: 1914 to Current" and got the next paragraph after that
    //if found, the $found variable would be true, and the previous conditional would run
    if(strcmp($paragraph->nodeValue,"Historical Inflation Rates: 1914 to Current") == 0)
    {
        $found = true;
    }
}

//need to set up the curl from before, but with a different URL
$curl = curl_init();  
curl_setopt($curl, CURLOPT_URL, $pageurl);  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
$content = curl_exec($curl);
$source = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($content);

//grab the first table from the DOM, hopefully there aren't any other ones!
$table = $dom->getElementsByTagName('table')->item(0);

//need to have separate variables for min and max year
$minyear = 0;
$maxyear = 0;
//array of year and average results
$yearAvgArray = [];

//go through the table and for each table row...
foreach($table->getElementsByTagName('tr') as $tr)
{

    //get the columns in this row
    $tds = $tr->getElementsByTagName('td'); 
    //we are looking for rows that only have 13 tds (not the table headers)
    // and only the rows that have a value in the 13th (item 12) row that is numeric
    // a numeric node value would mean there is an average for that year, meaning
    // it is a full year
    if(($tds->length == 13) and (is_numeric($tds->item(12)->nodeValue)))
    {
        //get the current year from the th tag for the row
        $currentyear = $tr->getElementsByTagName('th')->item(0)->nodeValue;
        //find the minimum year within the table
        if(($minyear > $currentyear) or ($minyear == 0))
        {
            $minyear = $currentyear;
        }
        //find the maximum year within the table
        elseif(($maxyear < $currentyear) or ($minyear == 0))
        {
            $maxyear = $currentyear;
        }
        //add the average to the table with the key being the year
        $yearAvgArray[$currentyear] = $tds->item(12)->nodeValue;
    }
}
//sort the array in descending order
arsort($yearAvgArray);

//print the description
echo $description, PHP_EOL;
//print the min year and max year, with the months...
//  months are hard coded as we are not printing any non-full years
echo 'This page contains data from JAN ' . $minyear . ' to DEC ' . $maxyear . PHP_EOL;
echo '#------------------#' . PHP_EOL;
echo '| year | inflation |' . PHP_EOL;
echo '#------------------#' . PHP_EOL;
//for each element in the array
foreach ($yearAvgArray as $key => $value) 
{
    //get the size of the value for white space printing purposes
    $valuesize = strlen($value);
    //print the key (year) and the value (avg) with white space that aligns to the right
    //  8 is the magic number!
    echo '| ' . $key . ' |   ' . $value . str_repeat(' ', 8-$valuesize) . '|' . PHP_EOL;
}
echo '#------------------#' . PHP_EOL;
?>