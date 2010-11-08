<script>

$(document).ready(function(){

	var ws = new WebSocket("ws://127.0.0.1:8080/subscribe");

	ws.onopen = function() {
		alert("sending");
	    ws.send("This is a message from the browser to the server");
	};
	ws.onmessage = function(event) {
	    alert("The server sent a message: " + event.data);
	};

	
});

</script>