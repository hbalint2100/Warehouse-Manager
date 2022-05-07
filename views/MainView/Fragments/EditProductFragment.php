<script>
        window.onload = function(){
            document.getElementById("netprice").addEventListener("input", function (e) {
                document.getElementById("grossprice").value = document.getElementById("netprice").value * 1.27;
            });
            window.stocks = 1;
        };

        function selected()
        {
            //todo remove duplicate warehouse
            for (let i = 1; i < <?php echo $this->getFragmentArray()['warehousecount']?? 1?>; i++) {
                document.getElementById("warehouseid-"+i).removeChild()
            }
        }

        function addStock()
        {
            if(window.stocks==<?php echo $this->getFragmentArray()['warehousecount']?? 1?>)
            {
                return;
            }
            var row = document.getElementById("stocks").insertRow(0);
            row.insertCell(0).innerHTML = "<select class=\"form-select\" id=\"warehouse-"+(++window.stocks)+"\" name=\"warehouseid-1\">"+
                                            "<?php 
                                                foreach($this->getFragmentArray()['warehouses'] as $warehouse)
                                                {
                                                    echo '<option value=\"'.$warehouse->getWarehouseId().'\">'.$warehouse->getWarehouseName().'</option>';
                                                }
                                            ?>"+"</select>";
            row.insertCell(1).innerHTML = "<input type=\"number\" min=\"0\" class=\"form-control\" id=\"amount-1\" name=\"amount-1\">"
        }
</script>
<div class="title">
    <h1><strong><?php echo $this->getFragmentArray()['title']?? ''; ?></strong></h1>
</div>
<div class="content center_parent">
    <div class="centered">
        <button class="btn btn-primary" onclick="location.href='/warehouse/products'">Back</button>
        <h2><?php if(isset($this->getFragmentArray()['title'])) {echo ($this->getFragmentArray()['title']=="New product")? "Create a new product": ("Edit product: ".$this->getFragmentArray()['productname']);} ?> </h2>
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
                                <tr>
                                    <td>
                                        <select class="form-select" id="warehouse-1" name="warehouseid-1">
                                            <?php 
                                                foreach($this->getFragmentArray()['warehouses'] as $warehouse)
                                                {
                                                    echo '<option value="'.$warehouse->getWarehouseId().'">'.$warehouse->getWarehouseName().'</option>';
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="amount-1" name="amount-1">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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