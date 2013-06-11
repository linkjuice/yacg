<?php  // AUCTION ADS HOOK
function auctionads($keyword = THIS_PAGE_KEYWORD, $ad_format = '250x250', $color_border="CFF8A3", $color_bg="FFFFFF", $color_heading ="00A0E2", $color_text="000000", $color_link="008000") {
	if (SHOW_ADS) {
		switch($ad_format) {
			case "728x90":
				$ad_width = "728";
				$ad_height = "90";
				break;
			case "468x60":
				$ad_width = "468";
				$ad_height = "60";
				break;
			case "300x250":
				$ad_width = "300";
				$ad_height = "250";
				break;
			case "250x250":
				$ad_width = "250";
				$ad_height = "250";
				break;
			case "180x150":
				$ad_width = "180";
				$ad_height = "150";
				break;
			case "160x600":
				$ad_width = "160";
				$ad_height = "600";
				break;
			case "120x600":
				$ad_width = "120";
				$ad_height = "600";
				break;
			case "160x160":
				$ad_width = "160";
				$ad_height = "160";
				break;
			case "468x180":
				$ad_width = "468";
				$ad_height = "180";
				break;
			case "336x160":
				$ad_width = "336";
				$ad_height = "160";
				break;
		}
		echo '<script type="text/javascript"><!--
auctionads_ad_client = "'.AUCTIONADS_ADCLIENT.'";
auctionads_ad_campaign = "'.AUCTIONADS_ADCAMPAIGN.'";
auctionads_ad_width = "'.$ad_width.'";
auctionads_ad_height = "'.$ad_height.'";
auctionads_ad_kw = "'.$keyword.'";
auctionads_color_border = "'.$color_border.'";
auctionads_color_bg = "'.$color_bg.'";
auctionads_color_heading = "'.$color_heading.'";
auctionads_color_text = "'.$color_text.'";
auctionads_color_link = "'.$color_link.'";
auctionads_options =  "n";
//--></script>
<script  src="http://ads.auctionads.com/pagead/show_ads.js" type="text/javascript">
</script>';
	}
}
?>