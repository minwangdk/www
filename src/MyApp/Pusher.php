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

    private $pops;
    private $users;
    private $newDB;
    private $db;
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
                    
    private $fetchOwnedPops;
    private $fetchOwnedCurr;
    private $updateUsersPops;
    private $updateUsersCurr;

    private $ticker;

    public function numberBreakdown($number, $getdecimalpart = TRUE) { //return decimal part of number
        $whole = floor($number);
        $fraction = $number - $whole;
        if ($getdecimalpart) {
            return $fraction;
        }else{
            return $whole;
        }
    }

    public function addPrice($scope) {
        foreach ($this->pops as $key => $pop) {                
            $this->pops[$key][$scope][] = array(
                0   => round(microtime(true)),
                1   => $pop["price"][1]
            );                                      
        }
    }

    //$interval in seconds, $scope is popprice array eg. popseconds, 
    //$offset = how many elements to keep in array at a time(use negative number), $savetoDB = savetoDB and trim DB
    public function poparrayTrimmer($scope, $offset, $saveToDB = FALSE) {        
        try 
        {   
            //first delete old records
            if ($saveToDB) {
                $this->db->beginTransaction();
                switch ($scope) {
                    case 'priceMinute':
                        $this->archivePrices    = $this->archivePrices1min;
                        $this->trimDBPrices     = $this->trimDBPrices1min;
                        $this->keepFor  = 60 * 60 * 24 * 2; //2 days
                    break;

                    case 'priceHour':
                        $this->archivePrices    = $this->archivePrices1hour;
                        $this->trimDBPrices     = $this->trimDBPrices1hour;
                        $this->keepFor  = 60 * 60 * 24 * 14; //2 weeks
                    break;

                    case 'priceDay':
                        $this->archivePrices    = $this->archivePrices1day;
                        $this->trimDBPrices     = $this->trimDBPrices1day;
                        $this->keepFor  = FALSE; //dont delete records
                }
                if ($this->keepFor) {
                    $this->lifetime = time() - $this->keepFor;
                    $this->trimDBPrices -> execute(array(                        
                    ':lifetime'     => $this->lifetime
                    ));
                }
                //debug
                echo "DB trimmed as part of trim\n";
            }

            foreach ($this->pops as $key => $pop) { 
                //data temporarily hold latest chunk of price array
                $sliceOfPop = array_slice($this->pops[$key][$scope], $offset);                         
                if ($saveToDB) { // save latest values to DB, default off      


                    // insert data to database                          
                    foreach ($sliceOfPop as $row) {
                        $this->archivePrices -> execute(array(                                
                            ':popid'        => $key,
                            ':timestamp'    => $row[0],
                            ':price'        => $row[1]
                        ));
                    }  
                    $this->db->commit();    
                    //debug
                    echo "saved to DB as part of trim\n";
                    var_dump($sliceOfPop);
                }
                // $sliceOfPop = array_slice($this->pops[$key][$scope], $offset);                    
                // unset($this->pops[$key][$scope]); 

                //copy data to array  (trim popprice) 
                $this->pops[$key][$scope] = $sliceOfPop;
            }

            //debug
            echo "trimmed " .$scope. "\n";                
        }
        catch(PDOException $e)
        {
            $this->db->rollback();
            echo $e->getMessage();
        }    
        
    }

    public function poparrayTimer($interval = 1) {
        $this->loop->addTimer($interval, function() { 
            //increment ticker
            $this->ticker += 1;

            //add price to pricesecond
            $this->addPrice('priceSecond');
            

            if ($this->ticker % 60 === 0) { //1min
                //add price to priceMinute
                $this->addPrice('priceMinute');

                //trimmer
                $this->poparrayTrimmer('priceSecond', -60, FALSE);

                //delay
                echo "pang! \n";
            }
            if ($this->ticker % 3600 === 0) { //1h
                //add price to priceHour
                $this->addPrice('priceHour');

                //trimmer
                $this->poparrayTrimmer('priceMinute', -60, TRUE);
                //delay
                echo "Kapang! \n";
            }
            if ($this->ticker % 86400 === 0) { //24h
                //add price to priceDay
                $this->addPrice('priceDay');

                //trimmer
                $this->poparrayTrimmer('priceHour', -24, TRUE);
                //delay
                echo "MOKapang! \n";
            }
            if ($this->ticker % 604800 === 0) { //7 days
                
                //trimmer
                $this->poparrayTrimmer('priceDay', -7, TRUE);
                //delay
                echo "Rasala! \n";
                $this->ticker = 0;
            }

            //debug
            // echo microtime() . "...\n";   
            // print_r($this->pops["1"]["priceSecond"]);   

            // end( $this->pops['1']['priceSecond'] );
            $this->p = end( $this->pops['1']['priceSecond'] ) ;
            $this->o = key( $this->pops['1']['priceSecond'] ) ;
            reset($this->pops['1']['priceSecond']);
            print_r($this->o);
            print_r($this->p);
            echo "\n";
            echo microtime() . "...\n"; 
            echo "ticker: " . $this->ticker . "\n";

            //call itself with 1s delay, adjusted for delay            
            $interval = 1 - $this->numberBreakdown(microtime(true));
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
        try 
        {// for onCall
            $this->fetchOwnedPops   = $this->db->prepare(
                "   SELECT  pops_id, quantity 
                    FROM    users_own_pops 
                    WHERE   users_id = :userid "
            );

            $this->fetchOwnedCurr   = $this->db->prepare(
                "   SELECT  popcorn, dollars, bitcoins
                    FROM    users_own_currency
                    WHERE   users_id = :userid "
            );
              
            $this->updateUsersPops  = $this->db->prepare( // get id from users table with identifier from userarray
                "   INSERT INTO     users_own_pops (users_id, pops_id, quantity)
                    VALUES      ((
                        SELECT  id 
                        FROM    users
                        WHERE   identifier = :identifier
                        ),
                        :pops_id,
                        :quantity 
                    )
                    ON DUPLICATE KEY UPDATE quantity = :quantity "
            );

            $this->updateUsersCurr  = $this->db->prepare(
                "   UPDATE  users_own_currency 
                    SET     popcorn     = :popcorn,
                            dollars     = :dollars,
                            bitcoins    = :bitcoins
                    WHERE   users_id    = (
                        SELECT  id 
                        FROM    users
                        WHERE   identifier = :identifier
                    )"
            );

            $this->archivePrices1min    = $this->db->prepare( 
                "   INSERT INTO     pop_prices_1min (pops_id, timestamp, price)
                    VALUES          (:popid, :timestamp, :price)  "
            );
            $this->trimDBPrices1min     = $this->db->prepare(
                "   DELETE FROM     pop_prices_1min
                    WHERE           timestamp < :lifetime  "
            );

            $this->archivePrices1hour   = $this->db->prepare( 
                "   INSERT INTO     pop_prices_1hour (pops_id, timestamp, price)
                    VALUES          (:popid, :timestamp, :price)  "
            );
            $this->trimDBPrices1hour    = $this->db->prepare(
                "   DELETE FROM     pop_prices_1hour
                    WHERE           timestamp < :lifetime   "
            );

            $this->archivePrices1day    = $this->db->prepare( 
                "   INSERT INTO     pop_prices_1day (pops_id, timestamp, price)
                    VALUES          (:popid, :timestamp, :price)  "
            );
            $this->trimDBPrices1day     = $this->db->prepare(
                "   DELETE FROM     pop_prices_1day
                    WHERE           timestamp < :lifetime   "
            );

        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }        

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
           

        // //per second, add latest price to priceSecond array
        // $this->poparrayTimer(0, 1, "priceSecond"); 
        // //trim priceSecond array to 59 entries every minute
        // $this->poparrayTrimmer(99999, "priceSecond", -3);

        // //per minute, add latest price to priceMinute array
        // $this->poparrayTimer(0, 60, "priceMinute");
        // //trim priceMinute array to 60 entries every 3600 seconds (60min) and save to DB and trim DB
        // $this->poparrayTrimmer(3600, "priceMinute", -60, TRUE);

        // //per hour, add latest price to priceHour array
        // $this->poparrayTimer(0, 3600, "priceHour");
        // //trim priceHour array to 24 entries every 24*3600 seconds (1day) and save to DB and trim DB
        // $this->poparrayTrimmer(24*3600, "priceHour", -24, TRUE);

        // //per 1day, add latest price to priceDay array
        // $this->poparrayTimer(0, 24*3600, "priceDay");
        // //trim priceDay array to 30 entries every 24*3600 seconds (1day) and save to DB
        // $this->poparrayTrimmer(7*24*3600, "priceDay", -7, TRUE);
       
        // //debug
        // $this->o = '';
        // $this->p = '';
        // // $this->loop->addPeriodicTimer(1, function() { 

        // //     echo microtime() . "\n"; 

        // // });
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

        echo "New connection: [Client #" . $conn->resourceId . "] " . (isset($userSess)?"[UserID: ".$userSess['username']."] \n" : "\n");


            
          
        // }
    }    
    public function onSubscribe(ConnectionInterface $conn, $topic) {             
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
        else { //subscribing to not-valid topic
            echo "Invalid Topic. Closing connection...";
            $conn-close();
        }
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
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }  
    public function onMessage(ConnectionInterface $from, $msg) {

    }
    public function onClose(ConnectionInterface $conn) {// HERE RUN GARBAGE COLLECTION ON USER ARRAY
        // $userSess = $conn->Session->all();
        // $thisuser = &$this->users[$userSess["userid"]];
        
        // if ($thisuser["usertopic"]->count() <= 1) {
        //     $thisuser["ownedpops"]["1"] += 1;
        //     $thisuser["ownedpops"]["2"] = 37;
        //     $thisuser["ownedpops"]["3"] = 37;
        //     $thisuser["ownedpops"]["4"] = 37;
            // try
            // {   
            //update user data
                // $this->updateUsers2 = $this->db->prepare(
                // "   INSERT INTO users_own_pops (users_id, pops_id, quantity)
                //     VALUES (:userid, :pops_id, :quantity)
                //     ON DUPLICATE KEY UPDATE quantity = :quantity "
                // );
                // foreach ($thisuser["ownedpops"] as $k=>$v) { //ownedpops
                //     $this->updateUsers2 -> execute(array(
                //         ':userid'   => $userSess["userid"],
                //         ':pops_id'  => $k,
                //         ':quantity' => $v   
                //     ));
                // }

            // }
            // catch(PDOException $e)
            // {
            //     echo $e->getMessage();
            // }    

            // remove user from userarray  

        // }
        //debug
        // echo "\nConn #" .$conn->resourceId. ": disconnected.\nRemaining on userarray usertopic: ";
        // print_r($thisuser["usertopic"]->count());
        // var_dump($thisuser["usertopic"]->getIterator());
    }       
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }    
}