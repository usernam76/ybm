<!--left -->
<div id="left_area">
<?php
	if( count($cArrRowsMenu2) > 0 ){

		echo "<div class='item'>";
		echo "<select style='width:100%'>";

		foreach($cArrRowsMenu2 as $data) {
			if( $cPageMenuIdx2 == $data['Menu_idx2'] ){
				echo "<option value='".$data['Page_url2']."' SELECTED>".$data['Menu_Name2']."</option>";
			}else{
				echo "<option value='".$data['Page_url2']."'>".$data['Menu_Name2']."</option>";
			}
		}
		echo "</select>";
		echo "</div>";
		echo "<div class='wrap_lnb'>";

		foreach($cArrRowsMenu3 as $data) {
			echo "<h3>· ".$data['Menu_Name3']."</h3>";

			echo "<ul>";
			foreach($cArrRowsMenu4 as $data4) {
				if( $cPageMenuIdx4 == $data4['Menu_idx4'] ){
					echo "<li><a href='".$data4['Page_url4']."' class='on'>".$data4['Menu_Name4']."</a></li>";
				}else{
					echo "<li><a href='".$data4['Page_url4']."'>".$data4['Menu_Name4']."</a></li>";
				}
			}
			echo "</ul>";
		}
		echo "</div>";
	}
?>
</div>
<!--left //-->