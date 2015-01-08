<?php
require '../vendor/autoload.php';

use Parse\ParseClient;
use Parse\ParseQuery;
use Parse\ParseCloud;
use Parse\ParseObject;
use ProgressBar\Manager;
use ProgressBar\Registry;

$parse_settings = require('../app/config/parse.php');
ParseClient::initialize($parse_settings['application_id'], $parse_settings['rest_api'], $parse_settings['master']);

$start  = new DateTime();

function getAllRecords($className, $loopCount=0, $result=array())
{
    $limit = 100;
    $query = new ParseQuery($className);
    $query->limit($limit);
    $query->skip($limit * $loopCount);
    try{
        
        $query_result = $query->find();
        if(count($query_result) > 0)
        {
            for($i = 0; $i < count($query_result); $i++)
            {
                $object = $query_result[$i];
                $result[] = $object;
            }

            $loopCount++;
            $result = getAllRecords($className, $loopCount, $result);
        }
        else
        {
            return $result;
        }
        
    } catch (ParseException $ex) {
        echo "error!" . print_r($ex);
    }
    
    return $result;
}

$sets = getAllRecords('sets');
$total_sets = count($sets);
$current_set = 0;
echo "Sets fetched.\n";

foreach($sets as $set)
{
    $object = $set;
    $set_name = $object->get('code');
    $url = 'http://mtgjson.com/json/' . $set_name . '.json';
    $contents = file_get_contents($url);
    $set_content = json_decode($contents, true);
    
    $current_set++;
    echo 'Saving ' . $object->get('name') . " ($current_set/$total_sets) \n";

    $progress = 0;
    $progressBar = new Manager(0, count($set_content['cards']));
    foreach($set_content['cards'] as $row)
    {
        $card = new ParseObject('Cards');
        $card->set('set', $set_name);
        foreach($row as $property => $value)
        {
            if(is_array($value))
            {
                $value = implode(',', $value);
            }

            $card->set($property, $value);
        }
        $card->save();
        $progress++;
        $progressBar->update($progress);
    }
}

$end = new DateTime();
$diff = $start->diff( $end );
echo 'Elapsed Time: ' . $diff->format( '%H:%I:%S' ); // -> 00:25:25