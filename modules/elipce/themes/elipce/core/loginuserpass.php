<!DOCTYPE html>
<html>
<head>
	<title>Connexion SSO</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="icon" href="<?php echo SimpleSAML_Module::getModuleURL('elipce/favicon.ico') ?>" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel='stylesheet' href="<?php echo SimpleSAML_Module::getModuleURL('elipce/styles.css'); ?>" type='text/css' />
    <link rel='stylesheet' href="<?php echo SimpleSAML_Module::getModuleURL('elipce/normalize.css'); ?>" type='text/css' />
    <style>body {background: url("<?php echo SimpleSAML_Module::getModuleURL('elipce/background.jpg') ?>") 50% fixed;</style>
</head>

<body>

<div class="wrapper">
    <!-- Form -->
    <form class="login" name="loginform" id="loginform" action="?" method="post">
        <img src="<?php echo SimpleSAML_Module::getModuleURL('elipce/logo.jpg') ?>" alt="logo_elipce"/>
        <!-- Username -->
        <input type="text" name="username" id="username" placeholder="Nom d'utilisateur" required autofocus
            <? if (isset($this->data['username'])): ?>
               value="<?= htmlspecialchars($this->data['username']) ?>"
            <? endif; ?>
        /><i class="fa fa-user"></i>
        <!-- Password -->
        <input type="password" name="password" id="user_pass" placeholder="Mot de passe" required />
        <i class="fa fa-key"></i>
        <!-- Messages -->
        <? if ($this->data['errorcode'] !== NULL): ?>
            <p class="alert error">
                Identifiants incorrects !
            </p>
        <? else: ?>
            <p class="alert info">
                demo / demo
            </p>
        <? endif; ?>
        <!-- Submit button -->
        <button type="submit" name="wp-submit" id="wp-submit">
            <i class="spinner"></i>
            <span class="state">Se connecter</span>
        </button>
        <!-- Hidden fields -->
        <? foreach ($this->data['stateparams'] as $name => $value): ?>
            <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>">
        <? endforeach; ?>
    </form>
    <!-- Footer -->
    <footer>
        <a target="blank" href="http://elipce.com/">Elipce Informatique &copy;</a>
    </footer>
</div>
</body>
</html>