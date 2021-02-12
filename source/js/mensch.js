function startTime() {
	var today = new Date();
	var y = today.getFullYear();
	var n = today.getMonth() + 1;
	var d = today.getDate();
	var h = today.getHours();
	var m = today.getMinutes();
	var s = today.getSeconds();
	n = checkTime(n);
	d = checkTime(d);
	m = checkTime(m);
	s = checkTime(s);
	$('#time').html(y + "." + n + "." + d + " " + h + ":" + m + ":" + s);
	var t = setTimeout(startTime, 500);
}

function checkTime(i) {
	if (i < 10) {
		i = "0" + i
	}
	return i;
}

$(function () {
	startTime();
	$('#example').DataTable({
		"paging": false
	});
});

function randomHash(hash_length) {
	var hash = '';
	var characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	var characters_length = characters.length;
	for (var i = 0; i < hash_length; i++) {
		hash += characters.charAt(Math.floor(Math.random() * characters_length));
	}
	return hash;
}

function generateHash() {
	$('#server_secure').val(randomHash(32));
	$('#server_token').val(randomHash(32));
}