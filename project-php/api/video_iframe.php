<?php
	$links=$_GET['links'];
	$w_video=$_GET['w_video'];
	$h_video=$_GET['h_video'];
	$arr_links=explode('=',$links);
?>
<iframe width="<?=$w_video?>" height="<?=$h_video?>" src="https://www.youtube.com/embed/<?=$arr_links[1]?>" frameborder="0" allowfullscreen></iframe>