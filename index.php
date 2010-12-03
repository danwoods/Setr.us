<?php 
session_start();
if(!isset($_SESSION['username'])){
  header('Location: login_html.php');
  }
   ?>
<!DOCTYPE html PUBLIC "-//w3c//DTD XHTML 1.1//EN"
 "HTTP://www.w3c.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <title>Setr.us > Playlist</title>
  <!--there are some variables declared in one .js file
      and then used in other, following, .js files (ie: currPositionInPL)
      so the order in which these are loaded is important -->
  <? echo "<script>var username = '" . $_SESSION['username'] . "';</script>";?>
  <script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" src="javascript/jquery-ui-1.7.2.custom.min.js"></script>
  <script type="text/javascript" src="javascript/users.js"></script>
  <script type="text/javascript" src="javascript/catalogue.js"></script>
  <script type="text/javascript" src="javascript/jquery.qtip-1.0.0-rc3.js"></script>
  <script type="text/javascript" src="javascript/playlist.js"></script>
  <script type="text/javascript" src="javascript/music_player.js"></script>
  <script type="text/javascript" src="javascript/site_updating.js"></script>
  <script type="text/javascript" src="javascript/jquery.fixedheadertable.1.0.js"></script>
  
  <link rel="stylesheet" type="text/css" href="stylesheets/results.css" />
  <link rel="stylesheet" type="text/css" href="stylesheets/redmond/jquery-ui-1.7.2.custom.css" />
  <link rel="stylesheet" type="text/css" href="stylesheets/fixedHeaderTable.css" />
  
  <script type="text/javascript">
  $(document).ready(function() {
    $('#playlist').sortable({ appendTo: '#playlist' });
    //mouseout does not always work for removing qtips
    $('#catalogueContainer').bind('mouseout', function() {
      $('.qtip').remove();
      });
  });
  </script>
  </head>
  
  <body onload="getUserInfo('<? echo $_SESSION['username']; ?>')">
  <div id="header">
  <div id="logo"><img src="images/logo_banner.png" /></div>
  <div id="login_register">
  <?php 
    if(isset($_SESSION['username'])){
      echo "<div id=\"user_settings_button\" onclick=\"javascript:window.open('http://localhost/Setr.us/music/uploader/uploaderFrontEnd.php')\">" . /*($_SESSION['username']) . " : settings*/"Upload</div>";
      echo "<div id=\"logout_button\" " . "onclick=\"javascript:location='http://localhost/Setr.us/php/logout.php'\">" . "Logout" . "</div>";
      }
    else{
      echo "<div id=\"login_button\" class=\"button\">login</div>";
      echo "<div id=\"register_button\" class=\"button\">register</div>";
      }
  ?>
  
  </div>
  </div>
  
  <div id="catalogueContainer" class='undragable'>
  <div id="results-heading" class='undragable'>
    <!-- <div id="favorites_root" class="expand_image" class='undragable'>
      <image class="expImg" src="images/16-arrow-right.png" onclick="expand(this, 'artists', 'fav_artist', '<? echo $_SESSION['username']; ?>');"/>
      <h1>Favorites</h1>
      </div>
    <div id="playlist_root" class="expand_image" class='undragable'>
      <image class="expImg" src="images/16-arrow-right.png" onclick="expand(this, 'artists', 'playlist', '<? echo $_SESSION['username']; ?>');"></image>
      <h1>Playlist</h1>
      </div> -->
    <div id="result_root" class="expand_image" class='undragable'>
      <image id="expAllArtistImg" class="expImg" src="images/16-arrow-right.png" onclick="expand(this, 'artists', 'artists');"></image>
      <h1>All Artist</h1>
      </div>
    </div>
  </div>
  
  <div id="playlistContainer">
  <div id="playlist_controls">
  <!--<div id="playlist_title" onClick="editPlaylistTitile();">Untitled</div>
  <div id="playlist_save" class='button' onclick="save_playlist(this.form);"><span class='save_pl_button'>save</span></div>-->
  <div id="playlist_clear" class='button' onclick="clear_playlist();"><span class='clear_pl_button'>clear</span></div>
  </div>
  
  <div id="playlistHeader"><div id="playlist_legend"><span class="playlist_item_name">Name</span><span class="playlist_item_artist">Artist</span><span class="playlist_item_date">Date</span><span class="playlist_item_location">Location</span></div></div>
  
  <div id='playlist'><p id='empty' class='empty'></p></div>
  
</div>
  </div>
  <div id="controls">
   
  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
             id="music_player" width="350" height="25"
             codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">
         <param name="movie" value="flashplayer/current/music_player.swf" />
         <param name="quality" value="high" />
         <param name="bgcolor" value="#ffffff" />
         <param name="allowScriptAccess" value="always" />
         <embed src="flashplayer/current/music_player.swf" quality="high" bgcolor="#ffffff"
             width="450" height="25" name="music_player" align="middle"
             play="true" loop="false" quality="high" allowScriptAccess="always"
             type="application/x-shockwave-flash"
             pluginspage="http://www.macromedia.com/go/getflashplayer">
         </embed>
     </object>


    </div>
  
 
  
  <script>
  $(function() {
		$("#active_playlist").sortable();
		
		$("#empty").droppable({
			drop: function(event, ui) {
				p_process_test(ui, $('#empty'));
			}
		});
	});


</script>
  
  </body>
  
