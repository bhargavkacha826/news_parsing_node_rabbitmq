// this file is for send news url to rabbitmq server

var amqp = require('amqplib/callback_api');
var mysql = require('mysql2');
var dbcon = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "password",
  database: 'news_parsing'
});

dbcon.connect(function (err) {
  if (err) throw console.log(err);
  console.log("Send News Url DB Connected!");
});

const url = "amqp://test:password@192.168.1.86:5672/";

// category wise news from https://highload.today/uk
const cat = ["front-end-uk", "back-end-uk", "blokcheyn-ta-krypta", "pidruchnik-z-rust", "mobile-app-uk", "knyzhky", "dobirky", "istoriyi", "rishennya-uk", "teoriya-uk", "zalizo", "intervyu-uk", "spetsproekty-uk"];
// const cat = ["novyny", "front-end-uk", "back-end-uk"];


// rabbitmq server url

amqp.connect(url, function (err, conn) {
  conn.createChannel(function (err, ch) {
    var q = 'TestCase';
    ch.assertQueue(q, { durable: false });
    console.log(" [*] RabbitMQ Queue : ", q);
    var urlString = "https://highload.today/uk/category/" + cat[Math.floor(Math.random() * cat.length)] + "/";//getMovieByIndes(i);
    ch.sendToQueue(q, new Buffer.from(urlString));
    console.log(" [*] News Url Send To RabbitMQ Server " + q + " Queue " + urlString);
  });
  setTimeout(function () { conn.close(); process.exit(0) }, 500);
});