// fastest array merger

Array.prototype.extend_forEach = function (array) {
    array.forEach(function(x) {this.push(x)}, this);    
}

//array1.extend_forEach(array2);