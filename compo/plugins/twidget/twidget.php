<?php
/*
Plugin Name: Twidget
Plugin URI: http://www.ludumdare.com/compo/
Description: Twitch.tv widget for Wordpress
Version: 1.0
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: BSD
*/

$plugin_dir = '/compo/wp-content/plugins/twidget/';

$TwidgetHasRun = false;

class Twidget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'twidget', // Base ID //
			'Twidget', // Name //
			array( 'description' => __( 'Twitch.tv widget', 'text_domain' ), ) // ARGS //
		);
	}
	
	function widget($args, $instance) {
		extract($args);
		
		global $plugin_dir;
		
		$apikey = $instance['apikey'];
		$game = $instance['game'];
		$faqurl = $instance['faqurl'];

		echo $before_widget;
		
//		error_reporting(-1);
		
		echo '<div id="TTV">';
			echo '<div class="Widget">';
				echo '<div id="TTV_Video" class="Head"></div>';
				echo '<div id="TTV_Streams" class="Body">Loading...</div>';
				echo '<div class="FarEdge"></div>';
			echo '</div>';
			echo '<div class="Foot">';
				echo '<div class="FootBody">';
					echo '<span class="FootImg">';
						echo '<object data="' . $plugin_dir . 'ImgTwitchGlitch.svg" width="24" height="24" type="image/svg+xml"></object>';
					echo '</span>';
					echo '&nbsp;&nbsp;';
					echo '<span class="FootText">';
						echo '<a href="http://www.twitch.tv/directory/game/' . rawurlencode($game) . '" target="_blank"> View All Streams</a>';
					echo '</span>';
					echo '&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<span class="FootText">';
						echo '<a href="' . $faqurl . '">FAQ</a>';
					echo '</span>';
					echo '<span id="TTV_Standby_Container" class="FootImg2">';
//						echo '<object id="TTV_Standby" data="' . $plugin_dir . 'ImgStandby.svg" width="22" height="22" type="image/svg+xml"></object>';
					echo '</span>';
				echo '</div>';
				echo '<div class="FootEdge"></div>';
			echo '</div>';
//			echo '<br />';
		echo '</div>';
		
		echo '<script>';
		echo 'var TwitchTV_APIKey = "' . $apikey . '";';
		echo 'var TwitchTV_Game = "' . $game . '";';
		echo 'var TwitchTV_FAQ = "' . $faqurl . '";';
		echo 'var TwitchTV_BaseDir = "' . $plugin_dir . '";';
		echo '</script>';

		global $TwidgetHasRun;
		$TwidgetHasRun = true;
		
		echo $after_widget;
	}
		
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['apikey'] = strip_tags( $new_instance['apikey'] );
		$instance['game'] = strip_tags( $new_instance['game'] );
		$instance['faqurl'] = strip_tags( $new_instance['faqurl'] );
		return $instance;
	}

	function form($instance) {
		if ( isset( $instance[ 'apikey' ] ) ) {
			$apikey = $instance[ 'apikey' ];
		}
		else {
			$apikey = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'game' ] ) ) {
			$game = $instance[ 'game' ];
		}
		else {
			$game = __( 'Diablo III', 'text_domain' );
		}
		
		if ( isset( $instance[ 'faqurl' ] ) ) {
			$faqurl = $instance[ 'faqurl' ];
		}
		else {
			$faqurl = __( 'streaming-faq/', 'text_domain' );
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'apikey' ); ?>"><?php _e( 'Twitch API Key:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'apikey' ); ?>" name="<?php echo $this->get_field_name( 'apikey' ); ?>" type="text" value="<?php echo esc_attr( $apikey ); ?>" />

		<label for="<?php echo $this->get_field_id( 'game' ); ?>"><?php _e( 'Game Name:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'game' ); ?>" name="<?php echo $this->get_field_name( 'game' ); ?>" type="text" value="<?php echo esc_attr( $game ); ?>" />

		<label for="<?php echo $this->get_field_id( 'faqurl' ); ?>"><?php _e( 'FAQ URL:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'faqurl' ); ?>" name="<?php echo $this->get_field_name( 'faqurl' ); ?>" type="text" value="<?php echo esc_attr( $faqurl ); ?>" />
		</p>
		<?php 
	}
}

