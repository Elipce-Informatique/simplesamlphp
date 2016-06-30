<h1>Inscription sur le fournisseur d'identité d'Elipce Informatique</h1>
<p>Bonjour, nous vous remercions pour votre inscription !</p>
<p>Cette email vous a été envoyé afin de pouvoir vérifier la validité de votre adresse : <pre><?php echo $this->data['email']; ?></pre></p>
<p>Pour continuer votre inscription, nous vous demandons de cliquer sur ce lien suivant :</p>
<p><pre><a href="<?php echo $this->data['registerurl']; ?>"><?php echo $this->data['registerurl']; ?></a></pre></p>
<p>Ce lien contient un jeton d'accès sécurisé qui expire au bout de <strong>5 jours</strong>. Passé ce délais il faut renouveler la demande à partir de la page de d'inscription.</p>
<p>Respectueusement, toute l'équipe d'Elipce.</p>