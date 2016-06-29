<?php
/**
 * Initialization
 */

// Load config
$config = SimpleSAML_Configuration::getInstance();
$uregconf = SimpleSAML_Configuration::getConfig('module_selfregister.php');
$formFields = $uregconf->getArray('formFields');

// Init database access
$store = sspmod_selfregister_Storage_UserCatalogue::instantiateStorage();

// Get a reference to our authentication source
$asId = $uregconf->getString('auth');
$as = new SimpleSAML_Auth_Simple($asId);

// Require the usr to be authentcated
$as->requireAuth();

// Retrieve attributes of the user
$attributes = $as->getAttributes();

// Init session
$session = SimpleSAML_Session::getSessionFromRequest();
$data = $session->getData('selfregister:updated', 'attributes');
if ($data !== NULL) {
	$attributes = $data;
}

// Form fields
$fields = array('pw1', 'pw2');

// Init feedback message
$feedback = [
	'error' => null,
	'success' => null
];

/**
 * Form was sent
 */
if(array_key_exists('sender', $_REQUEST)) {
	try {

		// Create validator
		$validator = new sspmod_selfregister_Registration_Validation(
			$formFields,
			$fields);

		// Validate inputs
		$validValues = $validator->validateInput();
		$newPw = sspmod_selfregister_Util::validatePassword($validValues);

		// Change password in database
		$store->changeUserPassword($attributes[$store->userIdAttr][0], $newPw);

		// Set feedback message
		$feedback['success'] = 'Votre mot de passe a été modifié avec succès !';

	} catch(sspmod_selfregister_Error_UserException $e) {
		// Set feedback message
		$feedback['error'] = 'Votre saisie est invalide !';
	}
	/**
	 * Logout
	 */
} elseif(array_key_exists('logout', $_GET)) {
	$as->logout(SimpleSAML_Module::getModuleURL('selfregister/index.php'));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex">
	<title>Changer de mot de passe</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?= SimpleSAML_Module::getModuleURL('selfregister/img/favicon.ico') ?>" />
	<!-- CSS -->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= SimpleSAML_Module::getModuleURL('selfregister/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">Elipce Informatique</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Mon compte <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="index.php">Modifier mes informations</a></li>
						<li><a href="changePassword.php">Changer mon mot de passe</a></li>
						<li><a href="delUser.php">Supprimer mon compte</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="index.php?logout=true">Se déconnecter</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>
<!-- Container -->
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<img class="img im-responsive center-block" src="<?= SimpleSAML_Module::getModuleURL('selfregister/img/logo.jpg') ?>"/>
			<form role="form" action="." method="post">
				<h2>Mot de passe <small>Changez votre mot de passe.</small>
				</h2>
				<hr class="colorgraph">
				<!-- Messages -->
				<? if ($feedback['error']): ?>
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							<div class="alert alert-danger alert-dismissible text-center" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
										aria-hidden="true">&times;</span></button>
								<span class="glyphicon glyphicon-exclamation-sign"></span>
								&nbsp;<?= $feedback['error'] ?>
							</div>
						</div>
					</div>
				<? elseif ($feedback['success']): ?>
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							<div class="alert alert-success alert-dismissible text-center" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
										aria-hidden="true">&times;</span></button>
								<span class="glyphicon glyphicon-ok"></span>
								&nbsp;<?= $feedback['success'] ?>
							</div>
						</div>
					</div>
				<? endif; ?>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-asterisk"></span>
								</div>
								<input type="password" name="pw1" id="pw1" class="form-control input-lg" autofocus
									   size="20" placeholder="Nouveau mot de passe" tabindex="1" required>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-asterisk"></span>
								</div>
								<input type="password" name="pw2" id="pw2" required tabindex="2" size="20"
									   class="form-control input-lg" placeholder="Confirmez le mot de passe" >
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-xs-6 col-sm-6 col-md-6">
						<input type="submit" name="sender" class="btn btn-lg btn-success btn-block" value="Modifier">
					</div>
				</div>
			</form>
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