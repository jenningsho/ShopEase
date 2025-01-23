# HTTP KERNEL

Comme un orchestrateur du cycle de vie des requetes/réponses :

Fonctionnement :

1 - Il reçoit une requete HTTP ( ce que l'utilisateur demande, par exemple "/api")
2 - Il déclenche des evenements comme 'Préparer la requete' ou "Trouver le controller", etc
3 - Il appelle un "controlleur" qui va produire une réponse.
4 - Il transforme la réponse en quelque chose que l'utilisateur peut voir ( une page web ou un message JSON)
5 - Il envoie cette réponse au navigateur


# Gestion des erreurs :

400 : erreurs de validation ou requete
401 : authentification manquante/échouée
403 : permission refusée
404 : ressources introuvables
500 : erreur serveur

A renvoyé en JSON de type:
{
    "status": 404,
    "error": "Ressource introuvable",
    "details": ....
}

on va utiliser les eventListeners et ExceptionEvent 
Création d'un gestionnaire centralisés des exceptions

