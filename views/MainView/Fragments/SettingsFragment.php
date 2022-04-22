<script>
    function setEditable()
    {
        if(document.getElementById("edit_save").firstChild.data!="Save")
        {
            document.getElementById("username").disabled = false;
            document.getElementById("password").disabled = false;
            document.getElementById("privilegelevel").disabled = false;
            document.getElementById("edit_save").firstChild.data = "Save";
        }
    }

    function editUser(clicked_id)
    {
        window.location.href = window.location.href+"/edit_user?userid="+clicked_id;
    }
    function addUser()
    {
        window.location.href = window.location.href+"/add_user";
    }
    function addWarehouse()
    {
        window.location.href = window.location.href+"/add_warehouse";
    }
</script>
<div class="title">
    <h1><strong>Settings</strong></h1>
</div>
<div class="content">
<div class="container-fluid pding mx-auto">
    <div class="row h-100 aligned-row">
        <div class="col-sm-7 h-100 container-fluid">
            <h2><strong>Users</strong></h2>
            <hr>
            <h3><strong>Your account</strong></h3>
            <hr>
            <form>
                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" id="username" name="username" placeholder=<?php echo $this->getFragmentArray()['username']?? '-'?> disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" name="password" placeholder="******" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="privilegelevel" class="col-sm-2 col-form-label">User type</label>
                    <div class="col-sm-1">
                        <select class="form-select" id="privilegelevel" name="privilegelevel" disabled>
                            <?php echo $this->getFragmentArray()['privilegelevel']==PrivilegeLevels::ADMIN? '<option selected value="admin">Admin</option>' : '' ;?>
                            <option <?php echo $this->getFragmentArray()['privilegelevel']==PrivilegeLevels::USER? 'selected' : '' ;?> value="user">User</option>
                        </select>
                    </div>
                </div>
                </form>
        </div>
        <div class="col-sm-5 h-100" >
            <button type="button" onclick="setEditable()" id="edit_save" class="btn btn-primary btns">Edit</button>
            <button type="button" class="btn btn-danger btns">Delete</button>
        </div>
    </div>
    <hr>
</div>
<div class="container-fluid pding">
    <?php 
        if($this->showOtherUsers())
        {  
            $output = '';
            if(!is_null($this->getFragmentArray())&&!is_null($this->getFragmentArray()['users']))
            {
                foreach($this->getFragmentArray()['users'] as $user)
                {
                    $output = $output . "<tr><td>".$user->getUserName()."</td><td>******</td><td>".($user->getPriviligeLevel()==PrivilegeLevels::ADMIN? 'Admin': 'User').
                    "</td><td><button id=\"".$user->getUserId()."\" onclick=\"editUser(this.id)\">Edit</button></td></tr>";
                }
            }
            echo
            "<h3><strong>Other users</strong></h3>
            <hr>
            <table class=\"table\"
                <thead>
                    <th>Username</th>
                    <th>Password</th>
                    <th>User type</th>
                    <th></th>
                </thead>
                <tbody>".
                $output
                ."</tbody>
            </table>" ;

            echo "<button onclick=\"addUser()\" class=\"btn btn-primary\">Add user</button>";
        }
    ?>
</div>
<div class="container-fluid pding">
    <h2><strong>Warehouses</strong></h2>
    <hr>
    <div class="container-fluid">
        <table class="table">
            <thead>
                <th>Name</th>
                <th>Details</th>
                <th></th>
            </thead>
            <tbody>
                <?php 
                    foreach($this->getFragmentArray()['warehouses'] as $warehouse)
                    {
                        echo '<tr<td>'.$warehouse->getWarehouseName().'</td><td>'.$warehouse->getDetails().'</td><td><button class="btn btn-primary"></button></td>>';
                    }
                ?>
            </tbody>
        </table>
        <?php echo $this->editWarehouses()? '<button onclick="addWarehouse()" class="btn btn-primary">New warehouse</button>': '';?>
    </div>
</div>
</div>
            