SimpleSAMLphp
=============

*SimpleSamlPhp est une application PHP composée de modules qui permet de fédérer des utilisateurs de différents services en ligne à travers le protocole SAML.*

### Installation générale

#### Récupération des sources

* Cloner le projet : 
```
git clone https://github.com/Elipce-Informatique/simplesamlphp.git /var/simplesamlphp --depth=1
```
* Mettre à jour les dépendances :
```
cd /var/simplesamlphp & composer update
```

#### Configurer Apache

* Ajouter un hôte virtuel Apache :
```
<VirtualHost *>
        ServerName service.example.com
        DocumentRoot /var/www/service.example.com

        SetEnv SIMPLESAMLPHP_CONFIG_DIR /var/simplesamlphp/config

        Alias /simplesaml /var/simplesamlphp/www
</VirtualHost>
```

#### Configuration générale

**Les fichiers de configuration se trouvent dans `/var/simplesamlphp/configuration`.**

* Définir un mot de passe administrateur pour l'interface web :
  * Générer un hash : `/var/simplesamlphp/bin/pwgen.php`
  * Modifier le mot de passe : `'auth.adminpassword' => 'setnewpasswordhere',`

### Création d'un IdP


### Liens utiles
