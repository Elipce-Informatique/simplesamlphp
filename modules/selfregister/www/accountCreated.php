<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex">
	<title>Compte créé</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?= SimpleSAML_Module::getModuleURL('selfregister/img/favicon.ico') ?>" />
	<!-- CSS -->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= SimpleSAML_Module::getModuleURL('selfregister/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<!-- Container -->
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<img class="img im-responsive center-block"
				 src="<?= SimpleSAML_Module::getModuleURL('selfregister/img/logo.jpg') ?>"/>
			<div class="jumbotron text-center">
				<h1><span class="glyphicon glyphicon-ok"></span></h1>
				<p>Compte créé avec succès !</p>
				<p class="text-muted">Vous pouvez modifier vos <em>informations personnelles</em> à tout moment en cliquant <a href="<?= SimpleSAML_Module::getModuleURL('selfregister/index.php') ?>">ici</a>.</p>
				<hr/>
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title"><strong>Services disponibles</strong></h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
							<a href="https://demo.elipce.com/" class="list-group-item">Portail de démonstration EDS</a>
							<a href="'https://sso.online.tableau.com/" class="list-group-item">Tableau Software</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br/>
<!-- Footer -->
<footer class="footer">
	<div class="container">
		<p class="text-muted">Elipce Informatique &copy;</p>
	</div>
</footer>
<!-- JS -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</body>
</html>