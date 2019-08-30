// A small Express Server to simulate Ginger API
// Author: Luc Varoqui (luc@varoqui.org)

const express = require('express');
const users = require('./users.json');

const app = express();

// Find by login
app.get('/v1/:login', (req, res) => {
	const user = users.find(el => {
		return el.login === req.params.login;
	});

	if (user) {
		res.send(user);
	} else {
		res.status(404).send('Not found');
	}
});

// Listen
app.listen(9000, () => {
	console.log('Fake Ginger listening at http://localhost:9000/v1/');
});
