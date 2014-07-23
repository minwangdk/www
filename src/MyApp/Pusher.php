<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\WampServerInterface;
use \PDO;
use Database;
use React\EventLoop\LoopInterface;

class Pusher implements WampServerInterface, MessageComponentInterface
{   
    private $loop;
    private $clients;

    protected $pops;
    protected $users;
    protected $newDB;
    protected $db;
    public function statsArray($popid) {
        if (array_key_exists($popid, $this->pops)) {
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
    }
    
    protected $fetchOwnedPops;
    protected $fetchPopcorn;

    public function __construct(LoopInterface $loop) {
        

        /**
         * Pass in the react event loop here
         */
        $this->loop = $loop;
        $this->clients = array();

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

        $this->users = array();

        //periodic write to database / garbage collection of user array and pop array
        // $this->loop->addTimer(59, function() {
            
        //     $this->users foreach as user{
        //        if (time() - user[LASTACTIVITY] > 1800) {
        //             user send to db;
        //             user->delete;
        //        }
        //     }

        // });

        // $this->loop->addTimer(59, function() {
        //     $this->pops foreach as pop {
        //         take last value add to bigger scope ie. 1min to 1hour
        //         send to db

        //     }
        // });

        //PDO prepare statements for onCall
        try 
        {
        $this->fetchOwnedPops = $this->db->prepare(
            "   SELECT pops_id, quantity 
                FROM users_own_pops 
                WHERE users_id = :userid ");

        $this->fetchPopcorn = $this->db->prepare(
            "   SELECT popcorn
                FROM users_own_currency
                WHERE users_id = :userid ");
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }        
    }

    public function onOpen(ConnectionInterface $conn) {
        //val
        $userSess = $conn->Session->all();  
         

        // if (array_key_exists($userSess["userid"], $this->users)) {
        //     //relationship: $thisuser = userarray[userid] = session userid
        //     $thisuser = &$this->users[$userSess["userid"]];

        //     array_push($thisuser['Connections'], $conn);

        // } else if ($this->clients[$conn->resourceId]['conn']) {


        // } else {
       
        // $this->clients[$conn->resourceId]['conn'] = $conn;

        echo "New connection:\t[Client #" . $conn->resourceId . "]\t" .
            (isset($userSess) ? "[UserID: ".$userSess['username']."] \n" : "\n")
            
        ;


            
          
        // }
    }    
    public function onSubscribe(ConnectionInterface $conn, $topic) {  
        $userSess = $conn->Session->all();
        //add usersessid to userarray
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
                                // HERE DO THE DAMN DB AND ACTUAL BACKEND BUY SELL

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

                        //if userid already exists in userarray, $thisuser = userarray[userid]
                        if (array_key_exists($userSess["userid"], $this->users))
                        {   
                            $thisuser = &$this->users[$userSess["userid"]];

                            //debug
                            echo "key already exists \n";
                            print_r($this->users);
                        }
                        else //else load user data into userarray from db
                        {
                            //debug
                            echo "key does not already exist \n";

                            //relationship: $thisuser = userarray[userid] = session userid
                            $this->users[$userSess["userid"]] = array();  
                            $thisuser = &$this->users[$userSess["userid"]];
                           
                            //fetchOwnedPops and fetchPopcorn prepare statements are in _construct
                            try
                            {                  
                                //get owned pops by userid from session                 
                                $this->fetchOwnedPops -> execute(array(':userid' => $userSess["userid"]));
                                $ownedpops = $this->fetchOwnedPops -> fetchAll(PDO::FETCH_KEY_PAIR);

                                //debug
                                echo "pops: \n";
                                print_r($ownedpops);
                            
                                //get popcorn by userid from session
                                $this->fetchPopcorn -> execute(array(':userid' => $userSess["userid"]));
                                $popcorn = $this->fetchPopcorn -> fetch();

                                //debug
                                echo "popcorn: \n";
                                print_r($popcorn);
                            }
                            catch(PDOException $e)
                            {
                                echo $e->getMessage();
                            }
                            //pass db data to userarray
                            $thisuser["popcorn"] = $popcorn["popcorn"];
                            $thisuser["ownedpops"] = $ownedpops;
                        } 
                        // return data to caller
                        $callResult = array(
                            "popstats"     =>   $this->statsArray($popid),
                            "userstats"    =>   array(
                                "popcorn"       =>  $thisuser["popcorn"],
                                "ownedpops"     =>  $thisuser["ownedpops"])
                        );

                        //debug
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
    public function onMessage(ConnectionInterface $from, $msg) {

    }
    public function onClose(ConnectionInterface $conn) {

        // HERE RUN GARBAGE COLLECTION ON USER ARRAY
    }       
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }    
}