<?php

include ("header.php");

?>

<div class="container text-center">

    <div class="col-md-5 col-sm-12">
        <div class="bigcart"></div>
        <h1>Paga API Demo @ <a href="http://lagos.startupweek.co/" target="_blank">Startup Weekend Lagos</a></h1>
        <p>
            A demo application showing some of the use cases of the Paga Connect API.
        </p>
    </div>

    <div class="col-md-7 col-sm-12 text-left">
        <ul>
            <li class="row list-inline columnCaptions">
                <span>QTY</span>
                <span>ITEM</span>
                <span>Price</span>
            </li>
            <?php
                $sum = 0;
                foreach ($this->items as $item){
                    $title = $item['name'];
                    $qty = $item['qty'];
                    $price = $item['price'];
                    $sum += $price;
                    echo "
                        <li class='row'>
                            <span class='quantity'>$qty</span>
                            <span class='itemName'>$title</span>
                            <span class='popbtn'><a class='arrow'></a></span>
                            <span class='price'>₦$price</span>
                        </li>
                    ";
                }
            ?>

            <li class="demo" id="pay-card">
                <label>
                    <input type="checkbox"> Pay With Card
                </label>
            </li>

            <li class="demo hidden" id="card-select-li">
                <label for="card-select">Choose Card Type</label>
                <select id="card-select" class="form-control">
                    <option value="mastercard">Master Card</option>
                    <option value="verve">Verve</option>
                    <option value="visa">Visa</option>
                </select>
            </li>
            <?php

                echo "
                    <li class='row totals'>
                        <span class='itemName'>Total:</span>
                        <span class='price'>₦$sum</span>
                        <span class='order'> <a class='text-center'>ORDER</a></span>
                    </li>
                ";
            ?>
        </ul>
    </div>

</div>

<?php

include("footer.php");

?>