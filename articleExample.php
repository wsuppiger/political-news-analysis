<div>
	<form>
	  <p>Article URL:</p>
	  <input type="url" name="url" id="url" placeholder="https://news.com/article" size="50">
	  <input name="table" value="onlineData" id="table" hidden>
	</form>
	<br/>
	<button onclick="getArticle(document.getElementById('url').value, 1)">get Article</button>
	<button id="statsButton" onclick="getArticle(document.getElementById('url').value, 2)">get Analysis</button>
	<img id="load" src="/images/loading.gif" width="15px" height="15px" style="visibility:hidden"/>
	<div id="response">
		<br/>
		<textarea id="article" rows="8" cols="50" placeholder="The article will be displayed here" readonly></textarea>
		<textarea id="stats" rows="8" cols="50" placeholder="The tone analysis will be displayed here" readonly></textarea>
	</div>
</div>

<script>

function getArticle(url, request) {
	document.getElementById("load").style.visibility = "visible";
	if (url.length == 0) { 
		document.getElementById("load").style.visibility = "hidden";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if ((this.readyState == 4 && this.status == 200) && request == 1) {
                document.getElementById("article").innerHTML = this.responseText;
				document.getElementById("load").style.visibility = "hidden";
            }
			else if((this.readyState == 4 && this.status == 200) && request == 2){
				 $('#statsButton').prop('disabled', true);
				 setTimeout(function() {
					   $('#statsButton').prop('disabled', false);
				 }, 5000);
				var str = this.responseText;
				str = str.replace(/<br>/g, "\n");
				document.getElementById("stats").innerHTML = str;
				document.getElementById("load").style.visibility = "hidden";
			}
        };
		var table = document.getElementById('table').value;

		xmlhttp.open("POST", "/politics/postURL.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("url=" + url + "&request="+ request + "&table=" + table);
    }
}
</script>