function AddTTVScripts() {
	global $TwidgetHasRun, $plugin_dir;
	if ( $TwidgetHasRun == true ) {
//	//	echo '<link rel="stylesheet" type="text/css" href="' .$plugin_dir. 'twidget.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' .$plugin_dir. 'twidget.min.css" />';
		echo '<script src="https://ttv-api.s3.amazonaws.com/twitch.min.js"></script>';
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>';
//	//	echo '<script src="' .$plugin_dir. 'jquery.min.js"></script>';
//	//	echo '<script src="' .$plugin_dir. 'twidget.js"></script>';
		echo '<script src="' .$plugin_dir. 'twidget.min.js"></script>';
		echo '<script>';
		echo '	setTimeout( function(){';
		echo '			InitTwitchTV();';
		echo '		}, 200 );';
		echo '</script>';	
	}
}

// * * * //

function broadcast_query_func( $query ) {
	$out = "";

	global $wpdb;
	$result = $wpdb->get_results($query, ARRAY_A);
	
	$services = Array(
		0=>'null',
		1=>'twitch',
		2=>'hitbox',
		3=>'youtube',
		4=>'twitch-gamedev'
	);
	
	$modes = Array(
		0=>"",
		1=>"DEV",
		2=>"PLAY"
	);
	
	$dev_patterns = Array(
		"dev","developing","deving","code","coding","create","creating",
		"make","making","art","draw","compose","composing","program",
		"unity","java","c++","c#","python","html","october challenge"
	);
	$play_patterns = Array(
		"play","playing"
	);
		
	$out .= "<div class='broadcast_table'>";
		$out .= "<div class='header row'>";
			$out .= "<div class='service_header' title='Service'>SV</div>";
			$out .= "<div class='avatar_header' title='Avatar'>A</div>";
			$out .= "<div class='name_header'>Name</div>";
			$out .= "<div class='online_header'>Online</div>";
			$out .= "<div class='viewers_header'>Viewers</div>";
			$out .= "<div class='mode_header'>Mode</div>";
			$out .= "<div class='status_header'>Status</div>";
			$out .= "<div class='units_header' title='Total Minutes (in Hours:Minutes)'>Total</div>";
		$out .= "</div>";

		foreach( $result as $row ) {
			// Figure out when we were last online //
			$online_time = intval($row['online']);
			if ( $row['live'] ) {
				$online = "NOW";
			}
			else if ( $online_time >= 60 ) {
				$hours = floor($online_time / 60);
				$online = "{$hours} hour".($hours > 1 ? "s":"")." ago";
			}
			else {
				$minutes = floor($online_time);
				$online = "{$minutes} minutes ago";	// Always Greater than 9 )
			}
			
			$score = intval($row['score']);

			$units_value = intval($row['units']);
			$units = floor($units_value/60) . ":" . str_pad($units_value%60, 2, '0', STR_PAD_LEFT);
			if ( intval($row['service_id']) === 4 ) {
				$value = $score;
				if ( $score > 0 ) {
					$units = floor($value/60) . ":" . str_pad($value%60, 2, '0', STR_PAD_LEFT);
				}
				else {
					$units = "--";
				}
			}
			
			$status = $row['status'];
			$status_lower = strtolower($status);
			$mode = 0;
			// Force DEV or PLAY mode //
			if ( strpos($status,"[PLAY]") !== FALSE ) {
				$mode = 2;
			}
			else if ( strpos($status,"[DEV]") !== FALSE ) {
				$mode = 1;
			}
			// Detect DEV or PLAY mode //
			if ( $mode === 0 ) {
				foreach( $play_patterns as $word ) {
					if ( strpos($status_lower,$word) !== FALSE ) {
						$mode = 2;
						break;
					}
				}
			}
			if ( $mode === 0 ) {
				foreach( $dev_patterns as $word ) {
					if ( strpos($status_lower,$word) !== FALSE ) {
						$mode = 1;
						break;
					}
				}
			}
			
			// Build Page //
			$out .= "<div class='" . ($row['live'] ? "live service".$row['service_id']." " : "") ."row'>";
				$out .= "<div class='service'><div class='service-icon{$row['service_id']}'></div></div>";
				$out .= "<div class='avatar'>".($row['avatar']?"<img src='{$row['avatar']}'>":"")."</div>";
				$out .= "<div class='name'><a href='{$row['url']}' title='{$row['user_id']}'>{$row['display_name']}</a> <span class='followers' title='Followers'>[{$row['followers']}]</span>".($row['mature']?" <span class='mature' title='Mature'>[M]</span>":"")."</div>";
				$out .= "<div class='online'>{$online}</div>";
				$out .= "<div class='viewers'>{$row['viewers']}</div>";
				$out .= "<div class='mode'>{$modes[$mode]}</div>";
				$out .= "<div class='status'>{$status}</div>";
				$out .= "<div class='units'>{$units}</div>";
			$out .= "</div>";
		}
	$out .= "</div>";
	
	// * * * //
	
	return $out;
}

