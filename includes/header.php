<nav class="navbar navbar-expand-sm navbar-dark">
	<a class="navbar-brand" href="/">Hi, <?php echo $_SESSION['first'];?>!</a>

	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
	    aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbar">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item">
				<a id="search" class="nav-link" href="/search">Search</a>
			</li>
			<li class="nav-item">
				<a id="create-album" class="nav-link" href="/create-album">Create Album</a>
			</li>
			<li class="nav-item">
				<a id="account" class="nav-link" href="/account">Account</a>
			</li>
			<li class="nav-item">
				<a id="webmail" class="nav-link" href="https://steeltotech.com:2096" target="_blank" rel="noopener noreferrer">Webmail</a>
			</li>
			<li class="nav-item">
				<a id="logout" class="nav-link" href="/logout">Log Out</a>
			</li>
		</ul>
	</div>
</nav>