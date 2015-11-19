<!-- The popover content -->

<div id="popover" style="display: none">
    <a href="#"><span class="glyphicon glyphicon-pencil"></span></a>
    <a href="#"><span class="glyphicon glyphicon-remove"></span></a>
</div>

<!-- JavaScript includes -->

<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="<?= $this->base ?>/assets/js/bootstrap.min.js"></script>
<script src="<?= $this->base ?>/assets/js/customjs.js"></script>

<script type="text/javascript">
    $(document).on("change", "#pay-card", function(e){
        var checked = $("#pay-card input[type=checkbox]").prop('checked');
        if (checked){
            $("#card-select-li").removeClass("hidden");
        }else{
            $("#card-select-li").addClass("hidden");
        }
    });

    $(document).on("click", ".order", function(e){
        var total = $(".totals .price").text();
        total = total.split("₦")[1];
        var productCode = (new Date()).getTime();
        var customer = "hello@mobnia.com";
        var invoice = parseInt(10000000 * Math.random());

        var payLoad = {
            'total' : total,
            'product_code': productCode,
            'customer': customer,
            'invoice' : invoice
        };

        var checked = $("#pay-card input[type=checkbox]").prop('checked');

        if (checked){
            var cardType = $("#card-select").val();
            if (cardType && cardType != ""){
                payLoad['with_card'] = true;
                payLoad['card_type'] = cardType;
            }
        }

        $.ajax({
            url: "/pay",
            type: "post",
            data: JSON.stringify(payLoad)
        }).done(function(e){
            console.log(e['url']);
            window.location.href = e['url'];
        }).fail(function(e){
            console.log(e);
        });
    });
</script>

</body>
</html>