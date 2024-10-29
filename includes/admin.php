<?php   
    if($_POST['beevou_hidden'] == 'Y') {  
        //Form data sent  
        $username = $_POST['beevou_username'];  
        update_option('beevou_username', $username);
		$password = $_POST['beevou_password'];  
        update_option('beevou_password', $password); 
		
		$show_icon = ($_POST['beevou_show_icon'] == 'y') ? 'y' : 'n';  
        update_option('beevou_show_icon', $show_icon);
		$show_description = ($_POST['beevou_show_description'] == 'y') ? 'y' : 'n';  
        update_option('beevou_show_description', $show_description);
		
		$show_icon = ($show_icon == 'n') ? '' : 'checked';
		$show_description = ($show_description == 'n') ? '' : 'checked';
?>  
<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>  
<?php  
    } else {  
        //Normal page display  
        $username = get_option('beevou_username'); 
		$password = get_option('beevou_password');
		
		$show_icon = (get_option('beevou_show_icon') == 'n') ? '' : 'checked';
		$show_description = (get_option('beevou_show_description') == 'n') ? '' : 'checked';
    }  
?> 

<div class="wrap">  
    <?php echo "<h2>" . __( 'Beevou Options', 'oscimp_trdom' ) . "</h2>"; ?>  
      
    <form name="beevou_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
        <input type="hidden" name="beevou_hidden" value="Y">  
        <?php    echo "<h4>" . __( 'Beevou User Settings', 'beevou_trdom' ) . "</h4>"; ?>  
        <p><?php _e("Beevou.net username: " ); ?><input type="text" name="beevou_username" value="<?php echo $username; ?>" size="20"> <?php _e("your beevou.net account username"); ?></p>
		<p><?php _e("Beevou.net password: " ); ?><input type="password" name="beevou_password" value="<?php echo $password; ?>" size="20"> <?php _e("your beevou.net account password"); ?></p>
        <hr /> 
			
		<?php    echo "<h4>" . __( 'Vouchers Display Settings', 'beevou_trdom' ) . "</h4>"; ?>  
        <p><?php _e("Show voucher icon: " ); ?><input type="checkbox" name="beevou_show_icon" value="y" <?php echo $show_icon; ?>> <?php _e("check to display the voucher icon in the vouchers list"); ?></p>
		<p><?php _e("Show voucher description: " ); ?><input type="checkbox" name="beevou_show_description" value="y" <?php echo $show_description; ?>> <?php _e("check to display the voucher description in the vouchers list"); ?></p>	
		
        <p class="submit">  
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'beevou_trdom' ) ?>" class="button button-primary" />  
        </p>  
    </form>
</div> 