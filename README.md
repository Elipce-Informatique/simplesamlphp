SimpleSAMLphp
=============

*SimpleSamlPhp est une application PHP composée de modules qui permet de fédérer des utilisateurs de différents services en ligne à travers le protocole SAML.*

## Installation générale

#### Récupération des sources

* Cloner le projet : 
```
git clone https://github.com/Elipce-Informatique/simplesamlphp.git /home/__user__/sso --depth=1
```
* Mettre à jour les dépendances :
```
cd /home/__user__/sso & composer update
```

#### Configurer Apache

* Ajouter un hôte virtuel Apache dans `/etc/apache2/sites-enabled` :
```
<VirtualHost *:80>
    DocumentRoot /home/__user__/sso/www
    ServerName  mondomaine.com

    ErrorLog /var/log/apache2/mondomaine.com_error.log
    CustomLog /var/log/apache2/mondomaine.com_access.log combined

    <Directory /home/__user__/sso/www/>
       Options -Indexes
       AllowOverride None
       DirectoryIndex /module.php/core/frontpage_welcome.php
    </Directory>
    
</VirtualHost>
```
* Générer un certificat SSL :
```
 cd /home/letsencrypt & ./letsencrypt-auto
```

#### Configuration générale

*Les fichiers de configuration se trouvent dans `./config`.*

* Générer un hash :
```
./bin/pwgen.php
```
* Définir un mot de passe administrateur pour l'interface web :
```
'auth.adminpassword' => 'setnewpasswordhere',
```
* Ajouter le chemin vers la racine :
```
'baseurlpath' => '/',
```
* Générer un sel aléatoire :
```
tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
```
* Copier le sel généré dans la configuration :
```
'secretsalt' => 'randombytesinsertedhere',
```
* Remplir les autres informations :
```
'technicalcontact_name'     => 'Andreas Åkre Solberg',
'technicalcontact_email'    => 'andreas.solberg@uninett.no',
...

'language.default'      => 'fr',
```

#### Activer un module
 
*Dans SimpleSamlPhp, chaque fonctionnalité fait l'objet d'un module. Tous les modules sont regroupés dans le repertoire `./modules`. Par défaut certains modules ne sont pas activés.*

* Pour activer un plugin :
```
touch ./modules/mon_module/enable
```
 
## Création d'un IdP

#### Configurer le module d'authentification

* Editer le fichier `./config/authsources.php` :
```
<?php
$config = array(
    'example-userpass' => array(
        'exampleauth:UserPass',
        'student:studentpass' => array(
            'uid' => array('student'),
            'eduPersonAffiliation' => array('member', 'student'),
        ),
        'employee:employeepass' => array(
            'uid' => array('employee'),
            'eduPersonAffiliation' => array('member', 'employee'),
        ),
    ),
);
```

Plusieurs méthodes sont disponibles sous forme de modules à activer :
* LinkedIn
* Facebook
* Twitter
* Base de données
* LDAP

#### Configurer l'IdP

*Le fournisseur d'identité est configuré les fichiers `./metadata/saml20-idp-hosted.php`.*
```
<?php
$metadata['__DYNAMIC:1__'] = array(
    /*
     * The hostname for this IdP. This makes it possible to run multiple
     * IdPs from the same configuration. '__DEFAULT__' means that this one
     * should be used by default.
     */
    'host' => '__DEFAULT__',

    /*
     * The private key and certificate to use when signing responses.
     * These are stored in the cert-directory.
     */
    'privatekey' => 'mondomaine.com.pem',
    'certificate' => 'mondomaine.com.crt',

    /*
     * The authentication source which should be used to authenticate the
     * user. This must match one of the entries in config/authsources.php.
     */
    'auth' => 'example-userpass',
);
```

