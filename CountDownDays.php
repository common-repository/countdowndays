<?php
/*
Plugin Name: CountDownDays
Plugin URI: http://arisnb.nulis.web.id/count-down-days.php
Description: CountDownDays - Plugin for counting down the event. Thanks to Purwedi Kurniawan http://exclusivewordpress.com.
Version: 1.0
Author: Aris Nurbawani
Author URI: http://www.nulis.web.id
*/


add_action('admin_menu','countdowndays_admin_menu_hook');
	
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
    add_action('init', 'InstallCountDownDaysPlugin');
}
if (isset($_GET['deactivate']) && $_GET['deactivate'] == 'true') {
    add_action('init', 'DeactiveCountDownDaysPlugin');
}
function JustView() {
    global $wpdb, $table_prefix;
    $SQL = "SELECT * FROM " . $table_prefix."countdowndays";
    $hasil = $wpdb->get_results($SQL);
	
	return $hasil;
    
}
function InstallCountDownDaysPlugin(){
  global $wpdb, $table_prefix;
  $create = "CREATE TABLE ".$table_prefix."countdowndays (`mulai` DATE NOT NULL ,`akhir` DATE NOT NULL ,`deskripsi1` TEXT NOT NULL,`deskripsi2` TEXT NOT NULL, model int(1) default '1' NOT NULL)";
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($create);
  
   $hasil=JustView();
   
   if (!$hasil) {
	  // foreach ($hasil as $hasilnya) {
		 //  if (empty($hasilnya->model)){
		   $Insert  =" INSERT INTO ".$wpdb->prefix."countdowndays (mulai,akhir,deskripsi1,deskripsi2,model) ";
		   $Insert  .=" VALUES((CURDATE()+INTERVAL -2 DAY), (CURDATE()+INTERVAL 2 DAY),'days have passed.','days to the big event live.','1' )";
		   dbDelta($Insert);
		  // }
	  // }
   }
   
}
function DeactiveCountDownDaysPlugin(){
global $wpdb,$table_prefix;
   	$droptable="DROP TABLE ".$table_prefix."countdowndays" ;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($droptable);
	
}

function SettingEvents() {
  global $wpdb, $table_prefix, $wp_query, $userdata;
   if (isset($_POST['mulai']) AND isset($_POST['akhir']) AND isset($_POST['deskripsi1']) AND isset($_POST['deskripsi2']) AND isset($_POST['model'])){
   

   $Update = "UPDATE ".$wpdb->prefix."countdowndays SET mulai='$_POST[mulai]', akhir='$_POST[akhir]', deskripsi1='$_POST[deskripsi1]', deskripsi2='$_POST[deskripsi2]', model='$_POST[model]' ";
   $wpdb->query($Update);

   $Update = " OPTIMIZE TABLE ".$wpdb->prefix."countdowndays";
   $wpdb->query($Update);
   }

}
function days_to($mulai){
			$skrg=date("Y-m-d");
			$daysto = ( (strtotime($skrg))-(strtotime($mulai)) )/86400 ;
			return $daysto;
		}
		
function days_end($finalday){
			$skrg=date("Y-m-d");
			$daysend = ( (strtotime($finalday))-(strtotime($skrg)) )/86400 ;
			return $daysend;
		}

	

function ViewCountDownDays() {
    global $wpdb, $table_prefix;
	
    $SQL = "SELECT * FROM " . $table_prefix."countdowndays";
    $hasil = $wpdb->get_results($SQL);
	
	if ($hasil) {
		foreach ($hasil as $hasilnya) {
			$mulais=strtotime($hasilnya->mulai);
			$finaldays=strtotime($hasilnya->akhir);
			$skrg=date("Y-m-d");
			$nowday=strtotime($skrg);
			if ($hasilnya->model=='1'){
				if(($nowday>=$mulais) && ($nowday<=$finaldays)){
					$hari=days_to($hasilnya->mulai);
					$hari=$hari;
					echo "<br /><font color=red>". $hari ."</font> ". $hasilnya->deskripsi1;
					$mundur=days_end($hasilnya->akhir);
					echo "<br /><font color=red>" .$mundur. "</font> ". $hasilnya->deskripsi2;
					
				}else {
					echo "Expired Events ";
					
					
				}
			}else /*($haslil['model']=='2')*/ {
				if(($nowday>=$mulais) && ($nowday<=$finaldays)){
					$mundur=days_end($hasilnya->akhir);
					echo "<br /><font color=red>" .$mundur. "</font> ". $hasilnya->deskripsi2;
					
				}else {
					echo "Expired Events ";
					
					
				}
			}
		}
    }
    
}

