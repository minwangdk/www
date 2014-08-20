<?php
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
    //for PriceHandling
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

    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }

?>