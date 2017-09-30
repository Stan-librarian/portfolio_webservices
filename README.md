# portfolio_webservices

Interrogation des webservices de Portfolio via PHP

cf https://sigbadmin.wordpress.com/2017/06/22/tester-les-webservices-en-php/

À la date du 30/09/2017, les webservices suivants sont interrogeables :
- login_request (Authentification)
- get_patron_summary_request (Détail d'un usager)
- current_loans_request / fetchLoans_request (Prêts d'un usager)
- current_reservations_request / fetchReservations_request (Réservations d'un usager)

------------------------------------------------------------------------
Utilisation :
- activer curl (en décommentant la ligne extension=php_curl.dll dans php.ini), si ce n’est pas fait
- copier les fichiers sur un serveur web avec PHP
- modifier le fichier config.ini pour indiquer l'IP de votre serveur Portfolio
------------------------------------------------------------------------

Using PHP to query the Bibliomondo Portfolio ILS webservices

--> Don't forget to activate curl if needed

At this time, the follwing webservices can be used :
- login_request (Authentification)
- get_patron_summary_request (Patron information)
- current_loans_request / fetchLoans_request (Patron loans)
- current_reservations_request / fetchReservations_request (Patron reservations)
------------------------------------------------------------------------
How to use :
- activate curl if needed 
- copy files on a PHP web server
- edit config.ini to enter your Portfolio server IP