/**
 * add the menu into WordPress admin menu
 * */ 
function countdowndays_admin_menu_hook(){
    if (function_exists('add_options_page')) {
		add_options_page(
			'Count Down Days',
			'Count Down Days',
			'manage_options',
			'CountDownDays.php',
			'countdowndays_create_admin_menu'
		);
	}
}




?>
<?
function countdowndays_create_admin_menu() { 
	global $wpdb;
	$hasil=JustView();
    foreach ($hasil as $hasilnya) {
?>
     
			<form method="post" name="options" target="_self">
			<table style="width: 100%;" border="0">
				<tr>
					<td width="150"><strong>1. Begin The Event </strong></td>
					<td>
						<input type="text" name="mulai" value="<?php echo $hasilnya->mulai; ?>" /> Y-m-d. If without it, Don't change &amp; form No.5 choice model 2.
					</td>
				</tr>
				<tr>
					<td><strong>2. The Event Ended or The long-awaited day.</strong></td>
					<td>
						<input type="text" name="akhir"  value="<?php echo $hasilnya->akhir; ?>"  /> Y-m-d. 
					</td>
				</tr>
				<tr>
					<td><strong>3. Description 1 :</strong></td>
					<td>
						<textarea type="text" name="deskripsi1"  value="<?php echo $hasilnya->deskripsi1; ?>" /><?php echo $hasilnya->deskripsi1; ?> </textarea>Exp: <font color=red>31</font> <strong>days have passed</strong>. Don't type <font color=red>31</font>, just description.
					</td>
				</tr>
				<tr>
					<td><strong>4. Description 2 :</strong></td>
					<td>
						<textarea type="text" name="deskripsi2"  value="<?php echo $hasilnya->deskripsi2; ?>" /><?php echo $hasilnya->deskripsi2; ?> </textarea> Exp: <font color=red>61</font> <strong> days to the big event live. </strong>. Don't type <font color=red>61</font>, just description. 
					</td>
				</tr>
				<tr>
					<td><strong>5. Model</strong></td>
					<td>
					<select name="model">
						<?php if ($hasilnya->model =='1' ) { ?>
							<option value="1" SELECTED>1</option>
							<option value="2">2</option>
						<?php } else { ?>
							<option value="1" >1</option>
							<option value="2" SELECTED>2</option>
						<?php } ?>

					</select> If model 1, don't empty form No.1. If model 2 don't change form No.1.
					</td>
				</tr>
				<tr>
					<td><strong>Save</strong></td>
					<td>
					<input type="submit" name="submit" value="Save" />
					</td>
				</tr>
				</table>
			</form> 
<?php } ?>
<?php 	
	if (isset($_POST['submit'])) {
		SettingEvents();
	}
} 


function widget_countdowndays($args) {
  extract($args);
  $options = get_option("widget_countdowndays");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Count Down Days',
	  
      );
  } 
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  echo countdowndays_widget($options);
  echo $after_widget;
}
function countdowndays_control()
{
  $options = get_option("widget_countdowndays");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Count Down Days',
	  
      );
  }    
  if ($_POST['countdowndays-submit'])
  {
    $options['title'] = htmlspecialchars($_POST['countdowndays-widgettitle']);
	update_option("widget_countdowndays", $options);
  }
    ?>
    <p>
        <label for="countdowndays-widgettitle">
            Title: 
        </label>	
        <input type="text" id="countdowndays-widgettitle" name="countdowndays-widgettitle" value="<?php echo $options['title'];?>" size="35"/>
        </p></ul>	
        <input type="hidden" id="countdowndays-submit" name="countdowndays-submit" value="1" />
    </p>
    <?php
}
function countdowndays_init()
{
  register_sidebar_widget( 'Count Down Days', 'widget_countdowndays');
  register_widget_control( 'Count Down Days', 'countdowndays_control', 300, 200 );    
}
add_action("plugins_loaded", "countdowndays_init");
?>
<?php



function countdowndays_widget(){
	 global $wpdb;
	ViewCountDownDays() ;
	
	
}


?>