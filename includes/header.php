<nav class="navbar navbar-expand-sm navbar-dark">
	<a class="navbar-brand" href="account.php">Hi, <?php echo $_SESSION['first'];?>!</a>

	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
	    aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbar">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item">
				<a class="nav-link" href="search.php">Search</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="upload.php">Upload</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="account.php">Account</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="logout.php">Log Out</a>
			</li>
		</ul>
	</div>
</nav>