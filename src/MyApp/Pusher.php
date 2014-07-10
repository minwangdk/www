<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use \PDO;
use Database;

class Pusher implements WampServerInterface {

    protected $pops;
    protected $users;
    protected $newDB;
    protected $db;
    public function statsArray($popid) {
        $pop = $this->pops[$popid];
        return array(
            "price"             => $pop["price"             ],
            "dailyChange"       => $pop["dailyChange"       ],
            "percent"           => $pop["percent"           ],
            "todaysRangeLow"    => $pop["todaysRangeLow"    ],
            "todaysRangeHigh"   => $pop["todaysRangeHigh"   ],
            "popularity"        => $pop["popularity"        ],
            "totalStocks"       => $pop["totalStocks"       ]
        );
    }

    public function __construct() {

        $this->newDB = new Database;
        $this->db = $this->newDB->db;
        $this->pops = array(
            "1"  => array(
                "name"  => "gangnamstyle",
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

                "dailyChange"       => 22.99,
                "percent"           => 12.99,
                "todaysRangeLow"    => 333.99,
                "todaysRangeHigh"   => 666.99,
                "popularity"        => '70,000,000,000.00',
                "totalStocks"       => '800,000,000'
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

        $this->users = array(
            
        );

    }
    public function onOpen(ConnectionInterface $conn) {        
        echo "New connection:\t[Client #" . $conn->resourceId . "].\n"; 
        // $conn->Session->set('wampstatus', 'wampiscool');
        // var_dump($conn->Session->getFlashBag()->get('notice', array()));
        // var_dump($conn->Session->all()); 
    }    
    public function onSubscribe(ConnectionInterface $conn, $topic) {  
        $popid = $topic->getID(); //gangnamstyle       
        echo "Subscribe:\t[Client #" . $conn->resourceId . "] [Topic: " . $popid . "].\n";
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // maybe chat
        $conn->close();
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        $popid = $topic->getId(); 

        echo "RPC:\t\t[Client #" . $conn->resourceId . "] [Topic: " . $topic . "]\n\t\t[Function: " . $params[0] . "] [Subfunction: " . $params[1] . "].\n";
        
        switch ($popid) 
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
                                $broadcastContent = array(
                                    "popstats"     =>   $this->statsArray($popid)
                                );
                                $topic->broadcast($broadcastContent);     
                                //return something to caller
                                return $conn->callResult($id, array('result' => 'it worked'));
                            break;

                            case "sell":

                            break;
                        }     
                    break;

                    case "fetch":          
                        $userSess = $conn->Session->all();
                        $thisuser;

                        if (array_key_exists($userSess["userid"], $this->users))
                        {   
                            
                            $thisuser = &$this->users[$userSess["userid"]];
                            echo "key alrdy exists \n";
                            print_r($this->users);

                            
                            

                        }else{
                            echo "key does not alrdy exist \n";
                            $this->users[$userSess["userid"]] = array();  
                            $thisuser = &$this->users[$userSess["userid"]];
                        

                            // if ($userSess["stocks"][$popid]) 
                            // {
                            //     $this->users[$userSess["userid"]]
                            //         ["stocks"]
                            //             [$popid]  =  $userSess["stocks"][$popid];                                
                            // }  


                    
                            //HERE SET PREPARED STATEMENTS WITH PLACEHOLDERS

                            //get owned pops by userid from session
                            try
                            {
                                
                                $userid = $userSess["userid"];
                                $sql = $this->db->prepare("SELECT pops_id, quantity FROM users_own_pops WHERE users_id = '$userid'");
                                $sql -> execute();
                                $pops = $sql->fetchAll(PDO::FETCH_KEY_PAIR);
                                echo "pops: \n";
                                print_r($pops);
                            }

                            catch(PDOException $e)
                            {
                                echo $e->getMessage();
                            }

                            //get popcorn by userid from session
                            try
                            {
                                $userid = $userSess["userid"];
                                $sql = $this->db->prepare("SELECT popcorn FROM users WHERE id = '$userid'");
                                $sql -> execute();
                                $popcorn = $sql->fetch();
                                echo "popcorn: \n";
                                print_r($popcorn);
                            }
                            catch(PDOException $e)
                            {
                                echo $e->getMessage();
                            }

                            $thisuser["popcorn"] = $popcorn["popcorn"];
                            $thisuser["ownedpops"] = $pops;

                        } 


                        $callResult = array(
                            "popstats"     =>   $this->statsArray($popid),
                            "userstats"    =>   array(
                                "popcorn"       =>  $thisuser["popcorn"],
                                "ownedpops"     =>  $thisuser["ownedpops"])
                        );
                        echo "callresult: \n";
                        print_r($callResult);

                        echo "thisuser var: \n";
                        print_r($thisuser);
                        return $conn->callResult($id, $callResult);                       
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