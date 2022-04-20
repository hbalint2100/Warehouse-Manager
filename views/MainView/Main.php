<div class="container container-main">
	<div class="row">
		<div class="left">
            <?php include_once $this->getNavbarPath() ?? throw new FileNotFoundException('Missing Navbar in Main.php') ?>
        </div> <!-- end left -->
        <div class="right">
            <?php include_once $this->getFragmentPath() ?? throw new FileNotFoundException('Missing Fragment in Main.php') ?>
        </div>
    </div>
</div>