<script>
    function userDelete()
    {
        window.location.href = window.location.href+"&delete=true";
    }
</script>

<div class="title">
    <h1><strong><?php echo $this->getFragmentArray()['title']?? ''; ?></strong></h1>
</div>
<div class="content center_parent">
    <div class="centered">
        <button class="btn btn-primary" onclick="location.href='/warehouse/settings'">Back</button>
        <h2><?php if(isset($this->getFragmentArray()['title'])) {echo ($this->getFragmentArray()['title']=="New user")? "Create a new user": ("Edit user: ".$this->getFragmentArray()['username']);} ?> </h2>
        <p>Username must be at least 4 characters, at least one of them a letter.<br>Password must be at least 8 characters which must contain at least one letter and one number.</p>
        <hr>
        <div class="container-fluid">
            <form method="POST">
                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" id="username" name="username" pattern="^(?=.*[A-Za-z])[A-Za-z\d]{4,}$" placeholder="<?php echo isset($this->getFragmentArray()['username']) ? $this->getFragmentArray()['username'] : '' ;?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                    <input type="password" placeholder="******" class="form-control" id="password" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="privilegelevel" class="col-sm-3 col-form-label">User type</label>
                    <div class="col-sm-1">
                        <select class="form-select" id="privilegelevel" name="privilegelevel">
                            <option <?php echo isset($this->getFragmentArray()['priviligelevel'])&&$this->getFragmentArray()['priviligelevel']==PrivilegeLevels::USER? "selected" :''; ?> value="user">User</option>
                            <option <?php echo isset($this->getFragmentArray()['priviligelevel'])&&$this->getFragmentArray()['priviligelevel']==PrivilegeLevels::ADMIN? "selected" :''; ?> value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <input type="submit" class="form-control" value="Save">
                    </div>
                    <?php echo isset($this->getFragmentArray()['title'])&& $this->getFragmentArray()['title']=='Edit user' ?
                    '<div class="col-sm-2">
                        <input type="button" class="form-control" value="Delete" onclick="userDelete()">
                    </div>' : '';
                    ?>
                    <?php echo isset($this->getFragmentArray()['validusername']) && !$this->getFragmentArray()['validusername'] ?
                    '<div class="col-sm-2">
                        <p class="message">Invalid username!</p>
                    </div>' : '';
                    ?>
                    <?php echo isset($this->getFragmentArray()['validpassword']) && !$this->getFragmentArray()['validpassword'] ?
                    '<div class="col-sm-2">
                        <p class="message">Invalid password!</p>
                    </div>' : '';
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>