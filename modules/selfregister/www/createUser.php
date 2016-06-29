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
        $email = filter_input(
            INPUT_GET,
            'email',
            FILTER_VALIDATE_EMAIL);
        if (!$email)
            throw new SimpleSAML_Error_Exception(
                'E-mail parameter in request is lost');

        $tg = new SimpleSAML_Auth_TimeLimitedToken($tokenLifetime);
        $tg->addVerificationData($email);
        $token = $_REQUEST['token'];
        if (!$tg->validate_token($token))
            throw new sspmod_selfregister_Error_UserException('invalid_token');

        $formGen = new sspmod_selfregister_XHTML_Form($formFields, 'newUser.php');

        $showFields = sspmod_selfregister_Util::genFieldView($viewAttr);
        $formGen->fieldsToShow($showFields);
        $formGen->setReadOnly('mail');

        $hidden = array(
            'emailconfirmed' => $email,
            'token' => $token);
        $formGen->addHiddenData($hidden);
        $formGen->setValues(
            array(
                'mail' => $email
            )
        );

        $formGen->setSubmitter('submit_change');
        $formHtml = $formGen->genFormHtml();

        $html = new SimpleSAML_XHTML_Template(
            $config,
            'selfregister:step3_register.tpl.php',
            'selfregister:selfregister');
        $html->data['formHtml'] = $formHtml;
        $html->show();
    } catch (sspmod_selfregister_Error_UserException $e) {
        // Invalid token
        $terr = new SimpleSAML_XHTML_Template(
            $config,
            'selfregister:step1_email.tpl.php',
            'selfregister:selfregister');

        $error = $terr->t(
            $e->getMesgId(),
            $e->getTrVars()
        );
        $terr->data['error'] = htmlspecialchars($error);
        $terr->data['systemName'] = $systemName;
        $terr->show();
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
        $values['mail'] = $hidden['emailconfirmed'] = $_REQUEST['emailconfirmed'];
        $values['token'] = $_REQUEST['token'];
        $values['pw1'] = '';
        $values['pw2'] = '';
        // Set feedback message
        $feedback['error'] = 'Votre saisie est invalide !';
    }
}
?>