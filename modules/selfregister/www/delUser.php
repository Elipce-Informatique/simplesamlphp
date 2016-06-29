<?php
/**
 * Initialization
 */

// Load configuration
$config = SimpleSAML_Configuration::getInstance();
$uregconf = SimpleSAML_Configuration::getConfig('module_selfregister.php');

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

/**
 * Form was sent
 */
if (array_key_exists('sender', $_POST)) {

    // Delete user object
    $store->delUser($attributes[$store->userIdAttr][0]);

    // Now when a User delete himself sucesfully, System log out him.
    // In the future when admin delete a user a msg will be showed
    // $html->data['userMessage'] = 'message_userdel';
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
    <div class="container-fluid">
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
        <div class="col-xs-12 col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2">
            <img class="img im-responsive center-block" src="img/logo.jpg"/>
            <div class="jumbotron text-center">
                <h1><span class="glyphicon glyphicon-exclamation-sign"></span></h1>
                <p>Êtes-vous sûr de vouloir supprimer votre compte ?</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-8 col-sm-offset-2 col-md-offset-2">
            <form role="form" action="?" method="post">
                <hr class="colorgraph">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <a href="index.php" class="btn btn-lg btn-primary btn-block">Non</a>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <input type="submit" name="sender" class="btn btn-lg btn-danger btn-block" value="Oui">
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