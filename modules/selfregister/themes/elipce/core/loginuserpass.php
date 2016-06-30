<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <title>Connexion SSO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= SimpleSAML_Module::getModuleURL('selfregister/img/favicon.ico') ?>"/>
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
            <!-- Form -->
            <form role="form" name="loginform" id="loginform" action="?" method="post">
                <h2>Single Sign On
                    <small>Connectez-vous.</small>
                </h2>
                <fieldset>
                    <hr class="colorgraph">
                    <!-- Messages -->
                    <? if ($this->data['errorcode'] !== NULL): ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="alert alert-danger alert-dismissible text-center" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                                    &nbsp;Le mot de passe ou l'utilisateur est incorrect.
                                </div>
                            </div>
                        </div>
                    <? else: ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="alert alert-info alert-dismissible text-center" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <span class="glyphicon glyphicon-info-sign"></span>
                                    &nbsp;Email : <strong>demo@elipce.com</strong> / Mot de passe : <strong>demo</strong>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                    <!-- Mail -->
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-user"></span>
                            </div>
                            <input type="email" name="username" id="username" class="form-control input-lg"
                                   value="<?= htmlspecialchars($this->data['username']) ?>" tabindex="1"
                                   placeholder="Nom d'utilisateur" required autofocus>
                        </div>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-asterisk"></span>
                            </div>
                            <input type="password" name="password" id="user_pass" class="form-control input-lg"
                                   placeholder="Mot de passe" tabindex="2" required>
                        </div>
                    </div>
                    <div class="row">
                        <a href="<?= SimpleSAML_Module::getModuleURL('selfregister/lostPassword.php') ?>"
                           class="btn btn-link pull-right">Mot de passe oublié ?</a>
                    </div>
                    <hr class="colorgraph">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <input type="submit" name="wp-submit" id="wp-submit"
                                   class="btn btn-lg btn-success btn-block" value="Se connecter">
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <a href="<?= SimpleSAML_Module::getModuleURL('selfregister/newUser.php') ?>"
                               class="btn btn-lg btn-primary btn-block">S'inscrire</a>
                        </div>
                    </div>
                </fieldset>
                <!-- Hidden fields -->
                <? foreach ($this->data['stateparams'] as $name => $value): ?>
                    <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>">
                <? endforeach; ?>
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