* Copier la clé privée et le certificat public :
```
mkdir cert
cp /etc/letsencrypt/live/mondomaine.com/privkey.pem ./cert/mondomaine.com.pem
cp /etc/letsencrypt/live/mondomaine.com/fullchain.pem ./cert/mondomaine.com.crt
```
* Changer la méthode d'authentification utilisée :
```
'auth' => 'example-userpass',
```
* Configurer le mapping des attributs :
```
 'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
        'authproc' => array(
            10 => array(
                'class' => 'core:AttributeMap',
                'userid'    => 'uid',
                'email'     => 'mail',
                'lastname'  => 'sn',
                'firstname' => 'givenName',
            ),
        ),
```
* Activer le module SAML :
```
touch ./modules/saml/enable
```

#### Ajouter des fournisseurs de service

*Le fournisseur d'identité a besoin de connaitre les fournisseurs de service qui vont s'y connecter. La déclaration se fait dans les fichiers suivants : `./metadata/saml20-sp-remote.php`.*

* Pour configurer un nouveau fournisseur de service :
```
<?php
$metadata['https://sp.example.org/simplesaml/module.php/saml/sp/metadata.php/default-sp'] = array(
    'AssertionConsumerService' => 'https://sp.example.org/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp',
    'SingleLogoutService'      => 'https://sp.example.org/simplesaml/module.php/saml/sp/saml2-logout.php/default-sp',
);
```

#### Configurer l'inscription
*L'inscription est gérée par le module `selfregister` et doit impérativement être configuré avant d'être activé.* 

* Créer la base de données :
```
CREATE DATABASE identities;
GRANT ALL on identities.* to 'user'@'localhost' IDENTIFIED by '1234';
FLUSH PRIVILEGES;
```
* Créer la table SQL :
```
CREATE TABLE users (
    `userid` int(32) NOT NULL AUTO_INCREMENT,
    `password` text NOT NULL,
    `salt` blob,
    `firstname` text,
    `lastname` text,
    `created` datetime NOT NULL,
    `email` varchar(255) NOT NULL,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`userid`),
    UNIQUE KEY `UE` (`email`)
    )
```
* Configurer la source d'authentification dans `./config/authsources.php` :
```
'selfregister-mysql' => array(
    'sqlauth:SQL',
        'dsn' => 'mysql:host=localhost;dbname=identites',
        'username' => 'user',
        'password' => '1234',
        'query' => 'SELECT userid, firstname, lastname, email FROM users WHERE userid = :username
                    AND password = SHA2 (
                        CONCAT(
                            (SELECT salt FROM users WHERE userid = :username),
                            :password
                        ),
                        512
                    )',
    ),
```
* Mettre à jour le mapping des attributs dans `./config/authsources.php` :
```
'authproc' => array(

    10 => array(
        'class' => 'core:AttributeMap',
        'userid'    => 'uid',
        'email'     => 'mail',
        'lastname'  => 'sn',
        'firstname' => 'givenName',
    ),
```
* Copier le fichier de configuration du module :
```
cp ./modules/selfregister/config-templates/module_selfregister.php ./config
```
* Configurer l'inscription et le mapping par défaut :
```
'system.name' => 'Elipce Informatique',

'mail.from'     => 'Admin <admin@domain.com>',
'mail.replyto'  => 'Admin <admin@domain.com>',
'mail.subject'  => 'Vérification E-mail',

'sql' => array(
    'user.id.param' => 'email',
),

'attributes'  => array(
    'username'      => 'mail',
    'firstname'     => 'givenName',
    'lastname'      => 'sn',
    'email'         => 'mail',
    'userPassword'  => 'password',
),
```
* Activer le module d'inscription :
```
touch ./modules/selfregister/enable
```

## Liens utiles
* [Documentation officielle](https://simplesamlphp.org/docs/stable/)
* [Modules disponibles](https://simplesamlphp.org/modules)
* [Dépôt OneLogin PHP SAML](https://github.com/onelogin/php-saml)
* [SAML pour les nuls](https://blog.surf.nl/en/saml-for-dummies/)
