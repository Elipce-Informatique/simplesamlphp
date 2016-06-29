<?php
/**
 * Initialization
 */

// Load module config
$config = SimpleSAML_Configuration::getInstance();
$uregconf = SimpleSAML_Configuration::getConfig('module_selfregister.php');

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

// Fetch form fields from config
$formFields = $uregconf->getArray('formFields');
$reviewAttr = $uregconf->getArray('attributes');

// Sort show fields
$showFields = array();
foreach ($formFields as $name => $field) {
	if(array_key_exists('show',$field['layout']) && $field['layout']['show']) {
		$showFields[] = $name;
	}
}

// Init database access
$store = sspmod_selfregister_Storage_UserCatalogue::instantiateStorage();

// Init feedback messages
$feedback = [
	'error' => null,
	'success' => null
];

/**
 * Form was sent
 */
if (array_key_exists('sender', $_POST)) {
	try {
		// Create validator
		$validator = new sspmod_selfregister_Registration_Validation(
			$formFields,
			$showFields
		);
		$validValues = $validator->validateInput();

		// Filter password
		$remove = array('userPassword' => NULL);
		$reviewAttr = array_diff_key($reviewAttr, $remove);

		// Inputs validation
		$userInfo = sspmod_selfregister_Util::processInput(
			$validValues,
			$reviewAttr
		);

		// Always prevent changes on User identification param in DataSource and Session
		unset($userInfo[$store->userIdAttr]);

		// Update user data in database
		$store->updateUser($attributes[$store->userIdAttr][0], $userInfo);

		// I must override the values with the userInfo values due in processInput i can change the values.
		// But now atributes from the logged user is obsolete, So I can actualize it and get values from session
		// but maybe we could have security problem if IdP isnt configured correctly.
		foreach($userInfo as $name => $value) {
			$attributes[$name][0] = $value;
		}

		$session->setData('selfregister:updated', 'attributes', $attributes, SimpleSAML_Session::DATA_TIMEOUT_SESSION_END);
		$values = sspmod_selfregister_Util::filterAsAttributes($attributes, $reviewAttr);

		// Set feedback message
		$feedback['success'] = 'Vos informations ont bien été modifiées !';

	} catch(sspmod_selfregister_Error_UserException $e){

		// Some user error detected
		$values = $validator->getRawInput();
		$values['mail'] = $attributes['mail'][0];

		// Set feedback message
		$feedback['error'] = 'Votre saisie est invalide !';
	}
	/**
	 * Logout
	 */
} elseif(array_key_exists('logout', $_GET)) {
	$as->logout(SimpleSAML_Module::getModuleURL('selfregister/index.php'));
/**
 * Callback URL
 */
} else {
	// Get user attributes
	$values = sspmod_selfregister_Util::filterAsAttributes($attributes, $reviewAttr);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex">
	<title>Mon compte</title>
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
			<img class="img im-responsive center-block" src="img/logo.jpg"/>
			<form role="form" action="." method="post">
				<h2>Mes informations <small>Modifiez vos informations.</small>
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
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-user"></span>
								</div>
								<input type="text" name="sn" id="sn" class="form-control input-lg" autofocus
									   value="<?= $values['sn'] ?>" placeholder="Nom" tabindex="1" required>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-user"></span>
								</div>
								<input type="text" name="givenName" id="givenName" class="form-control input-lg"
									   value="<?= $values['givenName'] ?>" placeholder="Prénom" tabindex="2" required>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-envelope"></span>
						</div>
						<input type="email" name="mail" id="mail" class="form-control input-lg" placeholder="Email"
							   value="<?= $values['mail'] ?>" tabindex="3" required>
					</div>
				</div>
				<hr class="colorgraph">
				<div class="row">
					<div class="col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-xs-6 col-sm-6 col-md-6">
						<input type="submit" name="sender" class="btn btn-lg btn-success btn-block" value="Enregistrer">
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