<?php 
//Renew token, set to session and save to DB, if login exists



if ($session->has('userid')) {
    require_once 'Database.php';    
    $newDB = new Database;  
    $db = $newDB->db;
    $newToken = random_text();

    $session->set('token', $newToken);

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