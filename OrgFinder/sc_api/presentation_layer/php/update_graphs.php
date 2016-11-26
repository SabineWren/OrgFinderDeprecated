<?php 

namespace thalos_api; 
use PDO;

require_once(__DIR__.'/../../database_layer/DBInterface.php');

function CreateChart($data, $dates, $filename)
{
    include_once(__DIR__."/pChart/class/pDraw.class.php"); 
    include_once(__DIR__."/pChart/class/pImage.class.php"); 
    include_once(__DIR__."/pChart/class/pData.class.php");

    $Data = new \pData(); 
    $Data->addPoints($data, 'data');
    $Data->setAxisDisplay(0,AXIS_FORMAT_METRIC);
    $Data->setSerieWeight('data', 2);
    $serieSettings = array("R"=>0,"G"=>250,"B"=>255,"Alpha"=>75);
    $Data->setPalette("data",$serieSettings);

    $Data->addPoints($dates, 'dates');
    $Data->setAbscissa('dates');
    $Data->setXAxisDisplay(AXIS_FORMAT_DATE, 'd');

    $myPicture = new \pImage(500,150,$Data,true);

    $myPicture->setFontProperties(array("FontName"=>__DIR__."/pChart/fonts/Forgotte.ttf","FontSize"=>18));
    $myPicture->setGraphArea(0,15,490,147);
    $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>255,"G"=>255,"B"=>255,"Alpha"=>5));
    $myPicture->drawScale();
    $myPicture->drawAreaChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_MANUAL,"DisplayR"=>255,"DisplayG"=>255,"DisplayB"=>255,'DisplayOffset'=>5));

    $myPicture->render(__DIR__.'/../images/tmp/'.$filename);
}

function GetUniqueRecordsChart($query_string, $filename, $data = null)
{
    $offset = (60*60*24); // One day in seconds
    $current_day = 6; // 7 days ago
    $dates = array();

    if($data == null)
    {
        $data = array();

        $DB = new DBInterface();
        $DB->Connect();

        while($current_day >= 0)
        {
            $time = time() - ($offset * $current_day);

            $query = $DB->db->prepare($query_string);
            $query->bindParam(':time',$time);
            $query->execute();
            $count = $query->fetch();

            $data[] = $count[0];
            $dates[] = $time;

            $current_day--;
        }
    }
    else
    {
        while($current_day >= 0)
        {
            $time = time() - ($offset * $current_day);
            $dates[] = $time;
            $current_day--;
        }
    }

    CreateChart($data, $dates, $filename);

    return $data;
}

GetUniqueRecordsChart(
        'SELECT COUNT(DISTINCT ip) FROM api_users WHERE date_first_seen <= :time',
        'unique_users.png');

$unique_orgs_data = GetUniqueRecordsChart(
        'SELECT COUNT(DISTINCT id) FROM organizations_rsi WHERE date_added <= :time',
        'unique_orgs.png');

$unique_accts_data = GetUniqueRecordsChart(
        'SELECT COUNT(DISTINCT id) FROM accounts_rsi WHERE date_added <= :time',
        'unique_accts.png');

$total_orgs_data = GetUniqueRecordsChart(
        'SELECT COUNT(entry_id) FROM organizations_rsi_info WHERE scrape_date <= :time',
        'total_orgs.png');

$total_accts_data = GetUniqueRecordsChart(
        'SELECT COUNT(entry_id) FROM accounts_rsi_info WHERE scrape_date <= :time',
        'total_accts.png');

$unique_total_data = array();
foreach($unique_orgs_data as $index=>$item)
{
    $unique_total_data[]  = $item + $unique_accts_data[$index];
}
GetUniqueRecordsChart(null,'unique_total.png',$unique_total_data);


$total_total_data = array();
foreach($total_orgs_data as $index=>$item)
{
    $total_total_data[]  = $item + $total_accts_data[$index];
}
GetUniqueRecordsChart(null,'total_total.png',$total_total_data);
