<html>
<head>
<title>js自动下载文件到本地</title>
<script language="javascript" type="text/javascript">

//js自动下载文件到本地
var xh;
function getXML(geturl) {
xh = new XMLHttpRequest();
xh.onreadystatechange = getReady;
xh.open("GET", geturl, true);
xh.send();
} 

function getReady() { 
if (xh.readyState == 4) {	
		saveFile("/Users/sun/Documents/ab.txt");
		return true;	
}
} 

</script>

</head>
<body>
	<form id="form1" runat="server">
		<div>
			<input type="button" value="124"
				onclick="getXML('http://local.pluto.com/html/app/campaign/48.txt')">
		</div>
	</form>
</body>
</html>