<script>
    function editProduct(clicked_id)
    {
        window.location.href = window.location.href.split('?')[0]+"/edit_product?productid="+clicked_id;
    }

    function addProduct()
    {
        window.location.href = window.location.href.split('?')[0]+"/add_product";
    }

    function next()
    {
        let maxSize = <?php echo $this->getFragmentArray()['maxSize']?? '0'?>;
        if(maxSize!=0&&!window.location.href.includes("?page",0))
        {
            window.location.href = window.location.href+"?page=1";
        }
        else if(maxSize!=0&&maxSize>window.location.href.split('?page=')[1])
        {
            window.location.href = window.location.href.split('?')[0] + "?page=" + (++(window.location.href.split('?page=')[1]));
        }
    }
    function previous()
    {
        if(!window.location.href.includes("?page",0))
        {
            window.location.href = window.location.href+"?page=0"+page;
        }
        else if((window.location.href.split('?page=')[1])!=0)
        {
            window.location.href = window.location.href.split('?')[0] + "?page=" + (--(window.location.href.split('?page=')[1]));
        }
    }

</script>

<div class="title">
    <h1><strong><?php echo $this->getFragmentArray()['title']?? ''; ?></strong></h1>
</div>
<div class="content center_parent">
    <div class="centered">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"><button class="btn" onclick="previous()">Previous</button></li>
                <li class="page-item"><button class="btn" onclick="next()">Next</a></li>
                <li class="page-item">Page: <?php echo ($this->getFragmentArray()['page_start']?? '').'-'.($this->getFragmentArray()['page_end']?? '')?></li>
                <li> <input type="text" name="search" form="search" placeholder="Name or item number"> </li>
                <li><input class="btn btn-primary" type="submit" form="search" value="Search"></li>
                <form id="search"></form>
            </ul>
        </nav>
        <?php 
        if(isset($this->fragmentArray['products']))
        {
            foreach($this->fragmentArray['products'] as $product)
            {
                    echo '
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h4 class="mb-0">
                                <button class="btn_title" data-toggle="collapse" data-target="#collapse-'.$product->getProductId().'" aria-expanded="true" aria-controls="collapseOne">
                                    <table class="btn_table">
                                        <tr><td>'.$product->getItemNumber().'</td><td>'.$product->getName().'</td></tr>
                                    </table>
                                </button>
                            </h4>
                        </div>
                        <div id="collapse-'.$product->getProductId().'" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body scrollable">
                                <table class="table">
                                    <thead>
                                        <th>Item number</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Net price</th>
                                        <th>Gross price</th>
                                    </thead>
                                    <tbody>
                                        <tr><td>'.$product->getItemNumber().'</td><td>'.($product->getName()?? '-').'</td><td>'.$product->getCategory().'</td><td>'.$product->getNetPrice().' HUF</td><td>'.($product->getGrossPrice()!=0? $product->getGrossPrice().' HUF' : '-' ).'</td></tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="card_warehouse">
                                    <div>
                                        <h5><strong>Stocks</strong></h5>
                                        <table class="table wrap">
                                            <thead>
                                                <th>Warehouse name</th>
                                                <th>Amount</th>
                                            </thead>
                                            <tbody>
                                                ';
                                                foreach($product->getStocks() as $stock)
                                                {
                                                    echo '<tr><td>'.$stock->getWarehouse()->getWarehouseName().'</td><td>'.$stock->getAmount().'</td></tr>';
                                                }
                                                echo
                                                '
                                            </tbody>
                                        </table>
                                    </div>
                                    <button id="'.$product->getProductId().'" onclick="editProduct(this.id)" type="button" class="btn btn-primary">Edit details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }        
        ?>
    </div>
    <button onclick="addProduct()" class="kc_fab_main_btn">+</button>
</div>