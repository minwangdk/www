<?php 




//Renew token, set to session and save to DB, if login exists

function setToken() {
    global $session;
    global $newDB;

    if ($session->has('userid')) {   
        $db = $newDB->db;
        $newToken = random_text();

        setcookie("UserToken", $newToken, 0, '/');

        try 
        {
            $setToken  = $db->prepare(
                "   UPDATE users
                    SET token = :newToken                  
                    Where id = :userid          ");
            $setToken->execute(array(
                ':newToken' => $newToken,
                ':userid'   => $session->get('userid')   ));
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
}

