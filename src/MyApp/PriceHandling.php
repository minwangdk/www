<?php
namespace MyApp;

class PriceHandling implements PriceHandlingInterface
{  
    protected $archivePrices1min;
    protected $trimDBPrices1min;
    protected $archivePrices1hour;
    protected $trimDBPrices1hour;
    protected $archivePrices1day;   

    public function addPrice ($scope) { //add current price to scope price array
        foreach ($this->pops as $key => $pop) {                
            $this->pops[$key][$scope][] = array(
                0   => round(microtime(true)),
                1   => $pop["price"][1]
            );                                      
        }
    }

    //$offset should use negative number to indicate how many values from end should be saved to DB
    public function saveToDB ($scope, $offset) {
        try 
        {   
            $this->db->beginTransaction();
            switch ($scope) { //archive prices
                case 'priceMinute':
                    $archivePrices  = $this->archivePrices1min;
                break;

                case 'priceHour':
                    $archivePrices  = $this->archivePrices1hour;
                break;

                case 'priceDay':
                    $archivePrices  = $this->archivePrices1day;
            }            

            foreach ($this->pops as $key => $pop) { 
                //data temporarily hold latest chunk of price array
                $sliceOfPop = array_slice($this->pops[$key][$scope], $offset);                         
                
                // insert data to database                          
                foreach ($sliceOfPop as $row) {
                    $archivePrices -> execute(array(                                
                        ':popid'        => $key,
                        ':timestamp'    => $row[0],
                        ':price'        => $row[1]
                    ));
                }                            
            }    
            $this->db->commit();   

            //debug
            echo "saved " . $scope .  " to DB\n";
                
        }
        catch(PDOException $e)
        {
            $this->db->rollback();
            echo $e->getMessage();
        }    
    }

    //$interval in seconds, $scope is popprice array eg. popseconds, 
    //$offset = how many elements to keep in array at a time(use negative number), $savetoDB = savetoDB and trim DB
    public function poparrayTrim($scope, $offset) {                
        foreach ($this->pops as $key => $pop) { 
            //data temporarily hold latest chunk of price array
            $sliceOfPop = array_slice($this->pops[$key][$scope], $offset);
            //copy data to array  (trim popprice) 
            $this->pops[$key][$scope] = $sliceOfPop;
        }      

        //debug
        echo "trimmed " .$scope. "\n";   
    }

    public function trimDBPrices ($scope) {
        try 
        {   
            //delete old records
            $this->db->beginTransaction();
            switch ($scope) {
                case 'priceMinute':                    
                    $trimDBPrices   = $this->trimDBPrices1min;
                    $keepFor        = 60 * 60 * 24 * 2; //2 days
                break;

                case 'priceHour':
                    $trimDBPrices   = $this->trimDBPrices1hour;
                    $keepFor        = 60 * 60 * 24 * 14; //2 weeks                
            }
            
            $lifetime = time() - $keepFor;
            $trimDBPrices -> execute(array(                        
            ':lifetime'     => $lifetime
            ));
            
            
            $this->db->commit();    

            //debug
            echo "DB trimmed\n";
        }
        catch(PDOException $e)
        {
            $this->db->rollback();
            echo $e->getMessage();
        }    
    }
    
}