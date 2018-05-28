<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Mass post news</title>
</head>
<body>
<h1>Mass Post for IBM Article Analysis</h1>
<form method="post" action="postMassURL.php">
	<select name="table">
	  <option value="opinionData">Opinions</option>
	  <option value="politicsData">Politics</option>
	</select>
	<br>
	<textarea name="url" placeholder="Enter URLs with line break inbetween each one" cols="200" rows="25" required></textarea>
	<br>
	<input type="submit">
</form>

</body>
</html>