jQuery(document).ready(function($){



    $( "#discounts" ).sortable();
    $( "#discounts" ).disableSelection();

    $("#discounts").on("keypress keyup blur", ".dc-numberonly", function (event) {
        //this.value = this.value.replace(/[^0-9\.]/g,'');
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }

    });



    $(".add-discount").on("click", function(e){

        e.preventDefault();


        field = "<div class=\"form-inline discount-move\">\n" +
            "        <p>\n" +
            "         <label>Min Amount:</label> <input type=\"text\" placeholder=\"Min Amount\" name=\"discount_min[]\" class='dc-numberonly' required >" +
            "         <label>Max Amount:</label> <input type=\"text\" placeholder=\"Max Amount\" name=\"discount_max[]\" class='dc-numberonly' required >" +
            "         <label>Discount Amount:</label> <input type=\"text\" placeholder=\"Discount\" name=\"discount_amount[]\" class='dc-numberonly' required >" +
            "        <button type=\"button\" class=\"button delete-discount\">Delete</button>\n" +
            "        </p>\n" +
            "    </div>";


        $("#discounts").append(field);

    });


    $("#discounts").on("click", ".delete-discount", function(e){

        e.preventDefault();


        $(this).parent().parent().remove();



    });





    if($("#discount_type").val() === "value_based_discount"){

        $(".coupon_amount_field").hide();
        $(".dynamic_discount_field ").show();

    }else{
        $(".coupon_amount_field").show();
        $(".dynamic_discount_field ").hide();

    }



    $("#discount_type").change(function(){


       if($(this).val() === "value_based_discount"){

           $(".coupon_amount_field").hide();
           $(".dynamic_discount_field ").show();

       }else{
           $(".coupon_amount_field").show();
           $(".dynamic_discount_field ").hide();

       }


    });


});