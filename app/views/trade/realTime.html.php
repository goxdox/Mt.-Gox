<script>

$(document).ready(function(){

	var ws = new WebSocket("ws://127.0.0.1:8080/connect");

	ws.onopen = function() {
		//alert("sending");
	    ws.send("subscribe");
	};
	ws.onmessage = function(event) {
	    alert("The server sent a message: " + event.data);
	};

	
});

</script>