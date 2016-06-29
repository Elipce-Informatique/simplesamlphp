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
 * Step 3: User access page from url in e-mail
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

        // Find user attributes
        $userValues = $store->findAndGetUser('mail', $email);

        // Set values
        $values = [
            'emailconfirmed' => $email,
            'token' => $token,
            'uid' => $userValues[$store->userIdAttr]
        ];

    } catch (sspmod_selfregister_Error_UserException $e) {
        // Redirect
        header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/index.php'));
        exit();
    }

} elseif (array_key_exists('sender', $_POST)) {
    try {
        // Prepare validator
        $listValidate = array('pw1', 'pw2');
        $validator = new sspmod_selfregister_Registration_Validation(
            $formFields,
            $listValidate);

        $email = filter_input(INPUT_POST, 'emailconfirmed', FILTER_VALIDATE_EMAIL);

        // Check email
        if (!$email)
            throw new SimpleSAML_Error_Exception(
                'E-mail parameter in request is lost');

        $tg = new SimpleSAML_Auth_TimeLimitedToken($tokenLifetime);
        $tg->addVerificationData($email);
        $token = $_REQUEST['token'];

        // Check token
        if (!$tg->validate_token($token))
            throw new sspmod_selfregister_Error_UserException('invalid_token');

        $userValues = $store->findAndGetUser('mail', $email);
        $validValues = $validator->validateInput();
        $newPw = sspmod_selfregister_Util::validatePassword($validValues);

        // Change password in database
        $store->changeUserPassword($userValues[$store->userIdAttr], $newPw);

        // Redirect
        header('Location: ' . SimpleSAML_Module::getModuleURL('selfregister/passwordRecovered.php'));
        exit();

    } catch (sspmod_selfregister_Error_UserException $e) {

        // Restore values
        $values['emailconfirmed'] = $_REQUEST['emailconfirmed'];
        $values['token'] = $_REQUEST['token'];
        $values['uid'] = $userValues[$store->userIdAttr];

        // Set feedback message
        $feedback['error'] = 'Le mot de passe est invalide !';
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
    <title>Changement de mot de passe</title>
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
                <? endif; ?>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-asterisk"></span>
                                </div>
                                <input type="password" name="pw1" id="pw1" class="form-control input-lg"
                                        placeholder="Nouveau mot de passe" tabindex="1" autofocus required>
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
                                <input type="password" name="pw2" id="pw2" required
                                       class="form-control input-lg" placeholder="Confirmez le mot de passe" tabindex="2">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-xs-6 col-sm-6 col-md-6">
                        <input type="hidden" name="uid" id="uid" value="<?= $values['uid'] ?>">
                        <input type="hidden" name="token" id="token" value="<?= $values['token'] ?>">
                        <input type="hidden" name="emailconfirmed" id="emailconfirmed" value="<?= $values['emailconfirmed'] ?>">
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