<h1>Tests</h1>

<h2>Simple client/server</h2>
<ul>
	<li>API endpoint: /api/v1</li>
	<li>Authentiation: basic</li>
	<li><a href="/simple-client.php" target="_blank">TEST</a></li>
</ul>

<h2>OAuth2</h2>
<ul>
	<li>API endpoint: /api/v2</li>
	<li>Authentiation: CLient Credentials</li>
	<li><a href="/oauth2-client.php" target="_blank">TEST</a></li>
</ul>

<h2>Server with config file</h2>
<ul>
	<li>API endpoint: /api/v3</li>
	<li>Authentiation: anonymous</li>
	<li>Server config: <?php echo realpath(__DIR__ . "/../config/server.yml"); ?>
	<li><a href="/config-client.php" target="_blank">TEST</a></li>
</ul>

