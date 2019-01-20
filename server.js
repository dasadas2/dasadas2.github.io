// TODO: Import HTML maket

//====IMPORTS====
// var info = require('./js/getInfo');
// var ejs = require('ejs');
var express = require('express');
var app = express();


//====VARS====
var ip = 'localhost';
var port = 4958;


//====CODE====
app.set('view engine', 'ejs');

app.get('/', function (req, res) {
  res.render("index");
});

app.get("/:car", function (req, res) {
  res.render('item', {
    model: req.params.car
  });
});

app.listen(port);
console.log(ip + ":" + port);