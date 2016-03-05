<div data-role="page" data-theme="a">
	<div data-role="header">
		<h1>News</h1>
	</div>
	<div data-role="content" data-theme="a">
	<?php
echo '<h2>'.$news_item['title'].'</h2>';
echo $news_item['text'];
?>
<br /> <br /> 
<input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="back" onClick="javascript:location.href='/news/'" value="Back">
</div>
</div>