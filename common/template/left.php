<!--left -->
<div id="left_area">
	<div class="item"> 
		<select style="width:100%">
<?php
	foreach($cArrRowsMenu2 as $data) {
		if( $cMenuIdx2 == $data['Menu_idx2'] ){
			echo "<option value='".$data['Page_url2']."' SELECTED>".$data['Menu_Name2']."</option>";
		}else{
			echo "<option value='".$data['Page_url2']."'>".$data['Menu_Name2']."</option>";
		}
	}
?>
		</select>
	</div>
	<div class="wrap_lnb">
<?php
	foreach($cArrRowsMenu3 as $data) {
		echo "<h3>· ".$data['Menu_Name3']."</h3>";

		echo "<ul>";
		foreach($cArrRowsMenu4 as $data4) {
			if( $cMenuIdx4 == $data4['Menu_idx4'] ){
				echo "<li><a href='".$data4['Page_url4']."' class='on'>".$data4['Menu_Name4']."</a></li>";
			}else{
				echo "<li><a href='".$data4['Page_url4']."'>".$data4['Menu_Name4']."</a></li>";
			}
		}
		echo "</ul>";
	}
?>
	</div>
</div>
<!--left //-->