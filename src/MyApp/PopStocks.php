<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\WampServerInterface;
use \PDO;
use Database;
use React\EventLoop\LoopInterface;

class PopStocks extends PriceHandling implements WampServerInterface
{   
    protected $loop;

    protected $pops;
    protected $users;    
    protected $newDB;
    protected $db;

    public function popStatsArray($popid) {
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

    public function userStatsArray($thisuser) {
        if (isset($thisuser)) {
            return array(
                "currency"      =>  $thisuser["currency"],
                "ownedpops"     =>  $thisuser["ownedpops"]
            );
        }
    }
                    
    protected $fetchOwnedPops;
    protected $fetchOwnedCurr;
    protected $updateUsersPops;
    protected $updateUsersCurr;

    protected $ticker;


    public function poparrayTimer($interval = 1) 
    {
        $this->loop->addTimer($interval, function() { 
            //increment ticker
            $this->ticker += 1;

            //add price to pricesecond
            $this->addPrice('priceSecond');
            

            if ($this->ticker % 60 === 0) { //1min
                //add price to priceMinute
                $this->addPrice('priceMinute');                
                //save to DB
                $this->saveToDB('priceMinute', -1);
                //poparray trimmer
                $this->poparrayTrim('priceSecond', -60);

                //debug
                echo "pang! \n";
            }
            if ($this->ticker % 3600 === 0) { //1h
                //add price to priceHour
                $this->addPrice('priceHour');
                //save to DB
                $this->saveToDB('priceHour', -1);
                //poparray trimmer
                $this->poparrayTrim('priceMinute', -1440); //hold 24hours worth of minutedata

                //debug
                echo "Kapang! \n";
            }
            if ($this->ticker % 86400 === 0) { //24h
                //add price to priceDay
                $this->addPrice('priceDay');
                //save to DB
                $this->saveToDB('priceDay', -1);
                //poparray trimmer
                $this->poparrayTrim('priceHour', -336); //hold 14 days worth of hourdata in poparray
                //DB trimmer
                $this->trimDBPrices ('priceMinute'); //hold 2 days worth of minutedata in database


                //debug
                echo "MOKapang! \n";
            }
            
            if ($this->ticker % 604800 === 0) { //7 days
                
                //poparray trimmer
                $this->poparrayTrim('priceDay', -7); //hold 7 days worth of dailydata in poparray

                //DB trimmer
                $this->trimDBPrices ('priceHour'); //hold 2 weeks worth of hourdata in database

                //reset ticker
                $this->ticker = 0;

                //debug
                echo "Rasala! \n";                
            }

            //debug
            // echo microtime() . "...\n";   
            // print_r($this->pops["1"]["priceSecond"]);   

            // end( $this->pops['1']['priceSecond'] );
            $this->p = end( $this->pops['1']['priceSecond'] ) ;
            $this->o = key( $this->pops['1']['priceSecond'] ) ;
            reset($this->pops['1']['priceSecond']);
            echo "key#";
            print_r($this->o);
            echo " ";
            print_r($this->p);
            echo microtime() . "...\n"; 
            echo "ticker: " . $this->ticker . "\n";
            
            //set delay for next cycle (1sec), compensating for time to run the cycle
            $mtime = microtime(true);
            $skew = $mtime - floor($mtime);
            $interval = 1 - $skew;
            $this->poparrayTimer($interval);
        });
    }   
    
    public function __construct(LoopInterface $loop) 
    {    
        
        /**
         * Pass in the react event loop here
         */
        $this->loop = $loop;

        $this->newDB = new Database;
        $this->db = $this->newDB->db;

        $this->users = array();
        $this->pops = array(
            "1"  => array(
                "name"              => "Gangnam Style",
                "price"             => array(
                    0 => 1403775813,
                    1 => 110.99
                ),
                "priceSecond"       => array(                                    
                    // 0 => array(
                    //     0 => 1403775814,
                    //     1 => 111.99
                    // ),
                    // 1 => array(
                    //     0 => 1403775815,
                    //     1 => 112.99
                    // ),
                    // 2 => array(
                    //     0 => 1403775816,
                    //     1 => 113.99
                    // ),
                    // 3 => array(
                    //     0 => 1403775817,
                    //     1 => 114.99
                    // ),
                    // 4 => array(
                    //     0 => 1403775818,
                    //     1 => 117.99
                    // ),
                    // 5 => array(
                    //     0 => 1403775819,
                    //     1 => 112.99
                    // )
                ),
                "priceMinute"       => array(
                    // 0 => array(
                    //     0 => 1403775813,
                    //     1 => 110.99
                    // )                    
                ),
                "priceHour"         => array(
                ),     
                "priceDay"          => array(
                ),
                "dailyChange"       => 22.99,
                "percent"           => 12.99,
                "todaysRangeLow"    => 333.99,
                "todaysRangeHigh"   => 666.99,
                "popularity"        => '70,000,000,000.00',
                "totalStocks"       => '800,000,000'
            )
             //   ,

            // "justinbieber" => array(
            //     "name" => "Justin Bieber",   
            //     "price" => array(
            //         "1403775814" => 111.99,
            //         "1403775815" => 112.99,
            //         "1403775816" => 113.99,
            //         "1403775817" => 114.99,
            //         "1403775818" => 117.99,
            //         "1403775819" => 112.99),
            //     "dailyChange" => 22.99,
            //     "percent" => 12.99,
            //     "todaysRangeLow" => 333.99,
            //     "todaysRangeHigh" => 666.99,
            //     "popularity" => '70,000,000,000.00',
            //     "totalStocks" => '800,000,000'
            // )
        );  

        $this->ticker = 0;
        


        //PDO prepare statements
        require 'PDO_PrepareStatements.php';        

        // periodic write to database / garbage collection of user array and pop array. 2147 maxiumum
        //userarray timer
        $this->loop->addPeriodicTimer(1800, function() {

            foreach ($this->users as $userkey=>$user) {

                if (isset($user["LASTACTIVITY"]) &&
                    time() - $user["LASTACTIVITY"] > 1799) {
                    try 
                    {
                        //send ownedpops to db
                        foreach ($user["ownedpops"] as $k=>$v) {
                            $this->updateUsersPops -> execute(array(
                                ':identifier'   => $user["identifier"],
                                ':pops_id'  => $k,
                                ':quantity' => $v   
                            ));
                        }
                        //send currency to db
                        $this->updateUsersCurr -> execute(array(
                            ':identifier'   => $user["identifier"],
                            ':popcorn'      => $user["currency"]["popcorn"],
                            ':dollars'      => $user["currency"]["dollars"],
                            ':bitcoins'     => $user["currency"]["bitcoins"]
                        ));

                        // user->delete;
                        unset($this->users[$userkey]);
                        //debug
                        echo "timer function executed.\n";
                    }
                    catch(PDOException $e)
                    {
                        echo $e->getMessage();
                    } 
                }
            }

            //debug
            echo "timer running...\n";
        });

        //poparray timer initiator
        $this->poparrayTimer();
       
    }
    public function onOpen(ConnectionInterface $conn) 
    {
        //val
        $userSess = $conn->Session->all();  
         

        // if (array_key_exists($userSess["userid"], $this->users)) {
        //     //relationship: $thisuser = userarray[userid] = session userid
        //     $thisuser = &$this->users[$userSess["userid"]];

        //     array_push($thisuser['Connections'], $conn);

        // } else if ($this->clients[$conn->resourceId]['conn']) {


        // } else {
       
        // $this->clients[$conn->resourceId]['conn'] = $conn;

        echo "New connection: [Client #" . $conn->resourceId . "] " . (isset($userSess)?"[UserID: ".$userSess['username']."] \n" : "\n");


            
          
        // }
    }    
    public function onSubscribe(ConnectionInterface $conn, $topic) 
    {             
        echo "Subscribe:\t[Client #" . $conn->resourceId . "] [Topic: " . $topic->getID() . "].\n";
        $userSess = $conn->Session->all();

        if (isset($this->pops[$topic->getID()])) { //subscribing to poptopic
           
        } 
        else if ($topic->getID() === $conn->Session->get('identifier')) { //subscribing to usertopic            
            //if userarray does not have user, create new user
            if (!isset($this->users[$userSess["userid"]])) {   
                unset($this->users[$userSess["userid"]]);
                $this->users[$userSess["userid"]] = array();   

                //debug
                echo "User DOES NOT already exist in userarray: Creating new user...\n";      

            }
            //save topic to userarray for broadcasting later
            if (!isset($this->users[$userSess["userid"]]["usertopic"])) {
                $this->users[$userSess["userid"]]["usertopic"] = &$topic;

                //debug
                echo "Usertopic saved to userarray:\n";                
                
            }
            //debug
            var_dump($this->users[$userSess["userid"]]["usertopic"]->getIterator());
        }
        else if (!$topic->getID()) { //no topic (maybe client not logged in)            
        }
        else { //subscribing to not-valid topic
            echo "Invalid Topic. Closing connection...";
            $conn->close();
        }
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) 
    {
        // maybe chat
        $conn->close();
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) 
    {
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
                //set $usersess
                if ($conn->Session->has("userid")) {
                    $userSess = $conn->Session->all();
                }                   

                //if userarray does not have user, create new user
                if (!isset($this->users[$userSess["userid"]])) {   
                    unset($this->users[$userSess["userid"]]);
                    $this->users[$userSess["userid"]] = array();   

                    //debug
                    echo "User DOES NOT already exist in userarray: Creating new user...\n";                      
                } else {                            
                    echo "User ALREADY exists\n";
                }
                //set $thisuser as shorthand
                $thisuser = &$this->users[$userSess["userid"]];

                //if userarray data is empty: fetch userdata from db and pass to userarray
                if (!array_key_exists("identifier"  , $thisuser) &&
                    !array_key_exists("currency"    , $thisuser) &&
                    !array_key_exists("ownedpops"   , $thisuser) )
                {   
                    //fetchOwnedPops and fetchOwnedCurr prepare statements are in _construct
                    try
                    {                  
                        //get owned pops from db                
                        $this->fetchOwnedPops -> execute(array(':userid' => $userSess["userid"]));
                        $ownedpops = $this->fetchOwnedPops -> fetchAll(PDO::FETCH_KEY_PAIR);
                        //get popcorn by userid from session
                        $this->fetchOwnedCurr -> execute(array(':userid' => $userSess["userid"]));
                        $ownedCurrency = $this->fetchOwnedCurr -> fetchAll();
                    }
                    catch(PDOException $e)
                    {
                        echo $e->getMessage();
                    }
                    //pass db data to userarray
                    $thisuser["identifier"] = $userSess["identifier"];                            
                    $thisuser["currency"]   = $ownedCurrency[0];
                    $thisuser["ownedpops"]  = $ownedpops;

                    //debug
                    echo "User data DOES NOT exist: Fetching data from db...\n";
                    echo "pops: \n";
                    print_r($ownedpops);
                    echo "ownedCurrency: \n";
                    print_r($ownedCurrency);
                } else {
                    echo "User data ALREADY exist: Returning data from userarray...\n";
                }
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
                                    "popstats"     =>   $this->popStatsArray($popid)
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
                        // return data for caller
                        $callResult = array(
                            "callresult"  => "fetch"
                        );
                        // return data to user topic
                        $userBC = array(
                            "popstats"     =>   $this->popStatsArray($popid),
                            "userstats"    =>   $this->userStatsArray($thisuser)
                        );

                        //debug
                        $thisuser["ownedpops"]["1"] += 1;
                        $thisuser["ownedpops"]["2"] += 2;
                        $thisuser["ownedpops"]["3"] += 3;
                        $thisuser["ownedpops"]["4"] += 4;
                        echo "Callresult: \n";
                        print_r($callResult);
                        echo "Userbroadcast: \n";
                        print_r($userBC);
                        echo "Thisuser: \n";
                        var_dump($thisuser);
                        echo "Count on userarray usertopic: ";
                        print_r($thisuser["usertopic"]->count());
                        echo "\n";
                }     
                //buy/sell/fetch RPC all do this at the end:
                //update last activity on thisuser
                $thisuser["LASTACTIVITY"] = time();
                //broadcast to user topic 
                $thisuser["usertopic"]->broadcast($userBC);
                //return callresult to caller
                return $conn->callResult($id, $callResult); 
        }
        //all calls do this at the end:
    } 
    public function onUnSubscribe(ConnectionInterface $conn, $topic) 
    {
    }     
    public function onClose(ConnectionInterface $conn) 
    {
    }       
    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
    }    
}