<?php
/**
 * Initialization
 */

// Load config
$config = SimpleSAML_Configuration::getInstance();
$uregconf = SimpleSAML_Configuration::getConfig('module_selfregister.php');
$tokenLifetime = $uregconf->getInteger('mailtoken.lifetime');
$viewAttr = $uregconf->getArray('attributes');
$formFields = $uregconf->getArray('formFields');

// Init database access
$store = sspmod_selfregister_Storage_UserCatalogue::instantiateStorage();

// Init feedback messages
$feedback['error'] = null;

/**
 * Step 2 : User have submitted e-mail adress for password recovery
 */
if (array_key_exists('emailreg', $_REQUEST)) {
	try {
		$email = filter_input(INPUT_POST, 'emailreg', FILTER_VALIDATE_EMAIL);

		// Check email validity
		if(!$email) {
			$rawValue = isset($_REQUEST['emailreg'])?$_REQUEST['emailreg']:NULL;
			if (!$rawValue) {
				throw new sspmod_selfregister_Error_UserException(
					'void_value',
					'mail', '',
					'Validation of user input failed.'
					.' Field:'.'mail'
					.' is empty');
			} else {
				throw new sspmod_selfregister_Error_UserException(
					'illegale_value',
					'mail',
					$rawValue,
					'Validation of user input failed.'
					.' Field:'.'mail'
					.' Value:'.$rawValue);
			}
		}

		// Check if email exists
		if (!$store->isRegistered('mail', $email)) {
			throw new sspmod_selfregister_Error_UserException(
				'email_not_found',
				$email, '',
				'Try to reset password, but mail address not found: '.$email
			);
		}

		// Generate email token
		$tg = new SimpleSAML_Auth_TimeLimitedToken($tokenLifetime);
		$tg->addVerificationData($email);
		$newToken = $tg->generate_token();

		$url = SimpleSAML_Module::getModuleURL('selfregister/recoverPassword.php');

		$registerurl = SimpleSAML_Utilities::addURLparameter(
			$url,
			array(
				'email' => $email,
				'token' => $newToken));

		// Build email template
		$mailt = new SimpleSAML_XHTML_Template(
			$config,
			'selfregister:lostPasswordMail_token.tpl.php',
			'selfregister:selfregister');

		$mailt->data['registerurl'] = $registerurl;
		$systemName = array('%SNAME%' => $uregconf->getString('system.name') );
		$mailt->data['systemName'] = $systemName;

		// Send email
		$mailer = new sspmod_selfregister_XHTML_Mailer(
			$email,
			$uregconf->getString('mail.subject'),
			$uregconf->getString('mail.from'),
			NULL,
			$uregconf->getString('mail.replyto'));
		$mailer->setTemplate($mailt);
		$mailer->send();

		// Redirect
		header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/emailSent.php'));
		exit();

	} catch(sspmod_selfregister_Error_UserException $e) {
		// Set feedback message
		$feedback['error'] = 'L\'email fourni n\'est pas valide !';
	}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex">
	<title>Mot de passe oublié</title>
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
			<form role="form" action="?" method="post">
				<h2>Mot de passe oublié <small>Saisissez votre email.</small></h2>
				<fieldset>
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
					<? endif; ?>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-envelope"></span>
							</div>
							<input type="email" name="emailreg" id="emailreg" class="form-control input-lg" autofocus
								   required placeholder="Email">
						</div>
					</div>
					<div class="row">

						<div class="col-xs-6 col-sm-6 col-md-6">
							<button onclick="history.back();"
							   class="btn btn-lg btn-success btn-block">Retour</button>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<input type="submit" name="save" id="wp-submit"
								   class="btn btn-lg btn-primary btn-block" value="Envoyer">
						</div>
					</div>
				</fieldset>
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