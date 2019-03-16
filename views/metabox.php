<?php
/**
 * Project: woocommerce-dynamic-coupons
 * User: Brendan Doyle 
 * Date: 14/03/2019
 * Time: 13:45
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="dynamic-coupons">



    <div class="alert alert-warning">
        <p>Discount rules are applied in the order below, so make sure your values are in order. You can drag and drop them to reorder. Click Update or Publish when finished.</p>
    </div>

    <div id="discounts">

       <?php foreach($discounts as $discount){?>

        <div class="form-inline">
            <p>
                <label>Min Amount:</label> <input type="text" placeholder="Min Amount" name="discount_min[]" value="<?php echo $discount['min'];?>" required >
                <label>Max Amount:</label> <input type="text" placeholder="Max Amount" name="discount_max[]" value="<?php echo $discount['max'];?>" required >
                <label>Discount Amount:</label> <input type="text" placeholder="Discount" name="discount_amount[]" value="<?php echo $discount['amount'];?>" required >
                <button type="button" class="button delete-discount">Delete</button>
            </p>
        </div>


        <?php } ?>

</div>

    <button type="button" class="button button-primary add-discount">Add Discount</button>




</div>
