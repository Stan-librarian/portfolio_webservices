# portfolio_webservices

------------------------------------------------------------------------

Licence : GPL

------------------------------------------------------------------------

[French]

Interrogation des webservices de Portfolio via PHP

cf https://sigbadmin.wordpress.com/2017/06/22/tester-les-webservices-en-php/

À la date du 30/09/2017, les webservices suivants sont interrogeables :
- login_request (Authentification)
- get_patron_summary_request (Détail d'un usager)
- current_loans_request / fetchLoans_request (Prêts d'un usager)
- current_reservations_request / fetchReservations_request (Réservations d'un usager)

------------------------------------------------------------------------

Utilisation :
- copier les fichiers sur un serveur web avec PHP
- activer curl (en décommentant la ligne extension=php_curl.dll dans php.ini), si ce n’est pas fait
- modifier le fichier config.ini pour indiquer l'IP de votre serveur Portfolio

------------------------------------------------------------------------

[English]

Using PHP to query the Bibliomondo Portfolio ILS webservices

--> Don't forget to activate curl if needed

At this time, the follwing webservices can be used :
- login_request (Authentification)
- get_patron_summary_request (Patron information)
- current_loans_request / fetchLoans_request (Patron loans)
- current_reservations_request / fetchReservations_request (Patron reservations)

------------------------------------------------------------------------

How to use :
- copy files on a PHP web server
- activate curl if needed 
- edit config.ini to enter your Portfolio server IP
