<?

	$items = $this->getTemplateObject()->getSearchItems();

//	print_r($items);

	if($items) {
		foreach($items["id"] as $index => $id) {
			print '<div class="device id:'.$id.' brand:'.$items["brand_id"][$index].'">'.$items["values"][$index].'</div>';
		}
		
	}
?>