function broadcast_list_func( $attr ) {
	// Default Attributes (Arguments) //
	$attr = shortcode_atts( Array(
		'hours' => 24
	), $attr );
	
	// * * * //

	$query = "
		SELECT *, 
			(timestamp > (NOW() - INTERVAL 6 MINUTE)) AS live,
			(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS online
		FROM `wp_broadcast_streams`
		WHERE service_id < 4 AND timestamp > (NOW() - INTERVAL {$attr['hours']} HOUR)
		    OR service_id >= 4 AND timestamp > (NOW() - INTERVAL 6 MINUTE)
		ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
			CASE 
				WHEN service_id < 4 THEN score
				WHEN service_id >= 4 AND score > 240 THEN score
			END DESC,
			viewers DESC;
	";

//		ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
//			units DESC;

	return broadcast_query_func( $query );
}
add_shortcode( 'broadcast_list', 'broadcast_list_func' );

function broadcast_top_func( $attr ) {
	// Default Attributes (Arguments) //
	$attr = shortcode_atts( Array(
		'count' => 10
	), $attr );
	
	// * * * //

	$query = "
		SELECT *, 
			(timestamp > (NOW() - INTERVAL 9 MINUTE)) AS live,
			(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS online
		FROM `wp_broadcast_streams`
		ORDER BY units DESC
		LIMIT ${attr['count']};
	";

	return broadcast_query_func( $query );
}
add_shortcode( 'broadcast_top', 'broadcast_top_func' );


function broadcast_widget_func() {
	$total_streams = 16;
	$total_viewers = 256;
	
	$query = "
		SELECT *,
			(timestamp > (NOW() - INTERVAL 6 MINUTE)) AS live,
			(TIMESTAMPDIFF(MINUTE,timestamp,NOW())) AS online
		FROM `wp_broadcast_streams`
		WHERE timestamp > (NOW() - INTERVAL 6 MINUTE)
		ORDER BY UNIX_TIMESTAMP(FROM_UNIXTIME(UNIX_TIMESTAMP(timestamp),'%Y-%m-%d %H:%i')) DESC,
			CASE 
				WHEN service_id < 4 THEN score
				WHEN service_id >= 4 AND score > 240 THEN score
			END DESC,
			viewers DESC
		LIMIT 18;
	";
	
	global $wpdb;
	$result = $wpdb->get_results($query, ARRAY_A);	
		
?>
<style>
.tvbox {
  background:#445;
  border-radius:10px;
  font-size:12px;
  overflow:hidden;
}

.tvbox .screen {
  background:#889;
  position:relative;
}

.tvbox .screen .view {
	background:#BBC;
	color:#445;
	font-size:18px;
	line-height:24px;
	vertical-align:middle;
	width:240px;
	
	position:absolute;
	top:50%;
	left:50%;
	-ms-transform: translate(-50%,-50%);
	-webkit-transform: translate(-50%,-50%);
	transform: translate(-50%,-50%);
}

.tvbox .screen .view .inv {
	background:#445;
	color:#BBC;
}

.tvbox .screen .view .header {
	text-align:center;
}
.tvbox .screen .view .item {
	overflow:auto;
	position:relative;
	padding:2px;
}
.tvbox .screen .view .footer {
	text-align:center;
}


.tvbox .screen .view .item:hover {
	background:#8CF;
}


.left {
	float:left;
}
.right {
	float:right;
}

.tvbox .screen .view .item .avatar {	
}
.tvbox .screen .view .item .name {	
}
.tvbox .screen .view .item .service {	
}
.tvbox .screen .view .item .viewers {
}


.tvbox .bar {
  padding:0 10px;
  overflow:auto;
}

.tvbox .bar .logo {
  background:#889;
  float:left;
}

.tvbox .bar .logo:hover {
  background:#ACF;
}

.tvbox .bar .label {
  background:#889;
  color:#BBC;
  font-weight:bold;
  padding:5px;
  border-radius:10px;
  float:left;
}

.tvbox .bar .number {
  color:#BBC;
  font-size:16px;
  line-height:25px;
  padding:0 5px;
  float:left;
}
</style>
<div class="tvbox">
  <div class="screen">
    <img src="/compo/wp-content/themes/ludum/ld2014/tv-inv.svg" width="292">
    <div class="view">
    	<div class="inv header">Watch <strong>LIVE</strong> GameDev</div>
    	<div class="item">
    		<span class="avatar left"><img src="http://static-cdn.jtvnw.net/jtv_user_pictures/metaldemon68-profile_image-02a11197962ebebb-300x300.png" width="24" height="24" /></span>
    		<span class="name left">Show #1</span>
    		<span class="viewers right">10</span>
    		<span class="service right"><img src="/compo/wp-content/plugins/twidget/service_twitch.png" width="24" height="24" /></span>
    	</div>
<?php
		$count = count($result);
		if ( $count > 4 ) {
			$count = 4;
		}
		
		$img_prefix = "/compo/wp-content/plugins/twidget/";

		$service_img = Array(
			0=>'',
			1=>$img_prefix.'service_twitch.png',
			2=>$img_prefix.'service_hitbox.png',
			3=>$img_prefix.'service_youtube.png',
			4=>$img_prefix.'service_twitch_gamedev.png'
		);

		for ( $idx = 0; $idx < $count; $idx++ ) {
		?>
	    	<div class="item">
	    		<span class="avatar left"><img src="<?php ?>" width="24" height="24" /></span>
	    		<span class="name left"><?php ?></span>
	    		<span class="viewers right"><?php ?></span>
	    		<span class="service right"><img src="<?php echo $service_img[$result[$idx]['service_id']]; ?>" width="24" height="24" /></span>
	    	</div>			
		<?php
		}
//    	<div class="item">Show #2</div>
//    	<div class="item">Show #3</div>
//    	<div class="item">Show #4</div>
//    	<div class="item">Show #5</div>
?>
    	<div class="inv footer">Watch More...</div>
    </div>
  </div>
  <div class="bar">
    <div style="float:right"><span class="label" style="background:#C46;color:#FCA">LIVE</span> <span class="number"><?php echo $total_streams; ?></span> <span class="label">VIEWERS</span> <span class="number" style="padding-right:0"><?php echo $total_viewers; ?></span></div><div class="logo"><a href="http://ludumdare.com/compo/tv/"><img src="/compo/wp-content/themes/ludum/ld2014/ldtv-inv.svg" height="25"></a></div>
  </div>
  <div style="padding-bottom:10px"></div>
</div>

<?php
}
add_shortcode( 'broadcast_widget', 'broadcast_widget_func' );


// Add Local Style Sheet style.css //
add_action( 'wp_enqueue_scripts', 'broadcast_add_my_stylesheet' );
function broadcast_add_my_stylesheet() {
    wp_register_style( 'broadcast-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'broadcast-style' );
}


add_action( 'widgets_init', create_function( '', 'register_widget( "twidget" );' ) );
add_action( 'wp_footer', 'AddTTVScripts', 500 );

?>