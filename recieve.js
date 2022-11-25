// this file is for receive news url to rabbitmq server

var fs = require('fs');
var mysql = require('mysql2');
var request = require('request');
var cheerio = require('cheerio');
var amqp = require('amqplib/callback_api');
var dbcon = mysql.createConnection({
	host: "localhost",
	user: "root",
	password: "password",
	database: 'news_parsing'
});

dbcon.connect(function (err) {
	if (err) throw console.log(err);
	console.log("Recieve News Url DB Connected!");
});

var callback = function (error, response, html) {
	if (!error) {
		var $ = cheerio.load(html);
		values = "";
		index = 0;
		$('div.lenta-item').filter(function (index) {
			if (index != 0) {
				var data = $(this);
				var title = data.children().eq(2).children().text();
				var description = data.children().last().text();
				var image = data.children().eq(4).children().find("noscript").find("img").attr("src");
				var d = new Date();
				var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
				dbcon.query(
					'SELECT count(title) as c from `news` Where title= "' + title + '"',
					function (err, results, fields) {
						if (results[0].c > 0) {
							// console.log("news updated");
							console.log("News Title Updated: " + title);
							dbcon.query('UPDATE `news` SET  created_at="' + date + '"Where title= "' + title + '"');
						} else {
							console.log("News Title Added: " + title);
							// console.log("news added");
							value = '(' + '"' + title + '","' + description + '","' + image + '","' + date + '"' + ')';
							dbcon.query("INSERT INTO `news` (title,description,image,created_at) VALUES " + value + "");
						}
					}
				);
			}
			index++;
		})
	}
}

// rabbitmq server url
const url = "amqp://test:password@192.168.1.86:5672/";
amqp.connect(url, function (err, conn) {
	conn.createChannel(function (err, ch) {
		var q = 'TestCase';
		console.log(" [*] RabbitMQ Queue : ", q);
		ch.assertQueue(q, { durable: false });
		console.log(" [*] Waiting for News Url From %s Queue. To exit press CTRL+C", q);
		ch.consume(q, function (msg) {
			console.log("Receive Called =========================================================================");
			console.log(" [x] News Url Received From %s Queue %s", q, msg.content.toString());
			var url = msg.content.toString()
			request(url, callback)
		}, { noAck: true });
	});
});