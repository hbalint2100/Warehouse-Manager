<script>
        window.onload = function(){
            document.getElementById("netprice").addEventListener("input", function (e) {
                document.getElementById("grossprice").value = Math.round(document.getElementById("netprice").value * 1.27);
                
            });
            window.stocks = <?php echo isset($this->getFragmentArray()['stocks'])? count($this->getFragmentArray()['stocks']) : '1'; ?>;
            document.getElementById("stocks_length").value = window.stocks;
            document.getElementById("warehouse-1").selectedIndex = 0;

        };

        function selected(selected_id)
        {
            //todo remove duplicate warehouse
            for (let i = 1; i <= <?php echo $this->getFragmentArray()['warehousecount']?? 1?>; i++) {
                if(("warehouse-"+i)!=selected_id)
                {
                    for(let j = 0; j < document.getElementById("warehouse-"+i).length; j++)
                    {
                        document.getElementById("warehouse-"+i).options[j].removeAttribute("hidden");
                        if(document.getElementById("warehouse-"+i).selectedIndex==document.getElementById(selected_id).selectedIndex&&j!=document.getElementById(selected_id).selectedIndex)
                        {
                            document.getElementById("warehouse-"+i).selectedIndex = j;
                        }
                    }
                    document.getElementById("warehouse-"+i).options[document.getElementById(selected_id).selectedIndex].setAttribute("hidden",true);
                }
            }
        }

        function addStock()
        {
            if(window.stocks==<?php echo $this->getFragmentArray()['warehousecount']?? 1?>)
            {
                return;
            }
            var row = document.getElementById("stocks").insertRow(0);
            row.insertCell(0).innerHTML = "<select onchange=\"selected(this.id)\" class=\"form-select\" id=\"warehouse-"+(++window.stocks)+"\" name=\"warehouseid-"+(window.stocks)+"\">"+
                                            "<?php 
                                                foreach($this->getFragmentArray()['warehouses'] as $warehouse)
                                                {
                                                    echo '<option value=\"'.$warehouse->getWarehouseId().'\">'.$warehouse->getWarehouseName().'</option>';
                                                }
                                            ?>"+"</select>";
            row.insertCell(1).innerHTML = "<input type=\"number\" min=\"0\" class=\"form-control\" id=\"amount-"+(window.stocks)+"\" name=\"amount-"+(window.stocks)+"\">"

            for (let i = 1; i < <?php echo $this->getFragmentArray()['warehousecount']?? 1?>; i++) 
            {
                if(("warehouse-"+i)!=("warehouse-"+window.stocks))
                {
                    document.getElementById("warehouse-"+window.stocks).options[document.getElementById("warehouse-"+i).selectedIndex].setAttribute("hidden",true);
                }
            }
            for(let j = 0; j < document.getElementById("warehouse-"+window.stocks).length; j++)
            {
                if(!document.getElementById("warehouse-"+window.stocks).options[j].getAttribute("hidden"))
                {
                    document.getElementById("warehouse-"+window.stocks).selectedIndex = j;
                }
            }
            document.getElementById("stocks_length").value = window.stocks;
        }

        function productDelete()
        {
            if(!window.location.href.includes("&delete=true",0))
            {
                window.location.href = window.location.href+"&delete=true";
            }
        }
</script>
<div class="title">
    <h1><strong><?php echo $this->getFragmentArray()['title']?? ''; ?></strong></h1>
</div>
<div class="content center_parent">
    <div class="centered">
        <button class="btn btn-primary" onclick="location.href='/warehouse/products'">Back</button>
        <h2><?php if(isset($this->getFragmentArray()['title'])) {echo ($this->getFragmentArray()['title']=="New product")? "Create a new product": ("Edit product: ".((isset($this->getFragmentArray()['productname']))? $this->getFragmentArray()['productname'] :''));} ?> </h2>
        <hr>
        <div class="container-fluid">
            <form method="POST">
                <div class="form-group row">
                    <label for="itemnumber" class="col-sm-2 col-form-label">Item number</label>
                    <div class="col-sm-2">
                    <input type="text" class="form-control" id="itemnumber" name="itemnumber" placeholder="<?php echo isset($this->getFragmentArray()['itemnumber']) ? $this->getFragmentArray()['itemnumber'] : 'Item number' ;?>">
                    </div>
                    <label for="productname" class="col-sm-1 col-form-label">Name</label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="productname" name="productname" placeholder="<?php echo isset($this->getFragmentArray()['productname']) ? $this->getFragmentArray()['productname'] : 'Name' ;?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="category" class="col-sm-2 col-form-label">Category</label>
                    <div class="col-sm-4">
                    <input type="text" class="form-control" id="category" name="category" placeholder="<?php echo isset($this->getFragmentArray()['category']) ? $this->getFragmentArray()['category'] : 'Category' ;?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="netprice" class="col-sm-2 col-form-label">Net price</label>
                    <div class="col-sm-3">
                    <input type="number" min="0" class="form-control" id="netprice" name="netprice" placeholder="<?php echo isset($this->getFragmentArray()['netprice']) ? $this->getFragmentArray()['netprice'] : 'Net price' ;?>">
                    </div>
                    <label for="grossprice" class="col-sm-2 col-form-label">Gross price</label>
                    <div class="col-sm-3">
                    <input type="number" min="0" class="form-control" id="grossprice" name="grossprice" placeholder="<?php echo isset($this->getFragmentArray()['grossprice']) ? $this->getFragmentArray()['grossprice'] : 'Gross price' ;?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-7">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Warehouse name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="stocks">
                                <?php 
                                if(isset($this->getFragmentArray()['stocks'])&&!is_null($this->getFragmentArray()['stocks']))
                                {
                                    $i = 1;
                                    foreach($this->getFragmentArray()['stocks'] as $stock)
                                    {
                                        echo '  <tr>
                                                    <td>
                                                        <select onchange="selected(this.id)" class="form-select" id="warehouse-'.$i.'" name="warehouseid-'.$i.'">
                                                            '; 
                                                                foreach($this->getFragmentArray()['warehouses'] as $warehouse)
                                                                {
                                                                    echo '<option value="'.$warehouse->getWarehouseId().'" '. (($stock->getWarehouse()->getWarehouseId()==$warehouse->getWarehouseId())?'selected ':'').'>'.$warehouse->getWarehouseName().'</option>';
                                                                }
                                                            echo '
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" class="form-control" placeholder="'.$stock->getAmount().'" id="amount-'.$i.'" name="amount-'.$i.'">
                                                    </td>
                                                </tr>';
                                        $i++;
                                    }
                                }
                                else
                                { 
                                    echo '
                                <tr>
                                    <td>
                                        <select onchange="selected(this.id)" class="form-select" id="warehouse-1" name="warehouseid-1">
                                            '; 
                                                foreach($this->getFragmentArray()['warehouses'] as $warehouse)
                                                {
                                                    echo '<option value="'.$warehouse->getWarehouseId().'">'.$warehouse->getWarehouseName().'</option>';
                                                }
                                            echo '
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="amount-1" name="amount-1">
                                    </td>
                                </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <input type="hidden" id="stocks_length" value="1" name="stocks_length">
                        <button type="button" onclick="addStock()">Add warehouse</button>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <input type="submit" class="form-control" value="Save">
                    </div>
                    <?php echo isset($this->getFragmentArray()['title'])&& $this->getFragmentArray()['title']=='Edit product' ?
                    '<div class="col-sm-2">
                        <input type="button" class="form-control" value="Delete" onclick="productDelete()">
                    </div>' : '';
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>