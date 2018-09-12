<!--left -->
<div id="left_area">
<?php
	if( count($cArrRowsMenu2) > 0 ){

		echo "<div class='item'>";
		echo "<select style='width:100%' id='menuIdx2' name='menuIdx2'>";

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
		
		$lOldMenuIdx3 = "";
		foreach($cArrRowsMenu3 as $data) {
			if( $lOldMenuIdx3 != $data['Menu_idx3'] ){
				if( $lOldMenuIdx3 != "" ){
					echo "</ul>";
				}
				echo "<h3>· ".$data['Menu_Name3']."</h3>";
				echo "<ul>";
			}

			if( $cPageMenuIdx4 == $data['Menu_idx4'] ){
				echo "<li><a href='".$data['Page_url4']."' class='on'>".$data['Menu_Name4']."</a></li>";
			}else{
				echo "<li><a href='".$data['Page_url4']."'>".$data['Menu_Name4']."</a></li>";
			}
			$lOldMenuIdx3 = $data['Menu_idx3'];
		}
		echo "</ul>";
		echo "</div>";
	}
?>
</div>
<!--left //-->

