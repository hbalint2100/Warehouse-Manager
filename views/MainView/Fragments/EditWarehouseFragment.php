<script>
    function warehouseDelete()
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
        <button class="btn btn-primary" onclick="location.href='/warehouse/settings'">Back</button>
        <h2><?php if(isset($this->getFragmentArray()['title'])) 
                {echo ($this->getFragmentArray()['title']=="New warehouse")? "Add a new warehouse" :
                     ("Edit warehouse: ".(isset($this->getFragmentArray()['warehousename'])? $this->getFragmentArray()['warehousename'] :''));} 
                ?> 
        </h2>
        <hr>
        <form id="warehouse_form" method="POST">
            <div class="form-group row">
                <label for="warehousename" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="warehousename" name="warehousename" placeholder="<?php echo isset($this->getFragmentArray()['warehousename']) ? $this->getFragmentArray()['warehousename'] : '' ;?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="details" class="col-sm-2 col-form-label">Details</label>
                <div class="col-sm-10">
                    <textarea rows="4" class="form-control"  id="details" form="warehouse_form" name="details" placeholder="<?php echo isset($this->getFragmentArray()['details']) ? $this->getFragmentArray()['details'] : '' ;?>"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
                <?php echo isset($this->getFragmentArray()['title'])&& $this->getFragmentArray()['title']=='Edit warehouse' ?
                    '<div class="col-sm-2">
                        <button type="button" class="btn btn-danger" onclick="warehouseDelete()">Delete</button>
                    </div>' : '';
                ?>
            </div>
        </form>
    </div>
</div>