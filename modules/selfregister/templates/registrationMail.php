<h1>Inscription - IdP Elipce</h1>
<p>Bonjour, nous vous remercions pour votre inscription !</p>
<p>Cette email vous a été envoyé afin de pouvoir vérifier la validité de votre adresse : <pre><?php echo $this->data['email']; ?></pre></p>
<p>Pour continuer votre inscription, nous vous demandons de cliquer sur ce lien suivant :</p>
<p><a href="<?php echo $this->data['registerurl']; ?>"><?php echo $this->data['registerurl']; ?></a></p>
<p><i>Ce lien contient un jeton d'accès sécurisé qui expire au bout de <strong>5 jours</strong>.</i></p>
<br/><p>Respectueusement, toute l'équipe d'Elipce.</p>