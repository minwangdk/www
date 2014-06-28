<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {

    protected $pops;
    public function statsArray($popname) {
        return array(
            "price" => $this->pops[$popname]["price"],
            "dailyChange" => $this->pops[$popname]["dailyChange"],
            "percent" => $this->pops[$popname]["percent"],
            "todaysRangeLow" => $this->pops[$popname]["todaysRangeLow"],
            "todaysRangeHigh" => $this->pops[$popname]["todaysRangeHigh"],
            "popularity" => $this->pops[$popname]["popularity"],
            "totalStocks" => $this->pops[$popname]["totalStocks"]
        );
    }

    public function __construct() {
        $this->pops = array(
            "gangnamstyle" => array(
                "price" => array(                    
                    0 => array(
                        0 => 1403775814,
                        1 => 111.99
                    ),
                    1 => array(
                        0 => 1403775815,
                        1 => 112.99
                    ),
                    2 => array(
                        0 => 1403775816,
                        1 => 113.99
                    ),
                    3 => array(
                        0 => 1403775817,
                        1 => 114.99
                    ),
                    4 => array(
                        0 => 1403775818,
                        1 => 117.99
                    ),
                    5 => array(
                        0 => 1403775819,
                        1 => 112.99
                    )
                ),      

                "dailyChange" => 22.99,
                "percent" => 12.99,
                "todaysRangeLow" => 333.99,
                "todaysRangeHigh" => 666.99,
                "popularity" => '70,000,000,000.00',
                "totalStocks" => '800,000,000'
            ),

            "justinbieber" => array(
                "name" => "Justin Bieber",   
                "price" => array(
                    "1403775814" => 111.99,
                    "1403775815" => 112.99,
                    "1403775816" => 113.99,
                    "1403775817" => 114.99,
                    "1403775818" => 117.99,
                    "1403775819" => 112.99),
                "dailyChange" => 22.99,
                "percent" => 12.99,
                "todaysRangeLow" => 333.99,
                "todaysRangeHigh" => 666.99,
                "popularity" => '70,000,000,000.00',
                "totalStocks" => '800,000,000'
            )
        );                
    }
    public function onOpen(ConnectionInterface $conn) {        
        echo "New connection:\t[Client #" . $conn->resourceId . "].\n";        
    }    
    public function onSubscribe(ConnectionInterface $conn, $topic) {  
        $popname = $topic->getID(); //gangnamstyle       
        echo "Subscribe:\t[Client #" . $conn->resourceId . "] [Topic: " . $popname . "].\n";
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // maybe chat
        $conn->close();
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        $popname = $topic->getId(); 

        echo "RPC:\t\t[Client #" . $conn->resourceId . "] [Topic: " . $topic . "]\n\t\t[Function: " . $params[0] . "] [Subfunction: " . $params[1] . "].\n";
        
        switch ($popname) 
        {   
            case 'popAdmin': //rpc for adding a new pop to topics
                switch ($params[0])
                {
                    case "add":   
                    break;

                    case "delete":
                    break;
                }
            break;

            default: //buy/sell/fetch RPC                
                switch ($params[0])
                {
                    case "buysell":  
                        switch ($params[1])   
                        {
                            case "buy":
                                //buysell function
                                //DO THE DAMN DB AND ACTUAL BACKEND 

                                //broadcast new stats to topic
                                $topic->broadcast($this->statsArray($popname));     
                                //return something to caller
                                return $conn->callResult($id, array('result' => 'it worked'));
                            break;

                            case "sell":

                            break;
                        }     
                    break;

                    case "fetch":
                        return $conn->callResult($id, $this->statsArray($popname));                         
                    break;
                }                                             
            break;
        }
    } 
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }  
    public function onClose(ConnectionInterface $conn) {
    }       
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }    
}