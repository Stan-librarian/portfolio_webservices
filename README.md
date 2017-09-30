# portfolio_webservices
Interrogation des webservices de Portfolio via PHP

cf https://sigbadmin.wordpress.com/2017/06/22/tester-les-webservices-en-php/

Attention, il faut activer curl (en décommentant la ligne extension=php_curl.dll dans php.ini), si ce n’est pas fait.

À la date du 30/09/2017, les webservices suivants sont interrogeables :
- login_request (Authentification)
- get_patron_summary_request (Détail d'un usager)
- current_loans_request / fetchLoans_request (Prêts d'un usager)

------------------------------------------------------------------------

Using PHP to query the Bibliomondo Portfolio ILS webservices

Don't forget to activate curl in needed

For the moment, the follwing webservices can be used :
- login_request (Authentification)
- get_patron_summary_request (Patron information)
- current_loans_request / fetchLoans_request (Patron loans)
