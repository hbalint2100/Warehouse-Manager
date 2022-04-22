<script>
    function Delete()
    {

    }
</script>

<div class="title">
    <h1><strong><?php echo $this->getFragmentArray()['title']?? ''; ?></strong></h1>
</div>
<div class="content center_parent">
    <div class="centered">
        <button class="btn btn-primary" onclick="history.back()">Back</button>
        <h2>Create a new user</h2>
        <hr>
        <div class="container-fluid">
            <form method="POST">
                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                    <input type="text" class="form-control" id="username" name="username">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" name="password">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="privilegelevel" class="col-sm-3 col-form-label">User type</label>
                    <div class="col-sm-1">
                        <select class="form-select" id="privilegelevel" name="privilegelevel">
                            <option value="admin">Admin</option>
                            <option selected value="user">User</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <input type="submit" class="form-control" value="Save">
                    </div>
                    <?php echo $this->getFragmentArray()['title']=='Edit user' ?
                    '<div class="col-sm-2">
                        <input type="button" class="form-control" value="Delete" onclick="Delete">
                    </div>' : '';
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>