<div class="title">
    <h1><strong>Settings</strong></h1>
</div>
<div class="container-fluid">
    <h2><strong>Users</strong></h2>
    <hr>
    <h3><strong>Your account</strong></h3>
    <hr>
    <form>
        <div class="form-group row">
            <label for="username" class="col-sm-2 col-form-label">Username</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="username" name="username" placeholder=<?php echo $this->getFragmentArray()['username']?? '-'?>>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-10">
            <input type="password" class="form-control" id="inputPassword3" placeholder="******">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2">Privilege level</div>
            <div class="col-sm-10">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck1">
                    <label class="form-check-label" for="gridCheck1">
                        Admin
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-30">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            <div class="col-sm-30">
                <button type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
        </form>
</div>
<div class="container-fluid">
    <h2><strong>Warehouses</strong></h2>
    <hr>
    <div class="container">
    </div>
</div>
            