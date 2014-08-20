<?php
namespace MyApp;

interface PriceHandlingInterface
{  

    public function addPrice ($scope); //add current price to scope price array

    public function saveToDB ($scope, $offset);     

    public function poparrayTrim($scope, $offset); 

    public function trimDBPrices ($scope);      
    
}