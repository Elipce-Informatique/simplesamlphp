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
$systemName = array('%SNAME%' => $uregconf->getString('system.name'));

// Init feedback messages
$feedback['error'] = null;

/**
 * Step 2 : User access page from url in e-mail
 */
if (array_key_exists('token', $_GET)) {
    try {
        $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);

        if (!$email)
            throw new SimpleSAML_Error_Exception(
                'E-mail parameter in request is lost');

        $tg = new SimpleSAML_Auth_TimeLimitedToken($tokenLifetime);
        $tg->addVerificationData($email);
        $token = $_REQUEST['token'];

        // Check token
        if (!$tg->validate_token($token))
            throw new sspmod_selfregister_Error_UserException('invalid_token');

        // Bind values
        $values = [
            'mail' => $email,
            'token' => $token
        ];

    } catch (sspmod_selfregister_Error_UserException $e) {
        // Invalid token
        header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/index.php'));
        exit();
    } catch (SimpleSAML_Error_Exception $e) {
        // Invalid email
        header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/index.php'));
        exit();
    }
    /**
     * Step 3 : Register user account
     */
} elseif (array_key_exists('sender', $_POST)) {
    try {
        // Prepare validator
        $listValidate = sspmod_selfregister_Util::genFieldView($viewAttr);
        $validator = new sspmod_selfregister_Registration_Validation(
            $formFields,
            $listValidate);
        $validValues = $validator->validateInput();

        // Validate form
        $userInfo = sspmod_selfregister_Util::processInput($validValues, $viewAttr);

        // Init database access
        $store = sspmod_selfregister_Storage_UserCatalogue::instantiateStorage();

        // Create user in database
        $store->addUser($userInfo);

        // Redirect
        header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/accountCreated.php'));
        exit();

    } catch (sspmod_selfregister_Error_UserException $e) {
        // Get values
        $values = $validator->getRawInput();
        // Restore values
        $values['mail'] = $_REQUEST['emailconfirmed'];
        $values['token'] = $_REQUEST['token'];
        $values['pw1'] = '';
        $values['pw2'] = '';
        // Set feedback message
        $feedback['error'] = 'Votre saisie est invalide !';
    }
} else {
    // Redirect
    header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/index.php'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <title>Nouveau compte</title>
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
                <h2>Enregistrement <small>Complétez les informations.</small></h2>
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
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-user"></span>
                                </div>
                                <input type="text" name="sn" id="sn" class="form-control input-lg" autofocus
                                       value="<?= $values['sn'] ?>" required placeholder="Nom" tabindex="1">
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
                                       value="<?= $values['givenName'] ?>" required placeholder="Prénom" tabindex="2">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </div>
                        <input type="email" name="mail" id="mail" class="form-control input-lg"
                               value="<?= $values['mail'] ?>" disabled>
                        <input type="hidden" name="token" id="token" value="<?= $values['token'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-asterisk"></span>
                                </div>
                                <input type="password" name="pw1" id="pw2" class="form-control input-lg"
                                       placeholder="Mot de passe" required tabindex="3">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-asterisk"></span>
                                </div>
                                <input type="password" name="pw2" id="pw2" required
                                       class="form-control input-lg" placeholder="Confirmation" tabindex="4">
                            </div>
                        </div>
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